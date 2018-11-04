<html>
<head>
    <title>Bank Transfer</title>
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
    </style>
</head>
<body onload="window.print()">
<div class="footer">
    {{--Footer Page <span class="pagenum"></span>--}}
    {{-- <span class="white-space-pre-line font-weight-bold">{!! nl2br($entity->docRefNo) !!}</span>--}}
</div>
<div id="watermark"></div>

@foreach ($entities as $entity)
   {{-- @if($loop->last)
        <div class="card-body content">
            @else--}}
                <div class="card-body content " class="{{ $loop->last ? '' : 'page-break' }}">
                 {{--   @endif--}}
                    <table style="width: 100%">
                        <tr style="width: 100%">
                            <td valign="top" style="width: 100%">
                                <h6>
                                    <span class="font-weight-bold">Doc Ref No</span>
                                    <span style="margin-left: 10px">{{$entity->documentCode}}</span>
                                </h6>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h6> {{ \App\helper\Helper::dateFormat($date)}} </h6>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                                <b>The Manager</b><br>
                                @if($entity->bank_account)
                                    <b>{{$entity->bank_account->bankName}}</b> <br>
                                    <b>{{$entity->bank_account->bankBranch}}</b>
                                @else
                                    <br><br>
                                @endif
                                <br><br><br>

                                Dear Sir,<br><br>
                                <u><b>Sub : FUND TRANSFER</b></u> <br> <br>
                                By debiting our Account No.
                                <b>@if($entity->bank_account){{$entity->bank_account->AccountNo}}@endif</b>
                                kindly transfer a sum
                                of
                                <b>@if($entity->bank_currency_by) {{$entity->bank_currency_by->CurrencyCode}}@endif {{number_format($entity->payAmountBank,$entity->decimalPlaces)}}</b>
                                [@if($entity->bank_currency_by) {{$entity->bank_currency_by->CurrencyCode}}@endif {{$entity->amount_word}}
                                and
                                {{$entity->floatAmt}}/@if($entity->decimalPlaces == 3)1000 @else 100 @endif] to the
                                following account as detailed below.<br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table style="width: 100%">
                                    @foreach($entity->memos as $memo)
                                        @if($memo->memoDetail)
                                            <tr style="width: 100%">
                                                <td valign="top" style="width:30%">
                                                    {{$memo->memoHeader}} : <br>
                                                </td>
                                                <td style="width: 2%">:</td>
                                                <td valign="top" style="width: 68%">
                                                    {{$memo->memoDetail}}<br>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Yours faithfully,<br>
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
                        <tr>
                            <td colspan="2">
                                <br><br><br>
                                Prepared By: {{$entity->chequePrintedByEmpName}}
                            </td>
                        </tr>
                    </table>
                </div>

        @endforeach
</body>
</html>