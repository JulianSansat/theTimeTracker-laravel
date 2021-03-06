<?php

namespace App\Http\Controllers;

use App\Log;
use Carbon\Carbon;
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

        $hasTeamFilter = (
           !empty($request->team_id) 
        );

        $hasUserFilter = (
           !empty($request->user_id) 
        );

        $hasYearFilter = (
            !empty($request->year)
        );

        $hasMonthFilter = (
            !empty($request->month)
        );

        $hasDayFilter = (
            !empty($request->day)
        );

        $hasDateIntervalFilter = (
            !empty($request->start_date)
            && !empty($request->end_date)
        );

        $logs = $log::when($isManager, function ($query) use ($request) {
            $query->withTrashed();
        })->when($hasTeamFilter, function ($query) use ($request){
            $query->whereHas('user', function ($query) use ($request){
                $query->whereHas('teams', function ($query) use ($request){
                    $query->where('team_id', '=', $request->team_id);
                });
            });
        })->when($hasYearFilter, function ($query) use ($request){
            $query->whereYear('start', $request->year);
        })->when($hasMonthFilter, function ($query) use ($request){
            $query->whereMonth('start', $request->month);
        })->when($hasDayFilter, function ($query) use ($request){
            $query->whereDay('start', $request->day);
        })->when($hasUserFilter, function ($query) use ($request){
            $query->where('user_id', '=', $request->user_id);
        })->when($hasDateIntervalFilter, function ($query) use ($request){
            $query->whereBetween('start', [$request->start_date, $request->end_date]);
        })->with([
            'user' => function ($query) {
                return $query->with('teams');
            }
        ]);

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
