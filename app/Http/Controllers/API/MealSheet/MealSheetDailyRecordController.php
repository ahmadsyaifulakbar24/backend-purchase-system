<?php

namespace App\Http\Controllers\API\MealSheet;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\MealSheet\MealSheetDailyRecordRequest;
use App\Http\Requests\MealSheet\MealSheetDailyRecordUpdateRequest;
use App\Http\Resources\MealSheet\MealSheetDailyRecordDetailResource;
use App\Http\Resources\MealSheet\MealSheetDailyRecordResource;
use App\Models\Formula;
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
        $records = $request->meal_sheet_record;

        // count formula
        $data = $this->execute_formula($input, $records);

        $result = DB::transaction(function () use ($data, $records) {
            $meal_sheet_detail = MealSheetDetail::create($data);
            $meal_sheet_detail->meal_sheet_record()->createMany($records);

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
        $records = $request->meal_sheet_record;
        if(empty($request->acknowladge_by)) {
            $input['acknowladge_by'] = null;
        }

        // count formula
        $data = $this->execute_formula($input, $records);

        $result = DB::transaction(function () use ($meal_sheet_detail, $data, $records) {
            $meal_sheet_detail->update($data);

            // delete and re-create record
            $meal_sheet_detail->meal_sheet_record()->delete();
            $meal_sheet_detail->meal_sheet_record()->createMany($records);

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

    public function daily_meal_sheet_pdf(MealSheetDetail $meal_sheet_detail)
    {

        $data = [
            'meal_sheet_detail' => $meal_sheet_detail,
            'checklist_image' => public_path('images/checklist.png')
        ];

        $pdf = Pdf::loadView('pdf.daily_meal_sheet', $data);
        $file_name = 'daily_meal_sheet-.pdf';
        return $pdf->download($file_name);
    }

    public function execute_formula($input, $records)
    {
         $formula = Formula::find($input['formula_id']);
         $mandays = 0;
         $casual_breakfast = 0;
         $casual_lunch = 0;
         $casual_dinner = 0;

        foreach ($records as $record) {
            $total = $record['breakfast'] + $record['lunch'] + $record['dinner'] + $record['super'];
            $accomodation = $record['accomodation'] == 1 ? 1 : null;
            $result = '';

            ob_start();
            eval("?>" . $formula->formula);
            ob_get_clean();

            if($result == 'mandays') {
                $mandays += 1;
            } else if($result = 'casual') {

                if($record['breakfast'] == 1) {
                    $casual_breakfast += 1;
                }
                 
                if($record['lunch'] == 1) {
                    $casual_lunch += 1;
                } 
                
                if($record['dinner'] == 1) {
                    $casual_dinner += 1;
                }
            }
        }

        $input['mandays'] = $mandays;
        $input['casual_breakfast'] = $casual_breakfast;
        $input['casual_lunch'] = $casual_lunch;
        $input['casual_dinner'] = $casual_dinner;

        return $input;
    }
}
