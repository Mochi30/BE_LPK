<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\Request;

class GalleryItemController extends Controller
{
    public function index(Request $request)
    {
        $query = GalleryItem::query();

        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        if ($request->boolean('featured_only')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        $query->orderBy('order_index')->orderBy('id', 'desc');

        return $query->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image_url' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:120'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $item = GalleryItem::create($data);

        return response()->json($item, 201);
    }

    public function show(GalleryItem $gallery)
    {
        return $gallery;
    }

    public function update(Request $request, GalleryItem $gallery)
    {
        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:120'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $gallery->update($data);

        return $gallery;
    }

    public function destroy(GalleryItem $gallery)
    {
        $gallery->delete();

        return response()->noContent();
    }
}
