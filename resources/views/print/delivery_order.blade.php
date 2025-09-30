<html @if(isset($lang) && $lang === 'ar') dir="rtl" @endif>
<head>
    <title>{{ __('custom.delivery_order') }}</title>
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
            font-size: 11.5px;
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
    <span class="white-space-pre-line font-weight-bold">{!! nl2br($entity->docRefNo) !!}</span>
</div>
<div id="watermark"></div>
<div class="card-body content" id="print-section">

    <table style="width: 100%"  class="table_height">
        <tr style="width: 100%">
            <td colspan="3" style="bottom: 0;position: absolute;text-align: right">
            <span style="font-weight: bold;">
                <h3 class="text-muted">
                    @if($entity->confirmedYN == 0 && $entity->approvedYN == 0)
                        {{ __('custom.not_confirmed') }}
                    @elseif($entity->confirmedYN == 1 && $entity->approvedYN == 0)
                        {{ __('custom.pending_approval') }}
                    @elseif($entity->confirmedYN == 1 && ($entity->approvedYN == 1 ||  $entity->approvedYN == -1))
                        {{ __('custom.fully_approved') }}
                    @endif
                    </h3>
`             </span>
            </td>
        </tr>
        <tr style="width: 100%">
            <td width="20%">
                @if($entity->logoExists)
                    <img src="{{$entity->companyLogo}}"
                    class="container">
                @endif
            </td>
            <td width="50%" class="text-center">
                <div class="text-center">

                    @if($entity->company)
                        <h3> {{$entity->company->CompanyName}}</h3>
                    @endif

                        <h3>
                            {{ __('custom.delivery_order') }}
                        </h3>
                </div>

            </td>
            <td width="30%"></td>
        </tr>

        <tr style="width: 100%">

        </tr>
    </table>

    <table style="width: 100%; margin-top: 15px">
        <tr style="width:100%">
            <td style="width: 30%">
                <table>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.customer') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            @if($entity->customer)
                                {{$entity->customer->CustomerName}}
                            @endif
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
                            {{$entity->customer->customerAddress1}}
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
                            {{$entity->contactPersonName}} - {{$entity->contactPersonNumber}}
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold;">{{ __('custom.narration') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>{{$entity->narration}}</span>
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
                            <span style="font-weight: bold;">{{ __('custom.document_code') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>{{$entity->deliveryOrderCode}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold;">{{ __('custom.date') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>
                                {{ \App\helper\Helper::dateFormat($entity->deliveryOrderDate)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold;">{{ __('custom.reference_number') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>
                                {{ $entity->referenceNo}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold;">{{ __('custom.segment') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>
                                @if($entity->segment)
                                    {{ $entity->segment->ServiceLineDes}}
                                @endif
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    {{--<hr>--}}
    <div style="margin-top: 30px">
        <table class="table table-bordered" style="width: 100%;">
            <thead>
            <tr class="theme-tr-head">
                <th></th>
                <th class="text-left">{{ __('custom.item') }}</th>
                <th class="text-left">{{ __('custom.uom') }}</th>
                @if($entity->orderType != 1)
                    <th class="text-left">{{ __('custom.part_number') }}</th>
                @endif
                <th class="text-left">{{ __('custom.quantity') }}</th>
                <th class="text-left">{{ __('custom.unit_price') }}</th>
                <th class="text-left">{{ __('custom.discount') }}</th>
                @if($entity->isVatEligible)
                <th class="text-left">{{ __('custom.vat_per_unit') }}</th>
                @endif
                <th class="text-left">{{ __('custom.net_unit') }}</th>
                <th class="text-left">{{ __('custom.total') }} {{empty($entity->transaction_currency) ? '' : '('.$entity->transaction_currency->CurrencyCode.')'}}</th>
            </tr>
            </thead>
            <tbody>
            {{$directTraSubTotal = 0}}
            @foreach ($entity->detail as $item)
                <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                    <td>{{$loop->iteration}}</td>
                    <td>{{$item->itemPrimaryCode.' - '.$item->itemDescription}}
                        <div style="font-size: 10px !important;">{{$item->itemPrimaryCode}}</div>
                    </td>
                    <td>
                        @if($item->uom_issuing)
                            {{$item->uom_issuing->UnitShortCode}}
                        @endif
                    </td>
                    @if($entity->orderType != 1)
                        <td>
                            @if(!empty($item->item_by) && !empty($item->item_by->secondaryItemCode))
                                {{$item->item_by->secondaryItemCode}}
                            @endif
                        </td>
                    @endif
                    <td class="text-right">{{number_format($item->qtyIssuedDefaultMeasure,2)}}</td>
                    <td class="text-right">{{number_format($item->unitTransactionAmount,$entity->currency)}}</td>
                    <td class="text-right">{{number_format($item->discountAmount,$entity->currency)}}</td>
                    @if($entity->isVatEligible)
                    <td class="text-right">{{number_format($item->VATAmount,$entity->currency)}}</td>
                    @endif
                    <td class="text-right">{{number_format(($item->unitTransactionAmount-$item->discountAmount),$entity->currency)}}</td>
                    <td class="text-right">{{number_format($item->transactionAmount,$entity->currency)}}</td>
                </tr>
                {{$directTraSubTotal+=$item->transactionAmount}}
            @endforeach
            </tbody>
            @if($entity->isVatEligible == 1)
            <tr>
                <td colspan="{{ $entity->orderType != 1 ? 9 : 8 }}" style="text-align: right; border-left: none !important;"><b>{{ __('custom.total') }}</b></td>
                <td class="text-right">
                    @if ($entity->detail)
                        {{number_format($directTraSubTotal, $entity->currency)}}
                    @endif
                </td>
            </tr>
            @endif
            @if($entity->isVatEligible == 0)
            <tr>
                <td colspan="{{ $entity->orderType != 1 ? 8 : 7 }}" style="text-align: right; border-left: none !important;"><b>{{ __('custom.total') }}</b></td>
                <td class="text-right">
                    @if ($entity->detail)
                        {{number_format($directTraSubTotal, $entity->currency)}}
                    @endif
                </td>
            </tr>
            @endif
            @if($entity->isVatEligible)
            <tr>
                <td colspan="{{ $entity->orderType != 1 ? 9 : 8 }}" style="text-align: right; border-left: none !important;"><b>{{ __('custom.vat') }}</b></td>
                <td class="text-right">
                    @if ($entity->detail && $entity->tax && $entity->tax->amount)
                        {{number_format($entity->tax->amount, $entity->currency)}}
                    @else
                        {{number_format(0, $entity->currency)}}
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="{{ $entity->orderType != 1 ? 9 : 8 }}" style="text-align: right; border-left: none !important;"><b>{{ __('custom.net_total') }}</b></td>
                <td class="text-right">
                    @if ($entity->detail && $entity->tax && $entity->tax->amount)
                        {{number_format(($directTraSubTotal + $entity->tax->amount), $entity->currency)}}
                    @else
                        {{number_format(($directTraSubTotal), $entity->currency)}}
                    @endif
                </td>
            </tr>
            @endif
        </table>
    </div>
    {{--<hr>--}}
    <div class="row" style="margin-top: 60px;margin-left: -8px">
        <table>
            <tr width="100%">
                <td width="60%">
                    <table width="100%">
                        <tr>
                            <td width="70px">
                                <span style="font-weight: bold;">{{ __('custom.confirmed_by') }} :</span>
                            </td>
                            <td width="400px">
                                @if($entity->confirmed_by)
                                    {{$entity->confirmed_by->empName}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="10%">

                </td>
                <td width="30%">
                    <table>
                        <tr>
                            <td width="70px">
{{--                                <span style="font-weight: bold;">Reviewed By :</span>--}}
                            </td>
                            <td>
{{--                                <div style="border-bottom: 1px solid black;width: 200px;margin-top: 7px;"></div>--}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="row" style="margin-top: 10px">
        <span style="font-weight: bold;">{{ __('custom.electronically_approved_by') }} :</span>
    </div>
    <div style="margin-top: 10px">
        <table>
            <tr>
                @foreach ($entity->approved_by as $det)
                    <td style="padding-right: 25px">
                        @if($det->employee)
                            {{$det->employee->empFullName }}
                            @if($det->employee->details)
                                @if($det->employee->details->designation)
                                    <br>{{$det->employee->details->designation->designation}}
                                @endif
                            @endif
                        @endif
                        <br>
                        @if($det->employee)
                            @if($det->approvedYN == -1)
                                {{ \App\helper\Helper::convertDateWithTime($det->approvedDate)}}
                            @elseif($det->rejectedYN == -1)
                                {{ \App\helper\Helper::convertDateWithTime($det->rejectedDate)}}
                            @endif
                        @endif
                    </td>
                @endforeach
            </tr>
        </table>
    </div>
</div>
</body>
</html>
