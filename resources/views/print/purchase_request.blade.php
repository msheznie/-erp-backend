<html>
<head>
    <title>Purchase Request</title>
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
        #watermark { position: fixed; bottom: 0px; right: 0px; width: 200px; height: 200px; opacity: .1; }
        .content {
            margin-bottom: 45px;
        }
    </style>
</head>
<body>
<div id="watermark"></div>
<div class="card-body content" id="print-section">

    <table>
        <tr style="width: 100%">
            <td colspan="3">
                @if($request->company)
                    <h6> {{$request->company->CompanyName}}</h6>
                @endif
            </td>
        </tr>
        <tr style="width: 100%">
            <td colspan="3">
                @if($request->company)
                    <h6>{{$request->company->CompanyAddress}}</h6>
                @endif
            </td>
        </tr>
    </table>

    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 30%">
                <table>
                    <tr>
                        <td width="50px">
                            <span style="font-weight:bold;">{{ __('custom.priority') }} </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            @if($request->priority_pdf)
                                {{$request->priority_pdf->priorityDescription}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight:bold;">{{ __('custom.requisitioner') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            @if($request->created_by)
                                {{$request->created_by->empName}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="50px">
                            <span style="font-weight:bold;">{{ __('custom.location') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            @if($request->location_pdf)
                                {{$request->location_pdf->locationName}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight:bold;">{{ __('custom.comments') }} </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            <span>{{$request->comments}}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%;text-align: center">
                <h3>
                    @if($request->documentSystemID == 1)
                        {{ __('custom.purchase') }}
                    @endif
                    @if($request->documentSystemID == 50)
                        {{ __('custom.work') }} 
                    @endif
                    @if($request->documentSystemID == 51)
                    {{ __('custom.direct') }}  
                    @endif
                    {{ __('custom.requisition') }}  
                </h3>
            </td>
            <td style="width: 30%">
                <table valign="top">
                    <tr>
                        <td width="90px">
                            <span style="font-weight:bold;">{{ __('custom.document_no') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            <span>{{$request->purchaseRequestCode}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="90px">
                            <span style="font-weight:bold;">{{ __('custom.date') }} </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            <span>
                                {{ \App\helper\Helper::dateFormat($request->createdDateTime)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="3" colspan="3" style="bottom: 0;position: absolute;">
                                <span style="font-weight:bold;">
                                    <h3 class="text-muted">
                                        @if($request->cancelledYN == -1)
                                        {{ __('custom.cancelled') }}
                                        @elseif($request->PRConfirmedYN == 0 && $request->approved == 0)
                                        {{ __('custom.not_confirmed') }}
                                        @elseif($request->PRConfirmedYN == 1 && $request->approved == 0  && $request->timesReferred == 0)
                                        {{ __('custom.pending_approval') }} 
                                        @elseif($request->PRConfirmedYN == 1 && $request->approved == 0 && $request->timesReferred > 0)
                                        {{ __('custom.referred_back') }}
                                        @elseif($request->PRConfirmedYN == 1 && ($request->approved == 1 || $request->approved == -1))
                                        {{ __('custom.fully_approved') }}
                                        @endif
                                        </h3>
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
                <th class="text-left">{{ __('custom.item_code') }}</th>
                <th class="text-left">{{ __('custom.item_description') }}</th>
                <th class="text-left">{{ __('custom.part_number') }}</th>
                <th class="text-left">{{ __('custom.uom') }}</th>
                <th class="text-left">{{ __('custom.qty_requested') }}</th>
                 @if($request->allowAltUom)
                <th class="text-left">{{ __('custom.alt_uom') }}</th>
                <th class="text-left">{{ __('custom.alt_qnty') }}</th>
                @endif
                <th class="text-left">{{ __('custom.qty_on_order') }}</th>

                @if($request->approved == -1)
                    <th class="text-left">{{ __('custom.po_qty') }}</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @foreach ($request->details as $item)
                <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                    <td style="padding-left: 5px;">{{$loop->iteration}}</td>
                    <td style="padding-left: 5px;">{{$item->itemPrimaryCode}}</td>
                    <td style="padding-left: 5px;">{{$item->itemDescription}}</td>
                    <td style="padding-left: 5px;"> {{$item->partNumber}}</td>
                    <td style="padding-left: 5px;">
                        @if($item->uom)
                            {{$item->uom->UnitShortCode}}
                        @endif
                    </td>
                    <td class="text-right" style="padding-right: 5px;">{{$item->quantityRequested}}</td>
                    @if($request->allowAltUom)
                        <td style="padding-left: 5px;">
                            @if($item->altUom)
                                {{$item->altUom->UnitShortCode}}
                            @endif
                        </td>
                        <td class="text-right" style="padding-right: 5px;">
                                {{$item->altUnitValue}}
                        </td>
                    @endif    
                    <td class="text-right" style="padding-right: 5px;">{{$item->quantityOnOrder}}</td>
                    @if($request->approved == -1)
                        <td class="text-right" style="padding-right: 5px;">
                            <b>{{$item->poQuantity}} </b>
                        </td>
                    @endif
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
                            <td width="110px">
                                <span style="font-weight:bold;">{{ __('custom.confirmed_by') }} :</span>
                            </td>
                            <td width="400px">
                                @if($request->confirmed_by)
                                    {{$request->confirmed_by->empName}}
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
                            <td width="100px">
                                <span style="font-weight:bold;">{{ __('custom.reviewed_by') }} :</span>
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
        <span style="font-weight:bold;">{{ __('custom.electronically_approved_by') }} :</span>
    </div>
    <div style="margin-top: 10px">
        <table>
            <tr>
                @foreach ($request->approved_by as $det)
                    <td style="padding-right: 25px" class="text-center">
                        @if($det->employee)
                            {{$det->employee->empFullName }}
                        @endif
                        <br><br>
                        @if($det->employee)
                          {{ \App\helper\Helper::dateFormat($det->approvedDate)}}
                        @endif
                    </td>
                @endforeach
            </tr>
        </table>
    </div>
</div>
</body>
</html>
