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
            <h4 style="text-align: center;">Supplier Ranking Summary</h4>
        </td>
    </tr>
    <tr>
        <td><strong>Tender Code:</strong></td>
        <td colspan="2">{{ $tenderMaster->tender_code }}</td>
        <td colspan="2"><strong>Tender Title:</strong></td>
        <td colspan="4">{{ $tenderMaster->title }}</td>
    </tr>
    <tr>
        @if ($isNegotiation == 1)
            <td colspan="1"><strong>
                    @if($isNegotiation == 1)
                        Negotiation Tender Code:
                    @endif
                </strong></td>
            <td colspan="2">
                @if ($tenderMaster->negotiation_code && $isNegotiation == 1)
                    {{ $tenderMaster->negotiation_code }}
                @endif
            </td>
        @endif
        @if ($tenderMaster->stage == 1 && $isNegotiation == 1)
            <td colspan="2">
                @if ($tenderMaster->stage == 1 )
                    <strong>Bid Opening Date:</strong>
                @endif
            </td>
            <td colspan="4">
                @if ($tenderMaster->stage == 1)
                    @if ($tenderMaster->bid_opening_date && $isNegotiation == 0)
                        {{\Carbon\Carbon::parse($tenderMaster->bid_opening_date)->format('d/m/Y')}}
                    @endif
                    @if (empty($tenderMaster->bid_opening_date) || $isNegotiation == 1)
                        -
                    @endif
                @endif
            </td>
        @endif
        @if ($tenderMaster->stage == 1 && $isNegotiation == 0)
            <td colspan="1">
                @if ($tenderMaster->stage == 1 )
                    <strong>Bid Opening Date:</strong>
                @endif
            </td>
            <td colspan="8">
                @if ($tenderMaster->stage == 1)
                    @if ($tenderMaster->bid_opening_date && $isNegotiation == 0)
                        {{\Carbon\Carbon::parse($tenderMaster->bid_opening_date)->format('d/m/Y')}}
                    @endif
                    @if (empty($tenderMaster->bid_opening_date) || $isNegotiation == 1)
                        -
                    @endif
                @endif
            </td>
        @endif

        @if ($tenderMaster->stage == 2)
            <td><strong>Technical Bid Opening Date:</strong></td>
            <td colspan="2">
                @if ($tenderMaster->technical_bid_opening_date)
                    {{\Carbon\Carbon::parse($tenderMaster->technical_bid_opening_date)->format('d/m/Y')}}
                @endif

                @if (empty($tenderMaster->technical_bid_opening_date))
                    -
                @endif
            </td>
            <td colspan="2"><strong>Commercial Bid Opening Date:</strong></td>
            <td colspan="4">

                @if ($tenderMaster->commerical_bid_opening_date)
                    {{\Carbon\Carbon::parse($tenderMaster->commerical_bid_opening_date)->format('d/m/Y')}}
                @endif
                @if (empty($tenderMaster->commerical_bid_opening_date))
                    -
                @endif
            </td>
        @endif
    </tr>
    <tr>
        <td><strong>Comment:</strong></td>
        <td colspan="8">
            @if ($isNegotiation == 1 )
                {{ $tenderMaster->negotiation_award_comment }}
            @endif
            @if ($isNegotiation == 0 )
                {{ $tenderMaster->award_comment }}
            @endif
        </td>
    </tr>
    </tbody>
</table>
<table style="width:100%" class="bit-tender-summary-report">
    <tr>
        <td style="text-align: center;"><strong>Sr. No</strong></td>
        <td style="text-align: center;"><strong>Bid Submission Code</strong></td>
        <td style="text-align: center;"><strong>Bid Submission Date</strong></td>
        <td style="text-align: center;"><strong>Supplier Name</strong></td>
        <td style="text-align: center;"><strong>Commercial Weightage</strong></td>
        <td style="text-align: center;"><strong>Technical Weightage</strong></td>
        <td style="text-align: center;"><strong>Total Weightage</strong></td>
        <td style="text-align: center;"><strong>Ranking</strong></td>
        <td style="text-align: center;"><strong>Awarding</strong></td>
    </tr>
    <tbody>
    @foreach ($awardSummary as $item)
        <tr>
            <td>{{ $loop->index+1 }}</td>
            <td>{{ $item->bidSubmissionCode }}</td>
            <td>{{ \Carbon\Carbon::parse($item->bidSubmittedDatetime)->format('d/m/Y') }}</td>
            <td>{{ $item->name }}</td>
            <td style="text-align: right;">{{ $item->com_weightage }}</td>
            <td style="text-align: right;">{{ $item->tech_weightage }}</td>
            <td style="text-align: right;">{{ $item->total_weightage }}</td>
            <td style="text-align: center;">{{ $item->combined_ranking }}</td>
            <td>
              @if ($item->award == 1)
                  Awarded
              @else
                Not Awarded
              @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
