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
        $data = $request->only(['title', 'description', 'button_text', 'button_url', 'order', 'is_active']);

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            try {
                $imagePath = $request->file('image')->store('slider-images', 'public');
                $data['image_url'] = asset('storage/' . $imagePath);
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Failed to upload image. Please try again.']);
            }
        } elseif ($request->filled('image_url')) {
            $data['image_url'] = $request->image_url;
        }

        try {
            SliderImage::create($data);
            return redirect()->route('dashboard.slider-images.index')
                ->with('success', 'Slider image created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Failed to create slider image. Please try again.']);
        }
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
        $data = $request->only(['title', 'description', 'button_text', 'button_url', 'order', 'is_active']);

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            try {
                $imagePath = $request->file('image')->store('slider-images', 'public');
                $data['image_url'] = asset('storage/' . $imagePath);
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Failed to upload image. Please try again.']);
            }
        } elseif ($request->filled('image_url')) {
            $data['image_url'] = $request->image_url;
        }

        try {
            $sliderImage->update($data);
            return redirect()->route('dashboard.slider-images.index')
                ->with('success', 'Slider image updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Failed to update slider image. Please try again.']);
        }
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
