<html>
<center>
    <tr>
       <td colspan="6"> </td>
        <td colspan="8">    
            <span style="font-size:35px;"> {{ __('custom.tax_invoice') }}</span>
        </td>

    <tr>

</center>

<br>
<br>
    <tr>
    <td colspan="10"> {{$request->CompanyName}}<br>{{$request->CompanyAddress}}</td>
    <td colspan="8"> {{ __('custom.invoice_no') }} : {{$request->bookingInvCode}}</td>
    </tr>
    <tr>
    <td colspan="10"> Tel:  {{$request->CompanyTelephone}}</td>
    <td colspan="8"> {{ __('custom.invoice_date') }} :  @if(!empty($request->bookingDate))
                                    {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                @endif</td>
    </tr>
    <tr>
    <td colspan="10"> Fax: {{$request->CompanyFax}}</td>
    <td colspan="8"> {{ __('custom.name_of_customer') }} : :@if($request->line_customerShortCode)
                                    {{$request->customer->CutomerCode}} -
                                @endif
                                {{$request->customer->ReportTitle}}</td>
    </tr>
    <tr>
    <td colspan="10"> <b>{{ __('custom.vat_no') }}: {{$request->vatRegistratonNumber}}</b></td>
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

            <th colspan="3" style="text-align: center">{{ __('custom.well') }}</th>
            <th colspan="3" style="text-align: center">{{ __('custom.network') }}</th>
            <th colspan="3" style="text-align: center">{{ __('custom.se') }}</th>
            <th colspan="3" style="text-align: right">{{ __('custom.amount') }}({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
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
            <th colspan="2" style="text-align: center">{{ __('custom.client_ref') }}</th>
            @if($request->is_po_in_line)
                <th colspan="2" style="text-align: center">{{ __('custom.po_line_item') }}</th>
            @endif
            <th colspan="2" style="text-align: center">{{ __('custom.details') }}</th>
            <th colspan="2" style="text-align: center">{{ __('custom.qty') }}</th>
            <th colspan="2" style="text-align: center">{{ __('custom.unit_rate') }}</th>
            <th colspan="2" style="text-align: right">{{ __('custom.amount') }} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
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
            <th colspan="12" style=" text-align: center">{{ __('custom.details') }}</th>


            <th style="width:140px;text-align: right">{{ __('custom.amount') }} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
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
            <th colspan="2">{{ __('custom.gl_code') }}</th>
            <th colspan="4">{{ __('custom.gl_code_description') }}</th>
            <th colspan="2">{{ __('custom.qty') }}</th>
            <th colspan="2">{{ __('custom.unit_rate') }}</th>
            <th colspan="2">{{ __('custom.vat_per_unit') }}</th>
            <th colspan="2">{{ __('custom.amount') }} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
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
            <th colspan="2" style="width:60%;text-align: center">{{ __('custom.description') }}</th>
            <th colspan="2" style="width:30%;text-align: center">{{ __('custom.part_number') }}</th>
            <th colspan="2" style="width:10%;text-align: center">{{ __('custom.quantity') }}</th>
            <th colspan="2" style="width:10%;text-align: center">{{ __('custom.unit_price') }}</th>
            <th colspan="2" style="width:10%;text-align: right">{{ __('custom.taxable_amount') }} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
            <th colspan="2" style="width:10%;text-align: right">{{ __('custom.taxable_rate') }}</th>
            <th colspan="2" style="width:10%;text-align: right">{{ __('custom.tax') }} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
            <th colspan="2" style="width:10%;text-align: right">{{ __('custom.total_amount') }} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
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
                    {{ __('custom.sub_total') }} ({{ __('custom.excluding_vat') }})
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
                    {{ __('custom.total_vat') }} ({{empty($request->currency) ? '' : $request->currency->CurrencyCode}}) ({{round( ( ($request->tax && $request->tax->taxPercent ) ? $request->tax->taxPercent : 0 ), 2)}} %)
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
                    {{ __('custom.total_amount_payable') }}
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
                            <td width="100px" colspan="3"><span class="font-weight-bold" style="text-decoration: underline;">{{ __('custom.bank_details') }} </span></td>
                        </tr>
                        <tr>
                            <td colspan="3" width="100px"><span class="font-weight-bold">{{ __('custom.bank') }} : </span>
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
                            <td colspan="3" width="100px"><span class="font-weight-bold">{{ __('custom.branch') }} : </span>
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
                            <td colspan="3" width="100px"><span class="font-weight-bold">{{ __('custom.account_no') }} : </span>
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
                            <td colspan="3" width="100px"><span class="font-weight-bold">{{ __('custom.swift_code') }} : </span>
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
                                    <span class="font-weight-bold">{{ __('custom.prepared_by') }} :</span><br>
                                    @if($request->createduser)
                                        {{$request->createduser->empName}}
                                    @endif <br>
                                    {{ \App\helper\Helper::dateFormat($request->createdDateAndTime)}}
                                </td>
                                <td colspan="3" width="50%" style="vertical-align: top;">
                                    <span class="font-weight-bold">{{ __('custom.approved_by') }} :</span><br>
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
