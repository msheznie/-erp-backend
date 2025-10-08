<html @if(isset($lang) && $lang === 'ar') dir="rtl" @endif>
<head>
    <title>{{ __('custom.quotation') }}</title>
    <style>
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
            margin-bottom: 100px;
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
            <td width="40%"><span
                        style="font-weight: bold;">{{ __('custom.confirmed_by') }} :</span> {{ $masterdata->confirmed_by? $masterdata->confirmed_by->empFullName:'' }}
            </td>
            <td><span style="font-weight: bold;">{{ __('custom.reviewed_by') }} :</span></td>
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            <td><span style="font-weight: bold;">{{ __('custom.electronically_approved_by') }} :</span></td>
        </tr>
        <tr>
            &nbsp;
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            @if ($masterdata->approved_by)
                @foreach ($masterdata->approved_by as $det)
                    <td style="padding-right: 25px;font-size: 9px;">
                        <div>
                            @if($det->employee)
                                {{$det->employee->empFullName }}
                            @endif
                        </div>
                        <div><span>
                @if(!empty($det->approvedDate))
                                    {{ \App\helper\Helper::convertDateWithTime($det->approvedDate)}}
                                @endif
              </span></div>
                        <div style="width: 3px"></div>
                    </td>
                @endforeach
            @endif
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            <td colspan="3" style="width:100%">
                <hr style="background-color: black">
            </td>
        </tr>
        <tr>
            <td style="width:33%; text-align: center;font-size: 10px;vertical-align: top;">
            </td>
            <td style="width:33%; text-align: center;font-size: 10px;vertical-align: top;">
                <span style="text-align: center">{{ __('custom.page') }} <span class="pagenum"></span></span><br>
                @if ($masterdata->company)
                    {{$masterdata->company->CompanyName}}
                @endif
            </td>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                <span style="margin-left: 50%;">{{ __('custom.printed_date') }} : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
        </tr>
    </table>
</div>
<div id="watermark"></div>
<div class="card-body content" id="print-section">
    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td valign="top" style="width: 20%">
                @if($masterdata->company)
                    <img src="{{$masterdata->company->logo_url}}" class="container">
                @endif
                
            </td>
            <td valign="top" style="width: 80%">
                @if($masterdata->company)
                    <span style="font-size: 24px;font-weight: 400"> {{$masterdata->company->CompanyName}}</span>
                @endif
                <br>
                <table>
                    <tr>
                        <td width="100px">
                            <span style="font-weight: bold;">{{ __('custom.document_code') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>{{$masterdata->quotationCode}}@if($masterdata->versionNo)\V{{$masterdata->versionNo}}@endif
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold;">{{ __('custom.document_date') }} </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>
                                {{ \App\helper\Helper::dateFormat($masterdata->documentDate)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold;">{{ __('custom.document_exp_date') }} </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>
                                {{ \App\helper\Helper::dateFormat($masterdata->documentExpDate)}}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr style="color: #d3d9df">

        
    <div>
        <span style="font-size: 18px">
            @if($masterdata->documentSystemID == 67)
                @if($masterdata->quotationType == 1)
                    {{ __('custom.rental_quotation') }}
                @else
                    {{ __('custom.sales_quotation') }}
                @endif
            @endif
            @if($masterdata->documentSystemID == 68)
                {{ __('custom.sales_order') }}
            @endif
        </span>
    </div>
    <br>
    <br>

    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 50%">
                <table>
                    <tr>
                        <td width="150px">
                            <span style="font-weight: bold;">{{ __('custom.customer_name') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            {{$masterdata->customerName}}
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.customer_address') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            {{$masterdata->customerAddress}}
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.customer_telephone') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            {{$masterdata->customerTelephone}}
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.email') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            {{$masterdata->customerEmail}}
                        </td>
                    </tr>
                     @if(($masterdata->documentSystemID == 67) || ($masterdata->documentSystemID == 68 && $masterdata->quotationType == 1))
                     <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.lead_time_days') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            @if($masterdata->leadTime)
                            {{$masterdata->leadTime}}
                            @endif
                        </td>
                    </tr>
                     @endif
                </table>
            </td>
            <td style="width: 50%">
                <table>
                    <tr>
                        <td width="150px">
                            <span style="font-weight: bold;">{{ __('custom.sales_person') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            @if($masterdata->sales_person)
                                {{$masterdata->sales_person->SalesPersonName}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.contact_person') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            {{$masterdata->contactPersonName}}
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.contact_person_telephone') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            {{$masterdata->contactPersonNumber}}
                        </td>
                    </tr>

                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.currency') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            @if($masterdata->transactionCurrency)
                            {{$masterdata->transactionCurrency}}
                            @endif
                        </td>
                    </tr>
                   
                </table>
            </td>
        </tr>
    </table>
    <div style="margin-top: 30px">
        <table class="table table-bordered" style="width: 100%;">
            <thead>
            <tr class="theme-tr-head">
                <th>#</th>
                <th>{{ __('custom.item_code') }}</th>
                <th>{{ __('custom.item_description') }}</th>
                <th>{{ __('custom.part_number') }}</th>
                <th>{{ __('custom.uom') }}</th>
                <th>{{ __('custom.quantity') }}</th>
                <th>{{ __('custom.unit_rate') }}</th>
                <th>{{ __('custom.discount') }}</th>
                @if($masterdata->isVatEligible)
                    <th>{{ __('custom.vat_percentage') }}</th>
                @endif
                <th>{{ __('custom.net_amount') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($masterdata->detail as $item)
                <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                    <td>{{$loop->iteration}}</td>
                    <td>{{$item->itemSystemCode}}</td>
                    <td>{{$item->itemDescription}}
                         <div style="font-size: 10px !important;">{{$item->comment}}</div>
                    </td>
                    <td>{{$item->itemReferenceNo}}</td>
                    <td>{{$item->unitOfMeasure}}</td>
                    <td class="text-right">{{$item->requestedQty}}</td>
                    <td class="text-right">{{number_format($item->unittransactionAmount, $masterdata->transactionCurrencyDecimalPlaces)}}</td>
                    <td class="text-right">{{number_format($item->discountAmount, $masterdata->transactionCurrencyDecimalPlaces)}}</td>
                    @if($masterdata->isVatEligible)
                        <td class="text-right">{{number_format($item->VATAmount, $masterdata->transactionCurrencyDecimalPlaces)}}</td>
                    @endif
                    <td class="text-right">{{number_format($item->transactionAmount, $masterdata->transactionCurrencyDecimalPlaces)}}</td>
                </tr>
            @endforeach
            @if($masterdata->isVatEligible)
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    <td colspan="9" class="text-right">{{ __('custom.sub_total') }}</td>
                    <td class="text-right ">{{number_format($netTotal, $masterdata->transactionCurrencyDecimalPlaces)}}</td>
                </tr>
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    <td colspan="9" class="text-right">{{ __('custom.vat_amount') }}</td>
                    <td class="text-right ">{{number_format($masterdata->VATAmount, $masterdata->transactionCurrencyDecimalPlaces)}}</td>
                </tr>
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    <td colspan="9" class="text-right">{{ __('custom.grand_total') }}</td>
                    <td class="text-right ">{{number_format(($netTotal + $masterdata->VATAmount), $masterdata->transactionCurrencyDecimalPlaces)}}</td>
                </tr>
            @endif

            @if(!$masterdata->isVatEligible)
                <tr style="border-top: 1px solid #333 !important;border-bottom: 1px solid #333 !important;">
                    <td colspan="8" class="text-right">{{ __('custom.grand_total') }}</td>
                    <td class="text-right ">{{number_format($netTotal, $masterdata->transactionCurrencyDecimalPlaces)}}</td>
                </tr>
            @endif

            </tbody>
        </table>
    </div>
    <hr style="color: #d3d9df">
    <div class="row">
        <table style="width:100%;padding-top: 3%;">
            <tr>
                <td style="width:13%;vertical-align: top;"><span style="font-weight: bold;">{{ __('custom.delivery_terms') }}</span></td>
                <td style="width:2%;vertical-align: top;"><span style="font-weight: bold;">:</span></td>
                <td style="width:85%;vertical-align: top;">{!! nl2br($masterdata->deliveryTerms) !!}</td>
            </tr>
        </table>
    </div>
    <div class="row">
        <table style="width:100%;padding-top: 3%;">
            <tr style="padding-bottom: 2%;">
                <td style="width:13%;vertical-align: top;"><span style="font-weight: bold;">{{ __('custom.penalty_terms') }}</span></td>
                <td style="width:2%;vertical-align: top;"><span style="font-weight: bold;">:</span></td>
                <td style="width:85%;vertical-align: top;">{!! nl2br($masterdata->panaltyTerms) !!}</td>
            </tr>
        </table>
    </div>
    <div class="row">
        <table style="width:100%;padding-top: 3%;padding-bottom: 50px">
            <tr style="padding-bottom: 2%;">
                <td style="width:13%;vertical-align: top;"><span style="font-weight: bold;">{{ __('custom.payment_terms') }}</span></td>
                <td style="width:2%;vertical-align: top;"><span style="font-weight: bold;">:</span></td>
                <td style="width:85%;vertical-align: top;">{{$paymentTermsView}}</td>
            </tr>
        </table>
    </div>
    <div class="row" style="font-size: 11.5px !important;">
        <div><span style="font-weight: bold;">{{ __('custom.notes') }} :</span></div>
        <div>{!! nl2br($masterdata->Note) !!}</div>
        {{--<table style="width:100%">
            <tr>
                <td style="width:11%;vertical-align: top;"><span style="font-weight: bold;">Notes :</span></td>
            </tr>
            <tr>
                <td style="width:88%;vertical-align: top;">{!! nl2br($masterdata->Note) !!}</td>
            </tr>
        </table>--}}
    </div>
</div>
