<?php

namespace App\Http\Controllers;

use App\Models\RoomPrompt;
use App\Models\AiCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DesignController extends Controller
{
    public function generateDesign(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
            'roomType' => 'required|string',
            'roomStyle' => 'required|string'
        ]);

        try {
            // Get custom prompt from database or use default
            $roomPrompt = RoomPrompt::where('room_type', $request->roomType)->first();
            
            if ($roomPrompt) {
                $prompt = str_replace('{roomStyle}', $request->roomStyle, $roomPrompt->prompt);
            } else {
                $prompt = "
                Create a REALISTIC interior redesign of this {$request->roomType} in {$request->roomStyle} style.
                This must look like an actual, professionally executed interior design — NOT conceptual, NOT artistic, NOT imaginary.

                STRICT RULES:
                - Keep the existing room layout, size, walls, floor tiles, ceiling height, windows, doors, and balcony EXACTLY the same.
                - Do NOT move, resize, replace, or redesign windows or doors.
                - Do NOT change room structure or proportions.

                DESIGN REQUIREMENTS:
                - Add practical, real-world furniture that can be bought and used in real homes.
                - Use realistic materials, finishes, fabrics, and colors commonly used in real interior projects.
                - Furniture placement must be functional, comfortable, and logical for daily use.
                - Lighting should look natural and achievable with real ceiling lights, wall lights, or lamps.
                - Add simple, tasteful decor (curtains, cushions, rug, wall art, plants) — nothing exaggerated.

                VISUAL QUALITY:
                - Photorealistic interior
                - Natural daylight + realistic indoor lighting
                - Accurate scale and proportions
                - Real textures (wood, fabric, glass, metal)
                - Looks like a real apartment ready to move in
                - No fantasy elements, no dramatic colors, no showroom exaggeration

                The final result should look like a real interior photograph of a fully furnished {$request->roomType}.
                ";
            }

            $response = Http::timeout(120)->withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/images/generations', [
                'model' => 'dall-e-3',
                'prompt' => $prompt,
                'n' => 1,
                'size' => '1024x1024',
                'quality' => 'standard'
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
                    'image_url' => $data['data'][0]['url']
                ]);
            }

            return response()->json(['success' => false, 'error' => 'Failed to generate image'], 500);

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