<html>
<head>
    
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
            top: 5.5cm;
            left: 22.5cm;
        }

        #top_supplier_div {
            position: absolute;
            top: 6cm;
            left: 4.5cm;
        }
        
        #top_date_div {
            position: absolute;
            top: 6.7cm;
            left: 20.5cm;
        }

        #reference_table_div {
            position: absolute;
            top: 10cm;
            left: 0.35cm;

        }

        #total_amount_div {
            position: absolute;
            top: 18.5cm;
            left: 1.85cm
        }

        #cheque_cheque_date {
            position: absolute;
            top: 19.8cm;
            left: 16.3cm;
        }

        #cheque_no_bottom {
            position: absolute;
            top: 22.3cm;
            left: 5cm;
        }

        #cheque_amount_no {
            position: absolute;
            top: 22.5cm;
            left: 15.3cm;
        }

        #cheque_payee {
            position: absolute;
            top: 20.6cm;
            left: 2.7cm;
        }

        #word_amount_table {
            width: 12.1cm;
            position: absolute;
            left: 2cm;
            top: 15cm;
        }

        #cheque_amount_word_para {
            line-height: 1cm;
            position: absolute;
            top: 21cm;
            left: 1.6cm;

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
    @if ($entity != null)
    <br><br>
    <br><br>
    <br><br>
    <br><br>
    <br><br>

    <div class="card-body content">
        <table style="width: 100%">
            <tr>
                <td style="width: 15%"><b>Paid To</b></td>
                <td style="width: 60%"><b>@if($entity->nameOnCheque){{$entity->nameOnCheque}}@endif</b></td>
                <td style="width: 10%"><b>Date </b></td>
                <td style="width: 15%"><b>{{\App\helper\Helper::dateFormat($entity->BPVchequeDate)}}</b></td>
            </tr>
            <br><br>
            <tr>
                <td style="width: 15%"><b>Bank Name</b></td>
                <td style="width: 60%"><b>@if($entity->bankaccount){{$entity->bankaccount->bankName}}@endif</b></td>
                <td style="width: 10%"><b>Currency </b></td>
                <td style="width: 15%"><b>@if($entity->bankcurrency){{$entity->bankcurrency->CurrencyName}}@endif</b></td>
            </tr>
            <tr>
                <td style="width: 15%"><b>BPV Code</b></td>
                <td style="width: 60%"><b>@if($entity->BPVcode){{$entity->BPVcode}}@endif</b></td>
            </tr>
        </table>
    </div>

    <div class="container">
        <table style="width: 100%">
            <tr>
                <td style="width: 50"><b>Account Name</b></td>
                <td style="width: 20%"><b>Remarks</b></td>
                <td style="width: 15%"><b>Ref Amount</b></td>
                <td style="width: 15%"><b>Amount</b></td>
            </tr>
            <br><br>
            <tr>
                <td style="width: 50%">{{$entity->nameOnCheque}}</td>
                <td style="width: 20%">-</td>
                <td style="width: 15%">{{number_format($entity->payAmountBank,$entity->decimalPlaces)}} </td>
                <td style="width: 15%">{{number_format($entity->payAmountBank,$entity->decimalPlaces)}}</td>
            </tr>
        </table>
    </div>
@endif

<div class="footer" >
    <div style="font-size: 16px !important;" id="cheque_cheque_date" > {{\App\helper\Helper::dateFormat($entity->BPVchequeDate)}} </div>
    <div style="font-size: 16px !important;" id="cheque_payee" >{{$entity->nameOnCheque}}</div>

    <table class="header-part" >
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
