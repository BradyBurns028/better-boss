<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

use App\Models\Admin;
use App\Models\User;
use App\Http\Resources\OrganizationResource;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;

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
    public function store(StoreOrganizationRequest $request)
    {
        $data = $request->validated();

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
    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        $data = $request->validated();

        $organization->fill($data);
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
