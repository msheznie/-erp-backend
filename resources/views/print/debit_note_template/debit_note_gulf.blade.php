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
        @if(isset($lang) && $lang === 'ar')
        body {
            font-family: 'Noto Sans Arabic', sans-serif;
        }
        @endif

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
            /*bottom: 40px;*/
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
        .border-top-remov{
            border-top: 1px solid #ffffff00 !important;
            border-left: 1px solid #ffffff00 !important;
            background-color: #ffffff !important;
            border-right: 0;
        }
        .border-bottom-remov{
            border-bottom: 1px solid #ffffffff !important;
            background-color: #ffffff !important;
            border-right:  1px solid #ffffffff !important;
        }
        .container
            {
                display: block;
                max-width:230px;
                max-height:95px;
                width: auto;
                height: auto;
            }

        .table_height
            {
                max-height: 60px !important;
            }

    </style>

<div class="content">
    <div class="row">
        <table style="width:100%" class="table_height">
            <tr>
                <td width="20%">
                    <img src="{{$entity->company->logo_url}}" width="180px" height="60px" class="container">
                </td>

                <td width="80%" style="text-align: center">
                    <div class="text-center">
                        <h3 class="font-weight-bold" style="font-size: 18px">
                            <b>
                            Tax Debit Note <span >(مذكرة الخصم الضريبي)</span>
                            </b>
                        </h3>
                    </div>
                </td>
                <td style="width: 30%" valign="bottom">
                                         <span class="font-weight-bold">

 `             </span>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <br>
    <div class="row">
        <table style="width:100%">
            <tr>
                <td>
                    <b>
                        <p>
                        {{$entity->company->CompanyName}}
                        </p>
                    </b>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        {{$entity->company->CompanyAddress}}
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        Tax Identification Number (TIN) : {{$entity->company->vatRegistratonNumber}}
                    </p>
                </td>
            </tr>
            
        </table>
    </div>
    <br>
    <div class="row">
        <table style="width:100%">
            <tr>
                <td>
                    <b>
                        <p>
                         @if($entity->type == 1)
                            @if($entity->supplier)
                                    {{$entity->supplier->supplierName}}
                            @endif
                         @endif

                         @if($entity->type == 2)
                            @if($entity->employee)
                                    {{$entity->employee->empName}}
                            @endif
                         @endif
                        </p>
                    </b>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        {{$entity->supplier->address}}
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        Tax Identification Number (TIN) : {{$entity->supplier->vatNumber}}
                    </p>
                </td>
            </tr>
            
        </table>
    </div>
    <br>
    <div class="row">
        <table style="width:100%">
            <tr>
                <td>
                    <p>
                        <b>Debit Note No : </b>{{$entity->debitNoteCode}}
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        <b>
                        Debit Note Date :  </b>@if(!empty($entity->debitNoteDate))
                                    {{\App\helper\Helper::dateFormat($entity->debitNoteDate) }}
                                @endif
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        <b>Invoice No : </b> {{$entity->invoiceNumber}}
                    </p>
                </td>
            </tr>
            
        </table>
    </div>
    <br>
    <div style="row">
        <table class="table table-bordered" style="width: 100%;">
            <thead>
                <tr class="theme-tr-head">
                    <th style="font-size: 15px">Item No</th>
                    <th style=" text-align: center;">Rationale for adjustment</th>
                    <th style="text-align: center;">Taxable amount after discount ({{empty($entity->transactioncurrency) ? '' : $entity->transactioncurrency->CurrencyCode}})<br>(excluding tax)</th>
                    <th style="text-align: center;">Tax Amount({{empty($entity->transactioncurrency) ? '' : $entity->transactioncurrency->CurrencyCode}})</th>
                    <th style="text-align: center;">Adjustment to Taxable Amount({{empty($entity->transactioncurrency) ? '' : $entity->transactioncurrency->CurrencyCode}})<br>(excluding tax)</th>
                    <th style="text-align: center;">Adjustment to Tax Amount ({{empty($entity->transactioncurrency) ? '' : $entity->transactioncurrency->CurrencyCode}})</th>
                    <th style="text-align: center;">VAT Rate (%)</th>
                    <th style="text-align: center;">Adjusted Total Amount({{empty($entity->transactioncurrency) ? '' : $entity->transactioncurrency->CurrencyCode}})<br>(incl. tax)</th>
                </tr>
            </thead>
            <tbody>
                
                {{$directTraSubTotal =0}}
                {{$directVATSubTotal =0}}
                {{$directNetSubTotal =0}}
                {{$numberFormatting= empty($entity->transactioncurrency) ? 2 : $entity->transactioncurrency->DecimalPlaces}}}}
                @foreach ($entity->detail as $item)
                    {{$directTraSubTotal +=$item->debitAmount}}
                    {{$directVATSubTotal +=$item->VATAmount}}
                    {{$directNetSubTotal +=$item->netAmount}}
                    <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                        <td>
                            {{$item->glCode}}
                        </td>
                        <td>
                            {{$item->comments}}
                        </td>
                        <td class="text-right">{{number_format($item->netAmount,$numberFormatting)}}</td>
                        <td class="text-right">{{number_format($item->VATAmount,$numberFormatting)}}</td>
                        <td class="text-right">{{number_format($item->netAmount,$numberFormatting)}}</td>
                        <td class="text-right">{{number_format($item->VATAmount,$numberFormatting)}}</td>
                        <td class="text-right">{{$item->VATPercentage}}</td>
                        <td class="text-right">{{number_format($item->debitAmount,$numberFormatting)}}</td>
                    </tr>

                @endforeach

                    <tr>
                        <td colspan="2" style="text-align: right;"><b>Total ({{empty($entity->transactioncurrency) ? '' : $entity->transactioncurrency->CurrencyCode}})</b></td>
                        <td colspan="2"></td>
                        <td style="text-align: right;">
                            <b>
                                @if ($entity->detail)
                                    {{number_format($directNetSubTotal,$numberFormatting)}}
                                @endif
                            </b>
                        </td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right;">
                            <b>
                                 @if ($entity->detail)
                                    {{number_format($directTraSubTotal,$numberFormatting)}}
                                @endif
                            </b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: right;">
                            <b>
                                Conversion Rate
                            </b>
                        </td>
                        <td style="text-align: right;">
                            <b>
                                {{$entity->localCurrencyER}}
                            </b>
                        </td>
                        <td colspan="2"></td>
                    </tr>

                      <tr>
                        <td colspan="2" style="text-align: right;"><b>Grand Total ({{empty($entity->localcurrency) ? '' : $entity->localcurrency->CurrencyCode}})</b></td>
                        <td colspan="2"></td>
                        <td style="text-align: right;">
                            <b>
                                @if ($entity->detail)
                                    {{number_format(($directNetSubTotal * $entity->localCurrencyER),$numberFormatting)}}
                                @endif
                            </b>
                        </td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right;">
                            <b>
                                 @if ($entity->detail)
                                    {{number_format(($directTraSubTotal * $entity->localCurrencyER),$numberFormatting)}}
                                @endif
                            </b>
                        </td>
                    </tr>
            </tbody>
        </table>
    </div>
</div>
