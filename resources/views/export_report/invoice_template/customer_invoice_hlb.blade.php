<html>
<center>
    <tr>
       <td colspan="6"> Image</td>
        <td colspan="8">    
            <span style="font-size:35px;"> Tax Invoice</span>
        </td>

    <tr>

</center>

<br>
<br>
    <tr>
    <td colspan="10"> {{$request->customer->ReportTitle}}</td>
    <td colspan="8">   @if(!empty($request->bookingDate))
                            {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                        @endif</td>
    </tr>
    <tr>
    <td colspan="10">{{$request->customer->customerAddress1}}</td>
    <td colspan="8"> INVOICE NO : {{$request->bookingInvCode}}</td>
    </tr>
    <tr>
    <td colspan="10"> {{$request->customer->customerAddress2}}</td>
    <td colspan="8">VAT NO : {{$request->vatNumber}}</td>
    </tr>

    
    <br>
    <br>
    <br>
    <div class="row">
        @if ($request->template == 2 && isset($request->item_invoice) && $request->item_invoice)
            <table class="table" style="width: 100%">
                <thead>
                    <tr class="theme-tr-head">
                        <th colspan="10">Description</th>
                        <th colspan="2" style="width:20%;text-align: center">Amount<br>({{empty($request->currency) ? '' : $request->currency->CurrencyCode}})</th>
                    </tr>
                </thead>

                <tbody class="body-amount">
                {{$decimal = 2}}
                {{$x=1}}
                {{$directTraSubTotal=0}}
                {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}

                @if(!empty($request->issue_item_details))
                    @foreach ($request->issue_item_details as $item)

                        @if ($item->sellingTotal != 0)
                            {{$directTraSubTotal +=$item->sellingTotal}}

                            <tr style="border-bottom: none !important;">
                                <td colspan="10" style="word-wrap:break-word;border-bottom: none !important;">{{$item->itemDescription}}</td>
                                <td colspan="2" class="text-right" style="border-left: 1px solid !important">{{number_format($item->sellingTotal,$numberFormatting)}}</td>
                            </tr>
                            {{ $x++ }}
                        @endif
                    @endforeach
                @endif

                </tbody>
                <tbody class="foot-amount">
                    <tr>
                        <td colspan="10" style="text-align: left; border-right: none !important;"><b>Total</b></td>
                        <td colspan="2" class="text-right" style="border-left: 1px solid !important">@if ($request->invoicedetails)
                                {{number_format($directTraSubTotal, $numberFormatting)}}
                            @endif</td>
                    </tr>
                    @if ($request->isVATEligible)
                        {{$totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
                        {{$directTraSubTotal+=$totalVATAmount}}
                        <tr>
                            <td colspan="10" style="text-align: left; border-right: none !important;"><b>VAT @ {{round( ( ($request->tax && $request->tax->taxPercent ) ? $request->tax->taxPercent : 0 ), 2)}}% </b></td>
                            <td colspan="2" class="text-right" style="border-left: 1px solid !important">{{number_format($totalVATAmount, $numberFormatting)}}</td>
                        </tr>

                        <tr>
                            <td colspan="10" style="text-align: left; border-right: none !important;"><b>Total Payable: ({{$request->amountInWordsEnglish}})</b></td>
                            <td colspan="2" class="text-right" style="border-left: 1px solid !important">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif
    </div>
    <br>
    <div class="row">
         <table style="width: 100%">
            <tr>
                <td width="100px" colspan="3"><span class="font-weight-bold" style="text-decoration: underline;"><b>Bank Details </b></span></td>
            </tr>
            <tr>
                <td colspan="4" width="100px"><span class="font-weight-bold">Bank Name</span></td>
                <td colspan="4" > :
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
                <td colspan="4" width="100px"><span class="font-weight-bold">Account Name</span></td>
                <td colspan="4">:
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
                <td colspan="4" width="100px"><span class="font-weight-bold">Account No</td>
                <td colspan="4">:
                    @if($request->secondaryLogoCompanySystemID)
                        @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                            {{$secondaryBankAccount->contract->secondary_bank_account->AccountNo}}
                        @endif
                    @else
                        {{($request->bankaccount) ? $request->bankaccount->AccountNo : ''}}
                    @endif
                </td>
            </tr>
        </table>
    </div>


    <br>
    <br>
    <br>
    <br>

    <div id="footer">
    <div class="" style="margin-top: 10px">
        <table style="width: 100%; text-align: right; ">
            <tr>
                <td>
                    -----------------------------
                </td>
            </tr>
            <tr>
                <td>
                    <span class="font-weight-bold">Authorized Signatory</span>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <table style="width: 100%; text-align: left; ">
        <tr>
            <td>
                <b>{{$request->company->CompanyURL}}</b>
            </td>
        </tr>
         <tr>
            <td>
                {{$request->CompanyAddress}}
            </td>
        </tr>
        <tr>
            <td>
                <b>TEL : </b> {{$request->CompanyTelephone}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>FAX : </b>{{$request->CompanyFax}}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>EMAIL : </b>{{$request->company->CompanyEmail}}
            </td>
        </tr>
    </table>
</div>


</html>