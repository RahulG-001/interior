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
        $masterBedroomStyles = json_decode(file_get_contents(resource_path('data/master-badroom.json')), true);
        $childrensBedroomStyles = json_decode(file_get_contents(resource_path('data/childrens-bedroom.json')), true);
        $guestBedroomStyles = json_decode(file_get_contents(resource_path('data/guest-bedroom.json')), true);
        $bathroomStyles = json_decode(file_get_contents(resource_path('data/bathroom-toilet.json')), true);
        $balconyStyles = json_decode(file_get_contents(resource_path('data/balcony-sit-out.json')), true);
        $homeOfficeStyles = json_decode(file_get_contents(resource_path('data/home-office-study-room.json')), true);
        
        // Commercial space styles
        $openPlanOfficeStyles = json_decode(file_get_contents(resource_path('data/open-plan-office.json')), true);
        $executiveOfficeStyles = json_decode(file_get_contents(resource_path('data/executive-office.json')), true);
        $conferenceRoomStyles = json_decode(file_get_contents(resource_path('data/conference-room.json')), true);
        $receptionLobbyStyles = json_decode(file_get_contents(resource_path('data/reception-lobby.json')), true);
        $coWorkingSpaceStyles = json_decode(file_get_contents(resource_path('data/co-working-space.json')), true);
        $retailApparelStyles = json_decode(file_get_contents(resource_path('data/retail-apparel.json')), true);
        $supermarketStyles = json_decode(file_get_contents(resource_path('data/supermarket.json')), true);
        $showroomStyles = json_decode(file_get_contents(resource_path('data/showroom.json')), true);
        $fineDiningRestaurantStyles = json_decode(file_get_contents(resource_path('data/fine-dining-restaurant.json')), true);
        $cafeCoffeeShopStyles = json_decode(file_get_contents(resource_path('data/cafe-coffee-shop.json')), true);
        $barLoungeStyles = json_decode(file_get_contents(resource_path('data/bar-lounge.json')), true);
        $hotelLobbyStyles = json_decode(file_get_contents(resource_path('data/hotel-lobby.json')), true);
        $hotelGuestRoomStyles = json_decode(file_get_contents(resource_path('data/hotel-guest-room.json')), true);
        $clinicMedicalOfficeStyles = json_decode(file_get_contents(resource_path('data/clinic-medical-office.json')), true);
        $salonSpaStyles = json_decode(file_get_contents(resource_path('data/salon-spa.json')), true);
        $gymFitnessCenterStyles = json_decode(file_get_contents(resource_path('data/gym-fitness-center.json')), true);
        $bankBranchStyles = json_decode(file_get_contents(resource_path('data/bank-branch.json')), true);
        $classroomLectureHallStyles = json_decode(file_get_contents(resource_path('data/classroom-lecture-hall.json')), true);
        
        return Inertia::render('design/index', [
            'kitchenStyles' => $kitchenStyles['kitchen_styles'],
            'bedroomStyles' => $bedroomStyles['bedroom_styles'],
            'livingRoomStyles' => $livingRoomStyles['living_room_styles'],
            'diningRoomStyles' => $diningRoomStyles['dining_room_styles'],
            'masterBedroomStyles' => $masterBedroomStyles['master_bedroom']['designs'],
            'childrensBedroomStyles' => $childrensBedroomStyles['childrens_bedroom']['designs'] ?? [],
            'guestBedroomStyles' => $guestBedroomStyles['guest_bedroom']['designs'] ?? [],
            'bathroomStyles' => $bathroomStyles['bathroom']['designs'] ?? [],
            'balconyStyles' => $balconyStyles['balcony']['designs'] ?? [],
            'homeOfficeStyles' => $homeOfficeStyles['home_office']['designs'] ?? [],
            
            // Commercial space styles
            'openPlanOfficeStyles' => $openPlanOfficeStyles['open_plan_office']['designs'] ?? [],
            'executiveOfficeStyles' => $executiveOfficeStyles['executive_office']['designs'] ?? [],
            'conferenceRoomStyles' => $conferenceRoomStyles['conference_room']['designs'] ?? [],
            'receptionLobbyStyles' => $receptionLobbyStyles['reception_lobby']['designs'] ?? [],
            'coWorkingSpaceStyles' => $coWorkingSpaceStyles['co_working_space']['designs'] ?? [],
            'retailApparelStyles' => $retailApparelStyles['retail_apparel']['designs'] ?? [],
            'supermarketStyles' => $supermarketStyles['supermarket']['designs'] ?? [],
            'showroomStyles' => $showroomStyles['showroom']['designs'] ?? [],
            'fineDiningRestaurantStyles' => $fineDiningRestaurantStyles['fine_dining_restaurant']['designs'] ?? [],
            'cafeCoffeeShopStyles' => $cafeCoffeeShopStyles['cafe_coffee_shop']['designs'] ?? [],
            'barLoungeStyles' => $barLoungeStyles['bar_lounge']['designs'] ?? [],
            'hotelLobbyStyles' => $hotelLobbyStyles['hotel_lobby']['designs'] ?? [],
            'hotelGuestRoomStyles' => $hotelGuestRoomStyles['hotel_guest_room']['designs'] ?? [],
            'clinicMedicalOfficeStyles' => $clinicMedicalOfficeStyles['clinic_medical_office']['designs'] ?? [],
            'salonSpaStyles' => $salonSpaStyles['salon_spa']['designs'] ?? [],
            'gymFitnessCenterStyles' => $gymFitnessCenterStyles['gym_fitness_center']['designs'] ?? [],
            'bankBranchStyles' => $bankBranchStyles['bank_branch']['designs'] ?? [],
            'classroomLectureHallStyles' => $classroomLectureHallStyles['classroom_lecture_hall']['designs'] ?? [],
        ]);
    }

    public function generateDesign(Request $request)
    {
        $request->validate([
            'image'     => 'required|string',
            'roomType'  => 'required|string',
            'spaceType' => 'required|string',
            'style'     => 'required|string',
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
                'living-room' => ['file' => 'living-room-styles.json', 'key' => 'living_room_styles'],
                'dining-room' => ['file' => 'dining-room-styles.json', 'key' => 'dining_room_styles'],
                'master-badroom' => ['file' => 'master-badroom.json', 'key' => 'master_bedroom', 'subkey' => 'designs'],
                'childrens-bedroom' => ['file' => 'childrens-bedroom.json', 'key' => 'childrens_bedroom', 'subkey' => 'designs'],
                'guest-bedroom' => ['file' => 'guest-bedroom.json', 'key' => 'guest_bedroom', 'subkey' => 'designs'],
                'bathroom-toilet' => ['file' => 'bathroom-toilet.json', 'key' => 'bathroom', 'subkey' => 'designs'],
                'balcony-sit-out' => ['file' => 'balcony-sit-out.json', 'key' => 'balcony', 'subkey' => 'designs'],
                'home-office-study-room' => ['file' => 'home-office-study-room.json', 'key' => 'home_office', 'subkey' => 'designs'],
                
                // Commercial spaces
                'open-plan-office' => ['file' => 'open-plan-office.json', 'key' => 'open_plan_office', 'subkey' => 'designs'],
                'executive-office' => ['file' => 'executive-office.json', 'key' => 'executive_office', 'subkey' => 'designs'],
                'conference-room' => ['file' => 'conference-room.json', 'key' => 'conference_room', 'subkey' => 'designs'],
                'reception-lobby' => ['file' => 'reception-lobby.json', 'key' => 'reception_lobby', 'subkey' => 'designs'],
                'co-working-space' => ['file' => 'co-working-space.json', 'key' => 'co_working_space', 'subkey' => 'designs'],
                'retail-apparel' => ['file' => 'retail-apparel.json', 'key' => 'retail_apparel', 'subkey' => 'designs'],
                'supermarket' => ['file' => 'supermarket.json', 'key' => 'supermarket', 'subkey' => 'designs'],
                'showroom' => ['file' => 'showroom.json', 'key' => 'showroom', 'subkey' => 'designs'],
                'fine-dining-restaurant' => ['file' => 'fine-dining-restaurant.json', 'key' => 'fine_dining_restaurant', 'subkey' => 'designs'],
                'cafe-coffee-shop' => ['file' => 'cafe-coffee-shop.json', 'key' => 'cafe_coffee_shop', 'subkey' => 'designs'],
                'bar-lounge' => ['file' => 'bar-lounge.json', 'key' => 'bar_lounge', 'subkey' => 'designs'],
                'hotel-lobby' => ['file' => 'hotel-lobby.json', 'key' => 'hotel_lobby', 'subkey' => 'designs'],
                'hotel-guest-room' => ['file' => 'hotel-guest-room.json', 'key' => 'hotel_guest_room', 'subkey' => 'designs'],
                'clinic-medical-office' => ['file' => 'clinic-medical-office.json', 'key' => 'clinic_medical_office', 'subkey' => 'designs'],
                'salon-spa' => ['file' => 'salon-spa.json', 'key' => 'salon_spa', 'subkey' => 'designs'],
                'gym-fitness-center' => ['file' => 'gym-fitness-center.json', 'key' => 'gym_fitness_center', 'subkey' => 'designs'],
                'bank-branch' => ['file' => 'bank-branch.json', 'key' => 'bank_branch', 'subkey' => 'designs'],
                'classroom-lecture-hall' => ['file' => 'classroom-lecture-hall.json', 'key' => 'classroom_lecture_hall', 'subkey' => 'designs'],
            ];

            // Process styles for both residential and commercial spaces
            if (isset($roomConfig[$request->roomType])) {

                $config = $roomConfig[$request->roomType];
                $path   = resource_path('data/' . $config['file']);

                if (file_exists($path)) {
                    $styles = json_decode(file_get_contents($path), true);
                    
                    if (isset($config['subkey'])) {
                        // For new JSON structure with designs array
                        $styleData = collect($styles[$config['key']][$config['subkey']])
                            ->firstWhere('style_id', $request->style);
                        $styleKeywords = $styleData['prompt_keywords'] ?? '';
                    } else {
                        // For old JSON structure
                        $styleKeywords = $styles[$config['key']][$request->style]['prompt_keywords'] ?? '';
                    }
                }
            }

            /* -------------------------------------------------
            | 3. STRONG PROMPT (LAYOUT & CAMERA LOCK)
            |-------------------------------------------------*/
            $spaceTypeText = $request->spaceType === 'commercial' ? 'commercial space' : $request->roomType;
            
            $prompt = "
            ABSOLUTE PIXEL-LOCK INSTRUCTION (CRITICAL):
            Modify the EXISTING IMAGE IN PLACE.
            Do NOT generate a new image.

            FRAME & CAMERA LOCK:
            - Identical framing, crop, and composition
            - Same handheld phone camera (≈26–28mm)
            - No recentering, rotation, zoom, or perspective correction
            - Final image must align perfectly when overlaid

            STRUCTURE PRESERVATION (NON-NEGOTIABLE):
            - Preserve ALL walls, doors, door frames, switches, and openings
            - No door may be removed, hidden, resized, or covered
            - Furniture must be placed AROUND doors, never in front

            TASK:
            This is an EXISTING {$spaceTypeText} photographed with a phone camera.
            Renovate and furnish it WITHOUT changing layout or geometry.

            STRICTLY FORBIDDEN:
            - Any room-type conversion
            - Any structural or camera change
            - Any layout reinterpretation

            ALLOWED CHANGES:
            - Surface finishes (walls, floor, ceiling)
            - Lighting fixtures (same mounting points)
            - Furniture only inside existing empty floor space

            " . $this->roomPrompt($request->roomType, $request->spaceType) . "

            DESIGN QUALITY:
            - Modern, minimal, premium
            - Neutral color palette
            - Realistic furniture scale
            - No clutter

            LIGHTING & REALISM:
            - Preserve original light direction
            - Natural shadows only
            - No HDR, no showroom look

            DEPTH & SCALE ANCHOR (CRITICAL):
            - Preserve original floor perspective exactly
            - Floor tile size, spacing, and convergence must remain unchanged
            - Do NOT compress or normalize room depth
            - The back wall must appear at the same distance as in the original image
            - Maintain original foreground-to-background depth ratio

            FINAL CHECK:
            - Must look like the SAME PHOTO
            - Must still clearly read as a {$spaceTypeText}
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
                'room_style' => $request->style,
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

    private function roomPrompt(string $roomType, string $spaceType = 'residential'): string
    {
        if ($spaceType === 'commercial') {
            return match ($roomType) {
                'open-plan-office' => "
                COMMERCIAL SPACE LOCK (CRITICAL):
                This image is an OPEN PLAN OFFICE ONLY.
                It must remain a commercial open office space.
                
                ALLOWED ADDITIONS (OFFICE ONLY):
                - Modern office desks and workstations
                - Ergonomic office chairs
                - Professional lighting fixtures
                - Office storage solutions
                ",
                'executive-office' => "
                COMMERCIAL SPACE LOCK (CRITICAL):
                This image is an EXECUTIVE OFFICE ONLY.
                It must remain a commercial executive office.
                
                ALLOWED ADDITIONS (EXECUTIVE OFFICE ONLY):
                - Executive desk and chair
                - Professional meeting area
                - Premium office furniture
                - Corporate decor elements
                ",
                'conference-room' => "
                COMMERCIAL SPACE LOCK (CRITICAL):
                This image is a CONFERENCE ROOM ONLY.
                It must remain a commercial conference room.
                
                ALLOWED ADDITIONS (CONFERENCE ROOM ONLY):
                - Conference table and chairs
                - Presentation equipment
                - Professional lighting
                - Corporate meeting furniture
                ",
                default => "
                COMMERCIAL SPACE LOCK (CRITICAL):
                This image is a COMMERCIAL SPACE ONLY.
                It must remain a commercial environment.
                
                ALLOWED ADDITIONS (COMMERCIAL ONLY):
                - Professional furniture appropriate for the space
                - Commercial-grade fixtures
                - Business-appropriate decor
                "
            };
        }
        
        return match ($roomType) {

            'living_room' => "
    ROOM TYPE LOCK (CRITICAL):
    This image is a LIVING ROOM ONLY.
    It must remain a living room.

    STRICTLY FORBIDDEN:
    - Beds, wardrobes, bedside tables, bedroom lamps
    - Dining tables or kitchen elements

    ALLOWED ADDITIONS (LIVING ROOM ONLY):
    - Modern sofa (2–3 seater or compact L-shape if space allows)
    - Wall-mounted TV or TV unit aligned to an existing wall
    - Optional center table or rug if floor space allows
    ",

            'bedroom' => "
    ROOM TYPE LOCK (CRITICAL):
    This image is a BEDROOM ONLY.
    It must remain a bedroom.

    STRICTLY FORBIDDEN:
    - Sofas, TV units meant for living rooms
    - Dining tables or kitchen elements

    ALLOWED ADDITIONS (BEDROOM ONLY):
    - Bed placed against an existing wall (realistic size)
    - Bedside tables with soft lamps
    - Wall-mounted or split AC above door or window line
    ",

            'dining_room' => "
    ROOM TYPE LOCK (CRITICAL):
    This image is a DINING ROOM ONLY.
    It must remain a dining room.

    STRICTLY FORBIDDEN:
    - Beds, sofas, TV units

    ALLOWED ADDITIONS (DINING ROOM ONLY):
    - Dining table set (4 or 6 seater if space allows)
    - Chairs aligned naturally
    - Optional hanging or ceiling dining light
    ",

            'kitchen' => "
    ROOM TYPE LOCK (CRITICAL):
    This image is a KITCHEN ONLY.
    It must remain a kitchen.

    STRICTLY FORBIDDEN:
    - Beds, sofas, dining tables

    ALLOWED ADDITIONS (KITCHEN ONLY):
    - Modular cabinets, countertop, backsplash
    - Built-in appliances only if space allows
    ",

            default => ''
        };
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