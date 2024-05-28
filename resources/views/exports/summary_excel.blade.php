<table>
    <tr>
        <td colspan="5"></td>
    </tr>
    <tr>
        <td colspan="5">PT SURYA BUANA LESTARIJAYA</td>
    </tr>
    <tr>
        <td colspan="5">Catering dan Accommodation Services</td>
    </tr>
    <tr>
        <td colspan="5"></td>
    </tr>
    <tr>
        <td colspan="5"></td>
    </tr>
    <tr>
        <td colspan="5">SUMMARY PHYSICAL INVENTORY</td>
    </tr>
    <tr>
        <td colspan="5">{{ $month_name }} - {{ $year }}</td>
    </tr>
    <tr>
        <td colspan="5"></td>
    </tr>
    <tr>
        <th>NO</th>
        <th>VESSEL</th>
        <th>MONTH</th>
        <th>AMOUNT</th>
        <th>DATE OF INVENTORY</th>
    </tr>

    @php
        $no = 1;
        $total_amount = 0;
    @endphp
    @foreach ($data as $data)
        <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $data->location }}</td>
            <td>{{ $month_name }}-{{ $year }}</td>

            @php
                $total_actual_stock_price = !empty($data->mor_month[0]) ? $data->mor_month[0]->mor_month_detail[0]->total_actual_stock_price : 0;
                $total_amount += $total_actual_stock_price;
            @endphp
            <td>{{ $total_actual_stock_price != 0 ? formatRupiah($total_actual_stock_price) : 'Rp. 0' }}</td>
            
            <td>date inventory</td>
        </tr>
    @endforeach
    <tr>
        <td></td>
        <td>TOTAL</td>
        <td></td>
        <td>{{  $total_amount != 0 ? formatRupiah($total_amount) : 'Rp. 0' }}</td>
        <td></td>
    </tr>
</table>