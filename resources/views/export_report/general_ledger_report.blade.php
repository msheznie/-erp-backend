<?php 
use Carbon\Carbon;
use Carbon\CarbonPeriod;
?>


<html>
    <table>
        <thead>
            <tr></tr>
            <tr>
                <th colspan="4"></th>
                <th  align='center' style="font-size:55%" ><h1>{{$reportTittle}}</h1></th>
            </tr>
            <tr></tr>
            <tr>
              <th style="font-size:15px;">Period From: {{(new Carbon($fromDate))->format('d/m/Y')}}</th>
              <th style="font-size:15px;">Period To: {{(new Carbon($toDate))->format('d/m/Y')}}</th>
            </tr>
            <tr>
                <th  style="font-size:15px;">{{$company->CompanyName}}</th>
            </tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
          </thead>
    </table>

    @if($reportSD == "all")
        <table>
            <thead>
                <tr>
                    <th>Company ID</th>
                    <th>Company Name</th>
                    <th>GL Code</th>
                    <th>Account Description</th>
                    <th>GL Type</th>
                    <th>Template Description</th>
                    <th>Document Type</th>
                    <th>Document Number</th>
                    <th>Date</th>
                    <th>Document Narration</th>
                    <th>Service Line</th>
                    <th>Contract</th>
                    <th>Supplier/Customer</th>

                    @if( in_array('confi_name', $extraColumns))
                        <th>Confirmed By</th>
                    @endif

                    @if( in_array('confi_date', $extraColumns))
                        <th>Confirmed Date</th>
                    @endif

                    @if( in_array('app_name', $extraColumns))
                        <th>Approved By</th>
                    @endif

                    @if( in_array('app_date', $extraColumns))
                        <th>Approved Date</th>
                    @endif

                    @if( $checkIsGroup->isGroup == 0)
                        <th>Debit (Local Currency - {{$currencyLocal}} )</th>
                        <th>Credit (Local Currency - {{$currencyLocal}} )</th>
                    @endif

                    <th>Debit (Reporting Currency - {{$currencyRpt}})</th>
                    <th>Credit (Reporting Currency - {{$currencyRpt}})</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($output as $item)
                <tr>
                    <td>{{$item->companyID}}</td>
                    <td>{{$item->CompanyName}}</td>
                    <td>{{$item->glCode}}</td>
                    <td>{{$item->AccountDescription}}</td>
                    <td>{{$item->glAccountType}}</td>
                    <td>{{$item->templateDetailDescription}}</td>
                    <td>{{$item->documentID}}</td>
                    <td>{{$item->documentCode}}</td>
                    <td>{{(new Carbon($item->documentDate))->format('d/m/Y')}}</td>
                    <td>{{$item->documentNarration}}</td>
                    <td>{{$item->serviceLineCode}}</td>
                    <td>{{$item->clientContractID}}</td>
                    <td>{{$item->isCustomer}}</td>

                    @if( in_array('confi_name', $extraColumns))
                        <td>{{$item->confirmedBy}}</td>
                    @endif

                    @if( in_array('confi_date', $extraColumns))
                        <td>{{(new Carbon($item->documentConfirmedDate))->format('d/m/Y')}}</td>
                    @endif

                    @if( in_array('app_name', $extraColumns))
                        <td>{{$item->approvedBy}}</td>
                    @endif

                    @if( in_array('app_date', $extraColumns))
                        <td>{{(new Carbon($item->documentFinalApprovedDate))->format('d/m/Y')}}</td>
                    @endif

                    @if( $checkIsGroup->isGroup == 0)
                        <td>{{round($item->localDebit, $decimalPlaceLocal)}}</td>
                        <td>{{round($item->localCredit, $decimalPlaceLocal)}}</td>
                    @endif
                    <td>{{round($item->rptDebit, $decimalPlaceRpt)}}</td>
                    <td>{{round($item->rptCredit, $decimalPlaceRpt)}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($reportSD == "glCode_wise")
        <table>
            {{-- @foreach ( as )
                
            @endforeach --}}
            <thead>
                <tr>
                    <th></th>
                </tr>
            </thead>
        </table>
    @endif

    
</html>