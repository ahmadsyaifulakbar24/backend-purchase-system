<?php

namespace App\Http\Controllers\API\MealSheet;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\MealSheet\MealSheetDailyRecordRequest;
use App\Http\Requests\MealSheet\MealSheetDailyRecordUpdateRequest;
use App\Http\Resources\MealSheet\MealSheetDailyRecordDetailResource;
use App\Http\Resources\MealSheet\MealSheetDailyRecordResource;
use App\Models\MealSheetDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MealSheetDailyRecordController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'meal_sheet_daily_id' => ['required', 'exists:meal_sheet_daily,id'],
        ]);
        $meal_sheet_daily_id = $request->meal_sheet_daily_id;

        $meal_sheet_detail = MealSheetDetail::where('meal_sheet_daily_id', $meal_sheet_daily_id)->get();

        return ResponseFormatter::success(
            MealSheetDailyRecordResource::collection($meal_sheet_detail),
            'success get meal sheet daily record data'
        );
    }

    public function store(MealSheetDailyRecordRequest $request)
    {
        $input = $request->safe()->except(['meal_sheet_record']);
        $record = $request->meal_sheet_record;

        $result = DB::transaction(function () use ($input, $record) {
            $meal_sheet_detail = MealSheetDetail::create($input);
            $meal_sheet_detail->meal_sheet_record()->createMany($record);

            return $meal_sheet_detail;
        });
        
        return ResponseFormatter::success(
            new MealSheetDailyRecordDetailResource($result),
            'success create meal sheet daily record data'
        );
    }

    public function show(MealSheetDetail $meal_sheet_detail)
    {
        return ResponseFormatter::success(
            new MealSheetDailyRecordDetailResource($meal_sheet_detail),
            'success show meal sheet daily record data'
        );
    }

    public function update(MealSheetDailyRecordUpdateRequest $request, MealSheetDetail $meal_sheet_detail)
    {
        $input = $request->safe()->except(['meal_sheet_record']);
        $record = $request->meal_sheet_record;

        $result = DB::transaction(function () use ($meal_sheet_detail, $input, $record) {
            $meal_sheet_detail->update($input);

            // delete and re-create record
            $meal_sheet_detail->meal_sheet_record()->delete();
            $meal_sheet_detail->meal_sheet_record()->createMany($record);

            return $meal_sheet_detail;
        });
        
        return ResponseFormatter::success(
            new MealSheetDailyRecordDetailResource($result),
            'success update meal sheet daily record data'
        );
    }

    public function destroy(MealSheetDetail $meal_sheet_detail)
    {
        $meal_sheet_detail->delete();

        return ResponseFormatter::success(
            null,
            'success delete meal sheet daily record data'
        );
    }

    public function daily_meal_sheet_pdf()
    {
        $data = [];
        $pdf = Pdf::loadView('pdf.daily_meal_sheet', $data);
        $file_name = 'daily_meal_sheet-.pdf';
        return $pdf->download($file_name);
    }
}
