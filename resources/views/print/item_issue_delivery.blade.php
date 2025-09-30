<html>
<head>
    <title>{{ __('custom.item_issue_delivery') }}</title>
    <style>
        @page {
            margin-left: 2%;
            margin-right: 3%;
            margin-top: 280px;
        }

        /* RTL Support for Arabic */
        @if(app()->getLocale() == 'ar')
        body {
            direction: rtl;
            text-align: right;
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

        body {
            font-size: 11px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }

        h3 {
            font-size: 24px;
        }

        h6 {
            font-size: 14px;
        }

        h4 {
            font-size: 16px;
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
            font-size: 11px;
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
            vertical-align: top;
            border-bottom: 2px solid #c2cfd6;
        }

        table.table-bordered {
            border: 0.5px solid #e2e3e5;
        }

        .table th, .table td {
            padding: 5px !important;
        }

        table.table-bordered {
            border-collapse: collapse;
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #f1f1f1;
        }

        table.table-bordered, .table-bordered th, .table-bordered td {
            border: 0.5px solid #EEEEEE;
        }

        table > thead > tr > th {
            font-size: 11px;
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
            height: 250px;
        }

        .header {
            top: -260px;
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
            margin-top: 0px;
        }

        .input-box {
            border: 0.5px solid #f4f4f4;
            width: 300px;
            height: 25px
        }
        thead .vertical-align {
            vertical-align : middle !important;
        }
        .container
            {
                display: block;
                max-width:230px;
                max-height:95px;
                width: auto;
                height: auto;
            }
    </style>
</head>
<body>
{{--<div class="footer">
    --}}{{--Footer Page <span class="pagenum"></span>--}}{{--
    --}}{{--  <span class="white-space-pre-line font-weight-bold">{!! nl2br($entity->docRefNo) !!}</span>--}}{{--
</div>--}}
{{--<div id="watermark"></div>--}}

<div >
    <table style="width: 100%">
        <tr style="width:100%">
            <td valign="top" style="width: 10%;">
                @if($entity->company)
                    <img src="{{$entity->company->logo_url}}" width="180px" height="60px" class="container">
                @endif
            </td>
            <td style="width: 45%" valign="top">
                <table>
                    <tr>
                        <td width="150px">
                            <span style="font-weight: bold;">{{__('custom.material_issue_no')}}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            {{$entity->itemIssueCode}}
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold;">{{__('custom.network_no')}}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            {{$entity->networkNo}}
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span style="font-weight: bold;">{{__('custom.contract_no')}}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>{{$entity->contractID}}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 45%">
                <table>
                    <tr>
                        <td width="100px">
                            <span style="font-weight: bold;">{{__('custom.work_order_no')}}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>{{$entity->workOrderNo}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="100px">
                            <span style="font-weight: bold;">{{__('custom.well_no')}}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>
                                {{ $entity->wellNO}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="100px">
                            <span style="font-weight: bold;">{{__('custom.rig_no')}}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>
                                {{ $entity->fieldName}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="100px">
                            <span style="font-weight: bold;">{{__('custom.customer_name')}}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>
                                @if($entity->customer_by)
                                    {{$entity->customer_by->CustomerName}}
                                @endif
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="150px">
                            <span style="font-weight: bold;">{{__('custom.item_delivered_on_site_date')}}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>
                              {{ \App\helper\Helper::dateFormat($entity->itemDeliveredOnSiteDate)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="100px">
                            <span style="font-weight: bold;">{{__('custom.purchase_order_no_#')}}</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight: bold;">:</span>
                        </td>
                        <td>
                            <span>
                                {{ $entity->purchaseOrderNo}}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table style="width: 100%">
        <tr style="width: 100%">
            <td colspan="3" class="text-center" style="text-align: center;">
                <h4 style="text-decoration: underline;">{{__('custom.delivery_ticket')}}</h4>
            </td>
        </tr>
    </table>
</div>
<div class="content">
    <div class="row">
        <table class="table table-bordered table-striped" style="width: 100%; border-style: solid; border-width: 0.5px; border-color:#f4f4f4;">
            <thead>
            <tr style="background-color: #ffffff !important; border-color:#000">
                <th  style="width: 2%; vertical-align : middle !important; text-align:center;">{{__('custom.item')}}</th>
                <th  style="width: 3%; vertical-align : middle !important; text-align:center;">{{__('custom.delivered_quantity')}}</th>
                <th  style="width: 4%; vertical-align : middle !important; text-align:center;">{{__('custom.back_load_quantity')}}</th>
                <th  style="width: 5%; vertical-align : middle !important; text-align:center;">{{$entity->companyID}} {{__('custom.item_code')}}</th>
                <th  style="width: 5%; vertical-align : middle !important; text-align:center;">{{__('custom.manufacture_part_no')}}</th>
                <th  style="width: 5%; vertical-align : middle !important; text-align:center;">{{__('custom.client_product_code')}}</th>
                <th  style="width: 14%; vertical-align : middle !important; text-align:center;">{{__('custom.item_description')}}</th>
                <th   colspan="2" style="width: 5%; vertical-align : middle !important; text-align:center;"> "{{__('custom.material_po_no_enter_13_digits_including_po_line_item')}}"
                </th>
                <th  style="width: 4%; vertical-align : middle !important; text-align:center;">{{__('custom.work_order_number')}}</th>
                <th  style="width: 6%; vertical-align : middle !important; text-align:center;">{{__('custom.batch_serial_number')}}</th>
                <th  style="width: 7%; vertical-align : middle !important; text-align:center;">{{__('custom.comments')}}</th>
            </tr>
            {{--<tr class="theme-tr-head">
                <th class="text-center" style="width: 1%"></th>
                <th class="text-center" style="width: 4%"></th>
            </tr>--}}
            </thead>
            <tbody>
            @foreach ($entity->details as $item)
                <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                    <td style="padding-left: 5px;">{{$loop->iteration}}</td>
                    <td style="padding-left: 5px;">{{round($item->qtyIssued,2)}}</td>
                    <td style="padding-left: 5px;">
                        {{$item->backLoad}}
                    </td>
                    {{--<td>
                        {{$item->used}}
                    </td>--}}
                    <td style="padding-left: 5px;">
                        {{$item->itemPrimaryCode}}
                    </td>
                    <td style="padding-left: 5px;">
                        @if($item->item_by)
                            {{$item->item_by->secondaryItemCode}}
                        @endif
                    </td>
                    <td style="padding-left: 5px;">
                        {{$item->clientReferenceNumber}}
                    </td>
                    <td style="padding-left: 5px;">
                        {{$item->itemDescription}}
                    </td>
                    <td style="width: 5%; padding-left: 5px;">
                        {{--{{$entity->purchaseOrderNo}}--}}
                        {{$item->p1}}
                    </td>
                    <td style="width: 2%; padding-left: 5px;">
                        {{$item->pl10}}
                    </td>
                    <td style="padding-left: 5px;">
                        {{$item->pl3}}
                    </td>
                    <td style="padding-left: 5px;">
                        {{$item->grvDocumentNO}}
                    </td>
                    <td style="padding-left: 5px;">
                        {{$item->deliveryComments}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="row"  style="padding-top:30px">
        <table width="100%">
            <tr width="100%">
                <td width="40%">
                    <table width="100%">
                        <tr>   &nbsp;</tr>
                    </table>
                </td>
                <td width="40%" valign="top">
                    <table width="100%">
                        <tr>
                            <td width="100px" valign="top">
                                <span style="font-weight: bold;">{{__('custom.ticket_issued_by')}} :</span>
                            </td>
                            <td valign="top" style="border-style: solid; border-width: 0.5px; border-color:#f4f4f4;">
                                <div class="input-box"></div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="20%" valign="top">
                       <span> "{{__('custom.comments_para_material_issue_delivery_print1')}}
                       </span>
                </td>
            </tr>
        </table>
    </div>
    <div class="row"  style="padding-top:30px">
        <table width="100%" style="margin-top: -3%">
            <tr width="100%">
                <td width="40%">
                    &nbsp;
                </td>
                <td width="40%" valign="top">
                    <table width="100%">
                        <tr>
                            <td width="100px" valign="top">
                                <span style="font-weight: bold;">{{__('custom.signature')}} :</span>
                            </td>
                            <td valign="top"  style="border-style: solid; border-width: 0.5px; border-color:#f4f4f4;">
                                <div class="input-box"></div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="20%" valign="top">
                       <span>
                        {{__('custom.comments_para_material_issue_delivery_print2')}}
                       </span>
                </td>
            </tr>
        </table>
    </div>
    <div class="row"  style="padding-top:30px">
        <table width="100%" style="margin-top: -3%">
            <tr width="100%" >
                <td width="40%">
                    <table width="100%">
                        <tr>  &nbsp;</tr>
                    </table>
                </td>
                <td width="40%" valign="top">
                    <table width="100%">
                        <tr>
                            <td width="100px">
                                <span style="font-weight: bold;">{{__('custom.date')}} :</span>
                            </td>
                            <td  style="border-style: solid; border-width: 0.5px; border-color:#f4f4f4;">
                                <div class="input-box"></div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="20%" valign="top">
                    <span>
                        {{__('custom.comments_para_material_issue_delivery_print3')}}"
                       </span>
                </td>
            </tr>
        </table>
    </div>
    <div class="row"  style="padding-top:30px">
        <table width="100%" style="margin-top: -3%">
            <tr width="100%">
                <td width="40%">
                    <table width="100%">
                        <tr>   &nbsp;</tr>
                    </table>
                </td>
                <td width="40%" valign="top">
                    <table width="100%">
                        <tr>
                            <td width="100px">
                                <span style="font-weight: bold;">{{__('custom.checked_by')}} :</span>
                            </td>
                            <td  style="border-style: solid; border-width: 0.5px; border-color:#f4f4f4;">
                                <div class="input-box"></div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="20%" valign="top">
                </td>
            </tr>
        </table>
    </div>
    <div class="row"  style="padding-top:30px">
        <table width="100%" style="margin-top: -3%">
            <tr width="100%">
                <td width="40%">
                    <table width="100%">
                        <tr>  &nbsp;</tr>
                    </table>
                </td>
                <td width="40%" valign="top">
                    <table width="100%">
                        <tr>
                            <td width="100px">
                                <span style="font-weight: bold;">{{__('custom.date')}} :</span>
                            </td>
                            <td  style="border-style: solid; border-width: 0.5px; border-color:#f4f4f4;">
                                <div class="input-box"></div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="20%" valign="top">
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
