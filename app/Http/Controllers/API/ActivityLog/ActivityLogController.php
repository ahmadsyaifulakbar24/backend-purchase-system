<?php

namespace App\Http\Controllers\API\ActivityLog;

use App\Exports\ActivityLogExport;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLog\ActivityLogResource;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from_date' => ['nullable', 'date'],
            'until_date' => ['nullable', 'date'],
            'asset_id' => ['nullable', 'exists:assets,id'],
            'search' => ['nullable', 'string'],
            'paginate' => ['nullable', 'in:0,1'],
            'limit' => ['nullable', 'numeric'],
            'export' => ['nullable', 'in:0,1'],
        ]);
        $from_date = $request->from_date;
        $until_date = $request->until_date;
        $asset_id = $request->asset_id;
        $search = $request->search;
        $limit = $request->input('limit', 10);

        $activity = Activity::query();
        
        $activity->when($from_date, function($query, $from_date) {
                    $query->where('created_at', '>', $from_date);
                })
                ->when($until_date, function($query, $until_date) {
                    $query->where('created_at', '<=', $until_date);
                })
                ->when($asset_id, function($query, $asset_id) {
                    $query->where('log_name', 'asset')->where('parent_id', $asset_id);
                })
                ->when($search, function($query, $search) {
                    $query->where(function($sub_query) use ($search) {
                        $sub_query->where('log_name', 'like', '%'. $search .'%')
                            ->orWhere('browser', 'like', '%'. $search .'%')
                            ->orWhere('ip', 'like', '%'. $search .'%')
                            ->orWhere('os', 'like', '%'. $search .'%');
                    });
                })
                ->orderBy('created_at', 'asc');
                $result = ($request->paginate) ? $activity->paginate($limit) : $activity->get();

        if(!$request->export) {
            return ResponseFormatter::success(ActivityLogResource::collection($result)->response()->getData(true), 'success get activity log data');
        } else {
            $export = new ActivityLogExport($result);
            return Excel::download($export, 'activity_log.csv');
        }

    }
}
