<html>
<center>
    <tr>
        <td colspan="3"> </td>
        <td><h1>Budget Commitment Details Report</h1>  </td>
        <td colspan="3"> </td>

    <tr>
</center>
<table>
    <thead>

    <tr>

        <td>Finance Year : {{ $fromDate }} - {{ $fromDate }}</td>
        <td> </td>
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
        <th>GL Code</th>
        <th>Account Description</th>
        <th>GL Type</th>
        <th>Budgeted Amount (Last Years)</th>
        <th>Commitments</th>
        <th>Total available budget</th>
        <th>Actutal amount spent till date (Current budget)</th>
        <th>Actutal amount spent till date (Previous year commitments)</th>
        <th>Commitments (All open POs for current year)</th>
        <th>Commitments (All open POs for Previous year)</th>
        <th>Balance</th>

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

    </tfoot>
</table>
</html>
