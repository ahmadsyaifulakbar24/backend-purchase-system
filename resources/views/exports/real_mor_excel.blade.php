<style>
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
      vertical-align: middle;
    }

    .text-center {
        text-align: center;
    }
</style>
@php
    $total_location = count($locations);
    $total_colspan = $total_location + 4;

    $array_data = [];

    foreach ($locations as $location) {
        $array_data[$location->id] = [
            'location' => $location->location
        ];
    }
@endphp
<table>
    <tr>
        <td colspan="{{ $total_colspan }}"></td>
    </tr>
    <tr>
        <td colspan="{{ $total_colspan }}"> PT. SURYA BUANA LESTARIJAYA </td>
    </tr>
    <tr>
        <td colspan="{{ $total_colspan }}"> Catering dan Accommodation Services </td>
    </tr>
    <tr>
        <td colspan="{{ $total_colspan }}"></td>
    </tr>
    <tr>
        <td colspan="{{ $total_colspan }}"> MONTHLY OPERATION RECORD </td>
    </tr>
    <tr>
        <td colspan="{{ $total_colspan }}"> PERIOD : {{ $month_name }} - {{ $year }} </td>
    </tr>
    <tr>
        <td colspan="{{ $total_colspan }}"></td>
    </tr>
    <tr>
        <td>SHIPMENT</td>
        <td>WEEK</td>
        <td>SUPPLIER</td>
        @foreach ($locations as $location)
            <td>{{ $location['location'] }}</td>
        @endforeach
        <td>AMOUNT</td>
    </tr>
    
    <tr>
        <td colspan="2">BEGINNING STOCK {{ $last_month_name }} {{ $last_year }}</td>
        <td></td>
        @php
            $beginning_stock_collect = collect($beginning_stock);
            // dd($beginning_stock_collect->toArray());
            $total_last_stock = 0;
        @endphp
        @foreach ($locations as $location)
            @php
                $last_mics = $beginning_stock_collect->firstWhere('id', $location->id);
                $last_actual_stock = !empty($last_mics->mor_month[0]) ? $last_mics->mor_month[0]->mor_month_detail[0]->total_actual_stock_price : 0;
                $total_last_stock += $last_actual_stock;
            @endphp
            <td>{{ $last_actual_stock }}</td>
        @endforeach
        <td>{{ $total_last_stock }}</td>
    </tr>
    
    @php
        $total_sum_total_item_price = 0;
        $total_sum_total_item_price_first = 0;
    @endphp
 
    @for ($i=1; $i <= 5; $i++)
        @php
            $new_data = collect($data);
            $item = $new_data->firstWhere('week', $i);
            $week_sum_total_item_price = 0;
        @endphp 

        @if(empty($item))
            <tr>
                <td>WEEK</td>
                <td>{{ numericToRoman($i) }}</td>
                @for ($a = 1; $a <= $total_location + 2; $a++)
                    <td></td>
                @endfor
            </tr>
        @else
            @php
                $total_supplier = count($item['supplier']);
                $suppliers = collect($item['supplier']);
                $supplier_no = 0;
            @endphp
            <tr>
                <td rowspan="{{ $total_supplier }}">WEEK</td>
                <td rowspan="{{ $total_supplier }}">{{ numericToRoman($item['week']) }}</td>
                <td>{{ $suppliers[0]['supplier_name'] }}</td>

                @php
                    $location_data = collect($suppliers[0]['location']);
                    $sum_total_item_price_first = $location_data->sum('total_item_price');
                    $week_sum_total_item_price = $sum_total_item_price_first;
                    $total_sum_total_item_price_first += $sum_total_item_price_first;
                @endphp
                
                @foreach ($locations as $location)
                    @php
                        $location_detail = $location_data->firstWhere('location_id', $location->id);
                    @endphp
                    <td>{{ !empty($location_detail) ? $location_detail['total_item_price'] : NULL }}</td>
                @endforeach
                    <td>{{ $sum_total_item_price_first }}</td>
            </tr>
            <tr>
                @foreach ($suppliers->slice(1) as $supplier)
                    @php 
                        $supplier_no++;
                        $location_data = collect($supplier['location']);
                        $sum_total_item_price = $location_data->sum('total_item_price');
                        $week_sum_total_item_price += $sum_total_item_price;
                        $total_sum_total_item_price += $sum_total_item_price
                    @endphp
                    <td>{{ $supplier['supplier_name'] }}</td>

                    @foreach ($locations as $location)
                        @php
                            $location_detail = $location_data->firstWhere('location_id', $location->id);
                        @endphp
                        <td>{{ !empty($location_detail) ? $location_detail['total_item_price'] : NULL }}</td>
                    @endforeach
                        <td>{{ $sum_total_item_price }}</td>
                @endforeach
            </tr>
        @endif
          <!-- Divider -->
        <tr>
            <td colspan="3"></td>
            @foreach ($locations as $location)
                @php
                    $total_price_week = !empty($wekk_summary[$i][$location->id]['total_item_price']) ? $wekk_summary[$i][$location->id]['total_item_price'] : 0;
                @endphp
                <td>{{ $total_price_week }}</td>
            @endforeach
            <td>{{ $week_sum_total_item_price }}</td>
        </tr>

        <!-- Divider -->
        <tr>
            <td colspan="{{ $total_colspan}}"></td>
        </tr>
    @endfor

    <!-- Grand total -->
    <tr>
        <td colspan="2">Grand Total</td>
        <td></td>
        @php
            $grant_total_all = $total_sum_total_item_price + $total_sum_total_item_price_first
        @endphp
        @foreach ($locations as $location)
            @php
                $total_location = !empty($total_item_price_location[$location->id]) ? $total_item_price_location[$location->id] : null;
                $total_location_total_item_price = !empty($total_location) ? $total_location['total_item_price'] : 0;
            @endphp
            <td>{{ $total_location_total_item_price }}</td>
        @endforeach
        <td>{{ $grant_total_all }}</td>
    </tr>
    <tr>
        <td colspan="2">Closing Stock</td>
        <td></td>
        @php
            $mics_collect = collect($mics_month);
            $total_actual_stock = 0;
        @endphp
        @foreach ($locations as $location)
            @php
                $mics = $mics_collect->firstWhere('id', $location->id);
                $actual_stock = !empty($mics->mor_month[0]) ? $mics->mor_month[0]->mor_month_detail[0]->total_actual_stock_price : 0;
                $total_actual_stock += $actual_stock;
            @endphp
            <td>{{ $actual_stock }}</td>
        @endforeach
        <td>{{ $total_actual_stock }}</td>
    </tr>
    <tr>
        <td colspan="2" >Consumption</td>
        <td></td>
        @php
            $comsumption_total = $grant_total_all - $total_actual_stock;
        @endphp
        @foreach ($locations as $location)
            @php
                $total_location = !empty($total_item_price_location[$location->id]) ? $total_item_price_location[$location->id] : null;
                $total_location_total_item_price = !empty($total_location) ? $total_location['total_item_price'] : 0;

                $mics = $mics_collect->firstWhere('id', $location->id);
                $actual_stock = !empty($mics->mor_month[0]) ? $mics->mor_month[0]->mor_month_detail[0]->total_actual_stock_price : 0;

                $consumtion = $total_location_total_item_price - $actual_stock;

                $array_data[$location->id]['consumption'] = $consumtion;
            @endphp
            <td>{{ $consumtion }}</td>
        @endforeach
        <td>{{ $comsumption_total }}</td>
    </tr>
    <tr>
        <td colspan="2">Manday</td>
        <td></td>
        @php
            $meal_sheat_collect = collect($meal_sheat_month);
            $total_mandays = 0; 
        @endphp
        @foreach ($locations as $location)
            @php
                $meal_sheet = $meal_sheat_collect->firstWhere('location_id', $location->id);
                $mandays = $meal_sheet['mandays'];
                $total_mandays += $mandays;

                $array_data[$location->id]['mandays'] = $mandays;
            @endphp
            <td>{{ $mandays }}</td>
        @endforeach
        <td>{{ $total_mandays }}</td>
    </tr>
    <tr>
        <td colspan="2">FOOD COST</td>
        <td></td>
        @php
            $total_food_cost = 0;
        @endphp
        @foreach ($locations as $location)
        @php
            $food_cost = $array_data[$location->id]['mandays'] == 0 ? $array_data[$location->id]['consumption'] : $array_data[$location->id]['consumption'] / $array_data[$location->id]['mandays'];
            $total_food_cost += $food_cost;
        @endphp
            <td>{{ $food_cost }}</td>
        @endforeach
        <td>{{ $total_food_cost }}</td>
    </tr>
    
    {{-- calculate sales  --}}
        @php
            $sales_collect = collect($sales);
            $all_total_amount = 0;
        @endphp
        @foreach ($meal_sheat_month as $meal_sheat)
            @php
                $mandays = $meal_sheat['mandays'];
                $casual_breakfast = $meal_sheat['casual_breakfast'];
                $casual_lunch = $meal_sheat['casual_lunch'];
                $casual_dinner = $meal_sheat['casual_dinner'];
                $supper = $meal_sheat['supper'];

                $sales_data = $sales_collect->firstWhere('location_id', $meal_sheat['location_id']);

                $sales_mandays = !empty($sales_data['manday']) ? $sales_data['manday'] : 0;
                $sales_minimum = !empty($sales_data['minimum']) ? $sales_data['minimum'] : 0;
                $sales_breakfast = !empty($sales_data['breakfast']) ? $sales_data['breakfast'] : 0;
                $sales_lunch = !empty($sales_data['lunch']) ? $sales_data['lunch'] : 0;
                $sales_dinner = !empty($sales_data['dinner']) ? $sales_data['dinner'] : 0;
                $sales_supper = !empty($sales_data['supper']) ? $sales_data['supper'] : 0;
                $sales_hk = !empty($sales_data['hk']) ? $sales_data['hk'] : 0;

                $amount_mandays = $mandays * $sales_mandays;
                $amount_breakfast = $casual_breakfast * $sales_breakfast;
                $amount_lunch = $casual_lunch * $sales_lunch;
                $amount_dinner = $casual_dinner * $sales_dinner;
                $amount_supper = $supper * $sales_supper;
                $amount_hk = $mandays * $sales_hk;

                $total_amount = $amount_mandays + $amount_breakfast + $amount_lunch + $amount_dinner + $amount_supper + $amount_hk;
                $total_mandays_hk = $sales_mandays + $sales_hk;
                $all_total_amount += $total_amount;

                $array_data[$meal_sheat['location_id']]['sales'] = $total_amount;
            @endphp
        @endforeach
    {{-- end calculate sales  --}}
    <tr>
        <td colspan="2">TAGIHAN CATERING</td>
        <td></td>
        @foreach ($locations as $location)
            @php
                $sales = $array_data[$location->id]['sales'];
            @endphp
            <td>{{ $sales }}</td>
        @endforeach
        <td>{{ $all_total_amount }}</td>
    </tr>

    <!-- Divider -->
    <tr>
        <td colspan="9"></td>
    </tr>

    <!-- Signatrue header -->
    <tr>
        <td colspan="2"></td>
        <td>Prepared By</td>
        <td>Checked By</td>
        <td>Approved By</td>
        <td>Approved By</td>
        <td>Acknowledge By</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td rowspan="6"></td>
        <td rowspan="6"></td>
        <td rowspan="6"></td>
        <td rowspan="6"></td>
        <td rowspan="6"></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="9"></td>
    </tr>
    <tr>
        <td colspan="9"></td>
    </tr>
    <tr>
        <td colspan="9"></td>
    </tr>
    <tr>
        <td colspan="9"></td>
    </tr>
    <tr>
        <td colspan="9"></td>
    </tr>
    
     <!-- Position -->
     <tr>
        <td colspan="2"></td>
        <td>Purchasing</td>
        <td>Quality Control</td>
        <td>Operation</td>
        <td>Operation</td>
        <td></td>
        <td colspan="2"></td>
    </tr>
    <!-- name -->
    <tr>
        <td colspan="2"></td>
        <td>Haikal Fajar</td>
        <td>Johan</td>
        <td>Rifki</td>
        <td>Dian Ane Wibowo</td>
        <td></td>
        <td colspan="2"></td>
    </tr>

    <tr>
        <td colspan="9"></td>
    </tr>

</table>
