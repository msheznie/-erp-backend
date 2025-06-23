<html>
<center>
    <table>
        <thead>
        <tr></tr>
        <tr>
            <td colspan="2"></td>
            @if($reportType == 1)
                <h1>Employee expense register</h1>
            @elseif($reportType == 2)
                <h1>Assets expense register</h1>
            @else
                <h1>Segment expense register</h1>
            @endif
        </tr>
        <tr></tr>
        <tr style="font-weight: bold">
            <td>Report Filter</td>
            <td>Start Date : {{ $fromDate }} </td>
            <td>End Date :  {{ $toDate }}</td>
            <td>Type :
                @if($reportType == 1)
                    Employee
                @elseif($reportType == 2)
                    Assets
                @else
                    Segments
                @endif
            </td>

            @if($reportType == 2)
                <td>Assets : {{$selectedAssets ?? 'Not Selected'}}</td>
            @endif

            @if($reportType == 3)
                <td>Segments : {{$selectedSegments ?? 'Not Selected'}} </td>
            @endif
            <td>

                @if($reportType == 2)
                    <span>Group By Asset :
                        @if(empty($groupBy))
                            NO
                        @else
                            YES
                        @endif
                    </span>
                @endif

                @if($reportType == 3)
                        <span>Group By Segment :
                            @if(empty($groupBy))
                                NO
                            @else
                                YES
                            @endif
                        </span>
                @endif
            </td>


        </tr>
        <tr></tr>
        <tr>
            <th class="text-center">Issue Code</th>
            <th class="text-center">Issue Date</th>
            <th class="text-center">Request No</th>
            <th class="text-center">Item Code</th>
            <th class="text-center">Item Description</th>
            <th class="text-center">UOM</th>
            <th class="text-center">Issued Qty</th>
            @if($reportType == 1 || $reportType == 2)
                <th class="text-center">Issued To - @if($reportType == 2) Asset Code @else Item Code @endif</th>
                <th class="text-center">Issued To - @if($reportType == 2) Asset Description @else Item Description @endif</th>
            @else
                <th class="text-center">Segment</th>
            @endif
            <th class="text-center">Qty</th>
            <th class="text-center">Cost</th>
            <th class="text-center">Amount</th>
        </tr>
        </thead>
        <tbody>
            @foreach($reportData->groupedResults as $key => $groupedData)
                <tr>
                    @if(empty($groupByAsset))
                        <td colspan="11">Item Code : {{$key}}</td>
                    @elseif($groupByAsset && $reportType == 2)
                        <td colspan="11">Asset Code : {{$groupedData[0]->expenseAllocations[0]->empID ?? NULL}}</td>
                    @else
                        <td colspan="11">Segment : {{$groupedData[0]->expenseAllocations[0]->empID ?? NULL}}</td>
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
                      <td>{{$item->empName ?? NULL}}</td>
                      @if($reportType == 1 || $reportType == 2)
                         <td>{{$item->empName ?? NULL}}</td>
                      @endif
                      <td>{{optional($item)->assignedQty}}</td>
                      <td>{{optional($expenseAllocations)->issueCostLocal}}</td>
                      <td>{{optional($item)->assignedQty * optional($expenseAllocations)->issueCostLocal}}</td>
                  </tr>
                    @endforeach
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
