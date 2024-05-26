<style>
    table, th, td {
      border: 1px solid black;
      padding: 6px;
      border-collapse: collapse;
      text-align: center;
    }
    td {
        width: 120;
        font-size: 0.8rem;
    }
    th {
        width: 120;
        font-size: 0.8rem;
        font-weight: bold;
    }
    .text-center {
        text-align: center;
    }
    .br-none {
        border-right: none;
    }
    .bl-none {
        border-left: none;
    }
    .bx-none {
        border-right: none;
        border-left: none;
    }
    .tr-h-100 {
        height: 80;
    }
    .divider {
        height: 30;
        border: none;
    }
    .text-header {
        font-size: 1.5rem;
    }
    .text-subheader {
        font-size: 1.3rem;
    }
    .bg-green {
        background-color: #A9D08E;
    }
    .bg-orange {
        background-color: #F4B084;
    }
    .bg-black {
        background-color: #757171;
        color: white;
    }
</style>
<table border="1">
    <tr class='tr-h-100'>
        <td colspan="1" class="br-none">
            <img src="" alt="checklist">
        <td colspan="3" class="bx-none">
            <div>
                <span class="text-header">PT. SURYA BUANA LESTARIJAYA</span>
                <br /> 
                <span>Catering dan Accomodation Services</span>
            </div>
        </td>
        <td colspan="1" class="bl-none"></td>
    </tr>
    <tr class="divider">
        <td colspan="5"></td>
    </tr>
    <tr class="bg-green">
        <td colspan="5" class="text-subheader">SUMMARY PHYSICAL INVENTORY <br /> {{ $month_name }} - {{ $year }}</td>
    </tr>
    <tr class="divider">
        <td colspan="5"></td>
    </tr>
    <tr class="bg-orange">
        <th>NO</th>
        <th>VESSEL</th>
        <th>MONTH</th>
        <th>AMOUNT</th>
        <th>DATE OF INVENTORY</th>
    </tr>
    <tbody>
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
        <tr class="bg-black">
            <td></td>
            <td>TOTAL</td>
            <td></td>
            <td>{{  $total_amount != 0 ? formatRupiah($total_amount) : 'Rp. 0' }}</td>
            <td></td>
        </tr>
    </tbody>
    <tr style="height: 150;" class="divider">
        <td colspan="5"></td>
    </tr>

</table>