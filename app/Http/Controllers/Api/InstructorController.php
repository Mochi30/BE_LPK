<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function index(Request $request)
    {
        $query = Instructor::query();

        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        $query->orderBy('order_index')->orderBy('name');

        return $query->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'photo_url' => ['nullable', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'string', 'max:255'],
            'expertise' => ['nullable', 'array'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $instructor = Instructor::create($data);

        return response()->json($instructor, 201);
    }

    public function show(Instructor $instructor)
    {
        return $instructor;
    }

    public function update(Request $request, Instructor $instructor)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'photo_url' => ['nullable', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'string', 'max:255'],
            'expertise' => ['nullable', 'array'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $instructor->update($data);

        return $instructor;
    }

    public function destroy(Instructor $instructor)
    {
        $instructor->delete();

        return response()->noContent();
    }
}
