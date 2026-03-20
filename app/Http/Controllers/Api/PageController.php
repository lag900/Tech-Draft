<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PageController extends Controller
{
    /**
     * Get a page by slug (Optimized and Cached)
     */
    public function show(Request $request, $slug)
    {
        $lang = $request->header('Accept-Language', 'ar');
        $cacheKey = "page_{$slug}_{$lang}";

        $page = Cache::remember($cacheKey, 3600, function() use ($slug) {
            return Page::where('slug', $slug)->where('is_active', true)->first();
        });

        if (!$page) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        return response()->json($page);
    }

    /**
     * Admin: Update page entirely
     */
    public function update(Request $request, $slug)
    {
        // Must be admin middleware in routes
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|array',
            'is_active' => 'boolean'
        ]);

        $page = Page::firstOrCreate(
            ['slug' => $slug],
            ['title' => $request->input('title'), 'content' => [], 'is_active' => true]
        );

        $page->title = $request->input('title');
        $page->content = $request->input('content');
        if ($request->has('is_active')) {
            $page->is_active = $request->input('is_active');
        }
        $page->save();

        // Clear cache
        Cache::forget("page_{$slug}_ar");
        Cache::forget("page_{$slug}_en");

        return response()->json(['message' => 'Page updated successfully', 'page' => $page]);
    }

    /**
     * Admin: Upload media for CMS
     */
    public function uploadMedia(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ]);

        $file = $request->file('image');
        
        // Use Intervention for image processing except for SVG
        if ($file->getClientOriginalExtension() === 'svg' || $file->getMimeType() === 'image/svg+xml') {
            $path = $file->store('cms/media', 'public');
            return response()->json(['url' => asset('storage/' . $path)]);
        }

        $manager = new ImageManager(new Driver());
        $img = $manager->read($file->getRealPath());
        
        if ($img->width() > 1920) {
            $img->scaleDown(width: 1920);
        }
        
        $filename = 'cms/media/' . uniqid('img_') . '.webp';
        $encoded = $img->toWebp(85);
        Storage::disk('public')->put($filename, $encoded->toString());

        return response()->json(['url' => asset('storage/' . $filename)]);
    }
}

