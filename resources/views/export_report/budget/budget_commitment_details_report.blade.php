<html>
    <tr>
        <td colspan="3"> </td>
        <td><h1>{{ trans('custom.budget_commitments_detail_report') }}</h1>  </td>
        <td colspan="3"> </td>

    <tr>
<table>
    <thead>

    <tr>
        <td>{{ trans('custom.date') }}: </td>
        <td> {{ $fromDate }}</td>
        <td> </td>
        <td> </td>
    </tr>
    <tr>
        <td>{{ trans('custom.segment') }}:</td>
        <td> {{$serviceLines}}</td>
        <td> </td>
        <td> </td>
    </tr>
    <tr>
        <td> {{ trans('custom.currency') }}:   </td>
        <td> @if($currency == 1) <span>{{ trans('custom.local_currency') }}</span> @else <span>{{ trans('custom.reporting_currency') }}</span> @endif</td>
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
        <th width="10">{{ trans('custom.gl_code') }}</th>
        <th width="50">{{ trans('custom.account_description') }}</th>
        <th width="10">{{ trans('custom.gl_type') }}</th>
        <th width="50">{{ trans('custom.budgeted_amount_last_years') }}</th>
        <th width="50">{{ trans('custom.commitments') }}</th>
        <th width="50">{{ trans('custom.total_available_budget') }}</th>
        <th width="50">{{ trans('custom.actual_amount_spent_till_date_current_budget') }}</th>
        <th width="50">{{ trans('custom.actual_amount_spent_till_date_previous_year_commitments') }}</th>
        <th width="50">{{ trans('custom.commitments_all_open_pos_for_current_year') }}</th>
        <th width="50">{{ trans('custom.commitments_all_open_pos_for_previous_year') }}</th>
        <th width="50">{{ trans('custom.balance') }}</th>

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
            <td colspan="3">{{ trans('custom.total') }}</td>
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
