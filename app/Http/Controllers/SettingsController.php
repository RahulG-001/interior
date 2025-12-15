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
}