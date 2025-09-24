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
        <tr>
        <th>{{trans('custom.description')}}</th>
            @foreach ($columnHeader as $column)
            <th style="text-align:right">{{$column['description']}}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($reportData as $header)
        @if($header['hideHeader'] == 0)
        <tr>
        <td>{{ $header['detDescription'] }}</td> 
        @foreach ($columns as $column)
            <td style="text-align:right">
                {{number_format($header[$column], $decimalPlaces)}}
            </td>
            @endforeach
        </tr>
        @endif
        @endforeach
        @if(sizeof($reportData) == 0)
        <tr>
            <td colspan="{{sizeof($columnHeader)}}">{{trans('custom.no_records_found')}}</td>
        </tr>
        @endif
    </tbody>
</table>
