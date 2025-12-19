
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
    
    @page {
        margin: 20px 30px 220px !important;
    }
  
    #footer {
        position: fixed;
        bottom: 0px;
        font-size: 10px;
    }

    body {
        font-size: 11.5px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        color: black;
    }

    @if(isset($lang) && $lang === 'ar')
        body {
        font-family: 'Noto Sans Arabic', sans-serif !important;
    }
    @endif

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
    }

    table > tbody > th > tr > td {
        font-size: 11.5px;
    }

    .theme-tr-head {
        background-color: #EBEBEB !important;
    }

    .text-left {
        text-align: left;
    }

    td {
        padding: 3px;
    }

    table {
        border-collapse: collapse;
        color: black;
    }

    .font-weight-bold {
        font-weight: 700 !important;
    }

    .table th {
        border: 1px solid !important;
    }

    .table td {
        border: 1px solid !important;
    }

    .table th {
        background-color: #8db3e2 !important;
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

    .pagenum:after {
        content: counter(page);
    }

    /*.content {
        margin-bottom: 30px;
    }
*/
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

    fieldset.scheduler-border {
        border: 1px solid #ddd !important;
        /*padding: 0 1.4em 1.4em 1.4em !important;*/
        padding: 0 0.5em 0em 0.8em !important;
        /*margin: 0 0 1.5em 0 !important;*/
        -webkit-box-shadow: 0px 0px 0px 0px #000;
        box-shadow: 0px 0px 0px 0px #000;
    }

    legend.scheduler-border {

        text-align: left !important;
        width: auto;
        padding: 5px;
        border-bottom: none;
    }

    legend {
        margin-top: -15px;
        font-size: 11.5px;
        color: black;
    }

    .thicker {
        font-weight: bold;
        font-size:11px;
    }

    .thick {
        font-weight: bold;
        font-size:9px;
    }

    .normal {
        font-weight: normal;
        font-size:10px;
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
    <br/>
    <br/>
    <br/>
    <br/>
    <div class="row">
        <table style="width:100%">
            <tr>
                <td width="30%">
                </td>


                <td width="40%" style="text-align: center;white-space: nowrap">
                    <div class="text-center">

                        <h3>
                            <b>TAX INVOICE</b>
                            <br>
                        </h3>

                      
                    </div>

                </td>
                <td style="width: 30%; text-align: right;">
                    <div style="display: flex;">
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="row">
        <br>
    </div>

    <div class="row">
        <table border="1" width="100%">
            <tr > 
                <td colspan="2" class="thicker">Seller</td>
                <td colspan="1" style="border-bottom: none;" ></td>
                <td colspan="1" class="thicker">Sales Order No.</td>
                <td colspan="1" class="thicker" style="text-align: center;">INVOICE NO: &nbsp;&nbsp;&nbsp;   {{$request->bookingInvCode}}</td>
                <td colspan="1" class="thicker">INVOICE DATE :&nbsp;&nbsp;&nbsp; @if(!empty($request->bookingDate))
                                                    {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                                    @endif
                </td>
            </tr> 
            <tr> 
                <td colspan="3"rowspan= "8"  style=" border-top: none;" class="thicker">
                    {{$request->CompanyName}}<br>
                    {{$request->CompanyAddress}}<br>
                    Tel: {{$request->CompanyTelephone}}<br>
                    Fax: {{$request->CompanyFax}}<br>
                    VAT NO: {{$request->vatRegistratonNumber}}<br>
                </td>
                <td colspan="1" class="thicker">
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

                </td>

                <td colspan="1" class="thicker"> Contract No:&nbsp;&nbsp;&nbsp;
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
                </td>
                <td colspan="1" class="thicker"> (CONTRACT) DATE:&nbsp;&nbsp;&nbsp;
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
                </td>

            </tr>
        </table>
    </div>
    <div>
        <br>
    </div>
    <div class="row">
        <table border="1" width="100%">
            <tr> 
                <td colspan="2" class="thicker">Buyer</td>
                <td colspan="2" style="border-bottom: none;" ></td>
                <td colspan="2" class="thicker">Consignee:</td>
                <td colspan="2" style="border-bottom: none;" ></td>
            </tr> 
            <tr> 
                <td colspan="4"  style=" border-top: none;" class="thicker">
                    @if(!empty($request->customer) )
                        {{isset($request->customer->ReportTitle)?$request->customer->ReportTitle:' '}}<br>
                        {{isset($request->customer->customerAddress1)?$request->customer->customerAddress1:' '}}<br>
                    @endif

                    @if(!empty($request->CustomerContactDetails) )
                        TEL: {{isset($request->CustomerContactDetails->contactPersonTelephone)?$request->CustomerContactDetails->contactPersonTelephone:' '}}<br>
                        FAX: {{isset($request->CustomerContactDetails->contactPersonFax)?$request->CustomerContactDetails->contactPersonFax:' '}}<br>
                    @endif
                       CUSTOMER VATIN : {{$request->vatNumber}}

                </td> 
                <td colspan="4"  style=" border-top: none;" class="thicker">
                    @if(!empty($request->customerInvoiceLogistic) )
                            {{isset($request->customerInvoiceLogistic['consignee_name'])?$request->customerInvoiceLogistic['consignee_name']:' '}}<br>
                            {{isset($request->customerInvoiceLogistic['consignee_address'])?$request->customerInvoiceLogistic['consignee_address']:' '}}<br>
                            {{isset($request->customerInvoiceLogistic['consignee_contact_no'])?$request->customerInvoiceLogistic['consignee_contact_no']:' '}}<br>
                    @endif
                </td> 
            </tr> 
        </table>
        <table border="1" width="100%">
            <tr> 
                <td colspan="2" style="text-align: center" class="thicker">COUNTRY OF ORIGIN</td>
                <td colspan="4" style="text-align: center" class="thicker">SULTANATE OF OMAN</td>
                <td colspan="8" rowspan="10"><span class="thicker"> Terms Of Payment: </span><br> 
                    <span class="normal">{{isset($request->customerInvoiceLogistic['payment_terms'])?$request->customerInvoiceLogistic['payment_terms']:' '}}</span>
                </td>
            </tr> 
            <tr> 

                <td colspan="2" style="text-align: center"><span class="thick"> Vessel Name</span> <br>
                    <span class="normal">{{isset($request->customerInvoiceLogistic['vessel_no'])?$request->customerInvoiceLogistic['vessel_no']:' '}}</span>
                </td> 
                <td colspan="2" style="text-align: center"><span class="thick"> Port Of Loading</span> <br> 
                    <span class="normal">{{isset($request->customerInvoiceLogistic['port_of_loading']['port_name'])?$request->customerInvoiceLogistic['port_of_loading']['port_name']:' '}}</span>
                </td> 
                <td colspan="2" style="text-align: center"><span class="thick"> Delivery Term</span> <br>
                    <span class="normal">{{isset($request->customerInvoiceLogistic['delivery_payment'])?$request->customerInvoiceLogistic['delivery_payment']:' '}}</span>
                </td> 
            </tr> 
        </table>
        <table border="1" width="100%">
            <tr>
                <td colspan="1" rowspan="4" style="text-align: center" class="thick">B/Lading No <br> 
                    <span class="normal">{{isset($request->customerInvoiceLogistic['b_ladding_no'])?$request->customerInvoiceLogistic['b_ladding_no']:' '}}</span>
                </td>
                <td colspan="1" rowspan="4" style="text-align: center" class="thick">Port of Discharge <br>
                    <span class="normal">{{isset($request->customerInvoiceLogistic['port_of_discharge']['port_name'])?$request->customerInvoiceLogistic['port_of_discharge']['port_name']:' '}}</span>
                </td>
                <td colspan="1"  style="text-align: center" class="thicker">No of Containers</td>
                <td colspan="4" class="thicker">Packing</td>
                <td colspan="4" >{{isset($request->customerInvoiceLogistic['packing'])?$request->customerInvoiceLogistic['packing']:' '}}</td>
            </tr>
            <tr>

                <td colspan="1" style="text-align: center">
                    <span class="normal">{{isset($request->customerInvoiceLogistic['no_of_container'])?$request->customerInvoiceLogistic['no_of_container']:' '}}</span>
                </td>
                <td colspan="4" class="thicker">Currency</td>
                <td colspan="4" >{{isset($request->currency->CurrencyName)?$request->currency->CurrencyName:''}}</td>
            </tr>
        </table>

            <table class="table">
                <thead>
                <tr style="background-color: #6798da;">
                @if(count($request->issue_item_details) > 0 )
                    <th style="width:10%;">Item</th>
                    <th style="width:25%;">Content</th>
                @endif
                @if(count($request->invoicedetails) > 0 )
                    <th style="width:10%;">Account Code</th>
                    <th style="width:25%;">Description</th>
                @endif
                    <th style="width:10%;text-align: center">Delivery Note No</th>
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
                                <td style="word-wrap:break-word;">{{$item->comments}}</td>
                                <td style="text-align: center;">{{isset($item->uom_issuing->UnitShortCode)?$item->uom_issuing->UnitShortCode:''}}</td>
                                <td style="text-align: center;">{{$item->qtyIssued}}</td>
                                <td class="text-right">{{number_format($item->sellingCostAfterMargin,$numberFormatting)}}</td>
                                <td style="text-align: center;">{{$item->VATPercentage}}%</td>
                                <td class="text-right">{{number_format($item->sellingTotal+$item->VATAmountLocal,$numberFormatting)}}</td>
                            </tr>
                            {{ $x++ }}
                        @endif
                    @endforeach
                @endif

                @if(!empty($request->invoicedetails))
                    @foreach ($request->invoicedetails as $item)

                            {{$directTraSubTotal +=$item->invoiceAmount}}

                            <tr style="border: 1px solid !important;">
                                <td style="text-align: center;">{{$item->glCode}}</td>
                                <td style="word-wrap:break-word;">{{$item->glCodeDes}}</td>
                                <td style="word-wrap:break-word;">{{$item->comments}}</td>
                                <td style="text-align: center;">{{isset($item->unit->UnitShortCode)?$item->unit->UnitShortCode:''}}</td>
                                <td style="text-align: center;">{{$item->invoiceQty}}</td>
                                <td class="text-right">{{number_format($item->unitCost,$numberFormatting)}}</td>
                                <td style="text-align: center;">{{$item->VATPercentage}}%</td>
                                <td class="text-right">{{number_format($item->invoiceAmount+$item->VATAmountLocal,$numberFormatting)}}</td>
                            </tr>
                            {{ $x++ }}
                    @endforeach
                @endif

                </tbody>
                <tbody>
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2" style="text-align: left; border-right: none !important;"><b>Total Taxable Value</b></td>
                    <td colspan="3" class="text-right">@if ($request->invoicedetails)
                            {{number_format($directTraSubTotal, $numberFormatting)}}
                        @endif</td>
                </tr>
                    {{$totalVATAmount = (($request->tax && $request->tax->amount) ? $request->tax->amount : 0)}}
                    {{$directTraSubTotal+=$totalVATAmount}}
                    <tr>
                        <td colspan="3"></td>
                        <td colspan="2" style="text-align: left; border-right: none !important;"><b>VAT @ {{round( ( ($request->tax && $request->tax->taxPercent ) ? $request->tax->taxPercent : 0 ), 2)}}%</b></td>
                        <td colspan="3" class="text-right">{{number_format($totalVATAmount, $numberFormatting)}}</td>
                    </tr>
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2" style="text-align: left; border-right: none !important;"><b>Advance</b></td>
                    @php
                        $sumAdvance = \App\Models\CustomerReceivePaymentDetail::where('bookingInvCodeSystem',$request->custInvoiceDirectAutoID)->sum('receiveAmountLocal');
                    @endphp
                    <td colspan="3" class="text-right">{{number_format($sumAdvance, $numberFormatting)}}</td>
                </tr>

                    <tr>
                        <td colspan="3"></td>
                        <td colspan="2" style="text-align: left; border-right: none !important;"><b>Net Receivable</b></td>
                        <td colspan="3" class="text-right">{{number_format($directTraSubTotal - $sumAdvance, $numberFormatting)}}</td>
                    </tr>

                    <tr>
                        <td colspan="3"></td>
                        <td colspan="2" style="text-align: left; border-right: none !important;"><b>Net Receivable in word</b></td>
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
                        <td colspan="3" class="text-right">{{$amountWord}}
                            @if ($floatAmt > 0)
                            and
                            {{$floatAmt}}/@if($request->currency->DecimalPlaces == 3)1000 @else 100 @endif
                            @endif
                            
                            only
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <table class="table">
                <tbody>
                    <tr>
                        <td colspan="8">We certify that the goods mentioned in this invoice are of Sultanate of Oman origin: Manufacturer Oman Chromite Company (SAOG)-Commodity chrome ore.</td>
                    </tr>
                </tbody>
            </table>
    </div>
  

