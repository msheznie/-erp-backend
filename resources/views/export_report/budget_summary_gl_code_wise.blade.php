<html>
<center>
    <tr>
        <td colspan="3"> </td>
        <td><h1>{{ trans('custom.report_gl_code_wise') }}</h1>  </td>
        <td colspan="3"> </td>

    <tr>
</center>
    <table>
    <thead>

    <tr>
        @php
            $financeYearBy = $entity['finance_year_by'] ?? null;
            if ($financeYearBy && isset($financeYearBy['bigginingDate'])) {
                $bigginingDt = new DateTime($financeYearBy['bigginingDate']);
                $bigginingDate = $bigginingDt->format('d/m/Y');
            } else {
                $bigginingDate = '-';
            }
            if ($financeYearBy && isset($financeYearBy['endingDate'])) {
                $endingDt = new DateTime($financeYearBy['endingDate']);
                $endingDate = $endingDt->format('d/m/Y');
            } else {
                $endingDate = '-';
            }
        @endphp
        <td>{{ trans('custom.finance_year') }} : {{ $bigginingDate }} - {{ $endingDate }}</td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td>{{ trans('custom.year') }} : {{ $entity['Year'] ?? '-' }}</td>

    </tr>
    <tr>

        <td>{{ trans('custom.segment') }} : {{ isset($entity['segment_by']['ServiceLineDes']) ? $entity['segment_by']['ServiceLineDes'] : '-' }}</td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td>{{ trans('custom.template') }} : {{ isset($entity['template_master']['description']) ? $entity['template_master']['description'] : '-' }}</td>

    </tr>
    <tr></tr>
    <tr></tr>

    <tr>
        <th>{{ trans('custom.template_description') }}</th>
        <th>{{ trans('custom.gl_code') }}</th>
        <th>{{ trans('custom.account_description') }}</th>
        <th>{{ trans('custom.gl_type') }}</th>
        <th>{{ trans('custom.local_amount') }}</th>
        <th>{{ trans('custom.reporting_amount') }}</th>
        <th>{{ trans('custom.commited_budget') }}</th>
        <th>{{ trans('custom.actual_consumption') }}</th>
        <th>{{ trans('custom.pending_document_amount') }}</th>
        <th>{{ trans('custom.balance') }}</th>
        <th>{{ trans('custom.adjusted_amount') }}</th>

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
        <td colspan="2">{{ trans('custom.total_amount') }}</td>
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
