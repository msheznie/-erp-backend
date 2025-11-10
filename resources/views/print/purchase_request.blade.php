<html>
<head>
    <title> {{ __('custom.purchase_request') }} </title>
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
        /* RTL Support for Arabic */
        @if(app()->getLocale() == 'ar')
        body {
            direction: rtl;
            text-align: right;
            font-family: 'Noto Sans Arabic', sans-serif;
        }
        
        .rtl-text-left {
            text-align: right !important;
        }
        
        .rtl-text-right {
            text-align: left !important;
        }
        
        .rtl-float-left {
            float: right !important;
        }
        
        .rtl-float-right {
            float: left !important;
        }
        
        .rtl-margin-left {
            margin-right: 0 !important;
            margin-left: auto !important;
        }
        
        .rtl-margin-right {
            margin-left: 0 !important;
            margin-right: auto !important;
        }
        
        .rtl-padding-left {
            padding-right: 0 !important;
            padding-left: auto !important;
        }
        
        .rtl-padding-right {
            padding-left: 0 !important;
            padding-right: auto !important;
        }
        
        table {
            direction: rtl;
        }
        
        .table th, .table td {
            text-align: right;
        }
        
        .text-right {
            text-align: left !important;
        }
        
        .text-left {
            text-align: right !important;
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
            bottom: -45px;
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

    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td valign="top" style="width: 20%">
                @if($request->company)
                    <img src="{{$request->company->logo_url}}" width="100" class="container">
                @endif
            </td>
            <td valign="top" style="width: 80%">
                
                <span style="font-size: 24px;font-weight: 400"> {{ $request->company?$request->company->CompanyName:'' }}</span>
                <br>
                <span style="font-weight: bold"> {{ $request->company?$request->company->CompanyAddress:'' }}</span>
                <br>
            </td>
        </tr>
    </table>
    <hr style="color: #d3d9df border-top: 2px solid black; height: 2px; color: black">
    
    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td style="text-align: center; font-weight: bold">
                <div>
                    <span style="font-size: 18px">
                        @if($request->documentSystemID == 1)
                        {{ __('custom.purchase_request') }}
                        @endif
                        @if($request->documentSystemID == 50)
                            {{ __('custom.work') }} 
                        @endif
                        @if($request->documentSystemID == 51)
                        {{ __('custom.direct') }}  
                        @endif
                        @if($request->documentSystemID != 1)
                        {{ __('custom.request') }}  
                        @endif
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 50%">
                <table>

    <tr>
        <td width="150px">
            <span style="font-weight:bold; font-size: 16px;">{{ __('custom.pr_title') }}</span>
        </td>
        <td width="10px">
            <span>:</span>
        </td>
        <td>
            <span>{{ $request->prTitle ? $request->prTitle : '-' }}</span>
        </td>
    </tr>

                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">{{ __('custom.request_code') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{ $request->purchaseRequestCode?$request->purchaseRequestCode:'-' }}
                        </td>
                    </tr>

                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">{{ __('custom.request_date') }}</span>
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
                        <td width="150px">
                            <span style="font-weight:bold;">{{ __('custom.request_by') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            @if($request->requestedby)
                                 {{ $request->requestedby->empName }}
                            @else
                                {{ $request->created_by->empName }}
                            @endif
                        </td>
                    </tr>
                    
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">{{ __('custom.reference_no') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{ $request->docRefNo?$request->docRefNo:'-' }}
                        </td>
                    </tr>

                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">{{ __('custom.location_required') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{ $request->location_pdf?$request->location_pdf->locationName:'-' }}
                        </td>
                    </tr>

                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">{{ __('custom.pr_type') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            @php
                                $prTypeLabels = [
                                    1 => __('custom.single_source'),
                                    2 => __('custom.closed_source'),
                                    3 => __('custom.open_source'),
                                    4 => __('custom.negotiated')
                                ];
                            @endphp
                            {{ $request->prType ? ($prTypeLabels[$request->prType] ?? '-') : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">{{ __('custom.justification') }}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{ $request->justification ? $request->justification : '-' }}
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%">
                <table>
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">{{ __('custom.priority') }} </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{ $request->priority_pdf?$request->priority_pdf->priorityDescription:'-' }}
                        </td>
                    </tr>

                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">{{ __('custom.segment') }} </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{ $request->segment?$request->segment->ServiceLineDes:'-' }}
                        </td>
                    </tr>

                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">{{ __('custom.comments') }} </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{ $request->comments?$request->comments:'-' }}
                        </td>
                    </tr>

                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">{{ __('custom.currency') }} </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{ $request->currency_by?$request->currency_by->CurrencyCode:'-' }}
                        </td>
                    </tr>
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">{{ __('custom.buyer') }} </span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{ $request->buyer?$request->buyer->empName:'-' }}
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
                <th class="text-left">{{ __('custom.comments') }}</th>
                <th class="text-left">{{ __('custom.part_number') }}</th>
                <th class="text-left">{{ __('custom.uom') }}</th>
                <th class="text-left">{{ __('custom.qty_requested') }}</th>
                <th class="text-left">{{ __('custom.estimated_unit_cost') }}</th>
                <th class="text-left">{{ __('custom.total') }}</th>
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
                    <td style="padding-left: 5px;">{{$item->comments}}</td>
                    <td style="padding-left: 5px;"> {{$item->partNumber}}</td>
                    <td style="padding-left: 5px;">
                        @if($item->uom)
                            {{$item->uom->UnitShortCode}}
                        @endif
                    </td>
                    <td class="text-right" style="padding-right: 5px;">{{!is_null($item->uom) && !is_null($item->uom->displayRoundOff) ? number_format($item->quantityRequested, $item->uom->displayRoundOff, '.', '') : number_format($item->quantityRequested, 5, '.', '')}}</td>
                    <td class="text-right" style="padding-right: 5px;">{{!is_null($item->uom) && !is_null($item->uom->displayRoundOff) ? number_format($item->estimatedCost, $item->uom->displayRoundOff, '.', '') : number_format($item->estimatedCost, 5, '.', '')}}</td>
                    <td class="text-right" style="padding-right: 5px;">{{!is_null($item->uom) && !is_null($item->uom->displayRoundOff) ? number_format($item->totalCost, $item->uom->displayRoundOff, '.', '') : number_format($item->totalCost, 5, '.', '')}}</td>


                    @if($request->allowAltUom)
                        <td style="padding-left: 5px;">
                            @if(!is_null($item->altUom))
                                {{$item->altUom->UnitShortCode}}
                            @endif
                        </td>
                        <td class="text-right" style="padding-right: 5px;">

                                {{!is_null($item->altUom) && !is_null($item->altUom->displayRoundOff) ? number_format($item->altUnitValue, $item->altUom->displayRoundOff, '.', '') : number_format($item->altUnitValue, 5, '.', '')}}
                        </td>
                    @endif

                    <td class="text-right" style="padding-right: 5px;">{{!is_null($item->uom) && !is_null($item->uom->displayRoundOff) ? number_format($item->quantityOnOrder, $item->uom->displayRoundOff, '.', '') : number_format($item->quantityOnOrder, 5, '.', '')}}</td>
                    @if($request->approved == -1)
                        <td class="text-right" style="padding-right: 5px;">
                            <b>{{!is_null($item->uom) && !is_null($item->uom->displayRoundOff) ? number_format($item->poQuantity, $item->uom->displayRoundOff, '.', '') : number_format($item->poQuantity, 5, '.', '')}} </b>
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
