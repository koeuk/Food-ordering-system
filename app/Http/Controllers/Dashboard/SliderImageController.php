<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SliderImage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SliderImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sliderImages = SliderImage::ordered()->get();
        
        return Inertia::render('Dashboard/SliderImages/Index', [
            'sliderImages' => $sliderImages
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Dashboard/SliderImages/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'required|url',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|url',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        SliderImage::create($request->all());

        return redirect()->route('dashboard.dashboard.slider-images.index')
            ->with('success', 'Slider image created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SliderImage $sliderImage)
    {
        return Inertia::render('Dashboard/SliderImages/Show', [
            'sliderImage' => $sliderImage
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SliderImage $sliderImage)
    {
        return Inertia::render('Dashboard/SliderImages/Edit', [
            'sliderImage' => $sliderImage
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SliderImage $sliderImage)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'required|url',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|url',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $sliderImage->update($request->all());

        return redirect()->route('dashboard.slider-images.index')
            ->with('success', 'Slider image updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SliderImage $sliderImage)
    {
        $sliderImage->delete();

        return redirect()->route('dashboard.slider-images.index')
            ->with('success', 'Slider image deleted successfully.');
    }

    /**
     * Get active slider images for public API
     */
    public function getActive()
    {
        $sliderImages = SliderImage::active()->ordered()->get();
        
        return response()->json($sliderImages);
    }
}
