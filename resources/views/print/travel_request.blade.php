<html>

<head>
    <title>{{ __('custom.travel_request') }}</title>
    <style>
        @page {
            margin-left: 30px;
            margin-right: 30px;
            margin-top: 30px;
        }

        .footer {
            position: absolute;
        }

        .footer {
            bottom: 0;
            height: 100px;
        }

        .footer {
            width: 100%;
            text-align: center;
            position: fixed;
            font-size: 10px;
            padding-top: -20px;
        }

        body {
            font-size: 12px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }

        h3 {
            font-size: 24.5px;
        }

        h6 {
            font-size: 14px;
        }

        h6,
        h3 {
            margin-top: 0px;
            margin-bottom: 0px;
            font-family: inherit;
            font-weight: bold;
            line-height: 1.2;
            color: inherit;
        }

        table>tbody>tr>td {
            font-size: 11.5px;
        }

        .theme-tr-head {
            background-color: rgb(215, 215, 215) !important;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        .table thead th {
            border-bottom: none !important;
        }

        .white-space-pre-line {
            white-space: pre-line;
            white-space: pre;
            word-wrap: normal;
        }

        .text-muted {
            color: #dedede !important;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #c2cfd6;
        }

        table.table-bordered {
            border: 1px solid #000;
        }

        .table th,
        .table td {
            padding: 6.4px !important;
        }

        table.table-bordered {
            border-collapse: collapse;
        }

        table.table-bordered,
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #e2e3e5;
        }

        table>thead>tr>th {
            font-size: 11.5px;
        }

        hr {
            margin-top: 16px;
            margin-bottom: 16px;
            border: 0;
            border-top: 1px solid
        }

        hr {
            -webkit-box-sizing: content-box;
            box-sizing: content-box;
            height: 0;
            overflow: visible;
        }

        .header {
            top: 0px;
        }

        .pagenum:before {
            content: counter(page);
        }

        #watermark {
            position: fixed;
            bottom: 0px;
            right: 0px;
            width: 200px;
            height: 200px;
            opacity: .1;
        }

        .content {
            margin-bottom: 100px;
        }

        .border-top-remov {
            border-top: 1px solid #ffffff00 !important;
            border-left: 1px solid #ffffff00 !important;
            background-color: #ffffff !important;
            border-right: 0;
        }

        .border-bottom-remov {
            border-bottom: 1px solid #ffffffff !important;
            background-color: #ffffff !important;
            border-right: 1px solid #ffffffff !important;
        }

        .container {
            display: block;
            max-width: 230px;
            max-height: 95px;
            width: auto;
            height: auto;
        }

        .table_height {
            max-height: 60px !important;
        }
    </style>
</head>

<body>
    <div class="footer">
        <table style="width:100%;">
            <tr>
                <td colspan="3" style="width:100%">
                    <hr style="background-color: black">
                </td>
            </tr>
            <tr>
                <td style="width:20%;font-size: 10px;vertical-align: top;">
                    <b style="text-align: center">Page <span class="pagenum"></span></b><br>
                </td>
                <td style="width:60%; text-align: center;font-size: 10px;vertical-align: top;">
                    <b>This is a computer generated document and does not require signature.</b>
                </td>
                <td style="width:20%;font-size: 10px;vertical-align: top; text-align: right">
                    <b style="">{{date("d F Y", strtotime(now()))}}</b>
                </td>
            </tr>
        </table>
    </div>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td style="width:40%;">
                        <table>
                            <tr>
                                <td>
                                    <img alt="Logo" class="company_logo" src="{{$company['logoPath']}}" width="180px" height="60px">
                                </td>
                            </tr>

                        </table>
                    </td>
                    <td style="width:60%;">
                        <table>
                            <tr>
                                <td colspan="3">
                                    <h3>
                                        <strong>{{$company['CompanyName']}}</strong>
                                    </h3>
                                    <h4>Business Trip Request</h4>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Requested By Emp Name</strong></td>
                                <td><strong>:</strong></td>
                                <td>{{$masterData['EName2']}}</td>
                            </tr>
                            <tr>
                                <td><strong>Requested Emp Code</strong></td>
                                <td><strong>:</strong></td>
                                <td>{{$masterData['ECode']}}</td>
                            </tr>
                            <tr>
                                <td><strong>Requested Emp Grade</strong></td>
                                <td><strong>:</strong></td>
                                <td>{{$masterData['gradeDescription']}}</td>
                            </tr>
                            <tr>
                                <td><strong>Travel Request Number</strong></td>
                                <td><strong>:</strong></td>
                                <td>{{$masterData['document_code']}}</td>
                            </tr>
                            <tr>
                                <td><strong>Travel Request Date</strong></td>
                                <td><strong>:</strong></td>
                                <td>
                                    {{date("d-m-Y", strtotime($masterData['date_travel']))}}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Comments</strong></td>
                                <td><strong>:</strong></td>
                                <td>{{$masterData['comments']}}</td>
                            </tr>
                            <tr>
                                <td><strong>Project Name</strong></td>
                                <td><strong>:</strong></td>
                                <td>{{$masterData['project_name']}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px">
        <table class="table table-bordered" style="width: 100%;">
            <thead>
                <tr class="theme-tr-head">
                    <th class="text-center">#</th>
                    <th class="text-center">Mode of trasport</th>
                    @if($masterData['travel_by'])
                    <th class="text-center">Travel By</th>
                    <th class="text-center">Distance (KM)</th>
                    @endif
                    <th class="text-center">Travel type</th>
                    @if($masterData['ticket_type'])
                    <th class="text-center">Ticket type</th>
                    @endif
                    <th class="text-center">Destination</th>
                    <th class="text-center">Duration (Days)</th>
                    <th class="text-center">Date of Travel</th>
                    <th class="text-center">Return Date</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                    <td>1.</td>
                    <td>
                        <span>{{$masterData['mode_of_transport_desc']}}</span>
                    </td>
                    @if($masterData['travel_by'])
                    <td>
                        <span>{{$masterData['travel_by_description']}}</span>
                    </td>
                    <td>
                        <span>{{$masterData['distance']}}</span>
                    </td>
                    @endif
                    <td>
                        <span>{{$masterData['travel_type_desc']}}</span>
                    </td>
                    @if($masterData['ticket_type'])
                    <td>
                        {{$masterData['ticket_type_description']}}
                    </td>
                    @endif
                    <td>
                        <span>{{$masterData['destination']}}</span>
                        @if($masterData['travel_type'] == 1)
                            <br>
                            <span style="font-weight:bold;">City : </span>
                            <span>{{$masterData['city']}}</span>
                        @endif
                    </td>

                    <td>
                        {{$masterData['duration']}}
                    </td>

                    <td>
                        {{date("d-m-Y", strtotime($masterData['date_travel']))}}
                    </td>

                    <td>
                        {{date("d-m-Y", strtotime($masterData['date_return']))}}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px">
        <table class="table table-bordered" style="width: 70%;">
            <thead>
                <tr class="theme-tr-head">
                    <th class="text-center">#</th>
                    <th class="text-center">Booking Type</th>
                    <th class="text-center">Narration</th>
                </tr>
            </thead>
            <tbody>
                @php
                $x = 1;
                @endphp
                @foreach ($tripRequestBookings as $item)

                <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                    <td>{{$x}}</td>
                    <td>{{$item['description']}}</td>
                    <td>{{$item['narration']}}</td>
                </tr>
                @php
                $x++;
                @endphp
                @endforeach

                @if(sizeof($tripRequestBookings) == 0)
                <tr>
                    <td class="text-center" colspan="3">No Records Found</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>