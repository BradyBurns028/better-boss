<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Filters\AdminFilter;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\AdminResource;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;

class AdminController extends AbstractController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Admin::query();

        // Includes
        $allowedIncludes = ['user', 'organizations'];
        $includes = array_filter(explode(',', (string) $request->query('include', '')));
        $includes = array_values(array_intersect($allowedIncludes, $includes));
        if (!empty($includes)) {
            $query->with($includes);
        }

        // Filters
        (new AdminFilter())->apply($request, $query);

        // Sorting
        $allowedSorts = ['id', 'user_id', 'created_at'];
        $sort = (string) $request->query('sort', 'id');
        $direction = 'asc';
        if (str_starts_with($sort, '-')) {
            $direction = 'desc';
            $sort = substr($sort, 1);
        }
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = max(1, min(100, (int) $request->query('per_page', 15)));
        $paginator = $query->paginate($perPage)->appends($request->query());

        $data = AdminResource::collection($paginator->items());
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
    public function store(StoreAdminRequest $request)
    {
        $data = $request->validated();

        // Create the user
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            // ensure user_type is set (DB requires NOT NULL)
            'user_type' => $data['user_type'] ?? 'admin',
        ]);

        // Create the admin record linked to the user
        $admin = Admin::create([
            'user_id' => $user->id,
        ]);

        return $this->response([
            'user'=>$user,
            'admin'=>$admin
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        $admin->load('user');

        return $this->response(data: AdminResource::make($admin));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $data = $request->validated();

        $user = $admin->user;

        $user->save();

        return $this->response($admin);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        $admin->delete();

        return $this->response(data: ['status' => 200, 'message' => 'Admin deleted successfully.']);
    }
}
