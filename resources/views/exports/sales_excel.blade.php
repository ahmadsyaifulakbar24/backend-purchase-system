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
    <tr>
        <td colspan="9"></td>
    </tr>
    <tr>
        <td colspan="9">SALES {{ $month_name }}-{{ $year }}</td>
    </tr>
    <tr>
        <td colspan="9"></td>
    </tr>

    @php
        $sales_collect = collect($sales);
        $all_total_amount = 0;
    @endphp
    @foreach ( $meal_sheat_month as $meal_sheat)
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
        @endphp
        <tr>
            <td>{{ $sales_minimum }}</td>
            <td rowspan="8">{{ $meal_sheat['location'] }}</td>
            <td>SALES {{ $month_name }} {{ $year }}</td>
            <td>QTY</td>
            <td>MEAL RATE</td>
            <td>AMOUNT</td>
            <td>NEW RATE</td>
            <td>AMOUNT</td>
            <td rowspan="8">{{ $total_mandays_hk }}</td>
        </tr>
        <tr>
            <td></td>
            <td>MANDAY</td>
            <td>{{ $mandays }}</td>
            <td>{{ $sales_mandays }}</td>
            <td>{{ $amount_mandays }}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>CASUAL BREAKFAST</td>
            <td>{{ $casual_breakfast }}</td>
            <td>{{ $sales_breakfast }}</td>
            <td>{{ $amount_breakfast }}</td>
            <td>-</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>CASUAL LUNCH</td>
            <td>{{ $casual_lunch }}</td>
            <td>{{ $sales_lunch }}</td>
            <td>{{ $amount_lunch }}</td>
            <td>-</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>CASUAL DINNER</td>
            <td>{{ $casual_dinner }}</td>
            <td>{{ $sales_dinner }}</td>
            <td>{{ $amount_dinner }}</td>
            <td>-</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>SUPPER</td>
            <td>{{ $supper }}</td>
            <td>{{ $sales_supper}}</td>
            <td>{{ $amount_supper }}</td>
            <td>-</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>HK</td>
            <td>{{ $mandays }}</td>
            <td>{{ $sales_hk }}</td>
            <td>{{ $amount_hk }}</td>
            <td>-</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>TOTAL</td>
            <td></td>
            <td></td>
            <td>{{ $total_amount }}</td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td colspan="8"></td>
        </tr>
    @endforeach
    <tr>
        <td></td>
        <td>GRAND TOTAL</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>{{ $all_total_amount }}</td>
        <td></td>
    </tr>
    


</table>