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
            	<th>Description</th>
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
            <td colspan="{{sizeof($columnHeader)}}">No Records Found</td>
        </tr>
        @endif
    </tbody>
</table>

</html>
