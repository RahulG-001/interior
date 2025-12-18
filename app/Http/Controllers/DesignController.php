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
        
        return Inertia::render('design/index', [
            'kitchenStyles' => $kitchenStyles['kitchen_styles']
        ]);
    }

    public function generateDesign(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
            'roomType' => 'required|string',
            'roomStyle' => 'required|string'
        ]);

        try {

             $styleKeywords = '';
            $extraDetails  = '';

            // Get custom prompt from database or use default
            $roomPrompt = RoomPrompt::where('room_type', $request->roomType)->first();

            if ($roomPrompt) {
                $prompt = str_replace(
                    ['{roomStyle}', '{roomType}'],
                    [$request->roomStyle, $request->roomType],
                    $roomPrompt->prompt
                );
            } else {
                $prompt = "Redesign this {roomType} into a professionally designed {roomStyle} interior.

                Preserve ONLY:
                - room layout
                - wall positions
                - door and window locations
                - ceiling height

                Add and install new furniture, cabinetry, finishes, colors, materials, lighting fixtures, and decor elements.

                If the room is empty or unfinished, fully furnish and complete the interior appropriately.

                Do NOT reuse any existing furniture or materials.

                Use realistic, real-world interior design standards with correct proportions and practical materials.

                The final image must look clearly different from the original, like a completed interior renovation.

                Photorealistic, natural lighting, professional interior photography.";
            }

            if ($request->roomType === 'kitchen' && $styleKeywords) {
                $prompt .= " Style focus: {$styleKeywords}.";
            }

            if ($extraDetails) {
                $prompt .= " Include details such as {$extraDetails}.";
            }

             $imageData = $request->image;

            if (str_starts_with($imageData, 'data:image')) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            }

            $imageContent = base64_decode($imageData);

            $tempImagePath = tempnam(sys_get_temp_dir(), 'room_') . '.png';
            file_put_contents($tempImagePath, $imageContent);

            [$width, $height] = getimagesize($tempImagePath);

            //  dd($prompt);
            $prompt = trim(preg_replace('/\s+/', ' ', $prompt));

            $prompt = str_replace(
                ['{roomType}', '{roomStyle}'],
                [$request->roomType, $request->roomStyle],
                $prompt
            );

            $image = imagecreatefromstring($imageContent);

            $rgbaImage = imagecreatetruecolor(imagesx($image), imagesy($image));
            imagealphablending($rgbaImage, false);
            imagesavealpha($rgbaImage, true);

            $transparent = imagecolorallocatealpha($rgbaImage, 0, 0, 0, 127);
            imagefill($rgbaImage, 0, 0, $transparent);

            imagecopy($rgbaImage, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

            $tempImagePath = tempnam(sys_get_temp_dir(), 'room_') . '.png';
            imagepng($rgbaImage, $tempImagePath);

            imagedestroy($image);
            imagedestroy($rgbaImage);

            $maskPath = tempnam(sys_get_temp_dir(), 'mask_') . '.png';

            $mask = imagecreatetruecolor(imagesx($rgbaImage), imagesy($rgbaImage));
            imagealphablending($mask, false);
            imagesavealpha($mask, true);

            // White = editable (opaque)
            $white = imagecolorallocatealpha($mask, 255, 255, 255, 0);
            imagefill($mask, 0, 0, $white);

            imagepng($mask, $maskPath);
            imagedestroy($mask);

            $response = Http::timeout(120)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                ])
                ->attach('image', file_get_contents($tempImagePath), 'room.png')
                ->attach('mask', file_get_contents($maskPath), 'mask.png')
                ->post('https://api.openai.com/v1/images/edits', [
                    'prompt' => $prompt,
                    'n'      => 1,
                    'size'   => '1024x1024',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Extract cost from response headers or calculate based on model/size
                $cost = $this->calculateCost('dall-e-3', '1024x1024', $response->headers());
                
                AiCredit::create([
                    'room_type' => $request->roomType,
                    'room_style' => $request->roomStyle,
                    'cost' => $cost,
                    'model' => 'dall-e-3',
                    'size' => '1024x1024',
                    'ip_address' => $request->ip()
                ]);
                
                return response()->json([
                    'success' => true,
                    'image_url' => $data['data'][0]['url'],
                    'prompt' => $prompt
                ]);
            }

            @unlink($tempImagePath);
            @unlink($maskPath);

            return response()->json([
                'success' => false,
                'error' => $response->json() ?? $response->body(),
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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