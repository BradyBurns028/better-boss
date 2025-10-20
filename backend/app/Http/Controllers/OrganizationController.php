<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

use App\Models\Admin;
use App\Models\User;
use App\Http\Resources\OrganizationResource;

class OrganizationController extends AbstractController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'admin_id' => 'required|integer|exists:admins,id',
            'owner_id' => 'nullable|integer|exists:users,id',
            'address' => 'nullable|string|max:1000',
        ]);

        $organization = Organization::create([
            'name' => $data['name'],
            'admin_id' => $data['admin_id'],
            'owner_id' => $data['owner_id'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return $this->response($organization);
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization)
    {
        $organization->load('admin', 'user', 'departments');

        return $this->response(data: OrganizationResource::make($organization));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'admin_id' => 'sometimes|required|integer|exists:admins,id',
            'owner_id' => 'sometimes|nullable|integer|exists:users,id',
            'address' => 'sometimes|nullable|string|max:1000',
        ]);

        if (isset($data['name'])) $organization->name = $data['name'];
        if (isset($data['admin_id'])) $organization->admin_id = $data['admin_id'];
        if (array_key_exists('owner_id', $data)) $organization->owner_id = $data['owner_id'];
        if (array_key_exists('address', $data)) $organization->address = $data['address'];

        $organization->save();

        return $this->response($organization);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        //
    }
}
