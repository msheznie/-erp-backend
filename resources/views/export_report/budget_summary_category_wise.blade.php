<html>
<center>
    <tr>
        <td colspan="3"> </td>
        <td><h1>{{ trans('custom.report_template_category_wise') }}</h1>  </td>
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
        <td>{{ trans('custom.finance_year') }} : {{ $bigginingDate }} - {{ $endingDate }}</td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td>{{ trans('custom.year') }} : {{ $entity['Year'] }}</td>

    </tr>
    <tr>

        <td>{{ trans('custom.segment') }} : {{ $entity['segment_by']['ServiceLineDes'] }}</td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td>{{ trans('custom.template') }} : {{ $entity['template_master']['description'] }}</td>

    </tr>
    <tr></tr>
    <tr></tr>

    <tr>
        <th>{{ trans('custom.template_description') }}</th>

        <th>{{ trans('custom.budget_amount') }} ({{ $rptCurrency->CurrencyCode ?? 'USD' }})</th>
        <th>{{ trans('custom.commited_budget') }}</th>
        <th>{{ trans('custom.actual_consumption') }}</th>
        <th>{{ trans('custom.pending_document_amount') }}</th>
        <th>{{ trans('custom.balance') }}</th>

    </tr>
    </thead>
    <tbody>
    @foreach($reportData as $item)
        <tr>
            <td>{{ $item->templateDetailDescription }}</td>
            <td>{{ number_format($item->totalRpt,2) }}</td>
            <td>{{ number_format($item->committedAmount,2) }}</td>
            <td>{{ number_format($item->actualConsumptionAmount,2) }}</td>
            <td>{{ number_format($item->pendingDocumentAmount,2) }}</td>
            <td>{{ number_format($item->balance,2) }}</td>

        </tr>
    @endforeach

    </tbody>
    <tfoot>
    <tr>

        <td>{{ trans('custom.total_amount') }}</td>

        <td>{{ number_format($total['totalRpt'],3) }}</td>
        <td>{{ number_format($total['committedAmount'],2) }}</td>
        <td>{{ number_format($total['actualConsumption'],2)}}</td>
        <td>{{ number_format($total['pendingDocumentAmount'],2) }}</td>
        <td>{{ number_format($total['balance'],2) }}</td>


    </tr>
    </tfoot>
</table>
</html>
