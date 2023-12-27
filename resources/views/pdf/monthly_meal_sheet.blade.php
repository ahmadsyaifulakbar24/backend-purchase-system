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
            padding: 2px 2px;
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
            padding: 10px 5px;
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
            vertical-align: top;
            padding: 2px 2px;
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
            vertical-align: middle;
            max-width: 60px;
        }
        .tg .text-left {
            text-align: center;
            vertical-align: top;
            text-align: left;
        }
        .tg .text-center {
            text-align: center;
            vertical-align: top;
            text-align: center;
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
    <table class="tg" style="width: 700px;">
        <thead>
            <tr>
                <th class="tg-c3ow" colspan="12">
                    <h1>PT.Surya Buana Lestarijaya</h1>
                    <b>Catering and Accomodation Service</b>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tg-7btt" colspan="12" style="border-bottom: none">
                    <h2>Summary Meal Count Sheet</h2>
                </td>
            </tr>
            <tr>
                <td class="tg-sn4r" colspan="3" style="border-right: none;"> Location : {{ $meal_sheet_monthly->meal_sheet_group->location->location }}</td>
                <td class="tg-de2y" style="border-left: none; border-right: none;"></td>
                <td class="tg-dvpl" colspan="8" style="border-left: none;">MONTH: {{ $month }} {{ $meal_sheet_monthly->year }}</td>
            </tr>

            <!-- Header -->
            <tr>
                <td class="tg-baqh" rowspan="2" style="vertical-align: middle;">Date</td>
                <td class="tg-baqh" colspan="{{ $meal_sheet_monthly->meal_sheet_group->meal_sheet_client()->count() }}">Client Group</td>
                <td class="tg-0lax" colspan="2">Total Account</td>
                <td class="tg-0lax" colspan="4">Casual Meals</td>
                <td class="tg-0lax"></td>
                <td class="tg-0lax" colspan="2" rowspan="2" style="vertical-align: middle; text-align: center;">Remark</td>
            </tr>
            <tr>
                @foreach ( $meal_sheet_monthly->meal_sheet_group->meal_sheet_client as $client)
                    <td class="tg-0lax">{{ $client->client_name }}</td>
                @endforeach
                <td class="tg-0lax">Onboard Actual</td>
                <td class="tg-0lax">As Per Contract</td>
                <td class="tg-0lax">Breakfast</td>
                <td class="tg-0lax">Lunch</td>
                <td class="tg-0lax">Dinner</td>
                <td class="tg-0lax">Supper</td>
                <td class="tg-0lax">Total</td>
            </tr>

            <!-- Content -->
            @php
                $total_onboard_actual = 0;
                $total_as_per_contract = 0;
                $total_casual_breakfast = 0;
                $total_casual_lunch = 0;
                $total_casual_dinner = 0;
                $total_super = 0;
                $total_total = 0;
                $total_client_group = [];
            @endphp
            @foreach ($meal_sheet_monthly->recap_per_day as $recap)
                @php
                    $client_group = Arr::keyBy($recap['client_group'], 'id');
                    $total_onboard_actual += $recap['onboard_actual'];
                    $total_as_per_contract += $recap['as_per_contract'];
                    $total_casual_breakfast += $recap['casual_breakfast'];
                    $total_casual_lunch += $recap['casual_lunch'];
                    $total_casual_dinner += $recap['casual_dinner'];
                    $total_super += $recap['super'];
                    $total_total += $recap['total'];

                    foreach ($recap['client_group'] as $client_data) {
                        $id = $client_data['id'];
                        $mandays = $client_data['mandays'];

                        if (isset($total_client_group[$id])) {
                            $total_client_group[$id] += $mandays;
                        } else {
                            $total_client_group[$id] = $mandays;
                        }
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ Carbon\Carbon::parse($recap['meal_sheet_date'])->format('j') }}</td>
                    @foreach ( $meal_sheet_monthly->meal_sheet_group->meal_sheet_client as $client)
                        <td class="text-center">{{ $client_group[$client->id]['mandays'] }}</td>
                    @endforeach
                    <td class="text-center">{{ $recap['onboard_actual'] }}</td>
                    <td class="text-center">{{ $recap['as_per_contract'] }}</td>
                    <td class="text-center">{{ $recap['casual_breakfast'] }}</td>
                    <td class="text-center">{{ $recap['casual_lunch'] }}</td>
                    <td class="text-center">{{ $recap['casual_dinner'] }}</td>
                    <td class="text-center">{{ $recap['super'] }}</td>
                    <td class="text-center">{{ $recap['total'] }}</td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                </tr>
            @endforeach
            <tr>
                <td class="text-center">Total</td>
                @foreach ( $meal_sheet_monthly->meal_sheet_group->meal_sheet_client as $client)
                    <td class="text-center">{{ $total_client_group[$client->id] }}</td>
                @endforeach
                <td class="text-center">{{ $total_onboard_actual }}</td>
                <td class="text-center">{{ $total_as_per_contract }}</td>
                <td class="text-center">{{ $total_casual_breakfast }}</td>
                <td class="text-center">{{ $total_casual_lunch }}</td>
                <td class="text-center">{{ $total_casual_dinner }}</td>
                <td class="text-center">{{ $total_super }}</td>
                <td class="text-center">{{ $total_total }}</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
            </tr>

            <!-- Divider -->
            <tr style="line-height: 15px; padding: 0;">
                <td colspan="12"></td>
            </tr>
            
            <!-- Footer -->
            <tr>
                <td colspan="3" style="border-top: none; border-right: none;">
                    <div style="min-height: 100px; text-align: center; margin-top: -10px;">
                        <p>Prepared By,</p>
                        <p style="margin-top: 60px;">{{ $meal_sheet_monthly->prepared_by['name'] }}</p>
                        <p style="margin-top: -10px; margin-bottom: 0px;">{{ $meal_sheet_monthly->prepared_by['position'] }}</p>
                    </div>
                </td>
                <td colspan="3" style="border-top: none; border-right: none; border-left: none;">
                    <div style="min-height: 100px; text-align: center; margin-top: -10px;">
                        <p>Checked By,</p>
                        <p style="margin-top: 60px;">{{ $meal_sheet_monthly->checked_by['name'] }}</p>
                        <p style="margin-top: -10px; margin-bottom: 0px;">{{ $meal_sheet_monthly->checked_by['position'] }}</p>
                    </div>
                </td>
                <td colspan="3" style="border-top: none; border-right: none; border-left: none;">
                    <div style="margin-left: -50px; min-height: 100px; text-align: center; margin-top: -10px;">
                        <p>Approved By,</p>
                        <p style="margin-top: 60px;">{{ $meal_sheet_monthly->approved_by['name'] }}</p>
                        <p style="margin-top: -10px; margin-bottom: 0px;">{{ $meal_sheet_monthly->approved_by['position'] }}</p>
                    </div>
                </td>
                <td colspan="3" style="border-top: none; border-left: none;">
                    <div style="min-height: 100px; text-align: center; margin-top: -10px;">
                        <p>Acknowledge By,</p>
                        <p style="margin-top: 60px;">{{ $meal_sheet_monthly->acknowladge_by['name'] }}</p>
                        <p style="margin-top: -10px; margin-bottom: 0px;">{{ $meal_sheet_monthly->acknowladge_by['position'] }}</p>
                    </div>
                </td>
            </tr>

            <tr style="line-height: 10px; padding: 0;">
                <td colspan="12" style="text-align: center; background-color: #bcbcbc;">
                    <span style="font-size: 0.6rem; font-weight: bold;">PT SURYA BUANA TESTARUAYA</span> <br>
                    <span style="font-size: 0.6rem;">Kompl€k Gading Bukit lndah Blok J No,07 Jalan Bukit Gading Raya RT 018 RW 008 Kelapa Gading.lakarta Utara 142 rt 0</span>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>