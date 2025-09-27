<html>
<head>
    <title>{{ __('custom.invoice') }}</title>
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
            font-size: 11px;
        }

        .theme-tr-head {
            background-color: #DEDEDE !important;
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
            padding: 6.4px !important;
        }

        table.table-bordered {
            border-collapse: collapse;
        }

        table.table-bordered, .table-bordered th, .table-bordered td {
            border: 1px solid black;
        }

        table > thead > tr > th {
            font-size: 11px;
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
            bottom: 40px;
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

        .table-border {
            /*border-top: 1px solid #333 !important;
            border-bottom: 1px solid #333 !important;*/
            border: none !important;
        }

        .table th, .table td {
            padding-top: 1px !important;
            padding-bottom: 1px !important;
        }

        .table, .table th, .table td, .table tr {
            /*border: 1px solid black !important;*/
            border: none !important;
        }
    </style>
</head>
<body onload="window.print();window.close()">
<div class="footer">
    {{--Footer Page <span class="pagenum"></span>--}}
    <span class="white-space-pre-line font-weight-bold">{!! nl2br($entity->docRefNo) !!}</span>
</div>
<div id="watermark"></div>
<div class="card-body content" id="print-section" >
    <table style="width: 100%">
        <tr style="width: 100%">
            <td colspan="3" class="text-center">
                @if($entity->warehouse_by)
                    <img src="{{$entity->warehouse_by->templateImgUrl}}" width="180px" height="60px">
                @endif
            </td>
        </tr>
        <tr style="width: 100%" class="text-center">
            <td colspan="3">
                @if($entity->warehouse_by)
                    <h6> {{$entity->warehouse_by->wareHouseDescription}} </h6>
                @endif
            </td>
        </tr>
    </table>

    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 30%">
                <table>
                    <tr>
                        <td width="50px">
                            <span class="font-weight-bold">Customer</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            Cash
                            {{--@if($entity->warehouse_by)
                                {{$entity->warehouse_by->wareHouseDescription}}
                            @endif--}}
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%;text-align: center">
            </td>
            <td style="width: 30%">
                <table>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Date </span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>
                                {{ date("Y-m-d h.sa", strtotime($entity->createdDateTime))}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Invoice No</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>{{$entity->invoiceCode}}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    {{--<hr>--}}
    <div style="margin-top: 5px">
        <table class="table table-bordered" style="width: 100%;">
            <thead>
            <tr>
                <th></th>
                <th class="text-left">Description</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Price</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($entity->details as $item)
                <tr class="table-border">
                    <td>{{$loop->iteration}}</td>
                    <td>{{$item->itemDescription}}</td>
                    <td class="text-right">{{$item->qty}}</td>
                    <td class="text-right">{{number_format($item->totalAmount,$entity->decimalPlaces)}}</td>
                    <td class="text-right">{{number_format($item->discountAmount,$entity->decimalPlaces)}}</td>
                    <td class="text-right">{{number_format($item->netAmount,$entity->decimalPlaces)}}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="6"><hr></td>
            </tr>
            <tr class="table-border">
                <td colspan="2" rowspan="7" valign="top">
                    Total Items - {{count($entity->details)}} <br>
                    Created by :
                     @if($entity->created_by) {{$entity->created_by->empName}} @endif
                </td>
                <td colspan="4" style="padding-top: -1px !important;padding-bottom: -1px !important;"></td>
            </tr>
            <tr class="table-border">
                <td colspan="2" class="text-left"><strong>Subtotal</strong></td>
                <td colspan="2" class="text-right">{{number_format($entity->subTotal,$entity->decimalPlaces)}}</td>
            </tr>
            <tr class="table-border">
                <td colspan="2" class="text-left"><strong>Discount ({{number_format($entity->discountPercentage,2)}}%)</strong></td>
                <td colspan="2"
                    class="text-right">{{number_format($entity->discountAmount,$entity->decimalPlaces)}}</td>
            </tr>
            <tr class="table-border">
                <td colspan="2" class="text-left"><strong>Grand Total</strong></td>
                <td colspan="2" class="text-right">{{number_format($entity->netTotal,$entity->decimalPlaces)}}</td>
            </tr>
            <tr class="table-border">
                <td colspan="2" class="text-left"><strong>Cash</strong></td>
                <td colspan="2" class="text-right">{{number_format($entity->cashAmount,$entity->decimalPlaces)}}</td>
            </tr>
            <tr class="table-border">
                <td colspan="2" class="text-left"><strong>Card</strong></td>
                <td colspan="2" class="text-right">{{number_format($entity->cardAmount,$entity->decimalPlaces)}}</td>
            </tr>
            <tr class="table-border">
                <td colspan="2" class="text-left"><strong>Change</strong></td>
                <td colspan="2" class="text-right">{{number_format($entity->balanceAmount,$entity->decimalPlaces)}}</td>
            </tr>
            <tr class="table-border">
                <td colspan="6" class="text-left"><br><br><br></td>
            </tr>
            <tr class="table-border">
                <td colspan="6" class="text-center">
                    @if($entity->warehouse_by)
                        <b>{{$entity->warehouse_by->posFooterNote}}</b>
                    @endif
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="row text-center" style="margin-top: 10px">
        <span class="font-weight-bold" style="font-size: 14px">Thank You!</span>
    </div>
</div>
</body>
</html>
