<?php

namespace App\Http\Controllers;

use App\Team;
use Illuminate\Http\Request;
use Exception;
use App\Exceptions\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\PaginationTotalPerPage as TotalPerPageTrait;

class TeamsController extends Controller
{
    use TotalPerPageTrait;

    public function index(Team $team, Request $request)
    {
        $isManager = false;

        if(auth()->user()){
            $isManager = (
                auth()->user()->checkAccess('teams', 'manage')
            );
        }
        
        $teams = $team::when($isManager, function ($query) use ($request) {
            $query->withTrashed();
        });

        return $teams->paginate($this->getTotalPerPage());
    }

    public function show(Team $teamModel, int $team)
    {
        $isManager = false;

        if(auth()->user()){
            $isManager = (
                auth()->user()->checkAccess('teams', 'manage')
            );
        }
        
        $team = $teamModel->withTrashed()->find($team);

        if (!$team) {
            throw new ModelNotFoundException;
        }

        if ($team->trashed() && !$isManager) {
            return response()->json('Forbidden', 403);
        }
        
        return $team;
    }

    public function store(Request $request, Team $team)
    {
        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json('Unauthorized', 401);
        }


        if (auth()->user()->cant('store', $team)) {
            return response()->json('Forbidden', 403);
        }

        $rules = $team::VALIDATION;

        $data  = $request->validate($rules);

        $data['author_id'] = auth()->user()->id;

        try {
            $createdTeam = $team->create($data);
            return response($createdTeam, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    public function update(Request $request, Team $team)
    {
        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json('Unauthorized', 401);
        }

        if (auth()->user()->cant('update', $team)) {
            return response()->json('Forbidden', 403);
        }

        $rules = Team::VALIDATION;

        $data = $request->validate($rules);

        if($team->update($data)){
            return response($team, 201);
        }

        return response()->json('error', 500);   
    }

    public function destroy(Team $teamModel, int $team)
    {
        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json('Unauthorized', 401);
        }

        if (auth()->user()->cant('destroy', $teamModel)) {
            return response()->json('Forbidden', 403);
        }

        $team = $teamModel->withTrashed()->find($team);

        if (!$team) {
            throw new ModelNotFoundException;
        }

        try {
            if ($team->trashed()) {
                $team->restore();
            } else {
                $team->delete();
            }
            return response(null, 204);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }
}
