<html>
<center>
    <tr>
       <td colspan="6"> Image</td>
        <td colspan="8">    
            <span style="font-size:35px;"> {{$request->CompanyName}}</span>
        </td>

    <tr>

    <tr>
    <td colspan="6"> </td>
    <td colspan="1">
    <b>@if($request->is_pdo_vendor)
                                VAT
                            @endif
                            Invoice</b>
                    </td>
    </tr>
</center>
<br>
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
         @endif</td>
    <td colspan="2"> Invoice Number : </td>
    <td colspan="2">{{$request->bookingInvCode}}</td>
    </tr>


    <tr>
    <td colspan="10">   
        @if($request->line_customerShortCode)
         <span>{{$request->customer->CutomerCode}}</span>
         @endif</td>
         <td colspan="2"> Invoice Date : </td>
    <td colspan="2"><span>
                                 @if(!empty($request->bookingDate))
                                        {{\App\helper\Helper::dateFormat($request->bookingDate) }}
                                    @endif
                    </span>
                        </td>
    </tr>

    <tr>
    <td colspan="10">   
        {{$request->customer->ReportTitle}}</td>
        @if($request->line_performaCode)
                                <td colspan="2"><span class="font-weight-bold">Proforma Invoice No -</span></td>
                                <td colspan="2"><span>{{$request->invoicedetail->performadetails->performaCode}}</span></td>
                            
        @endif
    </tr>


    <tr>
        <td colspan="10">   
            {{$request->customer->customerAddress1}}</td>
            <td colspan="2"></td>
    
    </tr>

    <tr>
        <td colspan="10">   
        @if($request->lineSecondAddress)
            <span>{{$request->customer->customerAddress2}}

            </span>
            @else
            <span>{{$request->customer->customerCity}}</span>
            @endif
        </td>


        @if($request->line_dueDate)
                        <td colspan="2" width="120px"><span class="font-weight-bold">Due Date -</span></td>
                        <td colspan="2"><span>
                                @if(!empty($request->invoiceDueDate))
                                    {{\App\helper\Helper::dateFormat($request->invoiceDueDate)}}
                                @endif
                    </span></td>
                @endif
    </tr>

    <tr>
    <td colspan="10">   
    @if ($request->is_pdo_vendor) {{$request->vendorCode}}   @endif</td>
    @if ($request->line_contractNo)
            <td colspan="2" width="120px"><span
                        class="font-weight-bold">Contract @if($request->line_paymentTerms) Ref
                    No @endif </span></td>
            <td colspan="2"><span>{{$request->invoicedetails[0]->clientContractID}}</span></td>
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
    <br>
    <br>

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
            <b><span>{{$request->invoicedetail->billmaster->ticketmaster->rig->RigDescription}}</span> |
                <span> {{$request->invoicedetail->billmaster->ticketmaster->regNo}}</span></b>
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
    <td colspan="14">
    </td>
    <td colspan="2">
    <div class="row">
        <div style="text-align: right"><b>Currency
                : {{empty($request->currency) ? '' : $request->currency->CurrencyCode}} </b></div>
    </div>
    </td>
    </tr>
    <div class="row">

        @if($request->linePdoinvoiceDetails)
            <table class="table table-bordered table-striped table-sm" style="width: 100%;">
                <thead>
                <tr class="">
                    <th colspan="1" style="width:1%"></th>
                    <th colspan="3" style="text-align: center">Client Reference</th>
                    <th colspan="3" style="text-align: center">PO Detail ID</th>
                    <th colspan="3" style="text-align: center">Item Description</th>
                    <th colspan="2" style="text-align: right">Quantity</th>
                    <th colspan="2" style="text-align: right">Unit Price</th>
                    <th colspan="2" style="text-align: right">Total Amount</th>
                </tr>
                </thead>
                <tbody>
                {{$decimal = 2}}
                {{$x=1}}
                {{$directTraSubTotal=0}}
                {{$numberFormatting=empty($request->currency) ? 2 : $request->currency->DecimalPlaces}}
                @foreach ($request->linePdoinvoiceDetails as $item)
                    {{$directTraSubTotal +=$item->amount}}
                    <tr style="border-top: 2px solid #333 !important;border-bottom: 2px solid #333 !important;background-color: white">
                        <td colspan="1">{{$x}}</td>
                        <td colspan="3">{{$item->client_referance}}</td>
                        <td colspan="3">{{$item->po_detail_id}}</td>
                        <td colspan="3">{{$item->item_description}}</td>
                        <td colspan="2" style="text-align: right">{{number_format($item->qty,2)}}</td>
                        <td colspan="2" style="text-align: right">{{number_format($item->unit_price,$numberFormatting)}}</td>
                        <td colspan="2" style="text-align: right" class="text-right">{{number_format($item->amount,$numberFormatting)}}</td>
                    </tr>
                    {{ $x++ }}
                @endforeach
                </tbody>

            </table>

        @endif

    </div>
    <div class="row">
        <table style="width:100%;" class="table table-bordered">
            <tbody>
            <tr>
                <td colspan="12" style="border-bottom: none !important;border-left: none !important;width: 60%;">.</td>
                <td colspan="2" class="text-right" style="width: 20%;border-bottom: none !important"><span
                            class="font-weight-bold"
                            style="border-bottom: none !important;font-size: 11.5px">Total:</span>
                </td>
                <td class="text-right"
                    style="font-size: 11.5px;width: 20%;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;background-color: #EBEBEB">
                <span class="font-weight-bold">
                @if ($request->linePdoinvoiceDetails)
                        {{number_format($directTraSubTotal, $numberFormatting)}}
                    @endif
                </span>
                </td>
            </tr>

            @if ($request->tax)
                {{$directTraSubTotal+=$request->tax->amount}}
                <tr>
                    <td colspan="12" style="border:none !important;">
                        .
                    </td>
                    <td colspan="2" class="text-right" style="border:none !important;"><span
                                class="font-weight-bold"
                                style="font-size: 11.5px">Tax Amount ({{$request->tax->taxPercent}} %)
                            </span></td>
                    <td class="text-right"
                        style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;"><span
                                class="font-weight-bold">{{number_format($request->tax->amount, $numberFormatting)}}</span>
                    </td>
                </tr>

                <tr>
                    <td colspan="12" style="border-bottom: none !important;border-top: none !important;border-left: none !important;">
                        .
                    </td>
                    <td colspan="2" class="text-right" style="border:none !important;"><span
                                class="font-weight-bold"
                                style="font-size: 11.5px">Net Amount</span>
                    </td>
                    <td class="text-right"
                        style="font-size: 11.5px;border-left: 1px #EBEBEB !important;border-right: 1px #EBEBEB !important;background-color: #EBEBEB">
                            <span class="font-weight-bold">

                                    {{number_format($directTraSubTotal, $numberFormatting)}}

                            </span>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
    </html>