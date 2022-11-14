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
        <td><strong>Tender Code:</strong></td>
        <td colspan="2">
            @if ($bidData[0]['tender_code'])
                {{$bidData[0]['tender_code']}}
            @endif
        </td>
        <td colspan="2"><strong>Tender Title:</strong></td>
        <td colspan="4">
            @if ($bidData[0]['title'])
                {{$bidData[0]['title']}}
            @endif
        </td>
    </tr>
    <tr>
        <td><strong>Tender Description:</strong></td>
        <td colspan="2">
            @if ($bidData[0]['description'])
                {{$bidData[0]['description']}}
            @endif
        </td>
        <td colspan="2"><strong>Tender Publish Date:</strong></td>
        <td colspan="4">
            {{\Carbon\Carbon::parse($bidData[0]['published_at'])->format('d/m/Y')}}
        </td>
    </tr>
    <tr>
        @if ($bidData[0]['stage'] == 1)
            <td>
                @if ($bidData[0]['stage'] == 1)
                    <strong>Bid Opening Date:</strong>
                @endif
            </td>
            <td colspan="8">
                @if ($bidData[0]['stage'] == 1)
                    {{\Carbon\Carbon::parse($bidData[0]['bid_submission_opening_date'])->format('d/m/Y')}}
                @endif
            </td>
        @endif
        @if ($bidData[0]['stage'] == 2)
                <td><strong>Technical Bid Opening Date:</strong></td>
                <td colspan="2">
                    @if ($bidData[0]['technical_bid_opening_date'])
                        {{\Carbon\Carbon::parse($bidData[0]['technical_bid_opening_date'])->format('d/m/Y')}}
                    @endif
                </td>
                <td colspan="2"><strong>Commercial Bid Opening Date:</strong></td>
                <td colspan="4">
                    {{\Carbon\Carbon::parse($bidData[0]['commerical_bid_opening_date'])->format('d/m/Y')}}
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
            <td><strong>Sr. No</strong></td>
            <td><strong>Supplier Registration No</strong></td>
            <td><strong>Name of Bidder</strong></td>
            <td><strong>Bid Submission Date</strong></td>
            @foreach ($bidData[0]['DocumentAttachments'] as $doc)
                <td><strong>{{$doc->attachmentDescription}}</strong></td>
            @endforeach
            <td><strong>Status</strong></td>
            <td colspan="1"><strong>Summary</strong></td>
        </tr>
    <tbody>
       @foreach ($bidData[0]['srm_bid_submission_master'] as $item)
            <tr>
                <td>{{$loop->index+1}}</td>
                <td>{{$item->SupplierRegistrationLink->id}}</td>
                <td>{{$item->SupplierRegistrationLink->name}}</td>
                <td>{{\Carbon\Carbon::parse($item->created_at)->format('d/m/Y')}}</td>
                @foreach ($attachments[$loop->index] as $doc2)
                    <td>
                        @if($doc2->bid_verify->status == 1)
                            Admitted
                        @endif
                        @if($doc2->bid_verify->status == 2)
                            Admit with condition
                        @endif
                        @if($doc2->bid_verify->status == 3)
                            Rejected
                        @endif
                        {{--{{$doc2->bid_verify->bis_submission_master_id}}--}}
                    </td>
                @endforeach
                <td>
                    @if ($item->doc_verifiy_status == 1)
                        Approved
                    @endif
                    @if ($item->doc_verifiy_status == 2)
                        Rejected
                    @endif
                </td>
                <td colspan="1">{{$item->doc_verifiy_comment}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
