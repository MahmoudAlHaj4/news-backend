<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AboutSection;

class AboutSectionController extends Controller
{
    public function show()
    {
        $about = AboutSection::first();

        if (!$about) {
            return response()->json(['message' => 'No data found'], 404);
        }

        return response()->json($about);
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|string',
            'banner' => 'nullable|string',
            'about_section' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'instagram' => 'nullable|string',
            'facebook' => 'nullable|string',
            'twitter' => 'nullable|string',
        ]);

        $about = AboutSection::first();

        if (!$about) {
            $about = AboutSection::create($request->only([
                'logo', 'banner', 'about_section', 'whatsapp', 'instagram', 'facebook', 'twitter'
            ]));
        } else {
            $about->update($request->only([
                'logo', 'banner', 'about_section', 'whatsapp', 'instagram', 'facebook', 'twitter'
            ]));
        }

        return response()->json(['message' => 'About section updated', 'data' => $about]);
    }
}
