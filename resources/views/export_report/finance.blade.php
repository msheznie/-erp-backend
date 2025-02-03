<html>
<table>
    <thead>
        <tr>
            <th colspan="5" align="center">{{$template->reportName}}</th>
        </tr>
        <tr>
            <th colspan="5" align="center">{{$company->CompanyName}}</th>
        </tr>
        <tr></tr>
        @if ($month != null)
            <tr>
                <th>As of - {{$month}}</th>
            </tr>
        @endif

        @if ($from_date != null && $to_date != null)
            <tr>
                <th>Period From - {{$from_date}}</th>
            </tr>
            <tr>
                <th>Period To - {{$to_date}} </th>
            </tr>
        @endif
        <tr>Currency: {{$currencyCode}}</tr>
        <tr></tr>
        <tr></tr>
        <tr>
            @if($fourthLevel)
                <th colspan="5">Description</th>
            @elseif($thirdLevel)
                <th colspan="4">Description</th>
            @elseif($secondLevel)
                <th colspan="3">Description</th>
            @elseif($firstLevel)
                <th colspan="2">Description</th>
            @else
                <th>Description</th>
            @endif
            @foreach ($columnHeader as $column)
            <th>{{$column['description']}}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($reportData as $header)
        @if($header->hideHeader == 0)
        <tr>
            @if($header->itemType == 1 || $header->itemType == 4 || $header->itemType == 6)
            <td>
                <strong>{{$header->detDescription}}</strong>
            </td>
            @if($firstLevel)
            <td></td>
            @endif
            @if($secondLevel)
            <td></td>
            @endif
            @if($thirdLevel)
            <td></td>
            @endif
            @if($fourthLevel)
            <td></td>
            @endif
            @foreach ($columns as $column)
            <td></td>
            @endforeach
            @endif
            @if($header->itemType == 3 || $header->itemType == 5)
            <td>
                <strong>{{$header->detDescription}}</strong>
            </td>
            @if($firstLevel)
            <td></td>
            @endif
            @if($secondLevel)
            <td></td>
            @endif
            @if($thirdLevel)
            <td></td>
            @endif
            @if($fourthLevel)
            <td></td>
            @endif
            @endif
            @foreach ($columns as $column)
            @if($header->itemType == 3)
            <td style="font-weight: bold;">
                @if(isset($header->$column))
                {{round($header->$column, $decimalPlaces)}}
                @else
                0
                @endif
            </td>
            @endif
            @if($header->itemType == 5)
            <td>
                @if(isset($header->$column))
                {{round($header->$column, $decimalPlaces)}}
                @else
                0
                @endif
            </td>
            @endif
            @endforeach
        </tr>
        @endif
        @if(isset($header->detail))
            @foreach ($header->detail as $data)
                <tr>
                    @if($data->isFinalLevel == 1)
                        <td></td>
                        @if($data->itemType == 3)
                            <td style="font-weight: bold;">
                                {{$data->detDescription}}
                            </td>
                        @else
                            <td>
                                {{$data->detDescription}}
                            </td>
                        @endif
                        @if($secondLevel)
                            <td></td>
                        @endif
                        @if($thirdLevel)
                            <td></td>
                        @endif
                        @if($fourthLevel)
                            <td></td>
                        @endif
                        @foreach ($columns as $column)
                            @if($data->itemType == 3)
                                <td style="font-weight: bold;">
                                    @if(isset($data->$column))
                                        {{round($data->$column, $decimalPlaces)}}
                                    @else
                                        0
                                    @endif
                                </td>
                            @else
                                <td>
                                    @if(isset($data->$column))
                                        {{round($data->$column, $decimalPlaces)}}
                                    @else
                                        0
                                    @endif
                                </td>
                            @endif
                        @endforeach
                    @endif
                    @if($data->isFinalLevel == 0)
                        <td></td>
                        <td>
                            {{$data->detDescription}}
                        </td>
                        @if($secondLevel)
                            <td></td>
                        @endif
                        @if($thirdLevel)
                            <td></td>
                        @endif
                        @if($fourthLevel)
                            <td></td>
                        @endif
                        @foreach ($columns as $column)
                            @if($data->itemType == 8 || $data->itemType == 7)
                                <td>
                                    @if(isset($data->$column))
                                        {{round($data->$column, $decimalPlaces)}}
                                    @else
                                        0
                                    @endif
                                </td>
                            @else
                                <td></td>
                            @endif
                        @endforeach
                    @endif
                </tr>
                @if($data->isFinalLevel == 1)
                    @if(@isset($data->glCodes) && $data->glCodes != null)
                        @foreach ($data->glCodes as $data2)
                            @if($data->expanded)
                                <tr>
                                    <td></td>
                                    <td>
                                        {{$data2->glCode}} - {{$data2->glDescription}}
                                    </td>
                                    @if($secondLevel)
                                        <td></td>
                                    @endif
                                    @if($thirdLevel)
                                        <td></td>
                                    @endif
                                    @if($fourthLevel)
                                        <td></td>
                                    @endif
                                    @foreach ($columns as $column)
                                        <td>
                                            @if(isset($data2->$column))
                                                {{round($data2->$column, $decimalPlaces)}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endif
                        @endforeach
                    @endif
                @endif
                @if(isset($data->detail))
                    @foreach ($data->detail as $dataSubTwo)
                        <tr>
                            @if($dataSubTwo->isFinalLevel == 1)
                                <td></td>
                                <td></td>
                                @if($dataSubTwo->itemType == 3)
                                    <td style="font-weight: bold;">
                                        {{$dataSubTwo->detDescription}}
                                    </td>
                                @else
                                    <td>
                                        {{$dataSubTwo->detDescription}}
                                    </td>
                                @endif
                                @if($thirdLevel)
                                    <td></td>
                                @endif
                                @if($fourthLevel)
                                    <td></td>
                                @endif
                                @foreach ($columns as $column)
                                    @if($dataSubTwo->itemType == 3)
                                        <td style="font-weight: bold;">
                                            @if(isset($dataSubTwo->$column))
                                                {{round($dataSubTwo->$column, $decimalPlaces)}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @else
                                        <td>
                                            @if(isset($dataSubTwo->$column))
                                                {{round($dataSubTwo->$column, $decimalPlaces)}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @endif
                                @endforeach
                            @endif
                            @if($dataSubTwo->isFinalLevel == 0)
                                <td></td>
                                <td></td>
                                <td>
                                    {{$dataSubTwo->detDescription}}
                                </td>
                                @if($thirdLevel)
                                    <td></td>
                                @endif
                                @if($fourthLevel)
                                    <td></td>
                                @endif
                                @foreach ($columns as $column)
                                    <td></td>
                                @endforeach
                            @endif
                        </tr>
                        @if($dataSubTwo->isFinalLevel == 1 && @isset($dataSubTwo->glCodes))
                            @foreach ($dataSubTwo->glCodes as $data23)
                                @if($dataSubTwo->expanded)
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            {{$data23->glCode}} - {{$data23->glDescription}}
                                        </td>
                                        @if($thirdLevel)
                                            <td></td>
                                        @endif
                                        @if($fourthLevel)
                                            <td></td>
                                        @endif
                                        @foreach ($columns as $column)
                                            <td>
                                                @if(isset($data23->$column))
                                                    {{round($data23->$column, $decimalPlaces)}}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                        @if(isset($dataSubTwo->detail))
                            @foreach ($dataSubTwo->detail as $dataSubThree)
                                <tr>
                                    @if($dataSubThree->isFinalLevel == 1)
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        @if($dataSubThree->itemType == 3)
                                            <td style="font-weight: bold;">
                                                {{$dataSubThree->detDescription}}
                                            </td>
                                        @else
                                            <td>
                                                {{$dataSubThree->detDescription}}
                                            </td>
                                        @endif
                                        @if($fourthLevel)
                                            <td></td>
                                        @endif
                                        @foreach ($columns as $column)
                                            @if($dataSubThree->itemType == 3)
                                                <td style="font-weight: bold;">
                                                    @if(isset($dataSubThree->$column))
                                                        {{round($dataSubThree->$column, $decimalPlaces)}}
                                                    @else
                                                        0
                                                    @endif
                                                </td>
                                            @else
                                                <td>
                                                    @if(isset($dataSubThree->$column))
                                                        {{round($dataSubThree->$column, $decimalPlaces)}}
                                                    @else
                                                        0
                                                    @endif
                                                </td>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if($dataSubThree->isFinalLevel == 0)
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            {{$dataSubThree->detDescription}}
                                        </td>
                                        @if($fourthLevel)
                                            <td></td>
                                        @endif
                                        @foreach ($columns as $column)
                                            <td></td>
                                        @endforeach
                                    @endif
                                </tr>
                                @if($dataSubThree->isFinalLevel == 1 && @isset($dataSubThree->glCodes))
                                    @foreach ($dataSubThree->glCodes as $data24)
                                        @if($dataSubThree->expanded)
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                    {{$data24->glCode}} - {{$data24->glDescription}}
                                                </td>
                                                @if($fourthLevel)
                                                    <td></td>
                                                @endif
                                                @foreach ($columns as $column)
                                                    <td>
                                                        @if(isset($data24->$column))
                                                            {{round($data24->$column, $decimalPlaces)}}
                                                        @else
                                                            0
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                @if(isset($dataSubThree->detail))
                                    @foreach ($dataSubThree->detail as $dataSubFour)
                                        <tr>
                                            @if($dataSubFour->isFinalLevel == 1)
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                @if($dataSubFour->itemType == 3)
                                                    <td style="font-weight: bold;">
                                                        {{$dataSubFour->detDescription}}
                                                    </td>
                                                @else
                                                    <td>
                                                        {{$dataSubFour->detDescription}}
                                                    </td>
                                                @endif
                                                @foreach ($columns as $column)
                                                    @if($dataSubFour->itemType == 3)
                                                        <td style="font-weight: bold;">
                                                            @if(isset($dataSubFour->$column))
                                                                {{round($dataSubFour->$column, $decimalPlaces)}}
                                                            @else
                                                                0
                                                            @endif
                                                        </td>
                                                    @else
                                                        <td>
                                                            @if(isset($dataSubFour->$column))
                                                                {{round($dataSubFour->$column, $decimalPlaces)}}
                                                            @else
                                                                0
                                                            @endif
                                                        </td>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if($dataSubFour->isFinalLevel == 0)
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                    {{$dataSubFour->detDescription}}
                                                </td>
                                                @foreach ($columns as $column)
                                                    <td></td>
                                                @endforeach
                                            @endif
                                        </tr>
                                        @if($dataSubFour->isFinalLevel == 1 && @isset($dataSubFour->glCodes))
                                            @foreach ($dataSubFour->glCodes as $data25)
                                                @if($dataSubFour->expanded)
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>
                                                            {{$data25->glCode}} - {{$data25->glDescription}}
                                                        </td>
                                                        @foreach ($columns as $column)
                                                            <td>
                                                                @if(isset($data25->$column))
                                                                    {{round($data25->$column, $decimalPlaces)}}
                                                                @else
                                                                    0
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @endif
            @endforeach

        @endif
        @if($accountType == 3 && $loop->last)
        <tr>
            <td><strong>Opening Balance</strong></td>
            @if($firstLevel)
            <td></td>
            @endif
            @if($secondLevel)
            <td></td>
            @endif
            @if($thirdLevel)
            <td></td>
            @endif
            @if($fourthLevel)
            <td></td>
            @endif
            @foreach ($openingBalance as $column)
            <td style="font-weight: bold;">
                {{round($column, $decimalPlaces)}}
            </td>
            @endforeach
        </tr>
        <tr>
            <td><strong>Closing Balance</strong></td>
            @if($firstLevel)
            <td></td>
            @endif
            @if($secondLevel)
            <td></td>
            @endif
            @if($thirdLevel)
            <td></td>
            @endif
            @if($fourthLevel)
            <td></td>
            @endif
            @foreach ($closingBalance as $column)
            <td style="font-weight: bold;">
                {{round($column, $decimalPlaces)}}
            </td>
            @endforeach
        </tr>
        @endif
        @if($accountType == 2 && $loop->last && $isUncategorize)
        <tr>
            <td><strong>Uncategorized</strong></td>
            @if($firstLevel)
            <td></td>
            @endif
            @if($secondLevel)
            <td></td>
            @endif
            @if($thirdLevel)
            <td></td>
            @endif
            @if($fourthLevel)
            <td></td>
            @endif
            @foreach ($columns as $column)
            <td style="font-weight: bold;">
                @if(isset($uncategorize->$column))
                {{round($uncategorize->$column, $decimalPlaces)}}
                @else
                0
                @endif
            </td>
            @endforeach
        </tr>
        @endif
        @if($accountType == 2 && $loop->last)
        <tr>
            <td><strong>Grand Total</strong></td>
            @if($firstLevel)
            <td></td>
            @endif
            @if($secondLevel)
            <td></td>
            @endif
            @if($thirdLevel)
            <td></td>
            @endif
            @if($fourthLevel)
            <td></td>
            @endif
            @foreach ($columns as $column)
            <td style="font-weight: bold;">

                @if(is_array($grandTotalUncatArr))
                    @if(isset($grandTotalUncatArr[$column]))
                        {{round($grandTotalUncatArr[$column], $decimalPlaces)}}
                    @else
                        0
                    @endif
                @else
                    @if(isset($grandTotalUncatArr->$column))
                        {{round($grandTotalUncatArr->$column, $decimalPlaces)}}
                    @else
                        0
                    @endif
                @endif
            </td>
            @endforeach
        </tr>
        @endif
        @if($accountType == 1 && $loop->last)
        <tr>
            <td><strong>Uncategorized</strong></td>
            @if($firstLevel)
            <td></td>
            @endif
            @if($secondLevel)
            <td></td>
            @endif
            @if($thirdLevel)
            <td></td>
            @endif
            @if($fourthLevel)
            <td></td>
            @endif
            @foreach ($columns as $column)
            <td style="font-weight: bold;">
                @if(isset($uncategorize[$column]))
                {{round($uncategorize[$column], $decimalPlaces)}}
                @else
                0
                @endif
            </td>
            @endforeach
        </tr>
        @endif
        @endforeach
        @if(sizeof($reportData) == 0)
        <tr>
            <td colspan="{{sizeof($columnHeader)}}">No Records Found</td>
        </tr>
        @endif
    </tbody>
</table>

</html>