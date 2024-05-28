@php
    $total_location = count($locations);
    $total_colspan = $total_location + 4;
@endphp
<table>
    <tr>
        <td colspan="{{ $total_colspan }}"></td>
    </tr>
    <tr>
        <td colspan="{{ $total_colspan }}"> PT SURYA BUANA LESTARIJAYA </td>
    </tr>
    <tr>
        <td colspan="{{ $total_colspan }}"> Catering dan Accommodation Services </td>
    </tr>
    <tr>
        <td colspan="{{ $total_colspan }}"></td>
    </tr>
    <tr>
        <td colspan="{{ $total_colspan }}"> REALISASI PURCHASE RECORD </td>
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
    
    @php
        $total_sum_total_item_price = 0;
        $total_sum_total_item_price_first = 0;
    @endphp

    @for ($i=1; $i <= 5; $i++)
        @php
            $new_data = collect($data);
            $item = $new_data->firstWhere('week', $i);
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
                    $total_sum_total_item_price_first = $sum_total_item_price_first;
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
    @endfor

    <!-- Divider -->
    <tr>
        <td colspan="{{ $total_colspan + 2 }}"></td>
    </tr>

    <!-- Menghitung Total -->
    <tr>
        <td></td>
        <td></td>
        <td>MENGHITUNG TOTAL</td>
        @foreach ($locations as $location)
            @php
                $total_location = !empty($total_item_price_location[$location->id]) ? $total_item_price_location[$location->id] : null;
            @endphp
            <td>{{ !empty($total_location) ? $total_location['total_item_price'] : null; }}</td>
        @endforeach
        <td>{{ $total_sum_total_item_price + $total_sum_total_item_price_first }}</td>
    </tr>

     <!-- Divider -->
     <tr>
        <td colspan="9"></td>
    </tr>

     <!-- Signatrue header -->
     <tr>
        <td></td>
        <td></td>
        <td>Prepared By</td>
        <td>Checked By</td>
        <td>Approved By</td>
        <td>Approved By</td>
        <td>Acknowledge By</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td rowspan="6"></td>
        <td rowspan="6"></td>
        <td rowspan="6"></td>
        <td rowspan="6"></td>
        <td rowspan="6"></td>
    </tr>
    
    <tr>
        <td><td>
    </tr>
    <tr>
        <td><td>
    </tr>
    <tr>
        <td><td>
    </tr>
    <tr>
        <td><td>
    </tr>
    <tr>
        <td><td>
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
    <!-- Nmae -->
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