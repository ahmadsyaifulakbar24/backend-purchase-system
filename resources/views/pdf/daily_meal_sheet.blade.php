<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style type="text/css">
        .tg {
            border-collapse: collapse;
            border-spacing: 0;
        }

        .tg td {
            border-color: black;
            border-style: solid;
            border-width: 1px;
            font-family: Arial, sans-serif;
            font-size: 11px;
            overflow: hidden;
            padding: 5px 5px;
            font-weight: bold;
            word-break: normal;
        }

        .tg th {
            border-color: black;
            border-style: solid;
            border-width: 1px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: normal;
            overflow: hidden;
            padding: 5px 5px;
            word-break: normal;
        }

        .tg .tg-baqh {
            text-align: center;
            vertical-align: top
        }

        .tg .tg-c3ow {
            border-color: inherit;
            text-align: center;
            vertical-align: top
        }

        .tg .tg-7btt {
            border-color: inherit;
            font-weight: bold;
            text-align: center;
            vertical-align: top
        }

        .tg .tg-sn4r {
            border-color: #333333;
            font-weight: bold;
            text-align: center;
            vertical-align: top
        }

        .tg .tg-de2y {
            border-color: #333333;
            text-align: center;
            vertical-align: top
        }

        .tg .tg-dvpl {
            border-color: inherit;
            text-align: right;
            vertical-align: top
        }

        .tg .tg-fymr {
            border-color: inherit;
            font-weight: bold;
            text-align: center;
            vertical-align: top
        }

        .tg .tg-0pky {
            border-color: inherit;
            text-align: center;
            vertical-align: top
        }

        .tg .tg-0lax {
            text-align: center;
            vertical-align: top
        }

        .tg .text-left {
            text-align: center;
            vertical-align: top;
            text-align: left;
        }

        .no {
            width: 10px;
        }
        .br-none {
            border-right: none;
        }
        .bl-none {
            border-left: none;
        }
        .bt-none {
            border-top: none;
        }
        .bb-none {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <table class="tg" style="width: 700px;" >
        <thead>
            <tr>
                <th class="tg-c3ow" colspan="10">
                    <h1>PT.Surya Buana Lestarijaya</h1>
                    <b>Catering and Accomodation Service</b>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tg-7btt" colspan="10" style="border-bottom: none">Meal Count Sheet and Accomodation Record</td>
            </tr>
            <tr>
                <td class="tg-sn4r" colspan="3" style="border-right: none; border-top: none">{{ $meal_sheet_detail->meal_sheet_daily->meal_sheet_group->location->location }}</td>
                <td class="tg-de2y" style="border-left: none; border-right: none; border-top: none">{{ $meal_sheet_detail->client->client_name }}</td>
                <td class="tg-dvpl" colspan="6" style="border-left: none; border-top: none">DATE : {{ Carbon\Carbon::parse($meal_sheet_detail->meal_sheet_daily->meal_sheet_date)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="tg-7btt" style="width: 10px;">NO</td>
                <td class="tg-7btt">NAME</td>
                <td class="tg-7btt">POSITION</td>
                <td class="tg-7btt">COMPANY</td>
                <td class="tg-7btt">B</td>
                <td class="tg-7btt">L</td>
                <td class="tg-7btt">D</td>
                <td class="tg-7btt">S</td>
                <td class="tg-7btt">TOTAL</td>
                <td class="tg-7btt">ACCOM</td>
            </tr>
            @php
                $no = 1;
                $total_breakfast = 0;
                $total_lunch = 0;
                $total_dinner = 0;
                $total_super = 0;
                $total_accomodation = 0;
                $total_total = 0;
            @endphp
            @foreach ($meal_sheet_detail->meal_sheet_record as $record)
                <tr>
                    <td class="tg-7btt" style="width: 5px;">{{ $no++ }}</td>
                    <td class="text-center">{{ $record->name }}</td>
                    <td class="text-center">{{ $record->position }}</td>
                    <td class="text-center">{{ $record->company }}</td>
                    <td class="tg-0pky">{{ $record->breakfast }}</td>
                    <td class="tg-0pky">{{ $record->lunch }}</td>
                    <td class="tg-0pky">{{ $record->dinner }}</td>
                    <td class="tg-0pky">{{ $record->super }}</td>
                    @php
                        $total = $record->breakfast + $record->lunch + $record->dinner + $record->super
                    @endphp
                    <td class="tg-0pky">{{ $total }}</td>
                    <td class="tg-0pky">{{ $record->accomodation }}</td>

                    @php
                        $total_breakfast += $record->breakfast;
                        $total_lunch += $record->lunch;
                        $total_dinner += $record->dinner;
                        $total_super += $record->super;
                        $total_accomodation += $record->accomodation;
                        $total_total += $total;
                    @endphp
                </tr>
            @endforeach
            <tr>
                <td class="tg-7btt" style="width: 5px;"></td>
                <td class="tg-7btt">Total</td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax">{{ $total_breakfast }}</td>
                <td class="tg-0lax">{{ $total_lunch }}</td>
                <td class="tg-0lax">{{ $total_dinner }}</td>
                <td class="tg-0lax">{{ $total_super }}</td>
                <td class="tg-0lax">{{ $total_total }}</td>
                <td class="tg-0lax">{{ $total_accomodation }}</td>
            </tr>
            <tr>
                <td style="border-right: none; border-bottom: none;"></td>
                <td style="border-left: none; border-right: none; border-bottom: none; text-align: right; font-weight: bold; font-size: 0.5rem">
                    <div>Mandays : {{ $meal_sheet_detail->mandays }}</div>
                    <div>Casual B Fast : {{ $meal_sheet_detail->casual_breakfast }}</div>
                    <div>Casual Lunch : {{ $meal_sheet_detail->casual_lunch }}</div>
                    <div>Casual Diner : {{ $meal_sheet_detail->casual_dinner }}</div>
                </td>
                <td style="border-left: none; border-bottom: none;" colspan="8"></td>
            </tr>
            <tr>
                <td colspan="2" style="border-top: none; border-right: none;">
                    <div style="min-height: 100px; text-align: center; margin-top: -15px;">
                        <p>Prepared By,</p>
                        <p style="margin-top: 60px;">{{ $meal_sheet_detail->prepared_by['name'] }}</p>
                        <p style="margin-top: -15px; margin-bottom: 0px;">{{ $meal_sheet_detail->prepared_by['position'] }}</p>
                    </div>
                </td>
                <td colspan="{{ !empty($meal_sheet_detail->acknowladge_by) ? 2 : 4 }}" 
                    style="
                        border-top: none; 
                        border-right: none; 
                        border-left: none; 
                        {{ !empty($meal_sheet_detail->acknowladge_by) ? 'padding-right: 50px;' : '' }}
                    "
                >
                    <div style="min-height: 100px; text-align: center; margin-top: -15px;">
                        <p>Checked By,</p>
                        <p style="margin-top: 60px;">{{ $meal_sheet_detail->checked_by['name'] }}</p>
                        <p style="margin-top: -15px; margin-bottom: 0px;">{{ $meal_sheet_detail->checked_by['position'] }}</p>
                    </div>
                </td>
                <td colspan="{{ !empty($meal_sheet_detail->acknowladge_by) ? 2 : 4 }}" 
                    style="
                        border-top: none; 
                        border-left: none; 
                        padding-left: 35px;
                        {{ !empty($meal_sheet_detail->acknowladge_by) ? 'border-right: none;' : '' }}
                    "
                >
                    <div style="margin-left: -50px; min-height: 100px; text-align: center; margin-top: -15px;">
                        <p>Approved By,</p>
                        <p style="margin-top: 60px;">{{ $meal_sheet_detail->approved_by['name'] }}</p>
                        <p style="margin-top: -15px; margin-bottom: 0px;">{{ $meal_sheet_detail->approved_by['position'] }}</p>
                    </div>
                </td>
                @if(!empty($meal_sheet_detail->acknowladge_by))
                    <td colspan="4" style="border-top: none; border-left: none;">
                        <div style="min-height: 100px; text-align: center; margin-top: -15px;">
                            <p>Acknowledge By,</p>
                            <p style="margin-top: 60px;">{{ $meal_sheet_detail->acknowladge_by['name'] }}</p>
                            <p style="margin-top: -15px; margin-bottom: 0px;">{{ $meal_sheet_detail->acknowladge_by['position'] }}</p>
                        </div>
                    </td>
                @endif
            </tr>
            <tr style="line-height: 10px; padding: 0;">
                <td colspan="10" style="text-align: center; background-color: #bcbcbc;">
                    <span style="font-size: 0.6rem; font-weight: bold;">PT SURYA BUANA TESTARUAYA</span> <br>
                    <span style="font-size: 0.6rem;">Kompl€k Gading Bukit lndah Blok J No,07 Jalan Bukit Gading Raya RT 018 RW 008 Kelapa Gading.lakarta Utara 142 rt 0</span>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>