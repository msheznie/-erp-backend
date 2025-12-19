<html>
<center>
    <tr>
        <td colspan="8" style="text-align: center">    
            <b>TAX INVOICE</b>
        </td>

    <tr>
</center>
<br>
<br>
<div class="row">
    <table>
        <tr>
            <td > {{$request->CompanyAddress}}</td>
            </tr>
            <tr>
            <td > Tel:  {{$request->CompanyTelephone}}</td>
            </tr>
            <tr>
            <td > Fax: {{$request->CompanyFax}}</td>
            </tr>
            <tr>
            <td > <b>VAT NO: {{$request->vatRegistratonNumber}}</b></td>
            </tr>
            <tr></tr>
            <tr></tr>
    </table>
    <table >
        <tr>
            <td>
                <table>
                        <tr>
                            <td><b>INVOICE NO </b></td>
                            <td>:
                                {{$request->bookingInvCode}}
                            </td>
                            
                            <td colspan="2"></td>

                            <td style="text-decoration: underline;"><b>Remittance Details </b></td>
                            <td>
                            </td>
                        </tr>

                        <tr>
                            <td><b>INVOICE DATE </b></td>
                            <td>:
                                @if(!empty($request->bookingDate))
                                    {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                @endif
                            </td>

                            <td colspan="2"></td>
                            <td ><span class="font-weight-bold"><b>BANK NAME</b></span></td>
                            <td> :
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
                            <td><b>Date Of Supply </b></td>
                            <td>:
                                @if(!empty($request->date_of_supply))
                                    {{\App\helper\Helper::dateFormat($request->date_of_supply) }}
                                @endif
                            </td>

                            <td colspan="2"></td>
                            <td ><span class="font-weight-bold"><b>ACCOUNT NAME</b></span></td>
                            <td> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->AccountName}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->AccountName : ''}}
                                @endif
                               
                            </td>
                        </tr>

                        <tr>
                            <td><b>Contract / PO No </b></td>
                            <td>:
                                @if(!empty($request->invoicedetails) )
                                    {{isset($request->invoicedetails[0]->clientContractID)?$request->invoicedetails[0]->clientContractID:''}}
                                @endif
                                @if($request->line_poNumber && isset($request->item_invoice) && $request->item_invoice)
                                    {{$request->PONumber}}
                                @endif
                            </td>

                            <td colspan="2"></td>
                            <td><span class="font-weight-bold"><b>ACCOUNT NO</b></span></td>
                            <td> :
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
                            <td></td>
                            <td></td>

                            <td colspan="2"></td>
                            <td ><span class="font-weight-bold"><b>IBAN NO</b></span></td>
                            <td> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$request->accountIBANSecondary}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->accountIBAN : ''}}
                                @endif
                                
                            </td>
                        </tr>

                        <tr>
                            <td><b>CUSTOMER NAME </b></td>
                            <td>:
                                {{$request->customer->ReportTitle}}
                            </td>

                            <td colspan="2"></td>
                            <td ><span class="font-weight-bold"><b>SWIFT Code</b> </span></td>
                            <td> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->accountSwiftCode}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->accountSwiftCode : ''}}
                                @endif
                                
                            </td>
                        </tr>

                        <tr>
                            <td><b>CUSTOMER ADDRESS </b></td>
                            <td>:
                                {{$request->customer->customerAddress1}}</td>
                        </tr>
                        <tr>
                            <td><b>CUSTOMER TELEPHONE </b></td>
                            <td>:
                                {{isset($request->CustomerContactDetails->contactPersonTelephone)?$request->CustomerContactDetails->contactPersonTelephone:''}}</td>
                        </tr>
                        <tr>
                            <td><b>CUSTOMER FAX </b></td>
                            <td>:
                                {{isset($request->CustomerContactDetails->contactPersonFax)?$request->CustomerContactDetails->contactPersonFax:''}}</td>
                        </tr>
                        <tr>
                            <td><b>CUSTOMER VATIN </b></td>
                            <td>:
                                {{$request->vatNumber}}</td>
                        </tr>
                </table>
            </td>
        </tr>
    </table>
</div>


<div class="row">
    @if ($request->template==1 && !$request->line_invoiceDetails && !$request->linePdoinvoiceDetails)
        <table class="table"  style="border: 1px solid !important;">
            <thead>
            <tr style="border: 1px solid !important;">
                <th colspan="1" >Item</th>
                <th colspan="2" style=" text-align: center">Description</th>
                <th colspan="1" style="text-align: center">QTY</th>
                <th colspan="1" style="text-align: center">Days(OP)</th>
                <th colspan="1" style="text-align: center">Price(OP)</th>
                <th colspan="1" style="text-align: center">Days(STB)</th>
                <th colspan="1" style="text-align: center">Price(STB)</th>
                <th colspan="2" style="text-align: center">Total Amount</th>
            </tr>
            </thead>

            <tbody>
            {{$decimal = 2}}
            {{$x=1}}
            {{$directTraSubTotal=0}}
            {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}

            @foreach ($request->profomaDetailData as $item)
                @if ($item->total != 0)
                    {{$directTraSubTotal +=$item->total}}
                    <tr style="border: 1px solid !important;">
                        <td colspan="1">{{$x}}</td>
                        <td colspan="2" style="word-wrap:break-word;">{{$item->description}}</td>
                        <td colspan="1"  style="text-align: right;">{{$item->Qty}}</td>
                        <td colspan="1" style="text-align: right;">{{$item->Days_OP}}</td>
                        <td colspan="1" style="text-align: right;">{{number_format($item->Price_OP,$numberFormatting)}}</td>
                        <td colspan="1" style="text-align: right;">{{$item->Days_STB}}</td>
                        <td colspan="1" style="text-align: right;">{{number_format($item->Price_STB,$numberFormatting)}}</td>
                        <td colspan="2" class="text-right">{{number_format($item->total,$numberFormatting)}}</td>
                    </tr>
                    {{ $x++ }}
                @endif
            @endforeach
            </tbody>

            <tbody>
                <tr>
                    <td colspan="7" style="text-align: right"><b>Total Before VAT</b></td>
                    <td colspan="1" style="text-align: center"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                    <td colspan="2" style="text-align: right">
                        @if ($request->invoicedetails)
                            {{number_format($directTraSubTotal, $numberFormatting)}}
                        @endif
                    </td>
                </tr>
                {{$directTraSubTotal+= ($request->tax) ? $request->tax->amount : 0}}
                {{$taxAmount = ($request->tax) ? $request->tax->amount : 0}}
                {{$taxPercent = ($request->tax) ? $request->tax->taxPercent : 0}}
                <tr>
                    <td></td>
                    <td colspan="7" style="text-align: right"><b>Value Added Tax {{$taxPercent}}%</b></td>
                    <td colspan="1" style="text-align: center"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                    <td colspan="2" style="text-align: right">{{number_format($taxAmount, $numberFormatting)}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="7" style="text-align: right"><b>Total Amount Including VAT</b></td>
                    <td colspan="1" style="text-align: center;"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                    <td colspan="2" style="text-align: right">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="10">PLEASE ISSUE ALL PAYMENT ON BELOW BANK ACCOUNT DETAILS : </td>
                </tr>
            </tbody>
            
        </table>
    @endif

        @if ($request->template <> 1 && !$request->line_invoiceDetails && !$request->item_invoice)
        <table class="table"  style="border: 1px solid !important;">
            <thead>
            <tr style="border: 1px solid !important;">
                <th colspan="1">Item</th>
                <th colspan="1">GL Code</th>
                <th colspan="2">Description</th>
                <th colspan="1">QTY</th>
                <th colspan="1">Unit Rate</th>
                <th colspan="2">Total Amount</th>
            </tr>
            </thead>

            <tbody>
            {{$decimal = 2}}
            {{$x=1}}
            {{$directTraSubTotal=0}}
            {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
            @foreach ($request->invoicedetails as $item)
                {{$directTraSubTotal +=$item->invoiceAmount}}
                <tr style="border: 1px solid !important;">
                    <td colspan="1">{{$x}}</td>
                    <td colspan="1">{{$item->glCode}}</td>
                    <td colspan="2">{{$item->glCodeDes}}</td>
                    <td colspan="1">{{number_format($item->invoiceQty,2)}}</td>
                    <td colspan="1">{{number_format($item->unitCost,$numberFormatting)}}</td>
                    <td colspan="2">{{number_format($item->invoiceAmount,$numberFormatting)}}</td>
                </tr>
                {{ $x++ }}
            @endforeach
            </tbody>

            <tbody>
                <tr>
                    <td colspan="5" style="text-align: right;"><b>Total Before VAT</b></td>
                    <td  colspan="1" style="text-align: center;"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                    <td colspan="2" style="text-align: right;">@if ($request->invoicedetails)
                    {{number_format($directTraSubTotal, $numberFormatting)}}
                @endif</td>
                </tr>
                @if ($request->tax)
                {{$directTraSubTotal+=$request->tax->amount}}
                    <tr>
                        <td colspan="5" style="text-align: right;"><b>Value Added Tax {{$request->tax->taxPercent}}%</b></td>
                        <td colspan="1" style="text-align: center;"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td colspan="2" style="text-align: right;">{{number_format($request->tax->amount, $numberFormatting)}}</td>
                    </tr>

                <tr>
                    <td colspan="5" style="text-align: right;"><b>Total Amount Including VAT</b></td>
                    <td colspan="1" style="text-align: center;"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                    <td colspan="2" style="text-align: right;">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                </tr>
                @endif
            </tbody>
            <tbody>
                <tr>
                    <td colspan="8">PLEASE ISSUE ALL PAYMENT ON BELOW BANK ACCOUNT DETAILS : </td>
                </tr>
            </tbody>
            
        </table>
    @endif

    @if ($request->template == 2 && isset($request->item_invoice) && $request->item_invoice)

            <table>
                <thead>
                    <tr style="border: 1px solid;">
                        <th colspan="1"></th>
                        <th colspan="2" >Item</th>
                        <th colspan="1" style="text-align: center">UOM</th>
                        <th colspan="1" style="text-align: center">QTY</th>
                        <th colspan="1" style="text-align: center">unit Cost</th>
                        <th colspan="2" style="text-align: center">Total Amount</th>
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

                            <tr style="border: 1px solid;">
                                <td colspan="1">{{$x}}</td>
                                <td colspan="2" style="word-wrap:break-word;">{{$item->itemPrimaryCode.' - '.$item->itemDescription}}</td>
                                <td colspan="1" style="text-align: right;">{{isset($item->uom_issuing->UnitShortCode)?$item->uom_issuing->UnitShortCode:''}}</td>
                                <td colspan="1" style="text-align: right;">{{$item->qtyIssued}}</td>
                                <td colspan="1" style="text-align: right;">{{number_format($item->sellingCostAfterMargin,$numberFormatting)}}</td>
                                <td colspan="2" style="text-align: right;">{{number_format($item->sellingTotal,$numberFormatting)}}</td>
                            </tr>
                            {{ $x++ }}
                        @endif
                    @endforeach
                @endif

                </tbody>
                <tbody>
                <tr>
                    <td colspan="5" style="text-align: right"><b>Total Before VAT</b></td>
                    <td colspan="1" style="text-align: center"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                    <td colspan="2" style="text-align: right">@if ($request->invoicedetails)
                            {{number_format($directTraSubTotal, $numberFormatting)}}
                        @endif</td>
                </tr>
                    @if ($request->isVATEligible)
                        {{$totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
                        {{$directTraSubTotal+=$totalVATAmount}}
                        <tr>
                            <td colspan="5" style="text-align: right"><b>Value Added Tax {{round( ( ($request->tax && $request->tax->taxPercent ) ? $request->tax->taxPercent : 0 ), 2)}}% </b></td>
                            <td colspan="1" style="text-align: center"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                            <td colspan="2" style="text-align: right">{{number_format($totalVATAmount, $numberFormatting)}}</td>
                        </tr>

                        <tr>
                            <td colspan="5" style="text-align: right"><b>Total Amount Including VAT</b></td>
                            <td colspan="1" style="text-align: center"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                            <td colspan="2" style="text-align: right">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                        </tr>
                    @endif
                </tbody>
                <tbody>
                <tr>
                    <td colspan="8">PLEASE ISSUE ALL PAYMENT ON BELOW BANK ACCOUNT DETAILS : </td>
                </tr>
                </tbody>
            </table>

    @endif
</div>





    <br>
    <br>
    <br>
    <br>
    <div id="footer">
        <div class="" style="">
            @if(!$request->line_rentalPeriod)
                <div class="" style="margin-top: 10px">
                    <table>
                        <tr>
                            <td>
                                <span class="font-weight-bold"><b>Approved By :</b></span>
                            </td>
                        </tr>
                        <tr>
                            @foreach ($request->approved_by as $det)
                                <td style="padding-right: 25px" class="text-center">
                                    <b>
                                    @if($det->employee)
                                        {{$det->employee->empFullName }}
                                        <br>

                                        @if($det->employee->details)
                                            @if($det->employee->details->designation)
                                                {{$det->employee->details->designation->designation}}
                                            @endif
                                        @endif
                                        <br><br>
                                        @if($det->employee)
                                            {{ \App\helper\Helper::convertDateWithTime($det->approvedDate)}}
                                        @endif
                                    @endif
                                    </b>
                                </td>
                            @endforeach
                        </tr>
                    </table>
                </div>
            @endif
        </div>
</div>






</html>
