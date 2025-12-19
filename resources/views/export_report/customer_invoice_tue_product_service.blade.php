<html>
<center>
    <tr>
       <td colspan="8"> Image</td>
        <td colspan="8">    
            <b>TAX INVOICE</b>
        </td>

    <tr>

    <tr>
    <td colspan="8"> </td>
    <td colspan="1">
    <b> فاتورة ضريبية</b>
                    </td>
    </tr>
</center>
<br>
<br>
<br>
    <tr>
    <td colspan="16"> {{$request->CompanyAddress}}</td>
    <td colspan="8"> {{$request->CompanyAddressSecondaryLanguage}}</td>
    </tr>
    <tr>
    <td colspan="16"> Tel:  {{$request->CompanyTelephone}}</td>
    <td colspan="8"> هاتف : {{$request->CompanyTelephone}}</td>
    </tr>
    <tr>
    <td colspan="16"> Fax: {{$request->CompanyFax}}</td>
    <td colspan="8"> فاكس : {{$request->CompanyFax}}</td>
    </tr>
    <tr>
    <td colspan="16"> <b>VAT NO: {{$request->vatRegistratonNumber}}</b></td>
    <td colspan="8"> الضريبي : {{$request->vatRegistratonNumber}}</td>
    </tr>
    <br>
    <br>


    <tr>
    <td colspan="16"><b>INVOICE NO : {{$request->bookingInvCode}}</b></td>
    <td colspan="8"> <b>رقم الفاتورة : {{$request->bookingInvCode}}</b></td>
    </tr>

    <tr>
    <td colspan="16"> <b>INVOICE DATE : @if(!empty($request->bookingDate))
                                    {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                @endif</td>
    <td colspan="8"> <b>تاريخ الفاتورة : @if(!empty($request->bookingDate))
                                    {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                @endif</b></td>
    </tr>



    <tr>
    <td colspan="16"> <b>QUOTE/DO TUE : 
                            @if($request->line_poNumber)
                                {{$request->PONumber}}
                            @endif</td>
    <td colspan="8"> <b>رقم التسعيرة : @if($request->line_poNumber)
                                {{$request->PONumber}}
                            @endif </b></td>
    </tr>



    <tr>
    <td colspan="16"> <b>Contract / PO No : 
                             @if(!empty($request->invoicedetails) )
                                {{isset($request->invoicedetails[0]->clientContractID)?$request->invoicedetails[0]->clientContractID:''}}
                            @endif
                        </b></td>
    <td colspan="8"> <b>رقم العقد/أمر الشراء : @if(!empty($request->invoicedetails) )
                                {{isset($request->invoicedetails[0]->clientContractID)?$request->invoicedetails[0]->clientContractID:''}}
                            @endif

                        </b></td>
    </tr>

    <br>
    <br>

    <tr>
    <td colspan="16"> <b>CUSTOMER NAME : {{$request->customer->ReportTitle}}</b></td>
    <td colspan="8"><b>أسم العميل : {{$request->customer->reportTitleSecondLanguage}}</b></td>
    </tr>
    <tr>
    <td colspan="16"> <b>ADDRESS : {{$request->customer->customerAddress1}}</b></td>
    <td colspan="8"> <b>عنوان العميل : {{$request->customer->addressOneSecondLanguage}}</b></td>
    </tr>
    <tr>
    <td colspan="16"><b>VAT NO : {{$request->vatNumber}}</b></td>
    <td colspan="8"> <b>الرقم الضريبي : {{$request->vatNumber}}</b></td>
    </tr>

    <br>
    <br>    



    <div class="row">
        @if ($request->template==1)
            <table class="table">
                <thead>
                <tr style="background-color: #6798da;">
                    <th colspan="1" style="width:5%;">Item<br>رقم المنتج</th>
                    <th colspan="3" style="width:20%; text-align: center">Our Reference<br>المرجع</th>
                    <th colspan="2" style="width:20%;text-align: center">Client Reference<br>مرجع العميل</th>
                    <th colspan="3" style="width:30%;text-align: center">Item Description<br>وصف السلعة</th>
                    <th colspan="2" style="width:5%;text-align: center">QTY<br>الكمية</th>
                    <th colspan="2" style="width:10%;text-align: center">Unit Rate<br> سعر الوحده</th>
                    <th colspan="2" style="width:10%;text-align: center">Total Amount<br>القيمة الكلية</th>
                </tr>
                </thead>

                <tbody>
                {{$decimal = 2}}
                {{$x=1}}
                {{$directTraSubTotal=0}}
                {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}

                    @foreach ($request->profomaDetailData as $item)
                        @if ($item->amount != 0)
                            {{$directTraSubTotal +=$item->amount}}
                            <tr style="border: 1px solid !important;">
                                <td colspan="1">{{$x}}</td>
                                <td colspan="3" style="word-wrap:break-word;">{{$item->OurRef}}</td>
                                <td colspan="2" style="word-wrap:break-word;">{{$item->ClientRef}}</td>
                                <td colspan="3" style="word-wrap:break-word;">{{$item->assetDescription}}</td>
                                <td colspan="2" style="text-align: right;">{{$item->qty}}</td>
                                <td colspan="2" style="text-align: right;">{{number_format($item->rate,$numberFormatting)}}</td>
                                <td colspan="2" style="text-align: right;">{{number_format($item->amount,$numberFormatting)}}</td>
                            </tr>
                            {{ $x++ }}
                        @endif
                    @endforeach
                </tbody>
                <tbody>
                    <tr>
                        <td colspan="11">.</td>
                        <td colspan="2" style="text-align: left; border-right: none !important;"><b>Total Before VAT ( الاجمالي قبل الضريبة )</b></td>
                        <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td class="text-right">@if ($request->invoicedetails)
                        {{number_format($directTraSubTotal, $numberFormatting)}}
                    @endif</td>
                    </tr>
                    {{$directTraSubTotal+= ($request->tax) ? $request->tax->amount : 0}}
                    {{$taxAmount = ($request->tax) ? $request->tax->amount : 0}}
                    {{$taxPercent = ($request->tax) ? $request->tax->taxPercent : 0}}
                    <tr>
                        <td colspan="11">.</td>
                        <td colspan="2" style="text-align: left; border-right: none !important;"><b>Value Added Tax {{$taxPercent}}% (ضريبة القيمة المضافة )</b></td>
                        <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td class="text-right">{{number_format($taxAmount, $numberFormatting)}}</td>
                    </tr>
                    <tr>
                    <td colspan="11">.</td>
                        <td colspan="2" style="text-align: left; border-right: none !important;"><b>Total Amount Including VAT(القيمة الكلية متضمنة ضريبة القيمة المضافة)</b></td>
                        <td style="text-align: center; border-left: none !important"><b>{{empty($request->currency) ? '' : $request->currency->CurrencyCode}}</b></td>
                        <td class="text-right">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                    </tr>
                </tbody>
                <tbody>
                    <tr>
                        <td colspan="7">PLEASE ISSUE ALL PAYMENT ON BELOW BANK ACCOUNT DETAILS : </td>
                    </tr>
                </tbody>
                
            </table>
        @endif
    </div>
    

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
                            <td colspan="3" width="100px"><span class="font-weight-bold"><b>BANK NAME</b></span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                     @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->bankName}}
                                      @endif
                                    @else
                                    {{($request->bankaccount) ? $request->bankaccount->bankName : ''}}
                                @endif
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="100px"><span class="font-weight-bold"><b>ACCOUNT NAME</b></span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->AccountName}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->AccountName : ''}}
                                @endif
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="100px"><span class="font-weight-bold"><b>ACCOUNT NO</b></span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->AccountNo}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->AccountNo : ''}}
                                @endif

                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="100px"><span class="font-weight-bold"><b>IBAN NO</b></span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$request->accountIBANSecondary}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->accountIBAN : ''}}
                                @endif
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" width="100px"><span class="font-weight-bold"><b>SWIFT Code</b> </span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->accountSwiftCode}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->accountSwiftCode : ''}}
                                @endif
                                </b>
                            </td>
                        </tr>
                    </table>
                </div>

                @if(!$request->line_rentalPeriod)
                    <div class="" style="margin-top: 10px">
                        <table style="width: 100%">
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
