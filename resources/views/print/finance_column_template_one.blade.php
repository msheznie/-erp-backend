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
                <span>{{trans('custom.printed_date_time')}} : {{date("d-M-y, h:i:s A")}}</span><br>
                <span>{{trans('custom.printed_by')}} : {{$employeeData->empName}}</span>
            </td>
            <td style="width:50%; text-align: center;font-size: 10px;vertical-align: bottom;">
                <span style="float: right;">{{trans('custom.page')}} <span class="pagenum"></span></span><br>
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
                    <span class="font-weight-bold">{{trans('custom.period_from')}} :{{ $from_date }} |
                        {{trans('custom.period_to')}} : {{ $to_date }}</span>
                </td>
            </tr>
        @endif

        @if ($month != null)
            <tr>
                <td colspan="2" style="width:100%;text-align: center;">
                    <span class="font-weight-bold">{{trans('custom.as_of')}} - {{$month}}</span>
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
                <span class="font-weight-bold">{{trans('custom.currency_label')}}: {{$currencyCode}}</span>
            </td>
        </tr>
        <br>
    </table>
</div>



<table style="width:100%;border:1px solid #9fcdff" class="table">
    <thead>
        @if($columnTemplateID == 2)
            <tr>
                @if($fourthLevel)
                    <th colspan="5"></th>
                @elseif($thirdLevel)
                    <th colspan="4"></th>
                @elseif($secondLevel)
                    <th colspan="3"></th>
                @elseif($firstLevel)
                    <th colspan="2"></th>
                @else
                    <th></th>
                @endif
                @foreach ($companyHeaderData as $company)
                    <th style="text-align: center;" colspan="{{sizeof($columnHeader)}}">
                        {{$segmentParentData[$company['companyCode']]}}
                    </th>
                @endforeach
                <th></th>
            </tr>
        @endif
        <tr>
            @if($fourthLevel)
                <th colspan="5"></th>
            @elseif($thirdLevel)
                <th colspan="4"></th>
            @elseif($secondLevel)
                <th colspan="3"></th>
            @elseif($firstLevel)
                <th colspan="2"></th>
            @else
                <th></th>
            @endif
            @foreach ($companyHeaderData as $company)
                <th style="text-align: center;" colspan="{{sizeof($columnHeader)}}">
                    @if($columnTemplateID == 1)
                        {{$company['companyCode']}}
                    @else 
                        {{$serviceLineDescriptions[$company['companyCode']]}}
                    @endif
                </th>
            @endforeach
            @if($columnTemplateID == 2)
                <th></th>
            @endif
        </tr>
        <tr>
            @if($fourthLevel)
                <th colspan="5">{{trans('custom.description')}}</th>
            @elseif($thirdLevel)
                <th colspan="4">{{trans('custom.description')}}</th>
            @elseif($secondLevel)
                <th colspan="3">{{trans('custom.description')}}</th>
            @elseif($firstLevel)
                <th colspan="2">{{trans('custom.description')}}</th>
            @else
                <th>{{trans('custom.description')}}</th>
            @endif
            @foreach ($companyHeaderData as $company)
                    @foreach ($columnHeader as $column)
                    <th>{{$column['description']}}</th>
                @endforeach
            @endforeach
            @if($columnTemplateID == 2)
                <th>{{trans('custom.total')}}</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($reportData as $header)
            @if($header['hideHeader'] == 0)
            <tr>
                @if($header['itemType'] == 1 || $header['itemType'] == 4)
                <td>
                    <strong>{{$header['detDescription']}}</strong>
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
                @if($header['itemType'] == 3)
                <td>
                    <strong>{{$header['detDescription']}}</strong>
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
                @foreach ($companyHeaderData as $company)
                    @foreach ($columns as $column)
                        @if($header['itemType'] == 3)
                        <td style="font-weight: bold; text-align:right;">
                                @if(isset($header['columnData'][$company['companyCode']][$column]))
                                    {{number_format($header['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
                                @else
                                    0
                                @endif
                        </td>
                        @endif
                    @endforeach
                @endforeach
                @if($columnTemplateID == 2 && $header['itemType'] == 3)
                    <td style="text-align:right;">{{number_format(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $header), $decimalPlaces)}}</td>
                @endif
            </tr>
            @endif
            {{-- start --}}
            @if(isset($header['detail']))
            @foreach ($header['detail'] as $data)
                <tr>
                    @if($data['isFinalLevel'] == 1)
                        <td></td>
                        @if($data['itemType'] == 3)
                        <td style="font-weight: bold;">
                            {{$data['detDescription']}}
                        </td>
                        @else
                        <td>
                            {{$data['detDescription']}}
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
                        @foreach ($companyHeaderData as $company)
                            @foreach ($columns as $column)
                            @if($data['itemType'] == 3)
                            <td style="font-weight: bold; text-align:right;">
                                @if(isset($data['columnData'][$company['companyCode']][$column]))
                                {{number_format($data['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
                                @else
                                0
                                @endif
                            </td>
                            @else
                            <td style="text-align:right;">
                                @if(isset($data['columnData'][$company['companyCode']][$column]))
                                {{number_format($data['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
                                @else
                                0
                                @endif
                            </td>
                            @endif
                            @endforeach
                        @endforeach
                        @if($columnTemplateID == 2)
                            <td style="text-align:right;">{{number_format(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $data), $decimalPlaces)}}</td>
                        @endif
                    @endif
                    @if($data['isFinalLevel'] == 0)
                    <td></td>
                    <td>
                        {{$data['detDescription']}}
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
                    <td></td>
                    @endforeach
                    @if($columnTemplateID == 2)
                        <td></td>
                    @endif
                    @endif
                </tr>
                @if($data['isFinalLevel'] == 1)
                @if($data['glCodes'] != null)
                    @foreach ($data['glCodes'] as $data2)
                @if($data['expanded'])
                <tr>
                    <td></td>
                    <td style="padding-left: 10px">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$data2['glCode']}} - {{$data2['glDescription']}}
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
                    @foreach ($companyHeaderData as $company)
                        @foreach ($columns as $column)
                        <td style="text-align: right">
                            @if(isset($data2['columnData']))
                                @if (isset($data2['columnData'][$company['companyCode']]))
                                    @if (isset($data2['columnData'][$company['companyCode']][$column]))
                                        {{number_format($data2['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
                                    @else
                                    {{number_format(0, $decimalPlaces)}}
                                    @endif
                                @else
                                {{number_format(0, $decimalPlaces)}}
                                @endif
                            @else
                            {{number_format(0, $decimalPlaces)}}
                            @endif
                        </td>
                        @endforeach
                    @endforeach
                    @if($columnTemplateID == 2)
                        <td style="text-align:right;">{{number_format(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $data2), $decimalPlaces)}}</td>
                    @endif
                </tr>
                @endif
                @endforeach
                @endif
                @endif
                @if(isset($data['detail']))
                @foreach ($data['detail'] as $dataSubTwo)
                <tr>
                    @if($dataSubTwo['isFinalLevel'] == 1)
                    <td></td>
                    <td></td>
                    @if($dataSubTwo['itemType'] == 3)
                    <td style="font-weight: bold;">
                        {{$dataSubTwo['detDescription']}}
                    </td>
                    @else
                    <td>
                        {{$dataSubTwo['detDescription']}}
                    </td>
                    @endif
                    @if($thirdLevel)
                    <td></td>
                    @endif
                    @if($fourthLevel)
                    <td></td>
                    @endif
                    @foreach ($companyHeaderData as $company)
                        @foreach ($columns as $column)
                            @if($dataSubTwo['itemType'] == 3)
                            <td style="font-weight: bold; text-align:right;">
                                @if(isset($dataSubTwo['columnData']))
                                    @if (isset($dataSubTwo['columnData'][$company['companyCode']]))
                                        @if (isset($dataSubTwo['columnData'][$company['companyCode']][$column]))
                                            {{number_format($dataSubTwo['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
                                        @else
                                        {{number_format(0, $decimalPlaces)}}
                                        @endif
                                    @else
                                    {{number_format(0, $decimalPlaces)}}
                                    @endif
                                @else
                                {{number_format(0, $decimalPlaces)}}
                                @endif
                            </td>
                            @else
                            <td style="text-align:right;">
                                @if(isset($dataSubTwo['columnData']))
                                    @if (isset($dataSubTwo['columnData'][$company['companyCode']]))
                                        @if (isset($dataSubTwo['columnData'][$company['companyCode']][$column]))
                                            {{number_format($dataSubTwo['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
                                        @else
                                        {{number_format(0, $decimalPlaces)}}
                                        @endif
                                    @else
                                    {{number_format(0, $decimalPlaces)}}
                                    @endif
                                @else
                                {{number_format(0, $decimalPlaces)}}
                                @endif
                            </td>
                            @endif
                        @endforeach
                    @endforeach
                    @if($columnTemplateID == 2)
                        <td style="text-align:right;">{{number_format(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $dataSubTwo), $decimalPlaces)}}</td>
                    @endif
                    @endif
                    @if($dataSubTwo['isFinalLevel'] == 0)
                    <td></td>
                    <td></td>
                    <td>
                        {{$dataSubTwo['detDescription']}}
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
                    @if($columnTemplateID == 2)
                        <td></td>
                    @endif
                    @endif
                </tr>
                @if($dataSubTwo['isFinalLevel'] == 1)
                @foreach ($dataSubTwo['glCodes'] as $data23)
                @if($dataSubTwo['expanded'])
                <tr>
                    <td></td>
                    <td></td>
                    <td style="padding-left: 10px">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$data23['glCode']}} - {{$data23['glDescription']}}
                    </td>
                        @if($thirdLevel)
                    <td></td>
                    @endif
                    @if($fourthLevel)
                    <td></td>
                    @endif
                        @foreach ($companyHeaderData as $company)
                    @foreach ($columns as $column)
                    <td style="text-align:right;">
                        @if(isset($data23['columnData']))
                            @if (isset($data23['columnData'][$company['companyCode']]))
                                @if (isset($data23['columnData'][$company['companyCode']][$column]))
                                    {{number_format($data23['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
                                @else
                                {{number_format(0, $decimalPlaces)}}
                                @endif
                            @else
                            {{number_format(0, $decimalPlaces)}}
                            @endif
                        @else
                        {{number_format(0, $decimalPlaces)}}
                        @endif
                    </td>
                    @endforeach
                    @endforeach
                    @if($columnTemplateID == 2)
                        <td style="text-align:right;">{{number_format(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $data23), $decimalPlaces)}}</td>
                    @endif
                </tr>
                @endif
                @endforeach
                @endif
                @if(isset($dataSubTwo['detail']))
                @foreach ($dataSubTwo['detail'] as $dataSubThree)
                <tr>
                    @if($dataSubThree['isFinalLevel'] == 1)
                    <td></td>
                    <td></td>
                    <td></td>
                    @if($dataSubThree['itemType'] == 3)
                    <td style="font-weight: bold;">
                        {{$dataSubThree['detDescription']}}
                    </td>
                    @else
                    <td>
                        {{$dataSubThree['detDescription']}}
                    </td>
                    @endif
                    @if($fourthLevel)
                    <td></td>
                    @endif
                        @foreach ($companyHeaderData as $company)
                    @foreach ($columns as $column)
                    @if($dataSubThree['itemType'] == 3)
                    <td style="font-weight: bold; text-align:right;">
                        @if(isset($dataSubThree['columnData']))
                            @if (isset($dataSubThree['columnData'][$company['companyCode']]))
                                @if (isset($dataSubThree['columnData'][$company['companyCode']][$column]))
                                    {{number_format($dataSubThree['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
                                @else
                                {{number_format(0, $decimalPlaces)}}
                                @endif
                            @else
                            {{number_format(0, $decimalPlaces)}}
                            @endif
                        @else
                        {{number_format(0, $decimalPlaces)}}
                        @endif
                    </td>
                    @else
                    <td style="text-align:right;">
                        @if(isset($dataSubThree['columnData']))
                            @if (isset($dataSubThree['columnData'][$company['companyCode']]))
                                @if (isset($dataSubThree['columnData'][$company['companyCode']][$column]))
                                    {{number_format($dataSubThree['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
                                @else
                                {{number_format(0, $decimalPlaces)}}
                                @endif
                            @else
                            {{number_format(0, $decimalPlaces)}}
                            @endif
                        @else
                        {{number_format(0, $decimalPlaces)}}
                        @endif
                    </td>
                    @endif
                    @endforeach
                    @endforeach
                    @if($columnTemplateID == 2)
                        <td style="text-align:right;">{{number_format(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $dataSubThree), $decimalPlaces)}}</td>
                    @endif
                    @endif
                    @if($dataSubThree['isFinalLevel'] == 0)
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        {{$dataSubThree['detDescription']}}
                    </td>
                    @if($fourthLevel)
                    <td></td>
                    @endif
                    @foreach ($columns as $column)
                    <td></td>
                    @endforeach
                    @if($columnTemplateID == 2)
                        <td></td>
                    @endif
                    @endif
                </tr>
                @if($dataSubThree['isFinalLevel'] == 1)
                @foreach ($dataSubThree['glCodes'] as $data24)
                @if($dataSubThree['expanded'])
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="padding-left: 10px">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$data24['glCode']}} - {{$data24['glDescription']}}
                    </td>
                    @if($fourthLevel)
                    <td></td>
                    @endif
                        @foreach ($companyHeaderData as $company)
                    @foreach ($columns as $column)
                    <td style="text-align:right;">
                        @if(isset($data24['columnData'][$company['companyCode']][$column]))
                        {{number_format($data24['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
                        @else
                        0
                        @endif
                    </td>
                    @endforeach
                    @endforeach
                    @if($columnTemplateID == 2)
                        <td style="text-align:right;">{{number_format(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $data24), $decimalPlaces)}}</td>
                    @endif
                </tr>
                @endif
                @endforeach
                @endif
                @if(isset($dataSubThree['detail']))
                @foreach ($dataSubThree['detail'] as $dataSubFour)
                <tr>
                    @if($dataSubFour['isFinalLevel'] == 1)
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    @if($dataSubFour['itemType'] == 3)
                    <td style="font-weight: bold;">
                        {{$dataSubFour['detDescription']}}
                    </td>
                    @else
                    <td>
                        {{$dataSubFour['detDescription']}}
                    </td>
                    @endif
                        @foreach ($companyHeaderData as $company)
                    @foreach ($columns as $column)
                    @if($dataSubFour['itemType'] == 3)
                    <td style="font-weight: bold; text-align:right;">
                        @if(isset($dataSubFour['columnData'][$company['companyCode']][$column]))
                        {{number_format($dataSubFour['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
                        @else
                        0
                        @endif
                    </td>
                    @else
                    <td style="text-align:right;">
                        @if(isset($dataSubFour['columnData'][$company['companyCode']][$column]))
                        {{number_format($dataSubFour['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
                        @else
                        0
                        @endif
                    </td>
                    @endif
                    @endforeach
                    @endforeach
                    @if($columnTemplateID == 2)
                        <td style="text-align:right;">{{number_format(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $dataSubFour), $decimalPlaces)}}</td>
                    @endif
                    @endif
                    @if($dataSubFour['isFinalLevel'] == 0)
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        {{$dataSubFour['detDescription']}}
                    </td>
                    @foreach ($columns as $column)
                    <td></td>
                    @endforeach
                    @if($columnTemplateID == 2)
                        <td></td>
                    @endif
                    @endif
                </tr>
                @if($dataSubFour['isFinalLevel'] == 1)
                @foreach ($dataSubFour['glCodes'] as $data25)
                @if($dataSubFour['expanded'])
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="padding-left: 10px">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$data25['glCode']}} - {{$data25['glDescription']}}
                    </td>
                    @foreach ($companyHeaderData as $company)
                    @foreach ($columns as $column)
                    <td style="text-align:right;">
                        @if(isset($data25['columnData'][$company['companyCode']][$column]))
                        {{number_format($data25['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
                        @else
                        0
                        @endif
                    </td>
                    @endforeach
                    @endforeach
                    @if($columnTemplateID == 2)
                        <td style="text-align:right;">{{number_format(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $data25), $decimalPlaces)}}</td>
                    @endif
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
            {{-- end --}}
            @if($accountType == 3 && $loop->last)
                <tr>
                    <td><strong>{{trans('custom.opening_balance')}}</strong></td>
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
                    @foreach ($companyHeaderData as $company)
                        {{$x=0}}
                        @foreach ($columns as $column)
                        <td style="font-weight: bold; text-align:right;">
                            {{number_format($openingBalance[$company['companyCode']][$x], $decimalPlaces)}}
                        </td>
                        {{ $x++ }}
                        @endforeach
                    @endforeach
                    @if($columnTemplateID == 2)
                        <td style="text-align:right;">{{number_format(\Helper::rowTotalOfReportTemplateBalance($companyHeaderData, $columns, $openingBalance), $decimalPlaces)}}</td>
                    @endif
                </tr>
                <tr>
                    <td><strong>{{trans('custom.closing_balance')}}</strong></td>
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
                    @foreach ($companyHeaderData as $company)
                        {{$j=0}}
                        @foreach ($columns as $column)
                            <td style="font-weight: bold;">
                                {{number_format($closingBalance[$company['companyCode']][$j], $decimalPlaces)}}
                            </td>
                            {{ $j++ }}
                        @endforeach
                    @endforeach
                    @if($columnTemplateID == 2)
                        <td>{{number_format(\Helper::rowTotalOfReportTemplateBalance($companyHeaderData, $columns, $closingBalance), $decimalPlaces)}}</td>
                    @endif
                </tr>
            @endif
            @if($accountType == 2 && $loop->last && $isUncategorize)
            <tr>
                <td><strong>{{trans('custom.uncategorized')}}</strong></td>
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
                @foreach ($companyHeaderData as $company)
                    @foreach ($columns as $column)
                    <td style="font-weight: bold; text-align:right;">
                        @if(isset($uncategorize['columnData'][$company['companyCode']][$column]))
                            {{number_format($uncategorize['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
                        @else
                            0
                        @endif
                    </td>
                    @endforeach
                @endforeach
                @if($columnTemplateID == 2)
                    <td style="text-align:right;">{{number_format(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $uncategorize), $decimalPlaces)}}</td>
                @endif
            </tr>
            @endif
            @if($accountType == 2 && $loop->last)
            <tr>
                <td><strong>{{trans('custom.grand_total')}}</strong></td>
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
                @foreach ($companyHeaderData as $company)
                    @foreach ($columns as $column)
                    <td style="font-weight: bold; text-align:right;">
                        {{number_format(\Helper::grandTotalValueOfReportTemplate($company['companyCode'], $column, $grandTotalUncatArr), $decimalPlaces)}}
                    </td>
                    @endforeach
                @endforeach
                    @if($columnTemplateID == 2)
                    <td style="text-align:right;">{{number_format(\Helper::rowTotalOfReportTemplateGrandTotal($companyHeaderData, $columns, $grandTotalUncatArr), $decimalPlaces)}}</td>
                @endif
            </tr>
            @endif
            @if($accountType == 1 && $loop->last)
            <tr>
                <td><strong>{{trans('custom.uncategorized')}}</strong></td>
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
                @foreach ($companyHeaderData as $company)
                @foreach ($columns as $column)
                <td style="font-weight: bold; text-align:right;">
                    @if(isset($uncategorize['columnData'][$company['companyCode']][$column]))
                    {{number_format($uncategorize['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
                    @else
                    0
                    @endif
                </td>
                @endforeach
                @endforeach
                @if($columnTemplateID == 2)
                    <td style="text-align:right;">{{number_format(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $uncategorize), $decimalPlaces)}}</td>
                @endif
            </tr>
            @endif
        @endforeach
        @if(sizeof($reportData) == 0)
            <tr>
                <td colspan="{{sizeof($columnHeader) + sizeof($companyHeaderData)}}">{{trans('custom.no_records_found')}}</td>
            </tr>
        @endif
    </tbody>
</table>
