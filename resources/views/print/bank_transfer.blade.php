<html>
<head>
    <title>{{ __('custom.bank_transfer') }}</title>
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
            font-family: inherit;
            font-weight: bold;
            line-height: 1.2;
            color: inherit;
        }

        table > tbody > tr > td {
            font-size: 14px;
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
            padding-top: 180px;
            padding-left: 50px;
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
        div.card-body{
            margin-top: -50px;
        }
    </style>
</head>
<body onload="window.print();">

<div class="footer">
</div>
<div id="watermark"></div>

@if ($entity != null)
    <div class="card-body content">
        <table style="width: 100%">
            <tr style="width: 100%">
                <td valign="top" style="width: 80%">
                    <b>
                        <span class="font-weight-bold">Doc Ref No</span>
                        <span style="margin-left: 10px">{{$entity->BPVcode}}</span>
                    </b>
                </td>
                <td valign="top" style="width: 20%">
                    <b>
                        <span>Date:</span>
                        <span>{{ \App\helper\Helper::dateFormat($date)}}</span>
                    </b>
                </td>
            </tr>

            <tr></tr>

            <tr>
                <td>
                    <b>To,</b>
                </td>
            </tr>
            <tr>
                <td>
                    <br>
                    <b>The Manager</b><br>
                    @if($entity->bankaccount)
                        <b>{{$entity->bankaccount->bankName}}</b> <br>
                        <b>{{$entity->bankaccount->bankBranch}}</b>
                    @else
                        <br><br>
                    @endif
                    <br><br><br>
                    <b>Subject</b>              :  Amount Transfer <br>
                    <b>Reference</b>            :  Our Account #@if($entity->bankaccount){{$entity->bankaccount->AccountNo}}@endif
                    <br><br><br>

                    Dear Sir,<br><br>
                    Please arrange to transfer an amount of 
                    <b>@if(isset($entity->supplierTransactionCurrencyDetails)) {{$entity->supplierTransactionCurrencyDetails->CurrencyCode}}@endif {{' '.number_format($entity->totalAmount,$entity->decimalPlaces)}}</b>
                    [@if($entity->supplierTransactionCurrencyDetails) {{$entity->supplierTransactionCurrencyDetails->CurrencyCode}}@endif {{$entity->amount_word}}
                    and
                    {{$entity->floatAmt}}/@if($entity->decimalPlaces == 3)1000 @else 100 @endif] only as folllow and debit the same to 
                    our above mentioned account under advice to us , with a conversion to the beneficiary currency at the prevailing exchange rate.<br>

                    <br><br>
                    <b>Beneficiary Name         :  </b> @if($entity->memos){{isset($entity->memos[0]->memoDetail)?$entity->memos[0]->memoDetail:''}}@endif <br>
                    <b>Beneficiary Address      :  </b> @if($entity->supplier){{$entity->supplier->address}}@endif
                    <br><br>

                    <b>Beneficiary Bank         :  </b> @if($entity->memos){{isset($entity->memos[1]->memoDetail)?$entity->memos[1]->memoDetail:''}}@endif <br>
                    <b>Beneficiary Bank Address       :  </b> @if($entity->memos){{isset($entity->memos[2]->memoDetail)?$entity->memos[2]->memoDetail:''}}@endif
                    <br><br>

                    <b>Beneficiary Account No   :  </b> @if($entity->memos){{isset($entity->memos[3]->memoDetail)?$entity->memos[3]->memoDetail:''}}@endif <br>
                    <b>Swift Code               :  </b> @if($entity->memos){{isset($entity->memos[9]->memoDetail)?$entity->memos[9]->memoDetail:''}}@endif <br>
                    <b>IBAN No                  :  </b> @if($entity->memos){{isset($entity->memos[8]->memoDetail)?$entity->memos[8]->memoDetail:''}}@endif
                    <br><br>
                    <b>Purpose of Remittance    :  </b> @if($entity->BPVNarration){{$entity->BPVNarration}}@endif
                    <br><br>

                </td>
            </tr>
                <td>
                    Thanking you, with best regards,<br>
                    <b>For :</b><br/><br/>
                    @if($entity->company)
                        <i><b> {{$entity->company->CompanyName}} </b></i>
                    @endif

                    <br><br><br><br>
                </td>
            </tr>
        </table>
        <table style="width: 100%">
            <tr style="width: 100%">
                <td valign="top" style="width: 50%">
                    _________________________
                </td>
                <td valign="top" style="width: 50%">
                    __________________________
                </td>
            </tr>
            <tr style="width: 100%">
                <td valign="top" style="width: 50%">
                    <b>Authorized Signatory</b>
                </td>
                <td valign="top" style="width: 50%">
                    <b>Authorized Signatory</b>
                </td>
            </tr>
        </table>
    </div>

@endif
</body>
</html>
