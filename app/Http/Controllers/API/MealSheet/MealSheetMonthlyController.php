<?php

namespace App\Http\Controllers\API\MealSheet;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\MealSheet\MealSheetMonthlyDetailResource;
use App\Http\Resources\MealSheet\MealSheetMonthlyResource;
use App\Models\MealSheetGroup;
use App\Models\MealSheetMonthly;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MealSheetMonthlyController extends Controller
{

    public function get(Request $request)
    {
        $max = Carbon::now()->format('Y');

        $request->validate([
            'meal_sheet_group_id' => ['required', 'exists:meal_sheet_groups,id'],
            'limit' => ['nullable', 'integer'],
            'paginate' => ['nullable', 'in:0,1'],
            'month' => ['nullable', 'numeric', 'min:1', 'max:12'],
            'year' => ['nullable', 'numeric', 'max:' . $max],
        ]);
        $meal_sheet_group_id = $request->meal_sheet_group_id;
        $paginate = $request->input('paginate', 1);
        $limit = $request->input('limit', 10);
        $month = $request->month;
        $year = $request->year;

        $meal_sheet_monthly = MealSheetMonthly::where('meal_sheet_group_id', $meal_sheet_group_id)
                                            ->when($month, function ($query, int $month) {
                                                $query->where('month', $month);
                                            })
                                            ->when($year, function ($query, int $year) {
                                                $query->where('year', $year);
                                            })
                                            ->orderBy('month', 'DESC')
                                            ->orderBy('year', 'DESC');

        $result = $paginate ? $meal_sheet_monthly->paginate($limit) : $meal_sheet_monthly->get();
        return ResponseFormatter::success(
            MealSheetMonthlyResource::collection($result)->response()->getData(true),
            'success get meal sheet monthly data'
        );
    }


    public function upsert(Request $request)
    {
        $max = Carbon::now()->format('Y');
        
        $request->validate([
            'month' => ['required', 'numeric', 'min:1', 'max:12'],
            'year' => ['required', 'numeric', 'max:' . $max],
            'meal_sheet_group_id' => ['required', 'exists:meal_sheet_groups,id'],
        ]);

        $month = $request->month;
        $year = $request->year;
        $meal_sheet_group_id = $request->meal_sheet_group_id;

        $meal_sheet_group = MealSheetGroup::find($meal_sheet_group_id);

        $meal_sheet_client = $meal_sheet_group->meal_sheet_client;

        $meal_sheet_dailys = $meal_sheet_group->meal_sheet_daily()
                                ->whereMonth('meal_sheet_date', $month)
                                ->whereYear('meal_sheet_date', $year);

        if($meal_sheet_dailys->count() < 1) {
            return response()->json([
                'message' => 'update or insert monthly meal sheet failed',
                'errors' => [
                    'meal_sheet_daily_data' => ['no data was found for the month and year entered']
                ]
            ], 404);
        }

        $data = [];

        $data['month'] = $month;
        $data['year'] = $year;
        $data['meal_sheet_group_id'] = $meal_sheet_group_id;

        $recap_per_day = [];
        foreach($meal_sheet_dailys->get() as $meal_sheet_dailys) {
            

            $client_group = [];
            $onboard_actual = 0;
            $as_per_contract = $meal_sheet_dailys->contract_value;
            $casual_breakfast = 0;
            $casual_lunch = 0;
            $casual_dinner = 0;
            $super = 0;

            foreach ($meal_sheet_client as $client) {
                $meal_sheet_detail = $meal_sheet_dailys->meal_sheet_detail()->where('client_id', $client->id)->first();
                if(!empty($meal_sheet_detail)) {
                    $total_super = $meal_sheet_detail->meal_sheet_record()->where('super', 1)->count();
                    $onboard_actual += $meal_sheet_detail->mandays;
                    $casual_breakfast += $meal_sheet_detail->casual_breakfast;
                    $casual_lunch += $meal_sheet_detail->casual_lunch;
                    $casual_dinner += $meal_sheet_detail->casual_dinner;
                    $super += $total_super;
                }
                

                $client_group[] = [
                    'id' => $client->id,
                    'client_name' => $client->client_name,
                    'mandays' => !empty($meal_sheet_detail->mandays) ? $meal_sheet_detail->mandays : 0,
                ];
            }

            $recap_per_day[] = [
                'meal_sheet_date' => $meal_sheet_dailys->meal_sheet_date,
                'client_group' => $client_group,
                'onboard_actual' => $onboard_actual,
                'as_per_contract' => ($onboard_actual > $as_per_contract) ? $onboard_actual : $as_per_contract,
                'casual_breakfast' => $casual_breakfast,
                'casual_lunch' => $casual_lunch,
                'casual_dinner' => $casual_dinner,
                'super' => $super,
                'total' => $casual_breakfast + $casual_lunch + $casual_dinner + $super,
            ];
        }

        $data['recap_per_day'] = $recap_per_day;
        $data['prepared_by'] = $meal_sheet_group->prepared_by;
        $data['checked_by'] = $meal_sheet_group->checked_by;
        $data['approved_by'] = $meal_sheet_group->approved_by;
        $data['acknowladge_by'] = $meal_sheet_group->acknowladge_by;

        $meal_sheet_monthly = $meal_sheet_group->meal_sheet_monthly()
                                            ->where([
                                                ['month', $month],
                                                ['year', $year]
                                            ])
                                            ->first();

        if(!empty($meal_sheet_monthly)) {
            $meal_sheet_monthly->update($data);
        } else {
            $meal_sheet_monthly = MealSheetMonthly::create($data);
        }

        return ResponseFormatter::success(
            new MealSheetMonthlyDetailResource($meal_sheet_monthly),
            'success upsert meal sheet montly data'
        );
    }

    public function show(MealSheetMonthly $meal_sheet_monthly)
    {
        return ResponseFormatter::success(
            new MealSheetMonthlyDetailResource($meal_sheet_monthly),
            'success show meal sheet monthly data'
        );
    }

    public function show_by_date(Request $request)
    {
        $max = Carbon::now()->format('Y');

        $request->validate([
            'meal_sheet_group_id' => ['required', 'exists:meal_sheet_groups,id'],
            'month' => ['required', 'numeric', 'min:1', 'max:12'],
            'year' => ['required', 'numeric', 'max:' . $max],
        ]);
        $meal_sheet_group_id = $request->meal_sheet_group_id;
        $month = $request->month;
        $year = $request->year;

        $meal_sheet_monthly = MealSheetMonthly::where('meal_sheet_group_id', $meal_sheet_group_id)
                                            ->where([
                                                ['month', $month],
                                                ['year', $year]
                                            ])
                                            ->orderBy('month', 'DESC')
                                            ->orderBy('year', 'DESC')
                                            ->first();

        if(!empty($meal_sheet_monthly)) {
            return ResponseFormatter::success(
                new MealSheetMonthlyDetailResource($meal_sheet_monthly),
                'success show by date meal sheet monthly data'
            );
        } else {
            return response()->json([
                'message' => 'show by date meal sheet monthly failed',
                'errors' => [
                    'meal_sheet_monthly' => ['data not found']
                ]
            ], 404);
        }
    }

    public function destroy(MealSheetMonthly $meal_sheet_monthly)
    {
        $meal_sheet_monthly->delete();

        return ResponseFormatter::success(
            null,
            'success delete meal sheet monthly data'
        );
    }

    public function monthly_meal_sheet_pdf()
    {
        $data = [];
        $pdf = Pdf::loadView('pdf.monthly_meal_sheet', $data);
        $file_name = 'monthly_meal_sheet-.pdf';
        return $pdf->download($file_name);
    }
}
