

<div class="row">
    <table>
        <tr>
            <th>
            </th>


            <th colspan="20">
                <div >
                    <h2 style="text-align: center;">
                        <b>TAX INVOICE</b>
                    </h2>

                    
                </div>

            </th>
            <th >
                <div>
                </div>
            </th>
        </tr>
    </table>
</div>

<div class="row">
    <br>
</div>

<div class="row">
    <table border="1" >

        <tr > 
            <th colspan="5" >Seller</th>
            <th colspan="5" >Sales Order No.</th>
            <th colspan="5" >INVOICE NO: &nbsp;&nbsp;&nbsp;   {{$request->bookingInvCode}}</th>
            <th colspan="5" >INVOICE DATE :&nbsp;&nbsp;&nbsp; @if(!empty($request->bookingDate))
                                                {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                                @endif
            </th>
        </tr> 
        <tr> 
            <th colspan="5" rowspan= "8"  >
                {{$request->CompanyName}}<br>
                {{$request->CompanyAddress}}<br>
                Tel: {{$request->CompanyTelephone}}<br>
                Fax: {{$request->CompanyFax}}<br>
                VAT NO: {{$request->vatRegistratonNumber}}<br>
            </th>
            <th colspan="5" >
                    @php
                    $quotations = DB::table('erp_custinvoicedirect')->selectRaw('erp_quotationmaster.quotationCode as quotationCode,erp_quotationmaster.referenceNo as referenceNo, erp_quotationmaster.documentDate as documentDate')
                    ->join('erp_customerinvoiceitemdetails', 'erp_customerinvoiceitemdetails.custInvoiceDirectAutoID', '=', 'erp_custinvoicedirect.custInvoiceDirectAutoID')
                    ->join('erp_quotationmaster', 'erp_quotationmaster.quotationMasterID', '=', 'erp_customerinvoiceitemdetails.quotationMasterID')
                    ->where('erp_custinvoicedirect.custInvoiceDirectAutoID', $request->custInvoiceDirectAutoID)
                    ->groupBy('erp_quotationmaster.quotationMasterID')
                    ->get();



                    $quotationsByDeo = DB::table('erp_custinvoicedirect')->selectRaw('erp_quotationmaster.quotationCode as quotationCode,erp_quotationmaster.referenceNo as referenceNo, erp_quotationmaster.documentDate as documentDate')
            ->join('erp_customerinvoiceitemdetails', 'erp_customerinvoiceitemdetails.custInvoiceDirectAutoID', '=', 'erp_custinvoicedirect.custInvoiceDirectAutoID')
             ->join('erp_delivery_order_detail', 'erp_delivery_order_detail.deliveryOrderDetailID', '=', 'erp_customerinvoiceitemdetails.deliveryOrderDetailID')
              ->join('erp_quotationmaster', 'erp_quotationmaster.quotationMasterID', '=', 'erp_delivery_order_detail.quotationMasterID')
              ->where('erp_custinvoicedirect.custInvoiceDirectAutoID', $request->custInvoiceDirectAutoID)
              ->groupBy('erp_quotationmaster.quotationMasterID')
            ->get();




                @endphp
                    @if($quotationsByDeo)
                @if(count($quotationsByDeo) == 1)
                    {{ $quotationsByDeo[0]->quotationCode }}
                    @endif
                    @endif
                    @if($quotations)
                        @if(count($quotations) == 1)
                            {{ $quotations[0]->quotationCode }}
                        @endif
                    @endif

            </th>

            <th colspan="5" > Contract No:&nbsp;&nbsp;&nbsp;
                @if($quotationsByDeo)
                    @if(count($quotationsByDeo) == 1)
                        {{ $quotationsByDeo[0]->referenceNo }}
                    @endif
                @endif
                @if($quotations)
                    @if(count($quotations) == 1)
                        {{ $quotations[0]->referenceNo }}
                    @endif
                @endif
            </th>
            <th colspan="5" > (CONTRACT) DATE:&nbsp;&nbsp;&nbsp;
                @if($quotationsByDeo)
                    @if(count($quotationsByDeo) == 1)
                        {{ \Carbon\Carbon::parse($quotationsByDeo[0]->documentDate)->format('d/m/Y') }}
                    @endif
                @endif
                @if($quotations)
                    @if(count($quotations) == 1)
                        {{ \Carbon\Carbon::parse($quotations[0]->documentDate)->format('d/m/Y') }}
                    @endif
                @endif
            </th>

        </tr>
    </table>
</div>

<div>
    <br>
</div>
<div class="row">
    <table>
        <tr> 
            <td colspan="1"></td>
            <th colspan="8">Buyer:</th>
            <th colspan="8" >Consignee:</th>
        </tr> 
        <tr> 
            <td colspan="1"></td>
            <th colspan="8">
                @if(!empty($request->customer) )
                    {{isset($request->customer->ReportTitle)?$request->customer->ReportTitle:' '}}<br>
                    {{isset($request->customer->customerAddress1)?$request->customer->customerAddress1:' '}}<br>
                @endif

                @if(!empty($request->CustomerContactDetails) )
                    TEL: {{isset($request->CustomerContactDetails->contactPersonTelephone)?$request->CustomerContactDetails->contactPersonTelephone:' '}}<br>
                    FAX: {{isset($request->CustomerContactDetails->contactPersonFax)?$request->CustomerContactDetails->contactPersonFax:' '}}<br>
                @endif
                   CUSTOMER VATIN : {{$request->vatNumber}}

            </th> 
            <th colspan="8" >
                @if(!empty($request->customerInvoiceLogistic) )
                        {{isset($request->customerInvoiceLogistic['consignee_name'])?$request->customerInvoiceLogistic['consignee_name']:' '}}<br>
                        {{isset($request->customerInvoiceLogistic['consignee_address'])?$request->customerInvoiceLogistic['consignee_address']:' '}}<br>
                        {{isset($request->customerInvoiceLogistic['consignee_contact_no'])?$request->customerInvoiceLogistic['consignee_contact_no']:' '}}<br>
                @endif
            </th> 
        </tr> 
    </table>

    <table>
        <tr>
            <td colspan="1"></td>
            <th colspan="4" >COUNTRY OF ORIGIN</th>
            <th colspan="4" >SULTANATE OF OMAN</th>
        </tr>
        <tr>
            <td colspan="1"></td>
            <th colspan="4">Vessel Name </th>
            <th colspan="4">Port Of Loading </th>
            <th colspan="6">Delivery Term </th>
            <th colspan="6">Terms Of Payment </th>
        </tr>

        <tr>
            <td colspan="1"></td>
            <td colspan="4">{{isset($request->customerInvoiceLogistic['vessel_no'])?$request->customerInvoiceLogistic['vessel_no']:' '}} </td>
            <td colspan="4">{{isset($request->customerInvoiceLogistic['port_of_loading']['port_name'])?$request->customerInvoiceLogistic['port_of_loading']['port_name']:' '}} </td>
            <td colspan="6">{{isset($request->customerInvoiceLogistic['delivery_payment'])?$request->customerInvoiceLogistic['delivery_payment']:' '}} </td>
            <td colspan="6">{{isset($request->customerInvoiceLogistic['payment_terms'])?$request->customerInvoiceLogistic['payment_terms']:' '}} </td>
        </tr>
    </table>

    <table >
        <tr>
            <td colspan="1"></td>
            <th colspan="4">B/Lading No </th>
            <th colspan="4">Port of Discharge</th>
            <th colspan="4">No of Containers</th>
            <th colspan="2">Packing</th>
            <td colspan="4">{{isset($request->customerInvoiceLogistic['packing'])?$request->customerInvoiceLogistic['packing']:' '}}</td>
        </tr>

        <tr>
            <td colspan="1"></td>
            <td colspan="4">{{isset($request->customerInvoiceLogistic['b_ladding_no'])?$request->customerInvoiceLogistic['b_ladding_no']:' '}}</td>
            <td colspan="4">{{isset($request->customerInvoiceLogistic['port_of_discharge']['port_name'])?$request->customerInvoiceLogistic['port_of_discharge']['port_name']:' '}}</td>
            <td colspan="4" style="text-align: center">{{isset($request->customerInvoiceLogistic['no_of_container'])?$request->customerInvoiceLogistic['no_of_container']:' '}}</td>
            <th colspan="2">Currency</th>
            <td colspan="4">{{isset($request->currency->CurrencyName)?$request->currency->CurrencyName:''}}</td>
        </tr>
    </table>

    <table >
        <thead>
            <tr style="background-color: #6798da; border: 1px solid !important;">
                <td colspan="1"></td>
            @if(count($request->issue_item_details) > 0 )
                <th colspan="3" style="text-align: center">Item</th>
                <th colspan="4" style="text-align: center">Content</th>
            @endif
            @if(count($request->invoicedetails) > 0 )
                <th colspan="3" style="text-align: center">Account Code</th>
                <th colspan="4" style="text-align: center">Description</th>
            @endif
                <th colspan="3" style="text-align: center">Delivery Note No</th>
                <th colspan="2" style="text-align: center">UOM</th>
                <th colspan="2" style="text-align: center">Quantity</th>
                <th colspan="2" style="text-align: center">Rate</th>
                <th colspan="2" style="text-align: center">VAT</th>
                <th colspan="3" style="text-align: center">Total Amount</th>
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
                        <td colspan="1"></td>
                        <td colspan="3" style="text-align: center;">{{$item->itemPrimaryCode}}</td>
                        <td colspan="4" style="text-align: left;">{{$item->itemDescription}}</td>
                        <td colspan="3" style="text-align: left;">{{$item->comments}}</td>
                        <td colspan="2" style="text-align: center;">{{isset($item->uom_issuing->UnitShortCode)?$item->uom_issuing->UnitShortCode:''}}</td>
                        <td colspan="2" style="text-align: center;">{{$item->qtyIssued}}</td>
                        <td colspan="2" style="text-align: right;">{{number_format($item->sellingCostAfterMargin,$numberFormatting)}}</td>
                        <td colspan="2" style="text-align: center;">{{$item->VATPercentage}}%</td>
                        <td colspan="3" style="text-align: right;">{{number_format($item->sellingTotal+$item->VATAmountLocal,$numberFormatting)}}</td>
                    </tr>
                    {{ $x++ }}
                @endif
            @endforeach
        @endif

        @if(!empty($request->invoicedetails))
            @foreach ($request->invoicedetails as $item)

                    {{$directTraSubTotal +=$item->invoiceAmount}}

                    <tr style="border: 1px solid !important;">
                        <td colspan="1"></td>
                        <td colspan="3" style="text-align: center;">{{$item->glCode}}</td>
                        <td colspan="4" style="text-align: left;">{{$item->glCodeDes}}</td>
                        <td colspan="3" style="text-align: left;">{{$item->comments}}</td>
                        <td colspan="2" style="text-align: center;">{{isset($item->unit->UnitShortCode)?$item->unit->UnitShortCode:''}}</td>
                        <td colspan="2" style="text-align: center;">{{$item->invoiceQty}}</td>
                        <td colspan="2" style="text-align: right;">{{number_format($item->unitCost,$numberFormatting)}}</td>
                        <td colspan="2" style="text-align: center;">{{$item->VATPercentage}}%</td>
                        <td colspan="3" style="text-align: right;">{{number_format($item->invoiceAmount+$item->VATAmountLocal,$numberFormatting)}}</td>
                    </tr>
                    {{ $x++ }}
            @endforeach
        @endif

        </tbody>
        <tbody>
            <tr style="border: 1px solid !important;">
                <td colspan="1"></td>
                <td colspan="10"></td>
                <td colspan="4" style="text-align: left;"><b>Total Taxable Value</b></td>
                <td colspan="7" style="text-align: right;">@if ($request->invoicedetails)
                        {{number_format($directTraSubTotal, $numberFormatting)}}
                    @endif</td>
            </tr>
            {{$totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
            {{$directTraSubTotal+=$totalVATAmount}}
            <tr style="border: 1px solid !important;">
                <td colspan="1"></td>
                <td colspan="10"></td>
                <td colspan="4" style="text-align: left;"><b>VAT @ {{round( ( ($request->tax && $request->tax->taxPercent ) ? $request->tax->taxPercent : 0 ), 2)}}%</b></td>
                <td colspan="7" style="text-align: right;">{{number_format($totalVATAmount, $numberFormatting)}}</td>
            </tr>
            <tr style="border: 1px solid !important;">
                <td colspan="1"></td>
                <td colspan="10"></td>
                <td colspan="4" style="text-align: left;"><b>Advance</b></td>
                @php
                    $sumAdvance = \App\Models\CustomerReceivePaymentDetail::where('bookingInvCodeSystem',$request->custInvoiceDirectAutoID)->sum('receiveAmountLocal');
                @endphp
                <td colspan="7" style="text-align: right;">{{number_format($sumAdvance, $numberFormatting)}}</td>
            </tr>

            <tr style="border: 1px solid !important;">
                <td colspan="1"></td>
                <td colspan="10"></td>
                <td colspan="4" style="text-align: left;"><b>Net Receivable</b></td>
                <td colspan="7" style="text-align: right;">{{number_format($directTraSubTotal - $sumAdvance, $numberFormatting)}}</td>
            </tr>

            <tr style="border: 1px solid !important;">
                <td colspan="1"></td>
                <td colspan="10"></td>
                <td colspan="4" style="text-align: left;"><b>Net Receivable in word</b></td>
                @php
                    $directTraSubTotalnumberformat=  number_format(($directTraSubTotal - $sumAdvance),empty($customerInvoice->currency) ? 2 : $customerInvoice->currency->DecimalPlaces);
                    $stringReplacedDirectTraSubTotal = str_replace(',', '', $directTraSubTotalnumberformat);
                    $amountSplit = explode(".", $stringReplacedDirectTraSubTotal);
                    $intAmt = 0;
                    $floatAmt = 00;

                    if (count($amountSplit) == 1) {
                        $intAmt = $amountSplit[0];
                        $floatAmt = 00;
                    } else if (count($amountSplit) == 2) {
                        $intAmt = $amountSplit[0];
                        $floatAmt = $amountSplit[1];
                    }
                    $numFormatter = new \NumberFormatter("ar", \NumberFormatter::SPELLOUT);
                    $floatAmountInWords = '';
                    $intAmountInWords = ($intAmt > 0) ? strtoupper($numFormatter->format($intAmt)) : '';

                    $numFormatterEn = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);

                    $floatAmt = (string)$floatAmt;

                    //add zeros to decimal point
                    if($floatAmt != 00){
                        $length = strlen($floatAmt);
                        if($length<$request->currency->DecimalPlaces){
                            $count = $request->currency->DecimalPlaces-$length;
                            for ($i=0; $i<$count; $i++){
                                $floatAmt .= '0';
                            }
                        }
                    }

                $amountWord = ucfirst($numFormatterEn->format($intAmt));
                $amountWord = str_replace('-', ' ', $amountWord);
                @endphp
                <td colspan="7" style="text-align: right;">{{$amountWord}}
                    @if ($floatAmt > 0)
                    and
                    {{$floatAmt}}/@if($request->currency->DecimalPlaces == 3)1000 @else 100 @endif
                    @endif
                    
                    only
                </td>
            </tr>
            <tr style="border: 1px solid !important;">
                <td colspan="1"></td>
                <td colspan="21" style="text-align: left;">We certify that the goods mentioned in this invoice are of Sultanate of Oman origin: Manufacturer Oman Chromite Company
                    (SAOG)-Commodity chrome ore.</td>

            </tr>
        </tbody>
    </table>

</div>
 