<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'message' => ['nullable', 'string'],
            'source' => ['nullable', 'string', 'max:120'],
        ]);

        $lead = Lead::create($data);

        return response()->json([
            'message' => 'Permintaan Anda sudah kami terima.',
            'data' => $lead,
        ], 201);
    }
}
