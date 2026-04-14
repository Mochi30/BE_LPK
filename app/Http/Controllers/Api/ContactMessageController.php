<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'source_page' => ['nullable', 'string', 'max:120'],
        ]);

        $message = ContactMessage::create($data);

        return response()->json([
            'message' => 'Terima kasih, pesan Anda sudah kami terima.',
            'data' => $message,
        ], 201);
    }
}
