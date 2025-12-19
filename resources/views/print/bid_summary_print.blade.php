<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    .bit-tender-summary-report {
        font-size: 12px;
    }
</style>

<table style="width:100%" class="bit-tender-summary-report">
    <tbody>
    <tr>
        <td colspan="9">
            <h4 style="text-align: center;">Bid Opening Summary</h4>
        </td>
    </tr>
    <tr>
        <td colspan="9">&nbsp;</td>
    </tr>
    <tr>
        <td><strong>
            @if($documentType == 0)
                Tender Code:
            @elseif($documentType == 1)
                RFQ Code:
            @elseif($documentType == 2)
                RFI Code:
            @elseif($documentType == 3)
                RFP Code:
            @endif
        </strong></td>
        <td colspan="2">
            @if ($bidData[0]['tender_code'])
                {{$bidData[0]['tender_code']}}
            @endif
        </td>
        <td colspan="2"><strong>
            @if($documentType == 0)
                Tender Title:
            @elseif($documentType == 1)
                RFQ Title:
            @elseif($documentType == 2)
                RFI Title:
            @elseif($documentType == 3)
                RFP Title:
            @endif

        </strong></td>
        <td colspan="4">
            @if ($bidData[0]['title'])
                {{$bidData[0]['title']}}
            @endif
        </td>
    </tr>
    <tr>
        <td><strong>
           @if($documentType == 0)
                Tender Description:
            @elseif($documentType == 1)
                RFQ Description:
            @elseif($documentType == 2)
                RFI Description:
            @elseif($documentType == 3)
                RFP Description:
            @endif
        </strong></td>
        <td colspan="2">
            @if ($bidData[0]['description'])
                {{$bidData[0]['description']}}
            @endif
        </td>
        <td colspan="2"><strong>
            @if($documentType == 0)
                Tender Publish Date:
            @elseif($documentType == 1)
                RFQ Publish Date:
            @elseif($documentType == 2)
                RFI Publish Date:
            @elseif($documentType == 3)
                RFP Publish Date:
            @endif
        </strong></td>
        <td colspan="4">
            {{\Carbon\Carbon::parse($bidData[0]['published_at'])->format('d/m/Y')}}
        </td>
    </tr>
    <tr>
        @if ($isNegotiation == 1)
        <td colspan="1"><strong>
                @if($isNegotiation == 1)
                    Negotiation Tender Code:
                @endif
            </strong></td>
        <td colspan="2">
            @if ($bidData[0]['negotiation_code'] && $isNegotiation == 1)
                {{$bidData[0]['negotiation_code']}}
            @endif
        </td>
        @endif
        @if ($bidData[0]['stage'] == 1 && $isNegotiation == 1)
            <td colspan="2">
                @if ($bidData[0]['stage'] == 1 )
                    <strong>Bid Opening Date:</strong>
                @endif
            </td>
            <td colspan="4">
                @if ($bidData[0]['stage'] == 1)
                    @if ($bidData[0]['bid_opening_date'] && $isNegotiation == 0)
                            {{\Carbon\Carbon::parse($bidData[0]['bid_opening_date'])->format('d/m/Y')}}
                    @endif
                    @if (empty($bidData[0]['bid_opening_date']) || $isNegotiation == 1)
                            -
                    @endif
                @endif
            </td>
        @endif
        @if ($bidData[0]['stage'] == 1 && $isNegotiation == 0)
            <td colspan="1">
                @if ($bidData[0]['stage'] == 1 )
                    <strong>Bid Opening Date:</strong>
                @endif
            </td>
            <td colspan="8">
                @if ($bidData[0]['stage'] == 1)
                    @if ($bidData[0]['bid_opening_date'] && $isNegotiation == 0)
                            {{\Carbon\Carbon::parse($bidData[0]['bid_opening_date'])->format('d/m/Y')}}
                    @endif
                    @if (empty($bidData[0]['bid_opening_date']) || $isNegotiation == 1)
                            -
                    @endif
                @endif
            </td>
        @endif

        @if ($bidData[0]['stage'] == 2)
                <td><strong>Technical Bid Opening Date:</strong></td>
                <td colspan="2">
                    @if ($bidData[0]['technical_bid_opening_date'])
                        {{\Carbon\Carbon::parse($bidData[0]['technical_bid_opening_date'])->format('d/m/Y')}}
                    @endif

                    @if (empty($bidData[0]['technical_bid_opening_date']))
                        -
                    @endif
                </td>
                <td colspan="2"><strong>Commercial Bid Opening Date:</strong></td>
                <td colspan="4">

                @if ($bidData[0]['commerical_bid_opening_date'])
                    {{\Carbon\Carbon::parse($bidData[0]['commerical_bid_opening_date'])->format('d/m/Y')}}
                @endif

                @if (empty($bidData[0]['commerical_bid_opening_date']))
                   -
                @endif
                   
                </td>
        @endif
    </tr>
    <tr>
        <td colspan="9">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align: center;" colspan="9"><strong>Comparative Statement - Technical Document Submitted by Bidder</strong></td>
    </tr>
    </tbody>
</table>
<table style="width:100%" class="bit-tender-summary-report">
        <tr>
            <td style="text-align: center;"><strong>Sr. No</strong></td>
            <td style="text-align: center;"><strong>Supplier Registration No</strong></td>
            <td style="text-align: center;"><strong>Bid Submission Code</strong></td>
            <td style="text-align: center;"><strong>Name of Bidder</strong></td>
            <td style="text-align: center;"><strong>Bid Submission Date</strong></td>
            @foreach ($bidData[0]['DocumentAttachments'] as $doc)
                <td style="text-align: center;"><strong>{{$doc->attachmentDescription}}</strong></td>
            @endforeach
            <td style="text-align: center;"><strong>Status</strong></td>
            <td colspan="1" style="text-align: center;"><strong>Summary</strong></td>
        </tr>
    <tbody>
       @foreach ($bidData[0]['srm_bid_submission_master'] as $item)
            <tr>
                <td>{{$loop->index+1}}</td>
                <td>{{$item->SupplierRegistrationLink->id}}</td>
                <td>{{$item->bidSubmissionCode}}</td>
                <td>{{$item->SupplierRegistrationLink->name}}</td>
                <td>{{\Carbon\Carbon::parse($item->bidSubmittedDatetime)->format('d/m/Y')}}</td>
                @foreach ($attachments[$loop->index] as $doc2)
                    <td style="text-align: center;">
                    @switch($doc2->bid_verify->status)
                        @case(1)
                            Yes
                        @break
                        @case(2)
                            Yes
                        @break
                        @case(3)
                            No
                        @break
                    @endswitch
                    </td>
                @endforeach 

                    
                        @if (($documentType != 0 && $count != 0) || $documentType == 0)
                        <td>
                            @if ($item->doc_verifiy_status == 1)
                                Approved
                            @elseif ($item->doc_verifiy_status == 2)
                                Rejected
                            @else
                                -
                            @endif
                        </td> 
                        @else
                        <td style="text-align: center;"> - </td>
                        @endif
                

                    @if (!empty($item->doc_verifiy_comment))
                    <td colspan="1">{{$item->doc_verifiy_comment}} </td>
                    @else
                    <td style="text-align: center;"> - </td>
                    @endif 
            </tr>
        @endforeach
    </tbody>
</table>
