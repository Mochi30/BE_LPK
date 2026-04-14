<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::query();

        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        $query->orderBy('order_index')->orderBy('id');

        return $query->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $faq = Faq::create($data);

        return response()->json($faq, 201);
    }

    public function show(Faq $faq)
    {
        return $faq;
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question' => ['sometimes', 'required', 'string', 'max:255'],
            'answer' => ['nullable', 'string'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $faq->update($data);

        return $faq;
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return response()->noContent();
    }
}
