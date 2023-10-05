<?php

namespace App\Http\Controllers\API\Param;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Param\ParamResource;
use App\Models\Param;
use Illuminate\Http\Request;

class ParamController extends Controller
{
    public function unit()
    {
        return $this->get_param('unit');
    }

    public function get_param(string $category)
    {
        $param = Param::where('category', $category)->orderBy('order', 'ASC')->orderBy('param', 'ASC')->get();
        return ResponseFormatter::success(
            ParamResource::collection($param),
            'success get ' . $category . ' data'
        );
    }
}
