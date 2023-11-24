<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>

<table style="width:100%; font-size: 12px;">
    <tbody>
    <tr>
        <td colspan="4">
            <h4 style="text-align: center;">Minutes of Tender Awarding Report</h4>
        </td>
    </tr>
    <tr>
        <td><strong>Tender Code:</strong></td>
        <td>{{ $tenderMaster->tender_code }}</td>
        <td><strong>Tender Title:</strong></td>
        <td>{{ $tenderMaster->title }}</td>
    </tr>
    <tr>
        <td><strong>Bid Opening Date:</strong></td>
        <td>{{\Carbon\Carbon::parse($tenderMaster->bid_opening_date)->format('d/m/Y')}}</td>
        <td><strong>Committee Minimum Approval:</strong></td>
        <td>{{ $tenderMaster->min_approval_bid_opening }}</td>
    </tr>
    <tr>
        <td><strong>Tender Awarded Supplier Name:</strong></td>
        <td colspan="3">{{ $tenderMaster->ranking_supplier->supplier->name }}</td>
    </tr>
    <tr>
        <td><strong>Tender Awarding Comment:</strong></td>
        <td colspan="3">{{ $tenderMaster->final_tender_award_comment }}</td>
    </tr>
    </tbody>
</table>
<br/>
<table style="width:100%; font-size: 12px;">
    <tr>
        <td style="text-align: center;"><strong>Committee Members</strong></td>
        <td style="text-align: center;"><strong>Approved Date & Time</strong></td>
        <td style="text-align: center;"><strong>Approved Status</strong></td>
    </tr>
    <tbody>
    @foreach ($employeeDetails as $item)
        <tr><td>{{ $item->employee->empID }} | {{$item->employee->empName}}</td>
            <td>{{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i:s') }}</td>
            <td>
                @if ($item->tender_award_commite_mem_status == 1)
                    Approved
                @else
                    Rejected
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>


