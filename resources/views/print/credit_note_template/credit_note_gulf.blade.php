<style type="text/css">
    <!--
    @page {
        margin-left: 3%;
        margin-right: 3%;
        margin-top: 4%;
    }

    .footer {
        position: absolute;
    }

    body {
        font-size: 11px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"
    }

    @if(isset($lang) && $lang === 'ar')
    body {
        font-family: 'Noto Sans Arabic', sans-serif;
    }
    @endif

    p,h3 {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    }

    p {
        font-size: 11px;
    }

    h3 {
        font-size: 1.53125rem;
    }

    h6 {
        font-size: 0.875rem;
    }

    h6, h3 {
        margin-bottom: 0.1rem;
        font-weight: 500;
        line-height: 1.2;
        color: inherit;
    }

    table > tbody > tr > td {
        font-size: 15px !important;
    }

    .theme-tr-head {
        background-color: #EBEBEB !important;
    }

    .text-left {
        text-align: left;
    }

    table {
        border-collapse: collapse;
    }

    .font-weight-bold {
        font-weight: 700 !important;
    }

    .table th {
        border: 1px solid rgb(127, 127, 127) !important;
    }

    .table th, .table td {
        padding: 0.4rem !important;
        vertical-align: top;
        border-bottom: 1px solid rgb(127, 127, 127) !important;
        /*font-size: 30px !important;*/
    }

    .table th {
        background-color: #EBEBEB !important;
    }

    tfoot > tr > td {
        border: 1px solid rgb(127, 127, 127);
    }

    .text-right {
        text-align: right !important;
    }

    .font-weight-bold {
        font-weight: 700 !important;
    }

    hr {
        border: 0;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    th {
        text-align: inherit;
        font-weight: bold;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }

    .white-space-pre-line {
        white-space: pre-line;
    }

    p {
        margin-top: 0 !important;
    }

    .title {
        font-size: 13px;
        font-weight: 600;
    }

    .footer {
        bottom: 0;
        height: 40px;
    }

    .footer {
        width: 100%;
        text-align: center;
        position: fixed;
        font-size: 10px;
        padding-top: -20px;
    }

    .pagenum:after {
        content: counter(page);
    }

    .content {
        margin-bottom: 45px;
    }

    #watermark {
        position: fixed;
        width: 100%;
        height: 100%;
        padding-top: 31%;
    }

    .watermarkText {
        color: #dedede !important;
        font-size: 30px;
        font-weight: 700 !important;
        text-align: center !important;
        font-family: fantasy !important;
    }

    #watermark {
        height: 1000px;
        opacity: 0.6;
        left: 0;
        transform-origin: 20% 20%;
        z-index: 1000;
    }


</style>


<div id="watermark">
         <span class="watermarkText">
           <h3 class="text-muted">

           </h3>
         </span>
</div>

<div class="content">
    <div class="row">
        <table style="width:100%">
            <tr>
                <td width="30%">
                    <img src="{{$request->company->logo_url}}" width="180px" height="60px"></td>

                <td width="50%" style="text-align: center">
                    <div class="text-center">
                        <h3 class="font-weight-bold">
                            <b>
                            Tax Credit Note <span >(مذكرة الائتمان الضريبي)</span>
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
                        {{$request->company->CompanyName}}
                        </p>
                    </b>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        {{$request->company->CompanyAddress}}
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        Tax Identification Number (TIN) : {{$request->company->vatRegistratonNumber}}
                    </p>
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
                        {{$request->customer->ReportTitle}}
                        </p>
                    </b>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        {{$request->customer->customerAddress1}}
                    </p>
                </td>
            </tr>
            @if(isset($request->customer->customerAddress2) && !is_null($request->customer->customerAddress2))
             <tr>
                <td>
                    <p>
                        {{$request->customer->customerAddress2}}
                    </p>
                </td>
            </tr>
            @endif
            <tr>
                <td>
                    <p>
                        Tax Identification Number (TIN) : {{$request->customer->vatNumber}}
                    </p>
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
                    <p>
                        <b>Credit Note No : </b>{{$request->creditNoteCode}}
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        <b>
                        Credit Note Date :  </b>@if(!empty($request->creditNoteDate))
                                    {{\App\helper\Helper::dateFormat($request->creditNoteDate) }}
                                @endif
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        <b>Invoice No : </b>
                    </p>
                </td>
            </tr>
            
        </table>
    </div>
    <br>
    <div class="row">
        <table class="table">
            <thead>
                <tr style="background-color: #6798da;">
                    <th style="font-size: 15px">Item No</th>
                    <th style=" text-align: center;">Rationale for adjustment</th>
                    <th style="text-align: center;">Taxable amount after discount ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})<br>(excluding tax)</th>
                    <th style="text-align: center;">Tax Amount({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
                    <th style="text-align: center;">Adjustment to Taxable Amount({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})<br>(excluding tax)</th>
                    <th style="text-align: center;">Adjustment to Tax Amount ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
                    <th style="text-align: center;">VAT Rate (%)</th>
                    <th style="text-align: center;">Adjusted Total Amount({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})<br>(incl. tax)</th>
                </tr>
            </thead>
            <tbody>
                
                {{$directTraSubTotal =0}}
                {{$directVATSubTotal =0}}
                {{$directNetSubTotal =0}}
                {{$numberFormatting= empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}}}
                @foreach ($request->details as $item)
                    {{$directTraSubTotal +=$item->creditAmount}}
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
                        <td class="text-right">{{number_format($item->creditAmount,$numberFormatting)}}</td>
                    </tr>

                @endforeach

                    <tr>
                        <td colspan="2" style="text-align: right;"><b>Total ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</b></td>
                        <td colspan="2"></td>
                        <td style="text-align: right;">
                            <b>
                                @if ($request->details)
                                    {{number_format($directNetSubTotal,$numberFormatting)}}
                                @endif
                            </b>
                        </td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right;">
                            <b>
                                 @if ($request->details)
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
                                {{$request->localCurrencyER}}
                            </b>
                        </td>
                        <td colspan="2"></td>
                    </tr>

                      <tr>
                        <td colspan="2" style="text-align: right;"><b>Grand Total ({{empty($request->local_currency) ? '' : $request->local_currency->CurrencyCode}})</b></td>
                        <td colspan="2"></td>
                        <td style="text-align: right;">
                            <b>
                                @if ($request->details)
                                    {{number_format(($directNetSubTotal * $request->localCurrencyER),$numberFormatting)}}
                                @endif
                            </b>
                        </td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right;">
                            <b>
                                 @if ($request->details)
                                    {{number_format(($directTraSubTotal * $request->localCurrencyER),$numberFormatting)}}
                                @endif
                            </b>
                        </td>
                    </tr>
            </tbody>
        </table>
    </div>
</div>









