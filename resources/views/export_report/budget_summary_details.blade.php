
<table>
    <tr>
        <td colspan="3"> </td>
        <td><strong><h1>{{ trans('custom.details') }}</h1></strong></td>
        <td colspan="3"> </td>
    </tr>
    <thead>
        <tr>
        <td><strong>{{ trans('custom.company_id') }}</strong></td>
        <td><strong>{{ trans('custom.department') }}</strong></td>
        <td><strong>{{ trans('custom.gl_code') }}</strong></td>
        <td><strong>{{ trans('custom.document_code') }}</strong></td>
        <td><strong>{{ trans('custom.year') }}</strong></td>
        <td><strong>{{ trans('custom.' . ($detailAmountLabelKey ?? 'pending_amount')) }}</strong></td>
    </tr>
    </thead>
    <tbody>
    @foreach($reportData as $item)
        @php
            $companyID = data_get($item, 'companyID');
            $department = data_get($item, 'serviceLine', data_get($item, 'serviceLineCode'));
            $glCode = data_get($item, 'financeGLcodePL', data_get($item, 'GLCode'));
            $documentCode = data_get($item, 'documentCode');
            $year = data_get($item, 'budgetYear', data_get($item, 'year'));
            $amount = data_get($item, 'lineTotal', data_get($item, 'actualConsumption', data_get($item, 'committedAmount', data_get($item, 'consumedRptAmount', 0))));
        @endphp
        <tr>
            <td>{{ $companyID }}</td>
            <td>{{ $department }}</td>
            <td>{{ $glCode }}</td>
            <td>{{ $documentCode }}</td>
            <td>{{ $year }}</td>
            <td>{{ number_format((float) $amount, 2) }}</td>

        </tr>
    @endforeach

    </tbody>
    <tfoot>
        <tr>
            <td colspan="4"></td>
            <td>{{ trans('custom.total') }}</td>
            <td>{{ number_format($total,2) }}</td>
        </tr>
    </tfoot>
</table>
