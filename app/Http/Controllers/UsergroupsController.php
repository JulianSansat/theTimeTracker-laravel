<?php

namespace App\Http\Controllers;

use App\Usergroup;
use Illuminate\Http\Request;
use Exception;
use App\Exceptions\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\PaginationTotalPerPage as TotalPerPageTrait;

class UsergroupsController extends Controller
{
    use TotalPerPageTrait;

    public function index(Usergroup $usergroup, Request $request)
    {

        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json('Unauthorized', 401);
        }

        $isManager = (
            auth()->user()->checkAccess('usergroups', 'manage')
        );

        if(!$isManager) {
            return response()->json('Forbidden', 403);
        }
        
        $usergroups = $usergroup::when($isManager, function ($query) use ($request) {
            $query->withTrashed();
        });

        return $usergroups->paginate($this->getTotalPerPage());
    }

    public function show(Usergroup $usergroupModel, int $usergroup)
    {
        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json('Unauthorized', 401);
        }

        $isManager = false;
        
        $isManager = (
            auth()->user()->checkAccess('usergroups', 'manage')
        );
        
        $usergroup = $usergroupModel->withTrashed()->find($usergroup);

        if (!$usergroup) {
            throw new ModelNotFoundException;
        }

        if ($usergroup->trashed() && !$isManager) {
            return response()->json('Forbidden', 403);
        }
        
        return $usergroup;
    }

    public function store(Request $request, Usergroup $usergroup)
    {
        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json('Unauthorized', 401);
        }

        if (auth()->user()->cant('store', $usergroup)) {
            return response()->json('Forbidden', 403);
        }
        $rules = $usergroup::VALIDATION;

        $data  = $request->validate($rules);

        $data['user_id'] = auth()->user()->id;

        try {
            $createdUsergroup = $usergroup->create($data);
            return response($createdUsergroup, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    public function update(Request $request, Usergroup $usergroup)
    {
        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json('Unauthorized', 401);
        }

        if (auth()->user()->cant('update', $usergroup)) {
            return response()->json('Forbidden', 403);
        }

        $rules = Usergroup::VALIDATION;

        $data = $request->validate($rules);

        if($usergroup->update($data)){
            return response($usergroup, 201);
        }

        return response()->json('error', 500);   
    }

    public function destroy(Usergroup $usergroupModel, int $usergroup)
    {
        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json('Unauthorized', 401);
        }

        if (auth()->user()->cant('destroy', $usergroupModel)) {
            return response()->json('Forbidden', 403);
        }

        $usergroup = $usergroupModel->withTrashed()->find($usergroup);

        if (!$usergroup) {
            throw new ModelNotFoundException;
        }

        try {
            if ($usergroup->trashed()) {
                $usergroup->restore();
            } else {
                $usergroup->delete();
            }
            return response(null, 204);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }
}
