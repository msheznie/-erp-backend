<html>
<center>
    <table>
        <thead>
        <tr></tr>
        <tr>
            <td colspan="2"></td>
            @if($reportType == 1)
                <h1>{{ trans('custom.employee_expense_register') }}</h1>
            @elseif($reportType == 2)
                <h1>{{ trans('custom.assets_expense_register') }}</h1>
            @else
                <h1>{{ trans('custom.segment_expense_register') }}</h1>
            @endif
        </tr>
        <tr></tr>
        <tr style="font-weight: bold"></tr>
            <td>{{ trans('custom.report_filter') }}</td>
            <td>{{ trans('custom.start_date') }} : {{ $fromDate }} </td>
            <td>{{ trans('custom.end_date') }} :  {{ $toDate }}</td>
            <td>{{ trans('custom.type') }} :
                @if($reportType == 1)
                    {{ trans('custom.employee') }}
                @elseif($reportType == 2)
                    {{ trans('custom.assets') }}
                @else
                    {{ trans('custom.segments') }}
                @endif
            </td>

            @if($reportType == 2)
                <td>{{ trans('custom.assets') }} : {{$selectedAssets ?? trans('custom.not_selected') }}</td>
            @endif

            @if($reportType == 3)
                <td>{{ trans('custom.segments') }} : {{$selectedSegments ?? trans('custom.not_selected') }} </td>
            @endif
            <td>

                @if($reportType == 2)
                    <span>{{ trans('custom.group_by_asset') }} :
                        @if(empty($groupBy))
                            {{ trans('custom.no') }}
                        @else
                            {{ trans('custom.yes') }}
                        @endif
                    </span>
                @endif

                @if($reportType == 3)
                        <span>{{ trans('custom.group_by_segment') }} :
                            @if(empty($groupBy))
                                {{ trans('custom.no') }}
                            @else
                                {{ trans('custom.yes') }}
                            @endif
                        </span>
                @endif
            </td>


        </tr>
        <tr></tr>
        <tr>
            <th class="text-center">{{ trans('custom.issue_code') }}</th>
            <th class="text-center">{{ trans('custom.issue_date') }}</th>
            <th class="text-center">{{ trans('custom.request_no') }}</th>
            <th class="text-center">{{ trans('custom.item_code') }}</th>
            <th class="text-center">{{ trans('custom.item_description') }}</th>
            <th class="text-center">{{ trans('custom.uom') }}</th>
            <th class="text-center">{{ trans('custom.issued_qty') }}</th>
            @if($reportType == 1 || $reportType == 2)
                <th class="text-center">{{ trans('custom.issued_to') }} - @if($reportType == 2) {{ trans('custom.asset_code') }} @else {{ trans('custom.item_code') }} @endif</th>
                <th class="text-center">{{ trans('custom.issued_to') }} - @if($reportType == 2) {{ trans('custom.asset_description') }} @else {{ trans('custom.item_description') }} @endif</th>
            @else
                <th class="text-center">{{ trans('custom.segment') }}</th>
            @endif
            <th class="text-center">{{ trans('custom.qty') }}</th>
            <th class="text-center">{{ trans('custom.cost') }}</th>
            <th class="text-center">{{ trans('custom.amount') }}</th>
        </tr>
        </thead>
        <tbody>
            @foreach($reportData->groupedResults as $key => $groupedData)
                <tr>
                    @if(empty($groupByAsset))
                        <td colspan="11">{{ trans('custom.item_code') }} : {{$key}}</td>
                    @elseif($groupByAsset && $reportType == 2)
                        <td colspan="11">{{ trans('custom.asset_code') }} : {{$groupedData[0]->expenseAllocations[0]->empID ?? NULL}}</td>
                    @else
                        <td colspan="11">{{ trans('custom.segment') }} : {{$groupedData[0]->expenseAllocations[0]->empID ?? NULL}}</td>
                    @endif
                </tr>

                @foreach($groupedData as $expenseAllocations)
                    @if(isset($expenseAllocations->expenseAllocations))
                        @foreach($expenseAllocations->expenseAllocations as $item)
                          <tr>
                              <td>{{$expenseAllocations->itemIssueCode}}</td>
                              <td>{{$expenseAllocations->issueDate}}</td>
                              <td>{{$expenseAllocations->RequestCode}}</td>
                              <td>{{$expenseAllocations->itemPrimaryCode}}</td>
                              <td>{{$expenseAllocations->itemDescription}}</td>
                              <td>{{$expenseAllocations->unit}}</td>
                              <td>{{$expenseAllocations->qtyIssued}}</td>
                              <td>{{$item->empID ?? NULL}}</td>
                              @if($reportType == 1 || $reportType == 2)
                                 <td>{{$item->empName ?? NULL}}</td>
                              @endif
                              <td>{{optional($item)->assignedQty}}</td>
                              <td>{{optional($expenseAllocations)->issueCostLocal}}</td>
                              <td>{{optional($item)->assignedQty * optional($expenseAllocations)->issueCostLocal}}</td>
                          </tr>
                        @endforeach

                    @else
                        <tr>
                            <td>{{$expenseAllocations->itemIssueCode}}</td>
                            <td>{{$expenseAllocations->issueDate}}</td>
                            <td>{{$expenseAllocations->RequestCode}}</td>
                            <td>{{$expenseAllocations->itemPrimaryCode}}</td>
                            <td>{{$expenseAllocations->itemDescription}}</td>
                            <td>{{$expenseAllocations->unit}}</td>
                            <td>{{$expenseAllocations->qtyIssued}}</td>
                            <td>-</td>
                            @if($reportType == 1 || $reportType == 2)
                                <td>-</td>
                            @endif
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td colspan="11"></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</center>
</html>
