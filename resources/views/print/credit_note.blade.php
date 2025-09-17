<html @if(isset($lang) && $lang === 'ar') dir="rtl" @endif>
<head>
<title>{{ trans('custom.credit_note') }}</title>
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

        /* RTL Support for Arabic */
    @if(isset($lang) && $lang === 'ar')
        body {
        direction: rtl;
        text-align: right;
    }

    .text-left {
        text-align: right !important;
    }

    .text-right {
        text-align: left !important;
    }

    table {
        direction: rtl;
    }

    .table th, .table td {
        text-align: right;
    }
    @endif

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

    table > tbody > th > tr > td {
        font-size: 11px;
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
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                <p><span style="font-weight: 700 !important;"><span>{!! nl2br($request->docRefNo) !!} </span></span>
                </p>
            </td>
            <td style="width:33%; text-align: center;font-size: 10px;vertical-align: top;">
                <span style="text-align: center">{{trans('custom.page')}} <span class="pagenum"></span></span><br>
                @if ($request->company)
                    {{$request->company->CompanyName}}
                @endif
            </td>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                <span style="margin-left: {{ isset($lang) && $lang === 'ar' ? '0%' : '38%' }};">{{ trans('custom.printed_date') }}  {{date("d-M-y", strtotime(now()))}}</span>
            </td>
        </tr>
    </table>
</div>
<div id="watermark">
         <span class="watermarkText">
           <h3 class="text-muted">

           </h3>
         </span>
</div>

<div class="content">
    <div class="row">
        <table style="width:100%" class="table_height">
            <tr>
                <td style="width: 30%;">
                    <img src="{{$request->company->logo_url}}" class="container"></td>

                <td style="width: 50%; text-align: center">
                    <div class="text-center">
                        <h3 style="font-weight: bold;">

                            @if ($request->company)
                                {{$request->company->CompanyName}}
                            @endif
                        </h3>
                        <h3 style="font-weight: bold;">
                            {{ trans('custom.credit_note') }}
                        </h3>
                    </div>

                </td>
                <td style="width: 30%" valign="bottom">
                                         <span style="font-weight: 700;">
{{--                         <h4 class="text-muted" style="opacity: 0.6;">
                             @if($request->confirmedYN == 0 && $request->approved == 0)
                                 Not Confirmed & Not Approved <br> Draft Copy
                             @endif
                             @if($request->confirmedYN == 1 && $request->approved == 0)
                                 Confirmed & Not Approved <br> Draft Copy
                             @endif
                         </h4>--}}
 `             </span>
                </td>
            </tr>
        </table>
    </div>
    <div class="row">
        <br>
    </div>
    <div class="row">
        <table style="width:100%; border-collapse: collapse;">
            <tr>
            <td style="width: 40%;">
                <b>{{ trans('custom.to') }} :</b>
                <br>
                <br>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td>{{$request->customer->CutomerCode}}</td>
                    </tr>
                    <tr>
                        <td>{{$request->customer->ReportTitle}}</td>
                    </tr>
                    <tr>
                        <td>{{$request->customer->customerAddress1}}</td>
                    </tr>
                    <tr>
                        <td>{{$request->customer->customerCity}}</td>
                    </tr>
                    <tr>
                            <td style="width: 20%"><span style="font-weight: bold;">{{ trans('custom.vat_no') }}</span></td>
                            <td style="width: 2%"><span style="font-weight: bold;">:</span></td>
                            <td style="width: 78%">{{$request->customer->vatNumber}} </td>
                        </tr>
                    </table>
            </td>
            <td style="width: 10%;"></td>
            <td style="width: 50%;">
                <br>
                <br>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 120px;"><span style="font-weight: bold;">{{ trans('custom.doc_code') }}</span></td>
                        <td style="width: 10px;"><span style="font-weight: bold;">:</span></td>
                        <td><span>{{$request->creditNoteCode}}</span></td>
                    </tr>
                    <tr>
                        <td style="width: 120px;"><span style="font-weight: bold;">{{ trans('custom.doc_date') }}</span></td>
                        <td style="width: 10px;"><span style="font-weight: bold;">:</span></td>
                        <td><span>
                                 @if(!empty($request->creditNoteDate))
                                    {{\App\helper\Helper::dateFormat($request->creditNoteDate) }}
                                @endif
                            </span></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </div>
    <div class="row">
        <br>
    </div>
    <div class="row">
        <b>{{ trans('custom.comments') }} : </b> {{$request->comments}}
    </div>

   <!--  @if($request->isVATApplicable)
        <div class="row">
            <b>VAT Percentage (%) : </b> {{$request->VATPercentage}}
        </div>
    @endif -->
    <div class="row">
        <div style="text-align: {{ isset($lang) && $lang === 'ar' ? 'left' : 'right' }}"><b>{{ trans('custom.currency') }}
                : {{empty($request->currency) ? '' : $request->currency->CurrencyCode}} </b></div>
    </div>
    <div class="row">
        <table class="table table-bordered" style="width: 100%; border-collapse: collapse;">
            <thead>
            <tr class="theme-tr-head">
                <th style="text-align: center; width: 30%;">{{ trans('custom.comments') }}</th>
                <th style="text-align: center; width: 25%;">{{ trans('custom.segments') }}</th>
                <th style="text-align: center; width: 20%;">{{ trans('custom.amount') }}</th>
                @if($request->isVATApplicable)
                    <th class="text-center" style="width: 15%;">{{ trans('custom.vat_amount') }}</th>
                    <th class="text-center" style="width: 15%;">{{ trans('custom.net_amount') }}</th>
                @endif
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
                    <td style="width: 30%;">{{$item->comments}}</td>
                    <td style="width: 25%;">
                        @if($item->segment)
                            {{$item->segment->ServiceLineDes}}
                        @endif
                    </td>
                    <td class="text-right" style="width: 20%;">{{number_format($item->creditAmount,$numberFormatting)}}</td>
                    @if($request->isVATApplicable)
                        <td class="text-right" style="width: 15%;">{{number_format($item->VATAmount,$numberFormatting)}}</td>
                        <td class="text-right" style="width: 15%;">{{number_format($item->netAmount,$numberFormatting)}}</td>
                    @endif
                </tr>

            @endforeach
            <tr>
                <td colspan="2" class="{{ isset($lang) && $lang === 'ar' ? 'text-left' : 'text-right' }}" style="border: 1px solid rgb(127, 127, 127)!important; width: 55%;"><span
                            style="font-weight: bold;"
                            style="font-size: 11px">{{ trans('custom.total') }}</span>
                </td>
                <td class="text-right"
                    style="border:1px solid #7f7f7f; font-weight:bold;">
                <span style="font-weight: bold;">
                @if ($request->details)
                        {{number_format($directTraSubTotal,$numberFormatting)}}
                    @endif
                </span>
                </td>
                @if($request->isVATApplicable)
                    <td class="text-right"
                    style="border:1px solid #7f7f7f; font-weight:bold;">
                <span style="font-weight: bold;">
                @if ($request->details)
                        {{number_format($directVATSubTotal,$numberFormatting)}}
                    @endif
                </span>
                    </td>
                    <td class="text-right"
                        style="border:1px solid #7f7f7f; font-weight:bold;">
                <span style="font-weight: bold;">
                @if ($request->details)
                        {{number_format($directNetSubTotal,$numberFormatting)}}
                    @endif
                </span>
                    </td>
                @endif
            </tr>
            </tbody>
        </table>
    </div>
    <div class="row" style="margin-top: 60px;">
        <table class="{{ isset($lang) && $lang === 'ar' ? 'rtl-table' : '' }}" style="width: 100%; border-collapse: collapse;">
            <tr style="width: 100%;">
                <td style="width: 60%;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 80px;">
                                <span style="font-weight: bold;">{{ trans('custom.prepared_by') }} :</span>
                            </td>
                            <td style="width: 400px;">
                                @if($request->createduser)
                                    {{$request->createduser->empName}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 10%;">
                </td>
                <td style="width: 40%;">
                    <table style="border-collapse: collapse;">
                        <tr>
                            <td style="width: 60px;">
                                <span style="font-weight: bold;">{{ trans('custom.checked_by') }} :</span>
                            </td>
                            <td style="border-bottom:1px solid #000;width:200px;height:20px;">
                                &nbsp;
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>


    <div class="row" style="margin-top: 10px">
        <span style="font-weight: bold;">{{ trans('custom.electronically_approved_by') }} :</span>
    </div>
    <div style="margin-top: 10px">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                @foreach ($request->approved_by as $det)
                    <td style="padding-right: 25px" class="text-center">
                        @if($det->employee)
                            {{$det->employee->empFullName }}
                        @endif
                        <br><br>
                        @if($det->employee)
                            {{ \App\helper\Helper::convertDateWithTime($det->approvedDate)}}
                        @endif
                    </td>
                @endforeach
            </tr>
        </table>
    </div>

</div>

</body>
</html>
