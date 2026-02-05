<?php

namespace App\Http\Controllers;

use App\Dtos\PageSerializeDto;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $limit = (int)$request->query('limit', 10);
        $offset = (int)$request->query('offset', 0);

        $users = User::offset($offset)->limit($limit)->get();

        return response()->json(new PageSerializeDto(
            items: UserResource::collection($users)->resolve(),
            total: User::count(),
            limit: $limit,
            offset: $offset
        ));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $user = User::create($validated);
        return new UserResource($user);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();
        $user->update($validated);
        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}
