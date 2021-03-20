<html>
<head>
    <title>
       Batch Submission
    </title>
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

        h6, h3 {
            margin-top: 0px;
            margin-bottom: 0px;
            font-family: inherit;
            font-weight: bold;
            line-height: 1.2;
            color: inherit;
        }

        table > tbody > tr > td {
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

        .table th, .table td {
            padding: 6.4px !important;
        }

        table.table-bordered {
            border-collapse: collapse;
        }

        table.table-bordered, .table-bordered th, .table-bordered td {
            border: 1px solid #e2e3e5;
        }

        table > thead > tr > th {
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
            margin-bottom: 45px;
        }

        .border-top-remov {
            border-top: 1px solid #ffffff00 !important;
            border-left: 1px solid #ffffff00 !important;
            background-color: #ffffff !important;
            /*border-right: 0;*/
        }

        .border-bottom-remov {
            border-bottom: 1px solid #ffffffff !important;
            background-color: #ffffff !important;
            border-right: 1px solid #ffffffff !important;
        }

    </style>
</head>
<body>
<div class="footer">
{{--    <table style="width:100%;">--}}
{{--        <tr>--}}
{{--            <td width="40%"><span--}}
{{--                        class="font-weight-bold">Confirmed By :</span> {{ $masterdata->confirmed_by? $masterdata->confirmed_by->empFullName:'' }}--}}
{{--            </td>--}}
{{--            <td><span class="font-weight-bold">Review By :</span></td>--}}
{{--        </tr>--}}
{{--    </table>--}}
{{--    <table style="width:100%;">--}}
{{--        <tr>--}}
{{--            <td><span class="font-weight-bold">Electronically Approved By :</span></td>--}}
{{--        </tr>--}}
{{--        <tr>--}}
{{--            &nbsp;--}}
{{--        </tr>--}}
{{--    </table>--}}
{{--    <table style="width:100%;">--}}
{{--        <tr>--}}
{{--            @if ($masterdata->approved_by)--}}
{{--                @foreach ($masterdata->approved_by as $det)--}}
{{--                    <td style="padding-right: 25px;font-size: 9px;">--}}
{{--                        <div>--}}
{{--                            @if($det->employee)--}}
{{--                                {{$det->employee->empFullName }}--}}
{{--                            @endif--}}
{{--                        </div>--}}
{{--                        <div><span>--}}
{{--                @if(!empty($det->approvedDate))--}}
{{--                                    {{ \App\helper\Helper::dateFormat($det->approvedDate)}}--}}
{{--                                @endif--}}
{{--              </span></div>--}}
{{--                        <div style="width: 3px"></div>--}}
{{--                    </td>--}}
{{--                @endforeach--}}
{{--            @endif--}}
{{--        </tr>--}}
{{--    </table>--}}
    <table style="width:100%;">
        <tr>
            <td colspan="3" style="width:100%">
                <hr style="background-color: black">
            </td>
        </tr>
        <tr>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
{{--                <span class="white-space-pre-line font-weight-bold">{!! nl2br($docRef) !!}</span>--}}
            </td>
            <td style="width:33%; text-align: center;font-size: 10px;vertical-align: top;">
                <span style="text-align: center">Page <span class="pagenum"></span></span><br>
{{--                @if ($masterdata->company)--}}
{{--                    {{$masterdata->company->CompanyName}}--}}
{{--                @endif--}}
            </td>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                <span style="margin-left: 50%;">Printed Date : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
        </tr>
    </table>
</div>
<div id="watermark"></div>
<div class="card-body content" id="print-section">
    <table style="width: 100%">
        <tr style="width: 100%">
            <td valign="top" style="width: 20%">
                @if($masterdata->company)
                    <img src="{{$masterdata->company->logo_url}}" width="180px" height="60px">
                @endif
                <br>

                <div>
                    <span style="font-size: 18px">
                    </span>
                </div>
            </td>
            <td valign="top" style="width: 80%">
                @if($masterdata->company)
                    <span style="font-size: 20px;font-weight: 300"> {{$masterdata->company->CompanyName}}</span>
                @endif
                <br>
                <table>


                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="text-center" style="font-size: 16px">
                <span class="font-weight-bold">Batch Submission</span>
            </td>

        </tr>

    </table>
    <hr style="color: #d3d9df">
    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 30%">
                <table>
                    <tr>
                        <td width="150px">
                            <span class="font-weight-bold">Agreement No</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            {{$masterdata->contractNumber}}
                        </td>
                    </tr>

                </table>
            </td>
            <td style="width: 30%"></td>
            <td style="width: 40%">
                <table>
                    <tr>
                        <td width="150px">
                            <span class="font-weight-bold">Batch No</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            {{$masterdata->customerInvoiceTrackingCode}}
                        </td>
                    </tr>

                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Submitted Date </span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>{{\App\helper\Helper::dateFormat($masterdata->submittedDate)}}</span>
                        </td>
                    </tr>
                </table>
            </td>

        </tr>
    </table>

        <div style="margin-top: 30px; padding: 2px" >
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                <tr class="theme-tr-head">
                    <th>SI#</th>
                    <th class="text-center">PO Number</th>
                    <th class="text-center">SE</th>
                    <th class="text-center">Rig</th>
                    <th class="text-center">Well</th>
                    <th class="text-center">Number</th>
                    <th class="text-center">Invoice Date</th>
                    <th class="text-center">Start Date</th>
                    <th class="text-center">End Date</th>
                    <th class="text-center">Month of Service</th>
                    <th class="text-center">Amount ({{$currencyCode}})</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $total=0
                @endphp
                @foreach ($masterdata->detail as $item)
                    @php
                        $total+= $item->amount
                    @endphp
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->PONumber}}</td>
                        <td>{{$item->wanNO}}</td>
                        <td>{{$item->rigNo}}</td>
                        <td>{{ $item->wellNo}}</td>
                        <td>{{ $item->bookingInvCode}}</td>
                        <td>{{ isset($item->bookingDate)?\App\helper\Helper::dateFormat($item->bookingDate):''}}</td>
                        <td>{{ isset($item->customer_invoice_direct->serviceStartDate)?\App\helper\Helper::dateFormat($item->customer_invoice_direct->serviceStartDate):''}}</td>
                        <td>{{ isset($item->customer_invoice_direct->serviceEndDate)?\App\helper\Helper::dateFormat($item->customer_invoice_direct->serviceEndDate):''}}</td>
                        <td>{{ $item->servicePeriod}}</td>
                        <td class="text-right">{{number_format($item->amount, 2)}}</td>
                    </tr>
                @endforeach
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    <td colspan="9" class="text-right border-bottom-remov">&nbsp;</td>
                    <td class="text-right" style="background-color: rgb(215,215,215)">Total Payment</td>
                    <td class="text-right"
                        style="background-color: rgb(215,215,215)">{{number_format($total, 2)}}</td>
                </tr>
                </tbody>
            </table>
        </div>

</div>
