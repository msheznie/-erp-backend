<html>
    <tr>
        <td colspan="6"> Image</td>
        <td colspan="8">
            <span style="font-size:35px;"> {{$request->CompanyName}}</span>
        </td>
    <tr>

    <tr>
        <td colspan="6"> </td>
        <td colspan="1">
            <b>@if($request->is_pdo_vendor) VAT @endif Invoice</b>
        </td>
    </tr>
<br>

<div class="row">
    <tr>
        <td colspan="10">Customer Details</td>
        <td colspan="8"> Invoice  Details</td>
    </tr>

    <tr>
        <td colspan="10"></td>
        <td colspan="8"> </td>
    </tr>
    <tr>
        <td colspan="10">
            @if($request->line_subcontractNo && !empty($request->invoicedetails) )
                <span>{{$request->invoicedetails[0]->clientContractID}}</span>
            @endif
        </td>
        <td colspan="2"> Invoice Number : </td>
        <td colspan="2">{{$request->bookingInvCode}}</td>
    </tr>

    <tr>
    <td colspan="10">
        @if($request->line_customerShortCode)
            <span>{{$request->customer->CutomerCode}}</span>
        @endif
    </td>
    <td colspan="2"> Invoice Date : </td>
    <td colspan="2">
        <span>@if(!empty($request->bookingDate))
                    {{\App\helper\Helper::dateFormat($request->bookingDate) }}
              @endif
        </span>
    </td>
    </tr>

    <tr>
    <td colspan="10"> {{$request->customer->ReportTitle}} </td>
    @if($request->line_performaCode)
        <td colspan="2"><span class="font-weight-bold">Proforma Invoice No -</span></td>
        <td colspan="2"><span>{{$request->invoicedetail->performadetails->performaCode}}</span></td>
    @endif
    </tr>


    <tr>
    <td colspan="10"> {{$request->customer->customerAddress1}} </td>
    @if($request->line_seNo)
        <td colspan="2"><span class="font-weight-bold">SE No -</span></td>
        <td colspan="2"><span>{{$request->wanNO}}</span></td>
    @endif
    </tr>

    <tr>
    <td colspan="10">
        @if($request->lineSecondAddress)
            <span>{{$request->customer->customerAddress2}}</span>
        @else
            <span>{{$request->customer->customerCity}}</span>
        @endif
    </td>


    @if($request->line_dueDate)
        <td colspan="2" width="120px"><span class="font-weight-bold">Due Date -</span></td>
        <td colspan="2">
            <span>
            @if(!empty($request->invoiceDueDate))
                {{\App\helper\Helper::dateFormat($request->invoiceDueDate)}}
            @endif
            </span>
        </td>
    @endif
    </tr>

    <tr>
    <td colspan="10">@if ($request->is_pdo_vendor) {{$request->vendorCode}}   @endif </td>
    @if ($request->line_contractNo)
        <td colspan="2" width="120px">
            <span class="font-weight-bold">
                Contract @if($request->line_paymentTerms) Ref No @endif
            </span>
        </td>
        <span>
            {{ isset($request->invoicedetails[0]) ? $request->invoicedetails[0]->clientContractID : 'N/A' }}
        </span>
    @endif
    </tr>

    <tr>
    <td colspan="10"> </td>
    @if ($request->line_poNumber)
        <td colspan="2"width="120px"><span class="font-weight-bold">PO Number -</span></td>
        <td colspan="2">{{$request->PONumber}}</td>
    @endif
   </tr>

   <tr>
    <td colspan="10"> </td>
    @if($request->line_paymentTerms)
        <td colspan="2" width="120px"><span class="font-weight-bold">Payment Terms -</span></td>
        <td colspan="2" >{{$request->paymentInDaysForJob}} Days</td>
    @endif
   </tr>


   <tr>
    <td colspan="10"> </td>
    @if ($request->line_unit)
        <td colspan="2" width="120px"><span class="font-weight-bold">Unit -</span></td>
        <td colspan="2">
        <span>{{isset($request->invoicedetail->billmaster->ticketmaster->rig->RigDescription)?$request->invoicedetail->billmaster->ticketmaster->rig->RigDescription:''}}</span>
        | <span>{{isset($request->invoicedetail->billmaster->ticketmaster->regNo)?$request->invoicedetail->billmaster->ticketmaster->regNo:''}}</span></td>
    @endif
   </tr>

   <tr>
    <td colspan="10"> </td>
    @if ($request->line_jobNo)
            <td colspan="2" width="120px"><span class="font-weight-bold">Job No -</span></td>
            <td colspan="2"><span>{{isset($request->invoicedetail->billmaster->ticketmaster->ticketNo)?$request->invoicedetail->billmaster->ticketmaster->ticketNo:''}}
            </span></td>
    @endif
   </tr>

   <tr>
    <td colspan="10"> </td>
    @if ($request->is_pdo_vendor)
        <td colspan="2" width="120px"><span class="font-weight-bold">TRN -</span></td>
    @endif
   </tr>

   <tr>
    <td colspan="10"> </td>
    @if ($request->is_pdo_vendor)
        <td colspan="2" width="120px"><span class="font-weight-bold">VAT Number</span></td>
        <td colspan="2"><span>{{$request->vatNumber}}</span></td>
    @endif
   </tr>
</div>
<br>
    @if($request->line_rentalPeriod)
        <tr>
            <td colspan="6">
                <div class="row" style="text-align: center">
                    <b>Rental Period From
                        {{\App\helper\Helper::dateFormat($request->invoicedetail->billmaster->rentalStartDate)}} -
                        {{\App\helper\Helper::dateFormat($request->invoicedetail->billmaster->rentalEndDate)}}</b>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <div class="row" style="">
                    <b><span>{{isset($request->invoicedetail->billmaster->ticketmaster->rig->RigDescription)?$request->invoicedetail->billmaster->ticketmaster->rig->RigDescription:''}}</span> |
                        <span> {{isset($request->invoicedetail->billmaster->ticketmaster->regNo)?$request->invoicedetail->billmaster->ticketmaster->regNo:''}}</span></b>
                </div>
            </td>
        </tr>
    @else
        <tr>
            <td colspan="6">
                <div class="row" style="">
                    <b>Comments : </b> {!! nl2br($request->comments) !!}
                </div>
            </td>
        </tr>
    @endif
    <tr>
    <td colspan="11">
    <td colspan="6">
        <div class="row">
            <div style="text-align: right"><b>Currency
                    : {{empty($request->currency) ? '' : $request->currency->CurrencyCode}} </b></div>
        </div>
    </td>
    </tr>
    <br>
    <div class="row">

        @if($request->linePdoinvoiceDetails)

            <table class="table table-bordered table-striped table-sm" style="width: 100%;">
                <thead>
                <tr class="">

                    <th colspan="2" style="text-align: center">Well</th>
                    <th colspan="2" style="text-align: center">Network</th>
                    <th colspan="2" style="text-align: center">SE</th>
                    <th colspan="2" style="text-align: right">Amount</th>
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

                        <td colspan="2" style="width: 25%">{{$item->wellNo}}</td>
                        <td colspan="2" style="width: 25%">{{$item->netWorkNo}}</td>
                        <td colspan="2" style="width: 25%">{{$item->SEno}}</td>
                        <td colspan="2" style="width: 25%;text-align: right">{{number_format($item->wellAmount,$numberFormatting)}}</td>

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
                    <th colspan="1" style="width:1%"></th>
                    <th colspan="2" style="text-align: center">Client Ref</th>
                    @if($request->is_po_in_line)
                        <th colspan="2"style="text-align: center">PO Line Item</th>
                    @endif
                    <th colspan="2" style="text-align: center">Details</th>
                    <th colspan="2" style="text-align: center">Qty</th>
                    <th colspan="2" style="text-align: center">Unit Rate</th>
                    <th colspan="2" style="text-align: right">Amount</th>
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
                        <td colspan="1">{{$x}}</td>
                        <td colspan="2" style="width: 12%">{{$item->ClientRef}}</td>
                        @if($request->is_po_in_line)
                            <td colspan="2" style="width: 12%">{{$item->pl3}}</td>
                        @endif
                        <td colspan="2">{{$item->assetDescription}}</td>
                        <td colspan="2" style="width: 8%;text-align: center">{{number_format($item->qty,2)}}</td>
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
                    <th style="width:1%"></th>
                    <th style=" text-align: center">Details</th>


                    <th style="width:140px;text-align: right">Amount</th>
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
                        <td>{{$x}}</td>
                        <td>{{$item->myStdTitle}}</td>


                        <td style="width: 100px"
                            class="text-right">{{number_format($item->sumofsumofStandbyAmount,$numberFormatting)}}</td>
                    </tr>
                    {{ $x++ }}
                @endforeach
                </tbody>

            </table>
        @endif

            @php
                $currencyCode = empty($request->currency) ? '' : $request->currency->CurrencyCode;
                $decimalPlaces = empty($request->currency) ? 2 : $request->currency->DecimalPlaces;
            @endphp

            @if(in_array($request->isPerforma, [2, 3, 4, 5]))
                @if ($request)
                    <table class="table table-bordered" style="width: 100%;">
                        <thead>
                        <tr>
                            <th colspan="5" style="text-align: center">Item Details</th>
                            <th colspan="8" style="text-align: center">Price ({{ $currencyCode }})</th>
                        </tr>
                        <tr class="theme-tr-head">
                            <th style="text-align: center">#</th>
                            <th style="text-align: center">Description</th>
                            <th style="text-align: center">Project</th>
                            <th style="text-align: center">Ref No</th>
                            <th style="text-align: center">UOM</th>
                            <th style="text-align: center">QTY</th>
                            <th style="text-align: center">Sales Price</th>
                            <th style="text-align: center">Dis %</th>
                            <th style="text-align: center">Discount Amount</th>
                            <th style="text-align: center">Selling Unit Price</th>
                            <th style="text-align: center">Taxable Amount</th>
                            <th style="text-align: center">VAT</th>
                            <th style="text-align: center">Net Amount ({{ $currencyCode }})</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{$x=1}}
                        {{$directTraSubTotal=0}}
                        @foreach ($request->issue_item_details as $item)
                            {{$directTraSubTotal +=$item->sellingTotal}}
                            <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                                <td>{{$x}}</td>
                                <td class="text-left">{{$item->itemPrimaryCode .' - '.$item->itemDescription}}</td>
                                <td class="text-left">  @if($item->project)
                                        {{$item->project->projectCode.' - '.$item->project->description}} @else - @endif
                                </td>
                                <td class="text-left">{{$item->part_no}}</td>
                                <td class="text-left">{{$item->uom_issuing->UnitShortCode}}</td>
                                <td class="text-right">{{$item->qtyIssuedDefaultMeasure}}</td>
                                <td class="text-right">{{number_format($item->salesPrice, $decimalPlaces)}}</td>
                                <td class="text-right">{{$item->discountPercentage}}</td>
                                <td class="text-right">{{number_format($item->discountAmount, $decimalPlaces)}}</td>
                                <td class="text-right">{{number_format($item->sellingCostAfterMargin, $decimalPlaces)}}</td>
                                <td class="text-right">{{number_format($item->taxable_amount, $decimalPlaces)}}</td>
                                <td class="text-right">{{number_format($item->VATAmount, $decimalPlaces)}}</td>
                                <td class="text-right">{{number_format($item->sellingTotal, $decimalPlaces)}}</td>
                            </tr>
                            {{ $x++ }}
                        @endforeach
                        </tbody>

                    </table>
                @endif
            @else
                @if ($request->template <> 1 && !$request->line_invoiceDetails)
                    <table class="table table-bordered" style="width: 100%;">
                        <thead>
                        <tr class="theme-tr-head">
                            <th style="width:3%"></th>
                            <th style="width:10%;text-align: center">GL Code</th>
                            <th style="width:50%;text-align: center">GL Code Description</th>
                            <th style="width:20%;text-align: center">Segment</th>
                            <th style="width:10%;text-align: center">UoM</th>
                            <th style="width:10%;text-align: center">QTY</th>
                            <th style="width:10%;text-align: center">Unit Rate</th>
                            <th style="width:10%;text-align: center">Total Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{$x=1}}
                        {{$directTraSubTotal=0}}
                        @foreach ($request->invoicedetails as $item)
                            {{$directTraSubTotal +=$item->invoiceAmount}}
                            <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;">
                                <td>{{$x}}</td>
                                <td class="text-left">{{$item->glCode}}</td>
                                <td class="text-left">{{$item->glCodeDes}}</td>
                                <td class="text-left">{{isset($item->department->ServiceLineDes)?$item->department->ServiceLineDes:''}}</td>
                                <td class="text-left">{{$item->unit->UnitShortCode}}</td>
                                <td class="text-right">{{number_format($item->invoiceQty,2)}}</td>
                                <td class="text-right">{{number_format($item->unitCost, $decimalPlaces)}}</td>
                                <td class="text-right">{{number_format($item->invoiceAmount, $decimalPlaces)}}</td>
                            </tr>
                            {{ $x++ }}
                        @endforeach
                        </tbody>

                    </table>
                @endif
            @endif
    </div>
    <div class="row">
        <table style="width:100%;" class="table table-bordered">
            <tbody>
            <tr>
                <td colspan="10" style="border-bottom: none !important;border-left: none !important;width: 60%;">.</td>
                <td colspan="2" class="text-right" style="width: 20%;border-bottom: none !important"><span
                            class="font-weight-bold"
                            style="border-bottom: none !important;font-size: 11.5px">Total:</span>
                </td>
                <td class="text-right"
                    style="font-size: 11.5px;width: 20%;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;background-color: #EBEBEB">
                <span class="font-weight-bold">
                @if ($request->invoicedetails)
                        {{number_format($directTraSubTotal, $decimalPlaces)}}
                    @endif
                </span>
                </td>
            </tr>

            @if ($request->tax)
                {{$directTraSubTotal+=$request->tax->amount}}
                <tr>
                    <td colspan="10" style="border:none !important;">
                        &nbsp;
                    </td>
                    <td colspan="2" class="text-right" style="border:none !important;"><span
                                class="font-weight-bold"
                                style="font-size: 11.5px">VAT Amount ({{$request->tax->taxPercent}} %)
                            </span></td>
                    <td class="text-right"
                        style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                                class="font-weight-bold">{{number_format($request->tax->amount, $decimalPlaces)}}</span>
                    </td>
                </tr>

                <tr>
                    <td colspan="10" style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                        &nbsp;
                    </td>
                    <td colspan="2" class="text-right" style="border:none !important;"><span
                                class="font-weight-bold"
                                style="font-size: 11.5px">Net Amount</span>
                    </td>
                    <td class="text-right"
                        style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;background-color: #EBEBEB">
                            <span class="font-weight-bold">

                                    {{number_format($directTraSubTotal, $decimalPlaces)}}

                            </span>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    <Br>
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
                        <tr >
                            <td colspan="3"><span class="font-weight-bold">Bank Details </span></td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td colspan="3"><span class="font-weight-bold">Bank Name </span></td>
                            <td colspan="3"> -
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
                            <td colspan="3"><span class="font-weight-bold">Branch </span></td>
                            <td colspan="3"> -
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
                            <td colspan="3"><span class="font-weight-bold">Ac Num </span></td>
                            <td colspan="3"> -
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
                            <td colspan="3"><span class="font-weight-bold">SWIFT Code </span></td>
                            <td colspan="3"> -
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
                                <td colspan="3">
                                    <span class="font-weight-bold">Prepared By :</span>
                                </td>
                                <td colspan="3">
                                    @if($request->createduser)
                                        {{$request->createduser->empName}}
                                    @endif
                                </td>
                                @if(!$request->is_pdo_vendor)
                                    <td colspan="3">
                                        <span class="font-weight-bold">Checked By :</span>
                                    </td>
                                    <td colspan="3">
                                        <div style="border-bottom: 1px solid black;width: 90px;margin-top: 7px;"></div>
                                    </td>
                                @endif
                                @if($request->lineApprovedBy && !$request->is_pdo_vendor)
                                    <td colspan="3">
                                        <span class="font-weight-bold">Approved By :</span>
                                    </td>
                                    <td colspan="3">
                                        <div style="border-bottom: 1px solid black;width: 90px;margin-top: 7px;"></div>
                                    </td>
                                @endif
                            </tr>
                        </table>
                    </div>


                    <div class="" style="margin-top: 10px">
                        <table style="width: 100%">
                            <tr>
                                <td>
                                    <span class="font-weight-bold">Electronically Approved By :</span>
                                </td>
                            </tr>
                            <tr>

                                @foreach ($request->approved_by as $det)
                                    <td style="padding-right: 25px" class="text-center">
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


                                    </td>
                                @endforeach
                            </tr>
                        </table>
                    </div>
                @else
                    {{--SGG PDO ONLY--}}
                    <div class="" style="">
                        <table width="100%">
                            <tr>
                                <td colspan="3">
                                    <span class="font-weight-bold">Prepared By :</span>
                                </td>
                                <td colspan="3">
                                    @if($request->createduser)
                                        {{$request->createduser->empName}}
                                    @endif
                                </td>
                                <td width="30%" style="">

                                </td>
                                <td colspan="3" style="text-align:center; border-top: 1px solid black;margin-top: 7px;">
                                    <span class="font-weight-bold">Authorized  Signatory :</span>
                                </td>


                            </tr>

                        </table>
                    </div>
                @endif

                <table style="width:100%;">

                    <tr>
                        @if($request->footerDate)
                            <td colspan="3"  style="width:33%;font-size: 10px;">
                                <span style="font-weight: bold; font-size: 12px ">  {{date("d/m/Y", strtotime(now()))}}</span>
                            </td>
                        @endif

                        @if($request->linePageNo)
                            <td colspan="3" style="width:33%; text-align: right;font-size: 12px;vertical-align: top;">
                                <span style="text-align: right;font-weight: bold;">Page <span
                                            class="pagenum"></span> <span class="pagecount"></span></span><br>

                            </td>
                        @endif
                    </tr>
                    @if($request->linefooterAddress)
                        <tr>
                            <td colspan="2"
                                style="font-size: 11px;font-style: italic">{{$request->company->CompanyAddress}} Tel
                                : {{$request->company->CompanyTelephone}} , Fax : {{$request->company->CompanyFax}} ,
                                E-mail : {{$request->company->CompanyEmail}}  </td>
                        </tr>
                    @endif
                </table>
        </div>
</div>
</html>
