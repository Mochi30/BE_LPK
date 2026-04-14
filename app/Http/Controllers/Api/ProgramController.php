<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::query();

        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        if ($request->boolean('featured_only')) {
            $query->where('is_featured', true);
        }

        $query->orderBy('order_index')->orderBy('title');

        return $query->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:programs,slug'],
            'summary' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_weeks' => ['nullable', 'integer', 'min:1'],
            'level' => ['nullable', 'string', 'max:100'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        $program = Program::create($data);

        return response()->json($program, 201);
    }

    public function show(Program $program)
    {
        return $program;
    }

    public function update(Request $request, Program $program)
    {
        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:programs,slug,'.$program->id],
            'summary' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_weeks' => ['nullable', 'integer', 'min:1'],
            'level' => ['nullable', 'string', 'max:100'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ]);

        if (array_key_exists('title', $data) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $program->update($data);

        return $program;
    }

    public function destroy(Program $program)
    {
        $program->delete();

        return response()->noContent();
    }
}
