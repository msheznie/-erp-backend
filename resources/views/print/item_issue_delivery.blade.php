<html>
<head>
    <title>Item Issue Delivery</title>
    <style>
        @page {
            margin-left: 2%;
            margin-right: 3%;
            margin-top: 280px;
        }

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
    </style>
</head>
<body>
{{--<div class="footer">
    --}}{{--Footer Page <span class="pagenum"></span>--}}{{--
    --}}{{--  <span class="white-space-pre-line font-weight-bold">{!! nl2br($entity->docRefNo) !!}</span>--}}{{--
</div>--}}
{{--<div id="watermark"></div>--}}

<div class="header">
    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 25%;">
                @if($entity->company)
                    <img src="{{$entity->company->logo_url}}" width="180px" height="60px">
                @endif
            </td>
            <td style="width: 35%" valign="top">
                <table>
                    <tr>
                        <td width="100px">
                            <span class="font-weight-bold">Material Issue No</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            {{$entity->itemIssueCode}}
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Network No</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            {{$entity->networkNo}}
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Contract No</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>{{$entity->contractID}}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%">
                <table>
                    <tr>
                        <td width="100px">
                            <span class="font-weight-bold">Work Order No</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>{{$entity->workOrderNo}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Well No </span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>
                                {{ $entity->wellNO}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Rig No </span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>
                                {{ $entity->fieldName}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Customer Name </span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
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
                        <td width="70px">
                            <span class="font-weight-bold">Item delivered on site date </span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>
                              {{ \App\helper\Helper::dateFormat($entity->itemDeliveredOnSiteDate)}}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Purchase Order #</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
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
            <td colspan="3" class="text-center">
                <h4 style="text-decoration: underline;">Delivery Ticket</h4>
            </td>
        </tr>
    </table>
</div>
<div class="content">
    <div class="row">
        <table class="table table-bordered table-striped" style="width: 100%;">
            <thead>
            <tr class="theme-tr-head">
                <th class="text-center vertical-align"  style="width: 2%">Item</th>
                <th class="text-center vertical-align"  style="width: 3%">Quantity Delivered</th>
                <th class="text-center vertical-align"  style="width: 4%">Quantity Back Load</th>
                <th class="text-center vertical-align"  style="width: 5%">{{$entity->companyID}} Item Code</th>
                <th class="text-center vertical-align"  style="width: 5%">Part No / Ref.Number</th>
                <th class="text-center vertical-align"  style="width: 5%">Client Product Code</th>
                <th class="text-center vertical-align"  style="width: 14%">Item Description</th>
                <th class="text-center vertical-align"   colspan="2" style="width: 5%">"Material PO no(enter 13 digits) including PO line
                    Item"
                </th>
                <th class="text-center vertical-align"  style="width: 4%">Work Order Number</th>
                <th class="text-center vertical-align"  style="width: 6%">Batch / Serial Number</th>
                <th class="text-center vertical-align"  style="width: 7%">Comments</th>
            </tr>
            {{--<tr class="theme-tr-head">
                <th class="text-center" style="width: 1%"></th>
                <th class="text-center" style="width: 4%"></th>
            </tr>--}}
            </thead>
            <tbody>
            @foreach ($entity->details as $item)
                <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                    <td>{{$loop->iteration}}</td>
                    <td>{{round($item->qtyIssued,2)}}</td>
                    <td>
                        {{$item->backLoad}}
                    </td>
                    {{--<td>
                        {{$item->used}}
                    </td>--}}
                    <td>
                        {{$item->itemPrimaryCode}}
                    </td>
                    <td>
                        @if($item->item_by)
                            {{$item->item_by->secondaryItemCode}}
                        @endif
                    </td>
                    <td>
                        {{$item->clientReferenceNumber}}
                    </td>
                    <td>
                        {{$item->itemDescription}}
                    </td>
                    <td style="width: 5%">
                        {{--{{$entity->purchaseOrderNo}}--}}
                        {{$item->p1}}
                    </td>
                    <td style="width: 2%">
                        {{$item->pl10}}
                    </td>
                    <td>
                        {{$item->pl3}}
                    </td>
                    <td>
                        {{$item->grvDocumentNO}}
                    </td>
                    <td>
                        {{$item->deliveryComments}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="row">
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
                                <span class="font-weight-bold">Ticket issued by :</span>
                            </td>
                            <td valign="top">
                                <div class="input-box"></div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="20%" valign="top">
                       <span style="padding-top:20px"> "Comments: 1. Customer Rep.provide stamp signature with details needed.
                       </span>
                </td>
            </tr>
        </table>
    </div>
    <div class="row">
        <table width="100%" style="margin-top: -3%">
            <tr width="100%">
                <td width="40%">
                    &nbsp;
                </td>
                <td width="40%" valign="top">
                    <table width="100%">
                        <tr>
                            <td width="100px" valign="top">
                                <span class="font-weight-bold">Signature :</span>
                            </td>
                            <td valign="top">
                                <div class="input-box"></div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="20%" valign="top">
                       <span style="padding-top:20px">
                        2. Supervisor to verify prior to signature that material purchase
                        order nos
                       </span>
                </td>
            </tr>
        </table>
    </div>
    <div class="row">
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
                                <span class="font-weight-bold">Date :</span>
                            </td>
                            <td>
                                <div class="input-box"></div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="20%" valign="top">
                    <span style="padding-top:20px">
                        (including PO line items) are filled out correctly in the
                        consumables ticket."
                       </span>
                </td>
            </tr>
        </table>
    </div>
    <div class="row">
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
                                <span class="font-weight-bold">Checked By :</span>
                            </td>
                            <td>
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
    <div class="row">
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
                                <span class="font-weight-bold">Date :</span>
                            </td>
                            <td>
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
