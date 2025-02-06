<style type="text/css">
    @page {
        margin: 100px 30px 40px;
    }

    #header {
        position: fixed;
        left: 0px;
        top: -100px;
        right: 0px;
        height: 50px;
        text-align: center;
    }

    #footer {
        position: fixed;
        left: 0px;
        bottom: 0px;
        right: 0px;
        height: 0px;
        font-size: 10px;
    }

    #footer .page:after {
        content: counter(page, upper-roman);
    }

    body {
        font-size: 9px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"
    }

    h3 {
        font-size: 1.53125rem;
    }

    h6 {
        font-size: 0.875rem;
    }

    h6, h3 {
        margin-bottom: 0.1rem;
        font-weight: 500;
        line-height: 1.2;
        color: inherit;
    }

    table > tbody > th > tr > td {
        font-size: 9px;
    }

    table > thead > th {
        font-size: 9px;
    }

    .theme-tr-head {
        background-color: #EBEBEB !important;
    }

    .text-left {
        text-align: left;
    }

    table {
        border-collapse: collapse;
        border-left:0px solid #ffffff !important;
        border-right:0px solid #ffffff !important;
    }

    .font-weight-bold {
        font-weight: 700 !important;
    }

    .table th, .table td {
        padding: 0.4rem !important;
        vertical-align: top;
        border: 1px solid #dee2e6 !important;
        border-left:0px solid #ffffff !important;
        border-right:0px solid #ffffff !important;
        /* border-bottom: 1px solid rgb(127, 127, 127) !important;*/
    }

    .table th {
        background-color: #EBEBEB !important;
    }

    tfoot > tr > td {
        border: 1px solid rgb(127, 127, 127);

    }

    .text-right {
        text-align: right !important;
    }

    .font-weight-bold {
        font-weight: 700 !important;
    }

    hr {
        border: 0;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    th {
        text-align: inherit;
        font-weight: bold;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }

    p {
        margin-top: 0 !important;
    }

    .pagenum:after {
        content: counter(page);
    }

    .content {
        margin-bottom: 45px;
    }

</style>

<div id="footer">
    <table style="width:100%;">
        <tr>
            <td style="width:50%;font-size: 10px;vertical-align: bottom;">
                <span>Printed Date & Time : {{date("d-M-y, h:i:s A")}}</span><br>
                <span>Printed By : {{$employeeData->empName}}</span>
            </td>
            <td style="width:50%; text-align: center;font-size: 10px;vertical-align: bottom;">
                <span style="float: right;">Page <span class="pagenum"></span></span><br>
            </td>
        </tr>
    </table>
</div>

<div class="header">
    <table style="width:100%;">
        <tr>
            <td style="width:100%;text-align: center;">
                <span class="font-weight-bold" style="font-size: 14px">{{$template->reportName}}</span>
            </td>
        </tr>
    </table>
    <table style="width:100%;">
        @if ($from_date != null && $to_date != null)
            <tr>
                <td colspan="2" style="width:100%;text-align: center;">
                    <span class="font-weight-bold">Period From :{{ $from_date }} |
                        Period To : {{ $to_date }}</span>
                </td>
            </tr>
        @endif

        @if ($month != null)
            <tr>
                <td colspan="2" style="width:100%;text-align: center;">
                    <span class="font-weight-bold">As of - {{$month}}</span>
                </td>
            </tr>
        @endif

        <tr>
            <td colspan="2" style="width:100%;text-align: center;">
                <span class="font-weight-bold">{{$company->CompanyName}}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width:100%;text-align: center;">
                <span class="font-weight-bold">Currency: {{$currencyCode}}</span>
            </td>
        </tr>
        <br>
    </table>
</div>


<table style="width:100%;border:1px solid #9fcdff" class="table">
    <thead>
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
            <th style="text-align:right">{{$column['description']}}</th>
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
            <td style="font-weight: bold; text-align:right">
                @if(isset($header->$column))
                {{number_format($header->$column, $decimalPlaces)}}
                @else
                0
                @endif
            </td>
            @endif
            @if($header->itemType == 5)
            <td style="text-align:right">
                @if(isset($header->$column))
                {{number_format($header->$column, $decimalPlaces)}}
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
                                <td style="font-weight: bold; text-align:right">
                                    @if(isset($data->$column))
                                        {{number_format($data->$column, $decimalPlaces)}}
                                    @else
                                        0
                                    @endif
                                </td>
                            @else
                                <td style="text-align:right">
                                    @if(isset($data->$column))
                                        {{number_format($data->$column, $decimalPlaces)}}
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
                                <td style="text-align:right">
                                    @if(isset($data->$column))
                                        {{number_format($data->$column, $decimalPlaces)}}
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
                                        <td style="text-align:right">
                                            @if(isset($data2->$column))
                                                {{number_format($data2->$column, $decimalPlaces)}}
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
                                        <td style="font-weight: bold; text-align:right">
                                            @if(isset($dataSubTwo->$column))
                                                {{number_format($dataSubTwo->$column, $decimalPlaces)}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @else
                                        <td style="text-align:right">
                                            @if(isset($dataSubTwo->$column))
                                                {{number_format($dataSubTwo->$column, $decimalPlaces)}}
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
                                            <td style="text-align:right">
                                                @if(isset($data23->$column))
                                                    {{number_format($data23->$column, $decimalPlaces)}}
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
                                                <td style="font-weight: bold; text-align:right">
                                                    @if(isset($dataSubThree->$column))
                                                        {{number_format($dataSubThree->$column, $decimalPlaces)}}
                                                    @else
                                                        0
                                                    @endif
                                                </td>
                                            @else
                                                <td style="text-align:right">
                                                    @if(isset($dataSubThree->$column))
                                                        {{number_format($dataSubThree->$column, $decimalPlaces)}}
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
                                                    <td style="text-align:right">
                                                        @if(isset($data24->$column))
                                                            {{number_format($data24->$column, $decimalPlaces)}}
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
                                                        <td style="font-weight: bold; text-align:right">
                                                            @if(isset($dataSubFour->$column))
                                                                {{number_format($dataSubFour->$column, $decimalPlaces)}}
                                                            @else
                                                                0
                                                            @endif
                                                        </td>
                                                    @else
                                                        <td style="text-align:right">
                                                            @if(isset($dataSubFour->$column))
                                                                {{number_format($dataSubFour->$column, $decimalPlaces)}}
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
                                                            <td style="text-align:right">
                                                                @if(isset($data25->$column))
                                                                    {{number_format($data25->$column, $decimalPlaces)}}
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
            <td style="font-weight: bold; text-align:right">
                {{number_format($column, $decimalPlaces)}}
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
            <td style="font-weight: bold; text-align:right">
                {{number_format($column, $decimalPlaces)}}
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
            <td style="text-align:right">
                @if(isset($uncategorize->$column))
                {{number_format($uncategorize->$column, $decimalPlaces)}}
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
            <td style="font-weight: bold; text-align:right">
                @if(is_array($grandTotalUncatArr))
                    @if(isset($grandTotalUncatArr[$column]))
                        {{number_format($grandTotalUncatArr[$column], $decimalPlaces)}}
                    @else
                        0
                    @endif
                @else
                    @if(isset($grandTotalUncatArr->$column))
                        {{number_format($grandTotalUncatArr->$column, $decimalPlaces)}}
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
            <td style="text-align:right">
                @if(isset($uncategorize[$column]))
                {{number_format($uncategorize[$column], $decimalPlaces)}}
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