<?php

namespace App\Http\Controllers;

use App\Models\RoomPrompt;
use App\Models\AiCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DesignController extends Controller
{
    public function index()
    {
        $kitchenStyles = json_decode(file_get_contents(resource_path('data/kitchen-styles.json')), true);
        $bedroomStyles = json_decode(file_get_contents(resource_path('data/bedroom-styles.json')), true);
        $livingRoomStyles = json_decode(file_get_contents(resource_path('data/living-room-styles.json')), true);
        $diningRoomStyles = json_decode(file_get_contents(resource_path('data/dining-room-styles.json')), true);
        
        return Inertia::render('design/index', [
            'kitchenStyles' => $kitchenStyles['kitchen_styles'],
            'bedroomStyles' => $bedroomStyles['bedroom_styles'],
            'livingRoomStyles' => $livingRoomStyles['living_room_styles'],
            'diningRoomStyles' => $diningRoomStyles['dining_room_styles']
        ]);
    }

    public function generateDesign(Request $request)
    {
        $request->validate([
            'image'     => 'required|string', // base64 image
            'roomType'  => 'required|string', // kitchen | bedroom | living_room | dining_room
            'roomStyle' => 'required|string', // style key
        ]);

        try {

            /* -------------------------------------------------
            | 1. Decode Base64 Image & Save Temporarily
            |-------------------------------------------------*/
            $imageData = $request->image;

            if (str_starts_with($imageData, 'data:image')) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            }

            $imageBinary = base64_decode($imageData);

            if ($imageBinary === false) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Invalid image data',
                ], 422);
            }

            $tempImagePath = storage_path('app/temp_' . uniqid() . '.png');
            file_put_contents($tempImagePath, $imageBinary);

            /* -------------------------------------------------
            | 2. Load Style Keywords (ROOM-AWARE)
            |-------------------------------------------------*/
            $styleKeywords = '';

            $roomConfig = [
                'kitchen'     => ['file' => 'kitchen-styles.json',     'key' => 'kitchen_styles'],
                'bedroom'     => ['file' => 'bedroom-styles.json',     'key' => 'bedroom_styles'],
                'living_room' => ['file' => 'living-room-styles.json', 'key' => 'living_room_styles'],
                'dining_room' => ['file' => 'dining-room-styles.json', 'key' => 'dining_room_styles'],
            ];

            if (isset($roomConfig[$request->roomType])) {

                $config = $roomConfig[$request->roomType];
                $path   = resource_path('data/' . $config['file']);

                if (file_exists($path)) {
                    $styles = json_decode(file_get_contents($path), true);

                    $styleKeywords =
                        $styles[$config['key']][$request->roomStyle]['prompt_keywords']
                        ?? '';
                }
            }

            /* -------------------------------------------------
            | 3. STRONG PROMPT (LAYOUT & CAMERA LOCK)
            |-------------------------------------------------*/
            $prompt = "
            ABSOLUTE PIXEL-LOCK INSTRUCTION (CRITICAL):
            You must modify the existing image IN PLACE.
            Do NOT generate a new image.

            FRAME & CAMERA LOCK:
            - Treat the original image as a fixed background plate
            - Output must have identical framing, crop, and composition
            - Do NOT recenter, recompose, shift, or reframe
            - The distance of all objects from the image edges must remain unchanged
            - The result should visually align if overlaid on the original image

            CAMERA CONSTRAINTS:
            - Same camera position
            - Same camera height
            - Same viewing angle
            - Same phone camera perspective
            - No zoom, no crop, no perspective correction
            - No architectural or showroom photography

            TASK:
            This is an existing {$request->roomType} photographed with a phone camera.
            Visually renovate the space by repainting and refacing surfaces ONLY.

            STRICTLY FORBIDDEN:
            - Any layout change
            - Any movement or resizing of cabinets, counters, walls, windows, or doors
            - Any change in passage width or counter depth
            - Any reinterpretation of geometry
            - Any camera or framing change

            ALLOWED CHANGES ONLY:
            - Surface finishes
            - Wall colors or tiles
            - Lighting fixtures
            - Cabinet finishes (if present)

            REALISM:
            - Natural lighting
            - Slight imperfections allowed
            - Must look like the SAME PHOTO with upgraded finishes
            - Not a render, not a catalog image
            ";

            if (!empty($styleKeywords)) {
                $prompt .= "\nSTYLE FOCUS: {$styleKeywords}\n";
            }

            $prompt = trim(preg_replace('/\s+/', ' ', $prompt));

            /* -------------------------------------------------
            | 4. OpenAI IMAGE EDIT
            |-------------------------------------------------*/
            $response = Http::timeout(120)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                ])
                ->attach('image', fopen($tempImagePath, 'r'), 'room.png')
                ->post('https://api.openai.com/v1/images/edits', [
                    'model'  => 'gpt-image-1',
                    'prompt' => $prompt,
                    'size'   => '1024x1024',
                    'n'      => 1,
                ]);

            unlink($tempImagePath);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'error'   => $response->json() ?? $response->body(),
                ], $response->status());
            }

            $data = $response->json();

            /* -------------------------------------------------
            | 5. Handle Base64 Output
            |-------------------------------------------------*/
            if (!isset($data['data'][0]['b64_json'])) {
                return response()->json([
                    'success' => false,
                    'error'   => 'No image returned by OpenAI',
                ], 500);
            }

            $finalImageBinary = base64_decode($data['data'][0]['b64_json']);

            $fileName = 'ai-designs/' . uniqid('design_') . '.png';
            Storage::disk('public')->put($fileName, $finalImageBinary);

            $imageUrl = asset('storage/' . $fileName);

            /* -------------------------------------------------
            | 6. Credit Log
            |-------------------------------------------------*/
            AiCredit::create([
                'room_type'  => $request->roomType,
                'room_style' => $request->roomStyle,
                'cost'       => 0,
                'model'      => 'gpt-image-1',
                'size'       => '1024x1024',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success'   => true,
                'image_url'=> $imageUrl,
                'prompt'   => $prompt,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function calculateCost($model, $size, $headers)
    {
        // Try to get cost from OpenAI response headers
        $usage = $headers['openai-processing-ms'] ?? null;
        
        // If no usage data, calculate based on OpenAI pricing
        $pricing = [
            'dall-e-3' => [
                '1024x1024' => 0.040,
                '1024x1792' => 0.080,
                '1792x1024' => 0.080
            ],
            'dall-e-2' => [
                '1024x1024' => 0.020,
                '512x512' => 0.018,
                '256x256' => 0.016
            ]
        ];
        
        return $pricing[$model][$size] ?? 0.040;
    }
}