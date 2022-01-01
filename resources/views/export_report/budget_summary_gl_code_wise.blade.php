<html>
<center>
    <tr>
        <td colspan="3"> </td>
        <td><h1>Report GL Code Wise</h1>  </td>
        <td colspan="3"> </td>

    <tr>
</center>
    <table>
    <thead>

    <tr>
        @php
            $bigginingDt = new DateTime($entity['finance_year_by']['bigginingDate']);
            $bigginingDate = $bigginingDt->format('d/m/Y');

            $endingDt = new DateTime($entity['finance_year_by']['endingDate']);
            $endingDate = $endingDt->format('d/m/Y');


        @endphp
        <td>Finance Year : {{ $bigginingDate }} - {{ $endingDate }}</td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td>Year : {{ $entity['Year'] }}</td>

    </tr>
    <tr>

        <td>Segment : {{ $entity['segment_by']['ServiceLineDes'] }}</td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td>Template : {{ $entity['template_master']['description'] }}</td>

    </tr>
    <tr></tr>
    <tr></tr>

    <tr>
        <th>Template Description</th>
        <th>GL Code	</th>
        <th>Account Description	</th>
        <th>GL Type</th>
        <th>Local Amount</th>
        <th>Reporting Amount</th>
        <th>Commited Budget</th>
        <th>Actual Consumption</th>
        <th>Pending Document Amount</th>
        <th>Balance</th>
        <th>Adjusted Amount</th>

    </tr>
    </thead>
    <tbody>
        @foreach($reportData as $item)
            <tr>
                <td>{{ $item->templateDetailDescription }}</td>
                <td>{{ $item->AccountCode }}</td>
                <td>{{ $item->AccountDescription }}</td>
                <td>{{ $item->glCodeType }}</td>
                <td>{{ number_format($item->totalLocal,3) }}</td>
                <td>{{ number_format($item->totalRpt,2) }}</td>
                <td>{{ number_format($item->committedAmount,2) }}</td>
                <td>{{ number_format($item->actuallConsumptionAmount,2) }}</td>
                <td>{{ number_format($item->pendingDocumentAmount,2) }}</td>
                <td>{{ number_format($item->balance,2) }}</td>
                <td>{{ number_format($item->adjusted_amount,2) }}</td>

            </tr>
        @endforeach

    </tbody>
    <tfoot>
    <tr>
        <td></td>
        <td></td>
        <td colspan="2">Total Amount</td>
        <td>{{ number_format($total['totalLocal'],2) }}</td>
        <td>{{ number_format($total['totalRpt'],3) }}</td>
        <td>{{ number_format($total['committedAmount'],2) }}</td>
        <td>{{ number_format($total['actuallConsumptionAmount'],2)}}</td>
        <td>{{ number_format($total['pendingDocumentAmount'],2) }}</td>
        <td>{{ number_format($total['balance'],2) }}</td>


    </tr>
    </tfoot>
</table>
</html>
