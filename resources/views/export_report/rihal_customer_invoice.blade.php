<html>
<center>
    <tr>
       <td colspan="6"> </td>
        <td colspan="8">    
            <span style="font-size:35px;"> Tax Invoice</span>
        </td>

    <tr>

</center>

<br>
<br>
    <tr>
    <td colspan="10"> {{$request->CompanyName}}<br>{{$request->CompanyAddress}}</td>
    <td colspan="8"> Invoice No : {{$request->bookingInvCode}}</td>
    </tr>
    <tr>
    <td colspan="10"> Tel:  {{$request->CompanyTelephone}}</td>
    <td colspan="8"> Invoice Date :  @if(!empty($request->bookingDate))
                                    {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                @endif</td>
    </tr>
    <tr>
    <td colspan="10"> Fax: {{$request->CompanyFax}}</td>
    <td colspan="8"> Name Of Customer : :@if($request->line_customerShortCode)
                                    {{$request->customer->CutomerCode}} -
                                @endif
                                {{$request->customer->ReportTitle}}</td>
    </tr>
    <tr>
    <td colspan="10"> <b>VAT NO: {{$request->vatRegistratonNumber}}</b></td>
    </tr>
    
    <br>
    <br>

    <div class="row">
    {{$directTraSubTotal=0}}
    {{$numberFormatting=0}}
@if($request->linePdoinvoiceDetails)

    <table class="table table-bordered table-striped table-sm" style="width: 100%;">
        <thead>
        <tr class="">

            <th colspan="3" style="text-align: center">Well</th>
            <th colspan="3" style="text-align: center">Network</th>
            <th colspan="3" style="text-align: center">SE</th>
            <th colspan="3" style="text-align: right">Amount({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
        </tr>
        </thead>
        <tbody>
        {{$decimal = 2}}
        {{$x=1}}
        {{$directTraSubTotal=0}}
        {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
        @foreach ($request->linePdoinvoiceDetails as $item)
            {{$directTraSubTotal +=$item->wellAmount}}
            <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;background-color: white">

                <td colspan="3" style="width: 25%">{{$item->wellNo}}</td>
                <td colspan="3" style="width: 25%">{{$item->netWorkNo}}</td>
                <td colspan="3" style="width: 25%">{{$item->SEno}}</td>
                <td colspa n="3" style="width: 25%;text-align: right">{{number_format($item->wellAmount,$numberFormatting)}}</td>

            </tr>
            {{ $x++ }}
        @endforeach
        </tbody>

    </table>
@endif

@if($request->line_invoiceDetails)
    <table class="table table-bordered table-striped table-sm" style="width: 100%;">
        <thead>
        <tr class="">
            <th colspan="1"  style="width:1%"></th>
            <th colspan="2" style="text-align: center">Client Ref</th>
            @if($request->is_po_in_line)
                <th colspan="2" style="text-align: center">PO Line Item</th>
            @endif
            <th colspan="2" style="text-align: center">Details</th>
            <th colspan="2" style="text-align: center">Qty</th>
            <th colspan="2" style="text-align: center">Unit Rate</th>
            <th colspan="2" style="text-align: right">Amount ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
        </tr>
        </thead>
        <tbody>
        {{$decimal = 2}}
        {{$x=1}}
        {{$directTraSubTotal=0}}
        {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
        @foreach ($request->line_invoiceDetails as $item)
            {{$directTraSubTotal +=$item->amount}}
            <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;background-color: white">
                <td colspan="1" >{{$x}}</td>
                <td colspan="2" style="width: 12%">{{$item->ClientRef}}</td>
                @if($request->is_po_in_line)
                    <td colspan="2" style="width: 12%">{{$item->pl3}}</td>
                @endif
                <td colspan="2" >{{$item->assetDescription}}</td>
                <td scolspan="2" tyle="width: 8%;text-align: center">{{number_format($item->qty,2)}}</td>
                <td colspan="2" style="width: 10%;text-align: right">{{number_format($item->rate,$numberFormatting)}}</td>

                <td colspan="2" style="width: 10%"
                    class="text-right">{{number_format($item->amount,$numberFormatting)}}</td>
            </tr>
            {{ $x++ }}
        @endforeach
        </tbody>

    </table>

@endif

@if ($request->template==1 && !$request->line_invoiceDetails && !$request->linePdoinvoiceDetails)

    <table class="table table-bordered table-striped table-sm" style="width: 100%;">
        <thead>
        <tr class="theme-tr-head">
            <th colspan="1" style="width:1%"></th>
            <th colspan="12" style=" text-align: center">Details</th>


            <th style="width:140px;text-align: right">Amount ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
        </tr>
        </thead>

        <tbody>
        {{$decimal = 2}}
        {{$x=1}}
        {{$directTraSubTotal=0}}
        {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}


        @foreach ($request->temp as $item)

            {{$directTraSubTotal +=$item->sumofsumofStandbyAmount}}
            <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                <td colspan="1">{{$x}}</td>
                <td colspan="12">{{$item->myStdTitle}}</td>


                <td style="width: 100px"
                    class="text-right">{{number_format($item->sumofsumofStandbyAmount,$numberFormatting)}}</td>
            </tr>
            {{ $x++ }}
        @endforeach
        </tbody>

    </table>
@endif

@if ($request->template <> 1 && !$request->line_invoiceDetails && isset($request->invoicedetails) && sizeof($request->invoicedetails) > 0)
    <table class="table table-bordered" style="width: 100%;">
        <thead>
        <tr class="theme-tr-head">
            <th colspan="1"></th>
            <th colspan="2">GL Code</th>
            <th colspan="4">GL Code Description</th>
            <th colspan="2">QTY</th>
            <th colspan="2">Unit Rate</th>
            <th colspan="2">VAT Per Unit</th>  
            <th colspan="2">Amount ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
        </tr>
        </thead>
        <tbody>
        {{$decimal = 2}}
        {{$x=1}}
        {{$directTraSubTotal=0}}
        {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
        @foreach ($request->invoicedetails as $item)
            {{$directTraSubTotal +=$item->invoiceAmount}}
            <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                <td colspan="1">{{$x}}</td>
                <td colspan="2">{{$item->glCode}}</td>
                <td colspan="4">{{$item->glCodeDes}}</td>
                <td colspan="2" class="text-center" style="text-align: center">{{number_format($item->invoiceQty,2)}}</td>
                <td colspan="2" class="text-right">{{number_format($item->unitCost,$numberFormatting)}}</td>
                <td colspan="2" class="text-right">{{number_format($item->VATAmountLocal,$numberFormatting)}}</td>
                <td colspan="2" class="text-right">{{number_format($item->invoiceAmount,$numberFormatting)}}</td>
            </tr>
            {{ $x++ }}
        @endforeach
        </tbody>

    </table>
@endif

@if ($request->template == 2 && isset($request->item_invoice) && $request->item_invoice)
    <table class="table table-bordered" style="width: 100%;">
        <thead>
        <tr class="theme-tr-head">
            <th colspan="1" style="width:3%"></th>
            <th colspan="2" style="width:60%;text-align: center">Description</th>
            <th colspan="2" style="width:30%;text-align: center">Part No / Ref.Number</th>
            <th colspan="2" style="width:10%;text-align: center">Quantity</th>
            <th colspan="2" style="width:10%;text-align: center">Unit Price</th>
            <th colspan="2" style="width:10%;text-align: right">Taxable Amount ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
            <th colspan="2" style="width:10%;text-align: right">Taxable Rate</th>
            <th colspan="2" style="width:10%;text-align: right">Tax ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
            <th colspan="2" style="width:10%;text-align: right">Total Amount ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
        </tr>
        </thead>
        <tbody>
        {{$decimal = 2}}
        {{$x=1}}
        {{$directTraSubTotal=0}}
        {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
        @if(!empty($request->issue_item_details))
            @foreach ($request->issue_item_details as $item)
                @if ($item->sellingTotal != 0)
                    {{$directTraSubTotal +=$item->sellingTotal}}
                    <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                        <td colspan="1" >{{$x}}</td>
                        <td colspan="2" >{{$item->itemPrimaryCode.' - '.$item->itemDescription}}</td>
                        <td colspan="2" style="text-align: center">{{$item->part_no}}</td>
                    <!-- <td>{{isset($item->uom_issuing->UnitShortCode)?$item->uom_issuing->UnitShortCode:''}}</td> -->
                        <td colspan="2" class="text-center" style="text-align: center">{{$item->qtyIssued}}</td>
                        <td colspan="2" class="text-right">{{number_format($item->sellingCostAfterMargin,$numberFormatting)}}</td>
                        <td colspan="2"  class="text-right">{{number_format($item->VATAmount,$numberFormatting)}}</td>
                        <td colspan="2"  class="text-right">{{$item->VATPercentage}}</td>
                        <td colspan="2"  class="text-right">{{number_format(($item->VATAmount * $item->qtyIssued),$numberFormatting)}}</td>
                        <td colspan="2" class="text-right">{{number_format($item->sellingTotal,$numberFormatting)}}</td>
                    </tr>
                    {{ $x++ }}
                @endif
            @endforeach
        @endif
        </tbody>

    </table>
@endif
</div>
<div class="row">
<table style="width:100%;" class="table table-bordered">
    <tbody>
    <tr>
       @if ($request->template <> 1 && !$request->line_invoiceDetails && isset($request->invoicedetails) && sizeof($request->invoicedetails) > 0)
        <td colspan="12" style="border:none !important;">
           .
        </td>
        @elseif(($request->template == 2 && isset($request->item_invoice) && $request->item_invoice))
        <td colspan="12" style="border:none !important;">
            .
        </td>
        @else
        <td colspan="12" style="border:none !important;">
          .
        </td>
        @endif
        <td colspan="3" class="text-right" style="border:none !important;width: 85%">
                <span class="font-weight-bold" style="font-size: 11.5px">
                    Subtotal (Excluding VAT)
                </span>
        </td>

        <td class="text-right"
            style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                    class="font-weight-bold">@if ($request->invoicedetails){{number_format($directTraSubTotal, $numberFormatting)}}@endif</span>
        </td>
    </tr>

    {{$totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
    {{$directTraSubTotal+= $totalVATAmount}}
    <tr>
    @if ($request->template <> 1 && !$request->line_invoiceDetails && isset($request->invoicedetails) && sizeof($request->invoicedetails) > 0)
        <td colspan="12" style="border:none !important;">
            &nbsp;
        </td>
        @elseif(($request->template == 2 && isset($request->item_invoice) && $request->item_invoice))
        <td colspan="12" style="border:none !important;">
            &nbsp;
        </td>
        @endif
        <td colspan="3" class="text-right" style="border:none !important;width: 85%">
                <span class="font-weight-bold" style="font-size: 11.5px">
                    Total VAT ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}) ({{round( ( ($request->tax && $request->tax->taxPercent ) ? $request->tax->taxPercent : 0 ), 2)}} %)
                </span>
        </td>
        <td class="text-right"
            style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span class="font-weight-bold">{{number_format($totalVATAmount, $numberFormatting)}}</span>
        </td>
    </tr>

    <tr>
       @if ($request->template <> 1 && !$request->line_invoiceDetails && isset($request->invoicedetails) && sizeof($request->invoicedetails) > 0)
        <td colspan="12" style="border:none !important;">
            &nbsp;
        </td>
        @elseif(($request->template == 2 && isset($request->item_invoice) && $request->item_invoice))
        <td colspan="12" style="border:none !important;">
            &nbsp;
        </td>
        @endif
        <td colspan="3" class="text-right" style="border:none !important;width: 85%">
                <span class="font-weight-bold" style="font-size: 11.5px">
                    Total Amount Payable
                </span>
        </td>
        <td class="text-right"
            style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                    class="font-weight-bold">{{number_format($directTraSubTotal, $numberFormatting)}}</span>
        </td>
    </tr>
    </tbody>
</table>
</div>

<br>
<br>
<br>
<br>
<div id="footer">
    @if($request->line_invoiceDetails)
        <div class="" style="">
            @else
                <div class="" style="">
                    @endif
                    <table>
                        <tr>
                            <td width="100px" colspan="3"><span class="font-weight-bold" style="text-decoration: underline;">Bank Details </span></td>
                        </tr>
                        <tr>
                            <td colspan="3" width="100px"><span class="font-weight-bold">Bank : </span>
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->bankName}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->bankName : ''}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="100px"><span class="font-weight-bold">Branch : </span>
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->bankBranch}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->bankBranch : ''}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="100px"><span class="font-weight-bold">Account No : </span>
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->AccountNo}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->AccountNo : ''}}
                                @endif

                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="100px"><span class="font-weight-bold">SWIFT Code : </span>
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->accountSwiftCode}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->accountSwiftCode : ''}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                @if(!$request->line_rentalPeriod)
                    <div class="" style="margin-top: 20px">
                        <table width="100%">

                            <tr>
                                <td colspan="3" width="50%" style="vertical-align: top;">
                                    <span class="font-weight-bold">Prepared By :</span><br>
                                    @if($request->createduser)
                                        {{$request->createduser->empName}}
                                    @endif <br>
                                    {{ \App\helper\Helper::dateFormat($request->createdDateAndTime)}}
                                </td>
                                <td colspan="3" width="50%" style="vertical-align: top;">
                                    <span class="font-weight-bold">Approved By :</span><br>
                                    @foreach ($request->approved_by as $det)
                                        @if($det->employee)
                                            {{$det->employee->empFullName }}
                                            <br>
                                            @if($det->employee)
                                                {{ \App\helper\Helper::convertDateWithTime($det->approvedDate)}}
                                            @endif
                                        @endif
                                        <br>
                                        <br>
                                    @endforeach
                                </td>
                            </tr>
                        </table>
                    </div>
            @endif

        </div>
</div>


</html>
