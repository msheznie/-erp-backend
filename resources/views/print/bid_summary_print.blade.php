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
        <td><strong>Tender Id:</strong></td>
        <td colspan="2">
            @if ($podata[0]['id'])
                {{$podata[0]['id']}}
            @endif
        </td>
        <td colspan="2"><strong>Tender Title:</strong></td>
        <td colspan="4">
            @if ($podata[0]['title'])
                {{$podata[0]['title']}}
            @endif
        </td>
    </tr>
    <tr>
        <td><strong>Tender Description:</strong></td>
        <td colspan="2">
            @if ($podata[0]['description'])
                {{$podata[0]['description']}}
            @endif
        </td>
        <td colspan="2"><strong>Tender Publish Date:</strong></td>
        <td colspan="4">22/10/2022</td>
    </tr>
    <tr>
        <td colspan="5"><strong>Bid Opening Date / Technical / Commercial Bid Opening Date:</strong></td>
        <td colspan="4">
            @if ($podata[0]['bid_submission_opening_date'])
                {{$podata[0]['bid_submission_opening_date']->format('d/m/Y')}}
            @endif</td>
    </tr>
    <tr>
        <td colspan="9">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align: center;" colspan="9"><strong>Comparative Statement - Technical Document Submitted by Bidder</strong></td>
    </tr>
        <tr>
            <td><strong><strong>Sr. No</strong></td>
            <td><strong>Supplier Registration No</strong></td>
            <td><strong>Name of Bidder</strong></td>
            <td><strong>Bid Submission Date</strong></td>
            <td></td>
            <td></td>
            @foreach ($podata[0]['DocumentAttachments'] as $doc)
                <td><strong>{{$doc->attachmentDescription}}</strong></td>
            @endforeach
            <td><strong>Status</strong></td>
            <td><strong>Summary</strong></td>
        </tr>
       @foreach ($podata[0]['srm_bid_submission_master'] as $item)
            <tr>
                <td>{{ $loop->index+1}}</td>
                <td>{{$item->SupplierRegistrationLink->id}}</td>
                <td>{{$item->SupplierRegistrationLink->name}}</td>
                <td>{{$item->created_at}}</td>
                <td></td>
                <td></td>
                @foreach ($podata[0]['DocumentAttachments'] as $doc2)
                    <td>{{$doc2->attachmentDescription}}</td>
                @endforeach
                <td>
                    @if ($item->doc_verifiy_status == 1)
                        Approved
                    @endif
                    @if ($item->doc_verifiy_status == 0)
                        Rejected
                    @endif
                </td>
                <td>{{$item->doc_verifiy_comment}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
