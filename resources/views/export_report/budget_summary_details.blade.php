
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
        <td><strong>{{ trans('custom.pending_amount') }}</strong></td>
    </tr>
    </thead>
    <tbody>
    @foreach($reportData as $item)
        <tr>
            <td>{{ $item['companyID'] }}</td>
            <td>{{ $item['serviceLine'] }}</td>
            <td>{{ $item['financeGLcodePL'] }}</td>
            <td>{{ $item['documentCode'] }}</td>
            <td>{{ $item['budgetYear'] }}</td>
            <td>{{ number_format($item['lineTotal'],2) }}</td>

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
