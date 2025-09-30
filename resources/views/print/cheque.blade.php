<html>
<head>
    <title>{{ __('custom.cheque') }}</title>
    <style>

        body {

            /*font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";*/
        }

        .table thead th {
            border-bottom: none !important;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #c2cfd6;
        }

        table.header-part,
        .header-part th,
        .header-part td {

        }


        #bpv_code_div {
            position: absolute;
            top: 0.4cm;
            left: 20.5cm;
        }

        #top_date_div {
            position: absolute;
            top: 1.9cm;
            left: 20.5cm;
        }

        #reference_table_div {
            position: absolute;
            top: 7.5cm;
            left: 1.85cm;

        }

        #total_amount_div {
            position: absolute;
            top: 18.5cm;
            left: 1.85cm
        }

        #cheque_cheque_date {
            position: absolute;
            top: 26.3cm;
            left: 21.5cm;
        }

        #cheque_amount_no {
            position: absolute;
            top: 29cm;
            left: 19.5cm;
        }

        #cheque_payee {
            position: absolute;
            top: 27.7cm;
            left: 5cm;
        }

        #word_amount_table {
            width: 12.1cm;
            position: absolute;
            left: 2cm;
            top: 28.4cm;
        }

        #cheque_amount_word_para {
            line-height: 1cm;

        }

        .paper_size {
            width: 24.1cm
        }

        .text-right {
            text-align: right;
        }

      </style>
</head>
<body onload="window.print();window.close()" >
<div class="content">
    <div style="font-size: 16px !important;" class="header-part" id="bpv_code_div">
            {{$entity->BPVcode}}
    </div>
    <div style="font-size: 16px !important;" class="header-part" id="top_date_div">
            {{ \App\helper\Helper::dateFormat($date)}}
    </div>

    <div id="reference_table_div">
        <table class="header-part paper_size" >
{{--            <tr >--}}
{{--                <td style="width: 1.5cm"></td>--}}
{{--                <td valign="top" style="width: 10cm">{{$entity->BPVNarration}}</td>--}}
{{--                <td valign="top" style="width: 7cm"></td>--}}
{{--                <td valign="top" class="text-right" style="width: 2.4cm" >--}}
{{--                    <b >{{number_format($entity->payAmountBank,$entity->decimalPlaces)}}</b>--}}
{{--                </td>--}}
{{--                <td style="width: 1.5cm"></td>--}}
{{--            </tr>--}}
            @if($entity->details)
                @if($entity->invoiceType == 2)
                    @foreach ($entity->details as $item)
                        <tr >
                            <td style="width: 1.5cm">{{ \App\helper\Helper::dateFormat($item->bookingInvoiceDate)}}</td>
                            <td valign="top" style="width: 10cm">{{$item->bookingInvDocCode}}</td>
                            <td valign="top" style="width: 7cm">{{$item->supplierInvoiceNo}}</td>
                            <td valign="top" class="text-right" style="width: 2.4cm" >
                                {{number_format($item->supplierInvoiceAmount,$entity->decimalPlaces)}}
                            </td>
                            <td style="width: 1.5cm"></td>
                        </tr>
                    @endforeach
                @endif

                @if($entity->invoiceType == 5)
                    @foreach ($entity->details as $item)
                        <tr >
                            <td style="width: 1.5cm"></td>
                            <td valign="top" style="width: 10cm">{{$item->purchaseOrderCode}}</td>
                            <td valign="top" style="width: 7cm"></td>
                            <td valign="top" class="text-right" style="width: 2.4cm" >
                                {{number_format($item->supplierTransAmount,$entity->decimalPlaces)}}
                            </td>
                            <td style="width: 1.5cm"></td>
                        </tr>
                    @endforeach
                @endif

                @if($entity->invoiceType == 3)
                    @foreach ($entity->details as $item)
                        <tr >
                            <td style="width: 1.5cm"></td>
                            <td valign="top" style="width: 10cm">{{$item->glCode}}</td>
                            <td valign="top" style="width: 7cm"></td>
                            <td valign="top" class="text-right" style="width: 2.4cm" >
                                {{number_format($item->DPAmount,$entity->decimalPlaces)}}
                            </td>
                            <td style="width: 1.5cm"></td>
                        </tr>
                    @endforeach
                @endif
            @endif
        </table>
    </div>

    <div id="total_amount_div">
        <table class="header-part paper_size" >
            <tr >
                <td style="width: 18.5cm"></td>
                <td  valign="top" class="text-right" style="width: 2.4cm; font-size: 16px !important;" >
                    {{number_format($entity->payAmountBank,$entity->decimalPlaces)}}
                </td>
                <td style="width: 1.5cm"></td>
            </tr>
        </table>
    </div>
</div>

<div class="footer" >
    <div style="font-size: 16px !important;" id="cheque_cheque_date" > {{\App\helper\Helper::dateFormat($entity->BPVchequeDate)}} </div>
    <div style="font-size: 16px !important;" id="cheque_payee" >{{$entity->nameOnCheque}}</div>

    <table id="word_amount_table" class="header-part" >
        <tr >
            <td valign="top" >
                <p style="font-size: 16px !important;" id="cheque_amount_word_para">  {{$entity->amount_word}} & {{$entity->floatAmt}}/@if($entity->decimalPlaces == 3)1000 @else 100 @endif only</p>
            </td>
        </tr>
        <div style="font-size: 16px !important;" id="cheque_amount_no" >{{number_format($entity->payAmountBank,$entity->decimalPlaces)}}</div>
    </table>
</div>
</body>
</html>
