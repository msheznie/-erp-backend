<style type="text/css">
    <!--
    @page {
        margin-left: 3%;
        margin-right: 3%;
        margin-top: 4%;
    }

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
        font-weight: 700 !important;
        text-align: center !important;
        font-family: fantasy !important;
    }

    #watermark {
        height: 1000px;
        opacity: 0.6;
        left: 0;
        transform-origin: 20% 20%;
        z-index: 1000;
    }


</style>

<div class="footer">
    <table style="width:100%;">
        <tr>
            <td width="40%"><span class="font-weight-bold">Confirmed By :</span> {{ $grvData->confirmed_by? $grvData->confirmed_by->empFullName:'' }}</td>
            <td><span class="font-weight-bold">Review By :</span> </td>
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            <td><span class="font-weight-bold">Electronically Approved By :</span></td>
        </tr>
        <tr>
            &nbsp;
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            @if ($grvData->approved_by)
                @foreach ($grvData->approved_by as $det)
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
                @if ($grvData->companydocumentattachment_by)
                    <p><span class="font-weight-bold"><span
                                    class="white-space-pre-line">{!! nl2br($grvData->companydocumentattachment_by?$grvData->companydocumentattachment_by[0]->docRefNumber:'') !!}</span></span>
                    </p>
                @endif
            </td>
            <td style="width:33%; text-align: center;font-size: 10px;vertical-align: top;">
                <span style="text-align: center">Page <span class="pagenum"></span></span><br>
                @if ($grvData->company)
                    {{$grvData->company->CompanyName}}
                @endif
            </td>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                <span style="margin-left: 55%;">Printed Date : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
        </tr>
    </table>
</div>
<div id="watermark">
         <span class="watermarkText">
           <h3 class="text-muted">
               @if($grvData->grvConfirmedYN == 0 && $grvData->approved == 0)
                   Not Confirmed & Not Approved <br> Draft Copy
               @endif
               @if($grvData->grvConfirmedYN == 1 && $grvData->approved == 0)
                   Confirmed & Not Approved <br> Draft Copy
               @endif
           </h3>
         </span>
</div>
<div class="content">
    <div class="row">
        <table style="width:100%">
            <tr>
                <td style="text-align: center">
                    <h2>{{ $grvData->company_by?$grvData->company_by->CompanyName:'' }}</h2>
                    <h2>Good Receipt Voucher</h2>
                </td>
            </tr>
        </table>
    </div>
    <div class="row">
        <table style="width:100%">
            <tr>
                <td style="width: 60%">
                    <table style="width: 100%">
                        <tr>
                            <td width="120px"><span class="font-weight-bold">Supplier Code</span></td>
                            <td width="40px"><span class="font-weight-bold">:</span></td>
                            <td><span>{{$grvData->supplierPrimaryCode}}</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-weight-bold">Supplier Name </span></td>
                            <td><span class="font-weight-bold">:</span></td>
                            <td><span>{{$grvData->supplierName}}</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-weight-bold">Doc Ref No </span></td>
                            <td><span class="font-weight-bold">:</span></td>
                            <td><span>{{$grvData->grvDoRefNo}}</span></td>
                        </tr>
                    </table>
                </td>
                <td style="width: 40%">
                    <table style="width:100%">
                        <tr>
                            <td width="120px"><span class="font-weight-bold">Doc Code</span></td>
                            <td width="40px"><span class="font-weight-bold">:</span></td>
                            <td><span>{{$grvData->grvPrimaryCode}}</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-weight-bold">Date </span></td>
                            <td><span class="font-weight-bold">:</span></td>
                            <td><span>{{\Helper::dateFormat($grvData->grvDate)}}</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-weight-bold">Posted Date </span></td>
                            <td><span class="font-weight-bold">:</span></td>
                            <td><span>{{\Helper::dateFormat($grvData->approvedDate)}}</span></td>
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
                            <td width="120px"><span class="font-weight-bold">Location</span></td>
                            <td width="40px"><span class="font-weight-bold">:</span></td>
                            <td><span>{{$grvData->location_by?$grvData->location_by->wareHouseDescription:''}}</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-weight-bold">Recieved By </span></td>
                            <td><span class="font-weight-bold">:</span></td>
                            <td><span>{{$grvData->created_by?$grvData->created_by->empFullName:''}}</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-weight-bold">Comments </span></td>
                            <td><span class="font-weight-bold">:</span></td>
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
                                <td><span class="font-weight-bold">Currency</span></td>
                                <td><span class="font-weight-bold">:</span></td>
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
                <th>Item Code</th>
                <th>Item Description</th>
                <th>Part No/Ref.Number</th>
                <th>Qty</th>
                <th>Unit Cost</th>
                <th>Discount</th>
                <th>Net Amount</th>
            </tr>
            </thead>
            <tbody>
            {{ $discountAmount = 0 }}
            {{ $netAmount = 0 }}
            {{ $x = 1 }}
            @foreach ($grvData->details as $det)
                {{ $discountAmount += $det->discountAmount }}
                {{ $netAmount += $det->netAmount }}
                <tr style="border-bottom: 1px solid black;">
                    <td>{{ $x  }}</td>
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
                <td colspan="6" class="text-right" style="border-bottom-color:white !important;border-left-color:white !important"><span class="font-weight-bold">Total</span></td>
                <td class="text-right"><span *ngIf="grvData.details" class="font-weight-bold">{{ number_format($discountAmount, $grvData->currency_by->DecimalPlaces) }}</span>
                <td class="text-right"><span *ngIf="grvData.details" class="font-weight-bold">{{number_format($netAmount, $grvData->currency_by->DecimalPlaces) }}</span>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
