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
                <th>{{trans('custom.as_of')}} - {{$month}}</th>
            </tr>
        @endif

        @if ($from_date != null && $to_date != null)
            <tr>
                <th>{{trans('custom.period_from')}} - {{$from_date}}</th>
            </tr>
            <tr>
                <th>{{trans('custom.period_to')}} - {{$to_date}} </th>
            </tr>
        @endif
        <tr>{{trans('custom.currency_label')}}: {{$currencyCode}}</tr>
        <tr></tr>
        <tr></tr>
        <tr>
            	<th>{{trans('custom.description')}}</th>
            @foreach ($columnHeader as $column)
            <th>{{$column['description']}}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($reportData as $header)
        <tr>
            <td>
                <strong>{{$header['detDescription']}}</strong>
            </td>
            @foreach ($columns as $column)
            <td style="font-weight: bold;">
                @if(isset($header[$column]))
                {{round($header[$column], $decimalPlaces)}}
                @else
                0
                @endif
            </td>
            @endforeach
        </tr>

        @endforeach
        @if(sizeof($reportData) == 0)
        <tr>
            <td colspan="{{sizeof($columnHeader)}}">{{trans('custom.no_records_found')}}</td>
        </tr>
        @endif
    </tbody>
</table>

</html>
