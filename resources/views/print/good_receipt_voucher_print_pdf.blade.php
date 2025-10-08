<style type="text/css">
    <!--
    @page {
        margin-left: 3%;
        margin-right: 3%;
        margin-top: 4%;
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

    .footer {
        position: absolute;
    }

    body {
        font-size: 11px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"
    }

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
        height: 50px;
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

<div class="content">
    <div class="row">
        <table style="width: 100%" class="table_height">
            <tr style="width: 100%">
                <td valign="top" style="width: 20%">
                    @if($grvData->company_by)
                        <img src="{{$grvData->company_by->logo_url}}" width="100" class="container">
                    @endif
                </td>
                <td valign="top" style="width: 80%">
                    
                    <span style="font-size: 24px;font-weight: 400"> {{ $grvData->company_by?$grvData->company_by->CompanyName:'' }}</span>
                    <br>
                    <table>
                        <tr>
                            <td width="100px">
                                <span style="font-weight: bold">{{ __('custom.doc_code') }}</span>
                            </td>
                            <td width="10px">
                                <span style="font-weight: bold">:</span>
                            </td>
                            <td>
                                <span>{{$grvData->grvPrimaryCode}}</span>
                            </td>
                        </tr>
                        <tr>
                            <td width="70px">
                                <span style="font-weight: bold"> {{ __('custom.doc_date') }}</span>
                            </td>
                            <td width="10px">
                                <span style="font-weight: bold">:</span>
                            </td>
                            <td>
                                <span>
                                    {{ \App\helper\Helper::dateFormat($grvData->grvDate)}}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td width="70px">
                                <span style="font-weight: bold">{{ __('custom.posted_date') }}</span>
                            </td>
                            <td width="10px">
                                <span style="font-weight: bold">:</span>
                            </td>
                            <td>
                                <span>
                                    {{ \App\helper\Helper::dateFormat($grvData->approvedDate)}}
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <hr style="color: #d3d9df border-top: 2px solid black; height: 2px; color: black">
    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td>
                <div>
                    <span style="font-size: 18px">
                        {{ __('custom.good_receipt_voucher') }}
                    </span>
                </div>
            </td>
        </tr>
    </table>
    <br>
    <br>

    <div class="row">
        <table style="width:100%">
            <tr>
                <td style="width: 60%">
                    <table style="width: 100%">
                        <tr>
                            <td width="120px"><span style="font-weight: bold">{{ __('custom.supplier_code') }}</span></td>
                            <td width="40px"><span style="font-weight: bold">:</span></td>
                            <td><span>{{$grvData->supplierPrimaryCode}}</span></td>
                        </tr>
                        <tr>
                            <td><span style="font-weight: bold">{{ __('custom.supplier_name') }}</span></td>
                            <td><span style="font-weight: bold">:</span></td>
                            <td><span>{{$grvData->supplierName}}</span></td>
                        </tr>
                        <tr>
                            <td><span style="font-weight: bold">{{ __('custom.doc_ref_no') }}</span></td>
                            <td><span style="font-weight: bold">:</span></td>
                            <td><span>{{$grvData->grvDoRefNo}}</span></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <div class="row">
        <table style="width:100%">
            <tr>
                <td style="width: 60%">
                    <table style="width:100%">
                        <tr>
                            <td width="120px"><span style="font-weight: bold">{{ __('custom.location') }}</span></td>
                            <td width="40px"><span style="font-weight: bold">:</span></td>
                            <td><span>{{$grvData->location_by?$grvData->location_by->wareHouseDescription:''}}</span></td>
                        </tr>
                        <tr>
                            <td><span style="font-weight: bold"> {{ __('custom.recieved_by') }}</span></td>
                            <td><span style="font-weight: bold">:</span></td>
                            <td><span>{{$grvData->created_by?$grvData->created_by->empFullName:''}}</span></td>
                        </tr>
                        <tr>
                            <td><span style="font-weight: bold"> {{ __('custom.comments') }}</span></td>
                            <td><span style="font-weight: bold">:</span></td>
                            <td><span>{{ $grvData->grvNarration }}</span></td>
                        </tr>
                    </table>
                </td>
                <td style="width: 40%">
                    <div style="float: right">
                        <table>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td><span style="font-weight: bold">{{ __('custom.currency') }}</span></td>
                                <td><span style="font-weight: bold">:</span></td>
                                <td valign="bottom">{{$grvData->currency_by?$grvData->currency_by->CurrencyCode:'' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <div class="row">
        <table style="width:100%;" class="table table-bordered table-striped table-sm">
            <thead>
            <tr style="border-top: 1px solid black;">
                <th>#</th>
                @if ($grvData->grvTypeID == 2)
                    <th>{{ __('custom.order_code') }}</th>
                @endif
                <th>{{ __('custom.item_code') }}</th>
                <th>{{ __('custom.item_description') }}</th>
                <th>{{ __('custom.manufacture_part_no') }}</th>
                <th>{{ __('custom.qty') }}</th>
                <th>{{ __('custom.unit_cost') }}</th>
                <th>{{ __('custom.discount') }}</th>
                <th>{{ __('custom.net_amount') }}</th>
            </tr>
            </thead>
            <tbody>
            {{ $discountAmount = 0 }}
            {{ $netAmount = 0 }}
            {{ $x = 1 }}
            @foreach ($grvData->details as $det)
                {{ $discountAmount += $det->discountAmount }}
                {{ $netAmount += $det->netAmount }}
                <tr style="border-bottom: 1px solid black; background-color: rgb(251, 251, 251);">
                    <td>{{ $x  }}</td>
                    @if ($grvData->grvTypeID == 2)
                        <td>{{$det->po_master->purchaseOrderCode ? $det->po_master->purchaseOrderCode : ""}}</td>
                    @endif
                    <td>{{$det->itemPrimaryCode}}</td>
                    <td>{{$det->itemDescription}}</td>
                    <td>{{$det->supplierPartNumber}}</td>
                    <td class="text-right">{{$det->noQty}}</td>
                    <td class="text-right">{{number_format($det->unitCost, $grvData->currency_by->DecimalPlaces)}}</td>
                    <td class="text-right">{{number_format($det->discountAmount, $grvData->currency_by->DecimalPlaces)}}</td>
                    <td class="text-right">{{number_format($det->netAmount, $grvData->currency_by->DecimalPlaces)}}</td>
                </tr>
                {{ $x++ }}
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                @if ($grvData->grvTypeID == 2)
                    <td style="border-bottom-color:white !important;border-left-color:white !important; border-right-color:white !important"></td>
                @endif
                <td colspan="6" class="text-right" style="border-bottom-color:white !important;border-left-color:white !important"><span style="font-weight: bold">{{ __('custom.total') }}</span></td>
                <td class="text-right" style="border: 1px solid black;"><span *ngIf="grvData.details" style="font-weight: bold">{{ number_format($discountAmount, $grvData->currency_by->DecimalPlaces) }}</span></td>
                <td class="text-right" style="border: 1px solid black;"><span *ngIf="grvData.details" style="font-weight: bold">{{number_format($netAmount, $grvData->currency_by->DecimalPlaces) }}</span></td>
            </tr>
            </tfoot>
        </table>

        @if (($grvData->grvConfirmedYN == 1 || $grvData->grvConfirmedYN == 0) && $grvData->approved == 0)
        <table style="  height: 1000px; 
                    opacity: 0.6; 
                    left: 0; 
                    transform-origin: 20% 20%; 
                    z-index: 1000;
                    position: fixed;
                    width: 100%;
                    height: 100%;
                    padding-top: 31%; margin-bottom: -10%;">
                <tr>
                    <td width="20%">

                    </td>
                    <td width="60%" style="text-align: center; font-weight: bold !important;">
                        <span class="watermarkText" style="font-weight: bold; ">
                            <h3 style=" font-size: 24.5px;
                                        margin-bottom: 0.1rem;
                                        font-weight: 500;
                                        line-height: 1.2;
                                        color: inherit;">
                                @if($grvData->grvConfirmedYN == 0 && $grvData->approved == 0)
                                    Not Confirmed & Not Approved <br> Draft Copy
                                @endif
                                @if($grvData->grvConfirmedYN == 1 && $grvData->approved == 0)
                                    Confirmed & Not Approved <br> Draft Copy
                                @endif
                            </h3>
                        </span>
                    </td>
                    <td width="20%">

                    </td>
                </tr>
            </table>
        @endif

    </div>
</div>
