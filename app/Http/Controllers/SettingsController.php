<?php

namespace App\Http\Controllers;

use App\Models\RoomPrompt;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        $prompts = RoomPrompt::all()->keyBy('room_type');
        return Inertia::render('settings/index', compact('prompts'));
    }

    public function updatePrompts(Request $request)
    {
        $request->validate([
            'prompts' => 'required|array',
            'prompts.*.room_type' => 'required|string',
            'prompts.*.prompt' => 'required|string'
        ]);

        foreach ($request->prompts as $promptData) {
            RoomPrompt::updateOrCreate(
                ['room_type' => $promptData['room_type']],
                ['prompt' => $promptData['prompt']]
            );
        }

        return response()->json(['success' => true]);
    }

    public function getCredits()
    {
        $totalCost = \App\Models\AiCredit::sum('cost');
        $totalGenerations = \App\Models\AiCredit::count();
        $todayCost = \App\Models\AiCredit::whereDate('created_at', today())->sum('cost');
        $todayGenerations = \App\Models\AiCredit::whereDate('created_at', today())->count();
        
        return response()->json([
            'total_cost' => $totalCost,
            'total_generations' => $totalGenerations,
            'today_cost' => $todayCost,
            'today_generations' => $todayGenerations
        ]);
    }
}