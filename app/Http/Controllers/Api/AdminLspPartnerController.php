<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LspPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminLspPartnerController extends Controller
{
    public function index(Request $request)
    {
        $query = LspPartner::query();

        if ($request->boolean('include_archived')) {
            $query->withTrashed();
        }

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('sector', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return $query->orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:lsp_partners,slug'],
            'logo_url' => ['nullable', 'string', 'max:255'],
            'sector' => ['required', 'string', 'max:255'],
            'schemes' => ['nullable', 'array'],
            'schemes.*' => ['string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:100'],
            'website_url' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'province' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $partner = LspPartner::create($data);

        return response()->json($partner, 201);
    }

    public function show(LspPartner $lspPartner)
    {
        return $lspPartner->loadCount('registrations');
    }

    public function update(Request $request, LspPartner $lspPartner)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:lsp_partners,slug,' . $lspPartner->id],
            'logo_url' => ['nullable', 'string', 'max:255'],
            'sector' => ['sometimes', 'required', 'string', 'max:255'],
            'schemes' => ['nullable', 'array'],
            'schemes.*' => ['string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:100'],
            'website_url' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'province' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (array_key_exists('name', $data) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $lspPartner->update($data);

        return $lspPartner->fresh();
    }

    public function destroy(LspPartner $lspPartner)
    {
        $lspPartner->delete();

        return response()->json([
            'message' => 'Data LSP Mitra diarsipkan. Penghapusan permanen memerlukan otorisasi Super Admin.',
        ]);
    }

    public function restore(int $id)
    {
        $partner = LspPartner::withTrashed()->findOrFail($id);
        $partner->restore();

        return $partner;
    }
}
