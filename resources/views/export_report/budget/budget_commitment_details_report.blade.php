<html>
    <tr>
        <td colspan="3"> </td>
        <td><h1>Budget Commitment Details Report</h1>  </td>
        <td colspan="3"> </td>

    <tr>
<table>
    <thead>

    <tr>
        <td>Date: </td>
        <td> {{ $fromDate }}</td>
        <td> </td>
        <td> </td>
    </tr>
    <tr>
        <td>Segment:</td>
        <td> {{$serviceLines}}</td>
        <td> </td>
        <td> </td>
    </tr>
    <tr>
        <td> Currency:   </td>
        <td> @if($currency == 1) <span>Local Currency</span> @else <span>Reporting Currency</span> @endif</td>
        <td> </td>
        <td> </td>
    </tr>
    <tr>

{{--        <td>Segment : {{ $entity['segment_by']['ServiceLineDes'] }}</td>--}}
{{--        <td> </td>--}}
{{--        <td> </td>--}}
{{--        <td> </td>--}}
{{--        <td>Template : {{ $entity['template_master']['description'] }}</td>--}}

    </tr>
    <tr></tr>
    <tr></tr>

    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th colspan="3"></th>
        <th colspan="5"></th>
    </tr>
    <tr>
        <th width="10">GL Code</th>
        <th width="50">Account Description</th>
        <th width="10">GL Type</th>
        <th width="50">Budgeted Amount (Last Years)</th>
        <th width="50">Commitments</th>
        <th width="50">Total available budget</th>
        <th width="50">Actutal amount spent till date (Current budget)</th>
        <th width="50">Actutal amount spent till date (Previous year commitments)</th>
        <th width="50">Commitments (All open POs for current year)</th>
        <th width="50">Commitments (All open POs for Previous year)</th>
        <th width="50">Balance</th>

    </tr>
    </thead>
    <tbody>
    @foreach($reportData as $item)
        <tr>
            <td>{{$item->glCode}}</td>
            <td>{{$item->accountsDescription}}</td>
            <td>{{$item->glTypes}}</td>
            <td>{{$item->budgetAmount}}</td>
            <td>{{$item->commitments}}</td>
            <td>{{$item->totalAvailableBudget}}</td>
            <td>{{$item->actualAmountSpentTillDateCB}}</td>
            <td>{{$item->actualAmountSpentTillDatePC}}</td>
            <td>{{$item->commitmentsForCurrentYear}}</td>
            <td>{{$item->commitmentsFromPreviosYear}}</td>
            <td>{{$item->balance}}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">Total</td>
            <td>{{$total['totalBudgetAmount']}}</td>
            <td>{{$total['totalCommitments']}}</td>
            <td>{{$total['totalAvailableBudget']}}</td>
            <td>{{$total['totalActualAmountSpentTillDateCB']}}</td>
            <td>{{$total['totalActualAmountSpentTillDatePC']}}</td>
            <td>{{$total['totalCommitmentsForCurrentYear']}}</td>
            <td>{{$total['totalCommitmentsFromPreviousYear']}}</td>
            <td>{{$total['total']}}</td>

        </tr>
    </tfoot>
</table>
</html>
