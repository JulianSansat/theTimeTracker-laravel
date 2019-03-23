<?php

namespace App\Http\Controllers;

use App\Log;
use Illuminate\Http\Request;
use Exception;
use App\Exceptions\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\PaginationTotalPerPage as TotalPerPageTrait;

class LogsController extends Controller
{
    use TotalPerPageTrait;

    public function index(Log $log, Request $request)
    {
        $isManager = false;

        if(auth()->user()){
            $isManager = (
                auth()->user()->checkAccess('logs', 'manage')
            );
        }
        
        $logs = $log::when($isManager, function ($query) use ($request) {
            $query->withTrashed();
        });

        return $logs->paginate($this->getTotalPerPage());
    }

    public function show(Log $logModel, int $log)
    {
        $isManager = false;

        if(auth()->user()){
            $isManager = (
                auth()->user()->checkAccess('logs', 'manage')
            );
        }
        
        $log = $logModel->withTrashed()->find($log);

        if (!$log) {
            throw new ModelNotFoundException;
        }

        if ($log->trashed() && !$isManager) {
            return response()->json('Forbidden', 403);
        }
        
        return $log;
    }

    public function store(Request $request, Log $log)
    {
        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json('Unauthorized', 401);
        }

        if (auth()->user()->cant('store', $log)) {
            return response()->json('Forbidden', 403);
        }

        $rules = $log::VALIDATION;

        $data  = $request->validate($rules);

        try {
            $createdLog = $log->create($data);
            return response($createdLog, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    public function destroy(Log $logModel, int $log)
    {
        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json('Unauthorized', 401);
        }

        if (auth()->user()->cant('destroy', $logModel)) {
            return response()->json('Forbidden', 403);
        }

        $log = $logModel->withTrashed()->find($log);

        if (!$log) {
            throw new ModelNotFoundException;
        }

        try {
            if ($log->trashed()) {
                $log->restore();
            } else {
                $log->delete();
            }
            return response(null, 204);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }
}
