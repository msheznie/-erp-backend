<html>
<head>
    <title>{{ __('custom.item_issue_voucher') }}</title>
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
            font-weight: bold
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
    </style>
</head>
<body>
<div id="watermark"></div>
<div class="card-body content" style="margin-bottom: 45px;" id="print-section">

    <table style="width: 100%">
        <tr style="width: 100%">
            <td colspan="3" style="bottom: 0;position: absolute;text-align: right">
            <span style="font-weight: bold">
                <h3 class="text-muted">
                    @if($entity->confirmedYN == 0 && $entity->approved == 0)
                        {{__('custom.not_confirmed')}}
                    @elseif($entity->confirmedYN == 1 && $entity->approved == 0)
                        {{__('custom.pending_approval')}}
                    @elseif($entity->confirmedYN == 1 && ($entity->approved == 1 ||  $entity->approved == -1))
                        {{__('custom.fully_approved')}}
                    @endif
                    </h3>
             </span>
            </td>
        </tr>
        <tr style="width: 100%">
            <td colspan="3" style="text-align: center;">
                @if($entity->company)
                    <h3> {{$entity->company->CompanyName}}</h3>
                @endif
            </td>
        </tr>
        {{-- <tr style="width: 100%">
             <td colspan="3">
                 @if($entity->company)
                     <h6>{{$entity->company->CompanyAddress}}</h6>
                 @endif
             </td>
         </tr>--}}
        <tr style="width: 100%">
            <td colspan="3" style="text-align: center;">
                <h3>
                    {{ __('custom.item_issue_voucher') }}
                </h3>
            </td>
        </tr>
    </table>

    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 50%">
                <table>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold;">{{ __('custom.warehouse') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold">:</span>
                        </td>
                        <td>
                            @if($entity->warehouse_by)
                                {{$entity->warehouse_by->wareHouseDescription}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight: bold">{{ __('custom.ref_no') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold">:</span>
                        </td>
                        <td>
                            {{$entity->issueRefNo}}
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold">{{ __('custom.comments') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold">:</span>
                        </td>
                        <td>
                            <span>{{$entity->comment}}</span>
                        </td>
                    </tr>
                </table>
            </td>
            {{-- <td style="width: 40%;text-align: center">
            </td> --}}
            <td style="width: 50%">
                <table>
                    <tr>
                        <td width="90px">
                            <span style="font-weight: bold">{{ __('custom.document_no') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold">:</span>
                        </td>
                        <td>
                            <span>{{$entity->itemIssueCode}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="90px">
                            <span style="font-weight: bold">{{ __('custom.date') }} </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold">:</span>
                        </td>
                        <td>
                            <span>
                                {{ \App\helper\Helper::dateFormat($entity->issueDate)}}
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
            <tr  style="background-color: #DEDEDE !important; border-color:#000">
                <th></th>
                <th style="text-align: left;">{{ __('custom.item_code') }}</th>
                <th style="text-align: left;">{{ __('custom.item_description') }}</th>
                <th style="text-align: left;">{{ __('custom.manufacture_part_no') }}</th>
                <th style="text-align: left;">{{ __('custom.uom') }}</th>
                <th style="text-align: left;">{{ __('custom.qty') }}</th>
                <th style="text-align: left;">{{ __('custom.cost') }}({{ $entity->localCurrencyCode }})</th>
                <th style="text-align: left;">{{ __('custom.comments') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($entity->details as $item)
                <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                    <td style="padding-left: 5px">{{$loop->iteration}}</td>
                    <td style="padding-left: 5px">{{$item->itemPrimaryCode}}</td>
                    <td style="padding-left: 5px">{{$item->itemDescription}}</td>
                    <td style="text-align: left; padding-left: 5px;">
                        @if($item->item_by)
                            {{$item->item_by->secondaryItemCode}}
                        @endif
                    </td>
                    <td style="padding-left: 5px">
                        @if($item->uom_issuing)
                            {{$item->uom_issuing->UnitShortCode}}
                        @endif
                    </td>
                    <td style="text-align: right; padding-right: 5px;">{{$item->qtyIssued}}</td>
                    <td style="text-align: right; padding-right: 5px;">{{round($item->issueCostLocal,$entity->localDecimalPlaces)}}</td>
                    <td style="text-align: left; padding-left: 5px;">
                        {{$item->comments}}
                    </td>
                </tr>
            @endforeach
            </tbody>
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
                                <span style="font-weight: bold">{{ __('custom.issued_by') }} :</span>
                            </td>
                            <td width="400px">
                                @if($entity->confirmed_by)
                                    {{$entity->confirmed_by->empName}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>

                <td width="40%">
                    <table>
                        <tr>
                            <td width="90px">
                                <span style="font-weight: bold">{{ __('custom.reviewed_by') }} :</span>
                            </td>
                            <td>
                                <div style="border-bottom: 1px solid black;width: 200px;margin-top: 7px;"></div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="row" style="margin-top: 10px">
        <span style="font-weight: bold">{{ __('custom.electronically_approved_by') }} :</span>
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
