<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Exception;
use App\Exceptions\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\PaginationTotalPerPage as TotalPerPageTrait;

class UsersController extends Controller
{
    use TotalPerPageTrait;

    public function index(User $user, Request $request)
    {

        try {
            $loggedUser = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json('Unauthorized', 401);
        }

        $isManager = (
            auth()->user()->checkAccess('users', 'manage')
        );

        $hasFirstNameFilter = (
            !empty($request->first_name)
        );
        $hasLastNameFilter = (
            !empty($request->last_name)
        );

        
        $users = $user::when($isManager, function ($query) use ($request) {
            $query->withTrashed();
        })->when($hasFirstNameFilter, function ($query) use ($request) {
            $query->where('first_name', 'like', '%' . $request->first_name . '%');
        })->when($hasLastNameFilter, function ($query) use ($request) {
            $query->where('last_name', 'like', '%' . $request->last_name . '%');
        });

        return $users->paginate($this->getTotalPerPage());
    }

    public function show(User $userModel, int $user)
    {
        try {
            $loggedUser = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json('Unauthorized', 401);
        }

        $isManager = false;

        $isManager = (
            auth()->user()->checkAccess('users', 'manage')
        );

        $user = $userModel->withTrashed()->find($user);

        if (!$user) {
            throw new ModelNotFoundException;
        }

        if ($user->trashed() && !$isManager) {
            return response()->json('Forbidden', 403);
        }
        
        return $user;
    }

    public function store(Request $request, User $user)
    {
        try {
            $loggedUser = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json('Unauthorized', 401);
        }

        if (auth()->user()->cant('store', $user)) {
            return response()->json('Forbidden', 403);
        }
        $rules = $user::VALIDATION;

        $data  = $request->validate($rules);

        $data['user_id'] = auth()->user()->id;

        try {
            $createdUser = $user->create($data);
            return response($createdUser, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $loggedUser = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json('Unauthorized', 401);
        }

        if (auth()->user()->cant('update', $user)) {
            return response()->json('Forbidden', 403);
        }

        $rules = User::VALIDATION;

        $rules['email'] .= ',' . $user->id;

        $data = $request->validate($rules);

        if($user->update($data)){
            return response($user, 201);
        }

        return response()->json('error', 500);   
    }

    public function destroy(User $userModel, int $user)
    {
        try {
            $loggedUser = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json('Unauthorized', 401);
        }

        if (auth()->user()->cant('destroy', $userModel)) {
            return response()->json('Forbidden', 403);
        }

        $user = $userModel->withTrashed()->find($user);

        if (!$user) {
            throw new ModelNotFoundException;
        }

        try {
            if ($user->trashed()) {
                $user->restore();
            } else {
                $user->delete();
            }
            return response(null, 204);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }
}
