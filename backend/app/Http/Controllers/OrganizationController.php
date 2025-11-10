<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Filters\OrganizationFilter;

use App\Models\Admin;
use App\Models\User;
use App\Http\Resources\OrganizationResource;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Enums\PermissionEnum;

class OrganizationController extends AbstractController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can(PermissionEnum::VIEW_ORGANIZATIONS->value)){
            return $this->error(403, 'You do not have permission to view all organizations.', 'forbidden');
        }

        $query = Organization::query();

        // Includes
        $allowedIncludes = ['admin', 'user', 'departments'];
        $includes = array_filter(explode(',', (string) $request->query('include', '')));
        $includes = array_values(array_intersect($allowedIncludes, $includes));
        if (!empty($includes)) {
            $query->with($includes);
        }

        // Filters
        (new OrganizationFilter())->apply($request, $query);

        // Pagination
        $perPage = max(1, min(100, (int) $request->query('per_page', 15)));
        $paginator = $query->paginate($perPage)->appends($request->query());

        $data = OrganizationResource::collection($paginator->items());
        $meta = [
            'page' => $paginator->currentPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
        ];

        return $this->response($data, $meta);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganizationRequest $request)
    {
        if(!auth()->user()->can(PermissionEnum::CREATE_ORGANIZATIONS->value)) {
            return $this->error(403, 'You do not have permission to create organizations.', 'forbidden');
        }

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
        if(!auth()->user()->can(PermissionEnum::VIEW_ORGANIZATIONS->value)) {
            return $this->error(403, 'You do not have permission to view organizations.', 'forbidden');
        }

        $organization->load('admin', 'user', 'departments');

        return $this->response(data: OrganizationResource::make($organization));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        if(!auth()->user()->can(PermissionEnum::EDIT_ORGANIZATIONS->value)) {
            return $this->error(403, 'You do not have permission to update organizations.', 'forbidden');
        }

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
        if(!auth()->user()->can(PermissionEnum::DELETE_ORGANIZATIONS->value)) {
            return $this->error(403, 'You do not have permission to delete organizations.', 'forbidden');
        }

        $organization->delete();

        return $this->response(data: ['status' => 200, 'message' => 'Organization deleted successfully.']);
    }
}
