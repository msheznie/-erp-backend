<html>
<head>
    <title>Cheque</title>
    <style>
        @page {
            margin-left: 30px;
            margin-right: 30px;
            margin-top: 30px;
            margin-bottom: 0px;
        }

        body {
            font-size: 12px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }

        h3 {
            font-size: 24.5px;
        }

        h6 {
            font-size: 18px;
        }

        h6, h3 {
            margin-top: 0px;
            margin-bottom: 0px;
            font-weight: bold;
            line-height: 1.2;
            color: inherit;
        }

        table > tbody > tr > td {
            font-size: 12px;
        }

        .theme-tr-head {
            background-color: #ffffff !important;
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

        tr td {
            padding: 5px 0;
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
            padding: 3px !important;
        }

        table.table-bordered {
            border-collapse: collapse;
        }

        table.table-bordered, .table-bordered th, .table-bordered td {
            border: 1px solid #e2e3e5;
        }

        table.table-bordered, .table-bordered th, .table-bordered td.details {
            border: 1px solid #e2e3e5;
            font-size: 10.5px !important;
            padding: 0 !important;
        }

        table.header-part, .header-part th, .header-part td {
            font-size: 12px !important;
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

        .header,
        .footer {
            width: 100%;
            text-align: left;
            position: fixed;
        }

        .header {
            top: 0px;
        }

        .footer {
            bottom: 280px;
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
            padding-top: 20px;
            padding-left: 0px;
            padding-right: 5px;
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

        .page-break {
            page-break-after: always;
        }

        td.border-less {
            border: 1px solid #ffffff;
        }
    </style>
</head>
{{--<body onload="window.print()">--}}
<body>

@foreach ($entities as $entity)
    <div class="footer">
        <table class="header-part" style="width: 100%">
            <tr style="width: 100%">
                <td valign="top" style="width: 50%"></td>
                <td valign="top" style="width: 50%;padding-left: 160px" class="text-center">
                    <b> {{ \App\helper\Helper::dateFormat($date)}} </b><br><br>
                </td>
            </tr>
            <tr style="width: 100%">
                <td valign="top" style="width: 50%;padding-right: 10px" class="text-right"><b>{{$entity->directPaymentPayee}}</b></td>
                <td valign="top" style="width: 50%" class="text-center"></td>
            </tr>
            <tr style="width: 100%">
                <td valign="top" style="width: 50%" class="text-right">
                    <b>  {{$entity->amount_word}} and {{$entity->floatAmt}}/@if($entity->decimalPlaces == 3)
                            1000 @else 100 @endif </b>
                </td>
                <td valign="top" style="width: 50%;padding-left: 70px" class="text-center"></td>
            </tr>
            <tr style="width: 100%">
                <td valign="top" style="width: 50%" class="text-center"></td>
                <td valign="top" style="width: 50%;padding-left: 80px" class="text-center">
                    <b>{{number_format($entity->payAmountBank,$entity->decimalPlaces)}}</b>
                </td>
            </tr>
        </table>
    </div>
    <div class="card-body content {{ $loop->last ? '' : 'page-break' }}">
        <div style="margin-top: 10px">
            <table class="header-part" style="width: 100%">
                <tr style="width: 100%">
                    <td valign="top" style="width: 80%"></td>
                    <td valign="top" style="width: 20%">
                        <b>
                            {{$entity->BPVcode}}
                        </b>
                    </td>
                </tr>
                <tr>
                    <td valign="top" style="width: 80%"></td>
                    <td valign="top" style="width: 20%">
                        <b> {{ \App\helper\Helper::dateFormat($date)}} </b>
                    </td>
                </tr>
            </table>
        </div>
        <div style="margin-top: 155px">
            @if($entity->details && $entity->invoiceType == 2)
                <table class="table table-bordered details" style="width: 100%;">
                    <tbody>
                    @foreach ($entity->details as $item)
                        <tr style="width: 100%;padding: 0 !important;" class="border-less">
                            {{--  <td class="border-less">{{$loop->iteration}}</td>--}}
                            <td class="border-less">{{\App\helper\Helper::dateFormat($item['bookingInvoiceDate'])}}</td>
                            <td class="border-less">{{$item['bookingInvDocCode']}}</td>
                            <td class="border-less">{{$item['supplierInvoiceNo']}}</td>
                            <td class="border-less text-right">{{number_format($item['supplierInvoiceAmount'],$entity->decimalPlaces)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

            @if($entity->details && $entity->invoiceType == 5)
                <table class="table table-bordered details" style="width: 100%;">
                    <tbody>
                    @foreach ($entity->details as $item)
                        <tr style="width: 100%;" class="border-less">
                            {{--  <td class="border-less">{{$loop->iteration}}</td>--}}
                            <td class="border-less"> -</td>
                            <td class="border-less">{{$item['purchaseOrderCode']}}</td>
                            <td class="border-less"> -</td>
                            <td class="border-less text-right">{{number_format($item['supplierTransAmount'],$entity->decimalPlaces)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
            @if($entity->details && $entity->invoiceType == 3)
                <table class="table table-bordered details" style="width: 100%;">
                    <tbody>
                    @foreach ($entity->details as $item)
                        <tr style="width: 100%;" class="border-less">
                            {{--  <td class="border-less">{{$loop->iteration}}</td>--}}
                            <td class="border-less"> -</td>
                            <td class="border-less">{{$item['glCode']}}</td>
                            <td class="border-less"> -</td>
                            <td class="border-less text-right">{{number_format($item['DPAmount'],$entity->decimalPlaces)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div style="margin-top: 100px">
            <table class="header-part" style="width: 100%">
                <tr style="width: 100%">
                    <td valign="top" style="width: 70%"><b>Settlement of Supplier Invoices</b></td>
                    <td valign="top" style="width: 30%" class="text-right">
                        <b>{{number_format($entity->payAmountBank,$entity->decimalPlaces)}}</b>
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endforeach
</body>
</html>