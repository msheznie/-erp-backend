<html>

<head>
    <title>{{ __('custom.asset_transfer') }}</title>
    <style>
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
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }

        @if(app()->getLocale() == 'ar')
        body {
            font-family: 'Noto Sans Arabic', sans-serif !important;
        }
        @endif

        h3 {
            font-size: 24.5px;
        }

        h6 {
            font-size: 14px;
        }

        h6,
        h3 {
            margin-top: 0px;
            margin-bottom: 0px;
            font-family: inherit;
            font-weight: bold;
            line-height: 1.2;
            color: inherit;
        }

        table>tbody>tr>td {
            font-size: 11.5px;
        }

        .theme-tr-head {
            background-color: rgb(215, 215, 215) !important;
        }

        .text-left {
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
        }

        .text-right {
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
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

        .table th,
        .table td {
            padding: 6.4px !important;
        }

        table.table-bordered {
            border-collapse: collapse;
        }

        table.table-bordered,
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #e2e3e5;
        }

        table>thead>tr>th {
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
            margin-bottom: 45px;
        }

        .border-top-remov {
            border-top: 1px solid #ffffff00 !important;
            border-left: 1px solid #ffffff00 !important;
            background-color: #ffffff !important;
            border-right: 0;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }

        .border-bottom-remov {
            border-bottom: 1px solid #ffffffff !important;
            background-color: #ffffff !important;
            border-right: 1px solid #ffffffff !important;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }
    </style>
</head>

<body>
    <div class="footer">
        <table style="width:100%;">
            <tr>
                <td width="40%"><span class="font-weight-bold">{{ trans('custom.confirmed_by') }} :</span> {{ $assetTransferMaster->confirmed_by? $assetTransferMaster->confirmed_by->Ename2:'' }}
                </td>
                <td><span class="font-weight-bold">{{ trans('custom.review_by') }} :</span></td>
            </tr>
        </table>
        <table style="width:100%;">
            <tr>
                <td><span class="font-weight-bold">{{ trans('custom.electronically_approved_by') }} :</span></td>
            </tr>
            <tr>
                &nbsp;
            </tr>
        </table>
        <table style="width:100%;">
        <tr>
            @if ($assetTransferMaster->approved_by)
                @foreach ($assetTransferMaster->approved_by as $det)
                    <td style="padding-right: 25px;font-size: 9px;">
                        <div>
                            @if($det->employee)
                                {{$det->employee->empFullName }}
                            @endif
                        </div>
                        <div><span>
                @if(!empty($det->approvedDate))
                                    {{ \App\helper\Helper::dateFormat($det->approvedDate)}}
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
                <td style="width:33%;font-size: 10px;vertical-align: top;">
                    <span class="white-space-pre-line font-weight-bold">&nbsp;</span>
                </td>
                <td style="width:33%; text-align: center;font-size: 10px;vertical-align: top;">
                    <span style="text-align: center">Page <span class="pagenum"></span></span><br>
                    @if ($assetTransferMaster->company)
                    {{$assetTransferMaster->company->CompanyName}}
                    @endif
                </td>
                <td style="width:33%;font-size: 10px;vertical-align: top;">
                    <span style="margin-left: {{ app()->getLocale() == 'ar' ? '0' : '50%' }}; margin-right: {{ app()->getLocale() == 'ar' ? '50%' : '0' }};">{{ trans('custom.printed_date') }} : {{date("d-M-y", strtotime(now()))}}</span>
                </td>
            </tr>
        </table>
    </div>
    <div id="watermark"></div>
    <div class="card-body content" id="print-section">
        <table style="width: 100%">
            <tr style="width: 100%">
                <td valign="top" style="width: 50%">
                    @if($assetTransferMaster->company)
                    <img src="{{$assetTransferMaster->company->logo_url}}" width="180px" height="60px">
                    @endif
                    <br>
                    <div>
                        <span style="font-size: 18px">
                            {{ trans('custom.asset_transfer') }}
                        </span>
                    </div>
                </td>
                <td valign="top" style="width: 50%">
                    @if($assetTransferMaster->company)
                    <span style="font-size: 24px;font-weight: 400"> {{$assetTransferMaster->company->CompanyName}}</span>
                    @endif
                    <br>
                    <table>
                        <tr>
                            <td width="100px">
                                <span class="font-weight-bold">{{ trans('custom.doc_code') }}</span>
                            </td>
                            <td width="10px">
                                <span class="font-weight-bold">:</span>
                            </td>
                            <td>
                                <span>{{$assetTransferMaster->document_code}} </span>
                            </td>
                        </tr>
                        <tr>
                            <td width="70px">
                                <span class="font-weight-bold">{{ trans('custom.doc_date') }} </span>
                            </td>
                            <td width="10px">
                                <span class="font-weight-bold">:</span>
                            </td>
                            <td>
                                <span>
                                    {{ \App\helper\Helper::dateFormat($assetTransferMaster->document_date)}}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td width="70px">
                                <span class="font-weight-bold">{{ trans('custom.type') }}</span>
                            </td>
                            <td width="10px">
                                <span class="font-weight-bold">:</span>
                            </td>
                            <td>
                                <span>
                                    {{$assetTransferMaster->transfer_type}}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td width="70px">
                                <span class="font-weight-bold">{{ trans('custom.reference_no') }}</span>
                            </td>
                            <td width="10px">
                                <span class="font-weight-bold">:</span>
                            </td>
                            <td>
                                <span>
                                    {{$assetTransferMaster->reference_no}}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td width="70px">
                                <span class="font-weight-bold">{{ trans('custom.narration') }}</span>
                            </td>
                            <td width="10px">
                                <span class="font-weight-bold">:</span>
                            </td>
                            <td>
                                <span>
                                    {{$assetTransferMaster->narration}}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td rowspan="3" colspan="3" style="bottom: 0;position: absolute;">
                            <span class="font-weight-bold">
                                    <h3 class="text-muted">
                                        @if($assetTransferMaster->confirmed_yn == 0 && $assetTransferMaster->approved_yn == 0)
                                        {{ trans('custom.not_confirmed') }}
                                        @elseif($assetTransferMaster->confirmed_yn == 1 && $assetTransferMaster->approved_yn == 0 && $assetTransferMaster->timesReferred == 0)
                                        {{ trans('custom.pending_approval') }}
                                        @elseif($assetTransferMaster->confirmed_yn == 1 && $assetTransferMaster->approved_yn == 0 && $assetTransferMaster->timesReferred > 0)
                                        {{ trans('custom.referred_back') }}
                                        @elseif($assetTransferMaster->confirmed_yn == 1 && ($assetTransferMaster->approved_yn == 1 || $assetTransferMaster->approved_yn == -1))
                                        {{ trans('custom.fully_approved') }}
                                        @endif
                                    </h3>
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <hr style="color: #d3d9df">
        <div style="margin-top: 30px">
            @if($assetTransferMaster->type == 1)
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                    <tr class="theme-tr-head">
                        <th>#</th>
                        <th class="text-center">{{ trans('custom.item_description') }}</th>
                        <th class="text-center">{{ trans('custom.comment') }}</th>
                        <th class="text-center">{{ trans('custom.link_asset') }}</th>
                        <th class="text-center">{{ trans('custom.create_pr') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assetTransferDetail as $item)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">

                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->assetRequestDetail->detail}}</td>
                        <td>{{$item->assetRequestDetail->comment}}</td>
                        <td>
                            @if($item->assetMaster)
                            {{$item->assetMaster->assetCodeConcat}}
                            @endif
                        </td>
                        <td style="text-align: center;">@if($item->pr_created_yn == 1)
                            {{ trans('custom.yes') }}
                            @endif
                            @if($item->pr_created_yn != 1)
                            {{ trans('custom.no') }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            @if($assetTransferMaster->type == 2)
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                    <tr class="theme-tr-head">
                        <th>#</th>
                        <th class="text-center">{{ trans('custom.asset') }}</th>
                        <th class="text-center">{{ trans('custom.from_location') }}</th>
                        <th class="text-center">{{ trans('custom.to_location') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assetTransferDetail as $itemDirect)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$loop->iteration}}</td>
                        <td> {{$itemDirect->assetMaster->assetCodeConcat}}</td>
                        <td> {{($itemDirect->fromLocation) ? $itemDirect->fromLocation->locationName : ''}}</td>
                        <td> {{($itemDirect->toLocation) ? $itemDirect->toLocation->locationName : ''}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            @if($assetTransferMaster->type == 3)
            <table class="table table-bordered" style="width: 100%;">
                <thead>
                    <tr class="theme-tr-head">
                        <th>#</th>
                        <th class="text-center">{{ trans('custom.asset') }}</th>
                        <th class="text-center">{{ trans('custom.from_employee') }}</th>
                        <th class="text-center">{{ trans('custom.to_employee') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assetTransferDetail as $itemDirect)
                    <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                        <td>{{$loop->iteration}}</td>
                        <td> {{$itemDirect->assetMaster->assetCodeConcat}}</td>
                        <td class="text-center"> {{($itemDirect->fromEmployee) ? $itemDirect->fromEmployee->empFullName : ''}}</td>
                        <td class="text-center"> {{($itemDirect->toEmployee) ? $itemDirect->toEmployee->empFullName : ''}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
