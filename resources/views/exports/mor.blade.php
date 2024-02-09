<style>
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
    }

    .text-center {
        text-align: center;
    }
</style>
<table>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <td colspan="88">PT. SURYA BUANA LESTARIJAYA</td>        
    </tr>

    <tr>
        <td>MONTH</td>
        <td>: {{ strtoupper($month) }} {{ $year }}</td>
        <td></td>
        <td></td>
        <td>LOCATION</td>
        <td>: {{ $location->location }}</td>
        <td colspan="82"></td>
    </tr>

    <tr>
        <td rowspan="3"> NO </td>
        <td rowspan="3"> DESCRIPTION </td>
        <td rowspan="3"> BRAND </td>
        <td rowspan="3"> SIZE </td>
        <td rowspan="3"> UNIT </td>
        <td rowspan="3"> PRICE </td>
        <td rowspan="2" colspan="2">OPENING STOCK</td>
        <td colspan="10">RECEIVING FOOD MATERIALS</td>
        <td rowspan="3" colspan="2">TOTAL</td>
        <td rowspan="2" colspan="62">MATERIAL INVENTORY CONTROL SHEET</td>
        <td colspan="2">TOTAL ISSUE</td>
        <td colspan="2">END STOCK</td>
        <td colspan="2">PHYSIC INVENTORY </td>
    </tr>

    <tr>
        <td colspan="2"> WEEK-1 </td>
        <td colspan="2"> WEEK-2 </td>
        <td colspan="2"> WEEK-3 </td>
        <td colspan="2"> WEEK-4 </td>
        <td colspan="2"> WEEK-V </td>
    </tr>

    <tr>
        <td colspan="2">31.04.2023</td>
        <td colspan="2">07/{{ $mm }}/{{ $year }}</td>
        <td colspan="2">14/{{ $mm }}/{{ $year }}</td>
        <td colspan="2">21/{{ $mm }}/{{ $year }}</td>
        <td colspan="2">28/{{ $mm }}/{{ $year }}</td>
        <td colspan="2">31/{{ $mm }}/{{ $year }}</td>
        <td colspan="62">DAILY</td>
        
        <td>QTY</td>
        <td>QMOUNT</td>
        <td>QTY</td>
        <td>QMOUNT</td>
        <td> QTY </td>
        <td>QMOUNT</td>
    </tr>

    <tr>
        <td> CATEGORY </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td> QTY </td>
        <td> AMOUNT </td>
        <td> QTY </td>
        <td> AMOUNT </td>
        <td> QTY </td>
        <td> AMOUNT </td>
        <td> QTY </td>
        <td> AMOUNT </td>
        <td> QTY </td>
        <td> AMOUNT </td>
        <td> QTY </td>
        <td> AMOUNT </td>
        <td></td>
        <td></td>
        <td>1</td>
        <td></td>
        <td>2</td>
        <td></td>
        <td>3</td>
        <td></td>
        <td>4</td>
        <td></td>
        <td>5</td>
        <td></td>
        <td>6</td>
        <td></td>
        <td>7</td>
        <td></td>
        <td>8</td>
        <td></td>
        <td>9</td>
        <td></td>
        <td>10</td>
        <td></td>
        <td>11</td>
        <td></td>
        <td>12</td>
        <td></td>
        <td>13</td>
        <td></td>
        <td>14</td>
        <td></td>
        <td>15</td>
        <td></td>
        <td>16</td>
        <td></td>
        <td>17</td>
        <td></td>
        <td>18</td>
        <td></td>
        <td>19</td>
        <td></td>
        <td>20</td>
        <td></td>
        <td>21</td>
        <td></td>
        <td>22</td>
        <td></td>
        <td>23</td>
        <td></td>
        <td>24</td>
        <td></td>
        <td>25</td>
        <td></td>
        <td>26</td>
        <td></td>
        <td>27</td>
        <td></td>
        <td>28</td>
        <td></td>
        <td>29</td>
        <td></td>
        <td>30</td>
        <td></td>
        <td>31</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    @php
        $category_no = 0;
        $grand_total_amount_opening_stock = 0;
        $grand_total_amount_week = [];
        $grand_total_amount_all_week= 0;
        $grand_total_amount_day = [];
        $grand_total_issue = 0;
        $grand_total_end_stock = 0;
        $grand_total_actual_stock = 0;
    @endphp
    @foreach ($item_product as $category_code => $sub_category)
        @php
            $category_no++;
            $no = 0;

            $total_amount_opening_stock = 0;
            $total_amount_week = [];
            $total_amount_all_week= 0;
            $total_amount_day = [];
            $total_issue = 0;
            $total_end_stock = 0;
            $total_actual_stock = 0;
        @endphp
        @foreach ($sub_category as $sub_category_code => $product)
            @php
                $no++;
                $first_item = $product->first();
            @endphp
            <tr>
                <td rowspan="">
                    @if ($no == 1)
                        {{ strtoupper($first_item->item_category->category) }}
                    @endif
                </td>
                <td colspan="5"> {{ strtoupper($first_item->sub_item_category->category) }}</td>
                <td colspan="82"></td>
            </tr>

            @foreach ($product as $data)
                <tr>
                    <td></td>
                    <td> {{ $data->name }} </td>
                    <td> {{ $data->brand }} </td>
                    <td> {{ $data->size}} </td>
                    <td> {{ $data->unit->param }} </td>
                    <td> {{ numberFormat($data->mor_month_detail?->price) }} </td>
                    <td> {{ number_format($data->mor_month_detail?->last_stock, 0, ',', '.') }} </td>

                    @php
                        $amount_opening_stock = $data->mor_month_detail?->price * $data->mor_month_detail?->last_stock;
                        $total_amount_opening_stock += $amount_opening_stock;
                    @endphp
                    <td> 
                        {{ numberFormat($amount_opening_stock) }}
                    </td>

                    @php
                        $all_week = [];
                        $quantity_all_week = 0;
                        for ($week=1; $week <= 5; $week++) { 
                            $week_data =  $data->delivery_order->where('week', $week)->first();
                            $total_quantity = !empty($week_data->total_quantity) ? $week_data->total_quantity : 0;
                            $quantity_all_week += $total_quantity;
                            $total_price = $total_quantity * $data->mor_month_detail?->price;

                            $all_week[$week] = [
                                'week' => !empty($week_data->week) ? $week_data->week : $week,
                                'total_quantity' => $total_quantity,
                                'total_price' => $total_price,
                            ];

                            if (!isset($total_amount_week[$week])) {
                                $total_amount_week[$week] = 0;
                            }
                            $total_amount_week[$week] += $total_price;
                        }
                    @endphp
                    
                    @foreach ($all_week as $list_week)
                        <td> {{ number_format($list_week['total_quantity'], 0, ',', '.') }} </td>
                        <td>{{ numberFormat($list_week['total_price']) }}</td>
                    @endforeach
                    
                    @php
                        $stock_available = $data->mor_month_detail?->last_stock + $quantity_all_week;
                        $price_stock_available = $stock_available * $data->mor_month_detail?->price;
                        $total_amount_all_week += $price_stock_available;
                    @endphp
                    <td> {{ number_format($stock_available, 0, ',', '.') }} </td>
                    <td>{{ numberFormat($price_stock_available) }}</td>

                    @php
                        $all_day = [];
                        $quantity_all_day = 0;

                        for ($day=1; $day <= 31; $day++) { 
                            $date = $year . '-' . $mm . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                            $day_data = $data->mor->where('date', $date)->first();
                            $day_total_quantity = !empty($day_data->quantity) ? $day_data->quantity : 0;
                            $quantity_all_day += $day_total_quantity;
                            $total_price_day = $day_total_quantity * $data->mor_month_detail?->price;
                            
                            $all_day[$day] = [
                                'day' => !empty($day_data->date) ? $day_data->date : $day,
                                'day_total_quantity' => $day_total_quantity,
                                'total_price_day' => $total_price_day,
                            ];

                            if (!isset($total_amount_day[$day])) {
                                $total_amount_day[$day] = 0;
                            }
                            $total_amount_day[$day] += $total_price_day;
                        }
                    @endphp

                    @foreach ($all_day as $list_day)
                        <td> {{ number_format($list_day['day_total_quantity'], 0, ',', '.') }} </td>
                        <td>{{ numberFormat($total_price_day) }}</td>
                    @endforeach

                    @php
                        $price_quantity_all_day = $quantity_all_day * $data->mor_month_detail?->price;
                        $total_issue += $price_quantity_all_day
                    @endphp
                    <td> {{ number_format($quantity_all_day , 0, ',', '.') }} </td>
                    <td>{{ numberFormat($price_quantity_all_day) }}</td>


                    @php
                        $end_stock = $stock_available - $quantity_all_day;
                        $price_end_stock = $end_stock  * $data->mor_month_detail?->price;
                        $total_end_stock += $price_end_stock;
                    @endphp
                    <td> {{ number_format($end_stock , 0, ',', '.') }} </td>
                    <td>{{ numberFormat($price_end_stock) }}</td>

                    
                    @php
                        $actual_stock = $data->mor_month_detail?->actual_stock;
                        $price_actual_stock = $actual_stock * $data->mor_month_detail?->price;
                        $total_actual_stock += $price_actual_stock;
                    @endphp
                    <td> {{ number_format($actual_stock , 0, ',', '.') }} </td>
                    <td>{{ numberFormat($price_actual_stock) }}</td>
                </tr>
            @endforeach
        @endforeach

        <tr>
            <td> {{ indexToAlphabet($category_no - 1) }} </td>
            <td> TOTAL {{ strtoupper($first_item->item_category->category) }} </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            @php
                $grand_total_amount_opening_stock += $total_amount_opening_stock;
            @endphp
            <td> {{ numberFormat($total_amount_opening_stock) }} </td>

            @php
                foreach ($total_amount_week as $key => $value) {
                    if (!isset($grand_total_amount_week[$key])) {
                        $grand_total_amount_week[$key] = 0;
                    }

                    $grand_total_amount_week[$key] += $value;
                }
            @endphp
            @foreach ($total_amount_week as $amount_week)
                <td></td>
                <td>{{ numberFormat($amount_week) }}</td>
            @endforeach
            
            @php
               $grand_total_amount_all_week += $total_amount_all_week;
            @endphp
            <td></td>
            <td>{{ numberFormat($total_amount_all_week) }}</td>


            @php
                foreach ($total_amount_day as $key => $value) {
                    if (!isset($grand_total_amount_day[$key])) {
                        $grand_total_amount_day[$key] = 0;
                    }

                    $grand_total_amount_day[$key] += $value;
                }
            @endphp
            @foreach ($total_amount_day as $amount_day)
                <td></td>
                <td>{{ numberFormat($amount_day) }}</td>
            @endforeach

            @php
               $grand_total_issue += $total_issue;
            @endphp
            <td></td>
            <td>{{ numberFormat($total_issue) }}</td>


            @php
               $grand_total_end_stock += $total_end_stock;
            @endphp
            <td></td>
            <td>{{ numberFormat($total_end_stock) }}</td>

            @php
               $grand_total_actual_stock += $total_actual_stock;
            @endphp
            <td></td>
            <td>{{ numberFormat($total_actual_stock) }}</td>
        </tr>
    @endforeach

    <tr></tr>

    <tr>
        <td></td>
        <td> GRAND TOTAL </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td> {{ numberFormat($grand_total_amount_opening_stock) }} </td>

        @foreach($grand_total_amount_week as $grand_total_amount_week) 
            <td></td>
            <td> {{ numberFormat($grand_total_amount_week) }} </td>
        @endforeach

        <td></td>
        <td> {{ numberFormat($grand_total_amount_all_week) }} </td>


        @foreach($grand_total_amount_day as $grand_total_amount_day) 
            <td></td>
            <td> {{ numberFormat($grand_total_amount_day) }} </td>
        @endforeach

        <td></td>
        <td> {{ numberFormat($grand_total_issue) }} </td>

        <td></td>
        <td> {{ numberFormat($grand_total_end_stock) }} </td>

        <td></td>
        <td> {{ numberFormat($grand_total_actual_stock) }} </td>
    </tr>

    <tr></tr>
    <tr></tr>
    <tr></tr>

    <tr>
        <td></td>
        <td>CATEGORY</td>
        <td>WEEK</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>TOTAL</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>OPENING STOCK</td>
        <td>I</td>
        <td>II</td>
        <td>III</td>
        <td>VI</td>
        <td>V</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>A</td>
        <td>FROZEN FOODS</td>
        <td> 3,729,475 </td>
        <td> 9,590,560 </td>
        <td> 10,797,360 </td>
        <td> 10,415,960 </td>
        <td> 10,103,065 </td>
        <td> 9,138,960 </td>
        <td> 53,775,380 </td>
        <td></td>
        <td></td>
        <td>   </td>
        <td> </td>
        <td>Prepared by:</td>
        <td></td>
        <td></td>
        <td>Prepared by:</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>DAIRY PRODUCT</td>
        <td> 1,086,853 </td>
        <td> 3,503,252 </td>
        <td> 3,830,086 </td>
        <td> 3,568,496 </td>
        <td> 3,121,181 </td>
        <td> 4,042,540 </td>
        <td> 19,152,408 </td>
        <td></td>
        <td></td>
        <td>   </td>
        <td></td>
        <td> </td>
        <td></td>
        <td></td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>


    <tr>
        <td></td>
        <td>TOTAL</td>
        <td> 20,099,325 </td>
        <td> 39,995,798 </td>
        <td> 40,211,158 </td>
        <td> 39,186,923 </td>
        <td> 38,407,232 </td>
        <td> 37,488,376 </td>
        <td> 215,388,812 </td>
        <td></td>
        <td></td>
        <td>   </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>FHISICAL INVENTORY</td>
        <td> - </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td> 38,949,282 </td>
        <td> 38,949,282 </td>
        <td></td>
        <td></td>
        <td></td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>CONSUMPTION</td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> 176,439,531 </td>
        <td> 176,439,531 </td>
        <td></td>
        <td></td>
        <td></td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>MANDAYS</td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> 1,364 </td>
        <td> 1,369 </td>
        <td></td>
        <td></td>
        <td></td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>FOOD COST</td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> - </td>
        <td> 129,354 </td>
        <td> 128,882 </td>
        <td></td>
        <td></td>
        <td></td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>