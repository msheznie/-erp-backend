

<html>

    <table>
        <thead>
            <tr>
                <th colspan="6"></th>
                <th colspan="10">    
                    <b> COMMERICAL INVOICE </b>
                </th>
            </tr>
        </thead>
    </table>

    <div class="row">
        <table>
            <tbody>
                <tr>
                    <td colspan="1" >Seller</td>
                    <td colspan="3">
                        {{$request->CompanyName}}
                    </td>
                </tr>
                <tr>
                    <td colspan="1" ></td>
                    <td colspan="3">
                        {{$request->CompanyAddress}}
                    </td>
                </tr>
                <tr>
                    <td colspan="1" ></td>
                    <td colspan="3">
                        Tel: {{$request->CompanyTelephone}}
                    </td>
                </tr>
                <tr>
                    <td colspan="1" ></td>
                    <td colspan="3">
                        Fax: {{$request->CompanyFax}}
                    </td> 
                </tr>
            </tbody>

            <tbody>
                <tr></tr>
                <tr>
                    <td colspan="3" >Sales Order No.</td>
                    <td colspan="3">INVOICE NO</td>
                    <td colspan="2">INVOICE DATE</td>
                    <td colspan="2"> Contract No.</td>
                    <td colspan="2"> (CONTRACT) DATE</td>
                </tr>
                <tr>
                    <td colspan="3">-</td>
                    <td colspan="3">{{$request->bookingInvCode}}</td>
                    <td colspan="2">@if(!empty($request->bookingDate))
                        {{isset($request->bookingDate)?\App\helper\Helper::dateFormat($request->bookingDate):'-'}}
                        @endif
                    </td>
                    <td colspan="2"> @if(!empty($request->invoicedetails) )
                        {{isset($request->invoicedetails[0]->clientContractID)?$request->invoicedetails[0]->clientContractID:'-'}}
                        @endif
                    </td>
                    <td colspan="2"> -</td>
                </tr>
            </tbody>

            <tbody>
                <tr></tr>
                <tr>
                    <td colspan="1" >Buyer</td>
                    <td colspan="4">
                        {{isset($request->customer->ReportTitle)?$request->customer->ReportTitle:'-'}}<br>
                    </td>
                </tr>
                <tr>
                    <td colspan="1" ></td>
                    <td colspan="3">
                        {{isset($request->customer->customerAddress1)?$request->customer->customerAddress1:'-'}}<br>
                    </td>
                </tr>
                <tr>
                    <td colspan="1" ></td>
                    <td colspan="3">
                        TEL: {{isset($request->CustomerContactDetails->contactPersonTelephone)?$request->CustomerContactDetails->contactPersonTelephone:'-'}}<br>
                    </td>
                </tr>
                <tr>
                    <td colspan="1" ></td>
                    <td colspan="3">
                        FAX: {{isset($request->CustomerContactDetails->contactPersonFax)?$request->CustomerContactDetails->contactPersonFax:'-'}}<br>
                    </td>
                </tr>
            </tbody>

            <tbody>
                <tr></tr>
                <tr>
                    <td colspan="1" >Consignee</td>
                    <td colspan="3">
                        {{isset($request->customerInvoiceLogistic['consignee_name'])?$request->customerInvoiceLogistic['consignee_name']:'-'}}<br>
                    </td>
                </tr>
                <tr>
                    <td colspan="1" ></td>
                    <td colspan="4">
                        {{isset($request->customerInvoiceLogistic['consignee_address'])?$request->customerInvoiceLogistic['consignee_address']:'-'}}<br>
                    </td>
                </tr>
                <tr>
                    <td colspan="1" ></td>
                    <td colspan="3">
                        {{isset($request->customerInvoiceLogistic['consignee_contact_no'])?$request->customerInvoiceLogistic['consignee_contact_no']:'-'}}<br>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="row">
        <table border="1" width="100%">
            <tbody>
                <tr></tr>
                <tr>
                    <td colspan="2">COUNTRY OF ORIGIN</td>
                </tr>
                <tr>
                    <td colspan="2">SULTANATE OF OMAN</td>
                </tr>
                <tr></tr>
                <tr>
                    <td colspan="2">Vessel Name</td>
                    <td colspan="2">Port Of Loading</td>
                    <td colspan="2">Delivery Term</td>
                    <td colspan="2">Terms Of Payment</td>
                </tr>
                <tr>
                    <td colspan="2">{{isset($request->customerInvoiceLogistic['vessel_no'])?$request->customerInvoiceLogistic['vessel_no']:'-'}}</td>
                    <td colspan="2">{{isset($request->customerInvoiceLogistic['port_of_loading']['port_name'])?$request->customerInvoiceLogistic['port_of_loading']['port_name']:'-'}}</td>
                    <td colspan="2">{{isset($request->customerInvoiceLogistic['delivery_payment'])?$request->customerInvoiceLogistic['delivery_payment']:'-'}}</td>
                    <td colspan="4">{{isset($request->customerInvoiceLogistic['payment_terms'])?$request->customerInvoiceLogistic['payment_terms']:'-'}}</td>
                </tr>

            </tbody>
        </table>
        <table border="1" width="100%">
            <tbody>
                <tr>
                    <td colspan="2">B/Lading No</td>
                    <td colspan="2">Port of Discharge</td>
                    <td colspan="2">No of Containers</td>
                    <td colspan="2">Packing</td>
                    <td colspan="2">Currency</td>
                </tr>
                <tr>
                    <td colspan="2">{{isset($request->customerInvoiceLogistic['b_ladding_no'])?$request->customerInvoiceLogistic['b_ladding_no']:'-'}}</td>
                    <td colspan="2">{{isset($request->customerInvoiceLogistic['port_of_discharge']['port_name'])?$request->customerInvoiceLogistic['port_of_discharge']['port_name']:'-'}}</td>
                    <td colspan="2">{{isset($request->customerInvoiceLogistic['no_of_container'])?$request->customerInvoiceLogistic['no_of_container']:'-'}}</td>
                    <td colspan="2">BULKS</td>
                    <td colspan="2">{{isset($request->currency->CurrencyName)?$request->currency->CurrencyName:'-'}}</td>
                </tr>
            </tbody>
        </table>

        @if (isset($request->item_invoice) && $request->item_invoice)

            <table class="table">
                <thead>
                <tr style="background-color: #6798da;">
                    <th style="width:10%;">Item</th>
                    <th style="width:25%;">Content</th>
                    <th style="width:10%;text-align: center">UOM</th>
                    <th style="width:10%;text-align: center">Quantity</th>
                    <th style="width:15%;text-align: center">Rate</th>
                    <th style="width:10%;text-align: center">VAT</th>
                    <th style="width:15%;text-align: center">Total Amount</th>
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

                            <tr style="border: 1px solid !important;">
                                <td style="text-align: center;">{{$item->itemPrimaryCode}}</td>
                                <td style="word-wrap:break-word;">{{$item->itemDescription}}</td>
                                <td style="text-align: center;">{{isset($item->uom_issuing->UnitShortCode)?$item->uom_issuing->UnitShortCode:''}}</td>
                                <td style="text-align: center;">{{$item->qtyIssued}}</td>
                                <td style="text-align: center;">{{number_format($item->sellingCostAfterMargin,$numberFormatting)}}</td>
                                <td style="text-align: center;">{{$item->VATPercentage}}</td>
                                <td class="text-center">{{number_format($item->sellingTotal+$item->VATAmountLocal,$numberFormatting)}}</td>
                            </tr>
                            {{ $x++ }}
                        @endif
                    @endforeach
                @endif

                </tbody>
                <tbody>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2" style="text-align: left; border-right: none !important;"><b>Total Taxable Value</b></td>
                    <td colspan="3" class="text-right">@if ($request->invoicedetails)
                            {{number_format($directTraSubTotal, $numberFormatting)}}
                        @endif</td>
                </tr>
                <tr></tr>
                @if ($request->isVATEligible)
                    {{$totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
                    {{$directTraSubTotal+=$totalVATAmount}}
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2" style="text-align: left; border-right: none !important;"><b>VAT @ {{round( ( ($request->tax && $request->tax->taxPercent ) ? $request->tax->taxPercent : 0 ), 2)}}%</b></td>
                        <td colspan="3" class="text-right">{{number_format($totalVATAmount, $numberFormatting)}}</td>
                    </tr>

                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2" style="text-align: left; border-right: none !important;"><b>Net Receivable</b></td>
                        <td colspan="3" class="text-right">{{number_format($directTraSubTotal, $numberFormatting)}}</td>
                    </tr>

                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2" style="text-align: left; border-right: none !important;"><b>Net Receivable in word</b></td>
                        <td colspan="3" class="text-right">{{$request->amountInWordsEnglish}}</td>
                    </tr>
                @endif
                </tbody>
            </table>
        @endif
    </div>
</html>
