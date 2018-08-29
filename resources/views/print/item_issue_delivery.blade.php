<html>
<head>
    <title>Item Issue Delivery</title>
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

        h4 {
            font-size: 18px;
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
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
{{--<div class="footer">
    --}}{{--Footer Page <span class="pagenum"></span>--}}{{--
    --}}{{--  <span class="white-space-pre-line font-weight-bold">{!! nl2br($entity->docRefNo) !!}</span>--}}{{--
</div>--}}
<div id="watermark"></div>
<div class="card-body content" id="print-section">
    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 25%;">
                @if($entity->company)
                    <img src="logos/{{$entity->company->companyLogo}}" width="180px" height="60px">
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
    <table class="table table-bordered" style="width: 100%;">
        <thead>
        <tr class="theme-tr-head">
            <th>#</th>
            <th colspan="3">Item</th>
            <th>{{$entity->companyID}}</th>
            <th>Manufacture</th>
            <th>PDO</th>
            <th>Item Description</th>
            <th colspan="3">"Material PO no(enter 13 digits) including PO line
                Item"
            </th>
            <th>GR Number</th>
        </tr>
        <tr class="theme-tr-head">
            <th style="width: 2%"></th>
            <th style="width: 5%">DEL</th>
            <th style="width: 5%">Bacl Load</th>
            <th style="width: 5%">USED</th>
            <th style="width: 5%">Item Code</th>
            <th style="width: 5%">Part No</th>
            <th style="width: 5%">Item SAP Number</th>
            <th style="width: 10%"></th>
            <th style="width: 5%"></th>
            <th style="width: 3%"></th>
            <th style="width: 5%"></th>
            <th style="width: 5%"></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($entity->details as $item)
            <tr style="border-top: 1px solid #ffffff !important;border-bottom: 1px solid #ffffff !important;">
                <td>{{$loop->iteration}}</td>
                <td>{{round($item->qtyIssued,2)}}</td>
                <td>
                    {{$item->backLoad}}
                </td>
                <td>
                    {{$item->used}}
                </td>
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
                <td>
                    {{--<!--{{$item->pl10}}-->--}}
                    {{$entity->purchaseOrderNo}}
                </td>
                <td>
                    /
                </td>
                <td>
                    {{$item->pl3}}
                </td>
                <td>
                    {{$item->grvDocumentNO}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <table style="  margin-top: 10px;">
        <tr width="100%">
            <td width="40%">
                <table width="100%">
                    <tr>
                        <td width="70px">
                            {{-- <span class="font-weight-bold">Issued By :</span>--}}
                        </td>
                        <td>
                            {{-- @if($entity->confirmed_by)
                                 {{$entity->confirmed_by->empName}}
                             @endif--}}
                        </td>
                    </tr>
                </table>
            </td>
            <td width="40%" valign="top">
                <table>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Ticket issued by :</span>
                        </td>
                        <td>
                            <div style="border: 0.5px solid black;width: 300px;margin-top: 7px;height: 25px"></div>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Signature  :</span>
                        </td>
                        <td>
                            <div style="border: 0.5px solid black;width: 300px;margin-top: 7px;height: 25px"></div>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Date :</span>
                        </td>
                        <td>
                            <div style="border: 0.5px solid black;width: 300px;margin-top: 7px;height: 25px"></div>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Checked By :</span>
                        </td>
                        <td>
                            <div style="border: 0.5px solid black;width: 300px;margin-top: 7px;height: 25px"></div>
                        </td>
                    </tr>
                    <tr>
                        <td width="70px">
                            <span class="font-weight-bold">Date :</span>
                        </td>
                        <td>
                            <div style="border: 0.5px solid black;width: 300px;margin-top: 7px;height: 25px"></div>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="20%" valign="top">
               <span style="padding-top:20px"> "Comments: 1. Customer Rep.provide stamp signature with details needed.
                2. Supervisor to verify prior to signature that material purchase
                order nos (including PO line items) are filled out correctly in the
                consumables ticket."
               </span>
            </td>
        </tr>
    </table>
</div>
</body>
</html>