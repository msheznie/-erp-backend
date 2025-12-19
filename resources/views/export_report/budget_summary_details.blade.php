<html>
<center>
    <tr>
        <td colspan="3"> </td>
        <td><h1>{{ trans('custom.details') }}</h1>  </td>
        <td colspan="3"> </td>

    <tr>
</center>

<table>
    <thead>
    <tr>
    <td>{{ trans('custom.company_id') }}</td>
    <td>{{ trans('custom.department') }}</td>
    <td>{{ trans('custom.gl_code') }}</td>
    <td>{{ trans('custom.document_code') }}</td>
    <td>{{ trans('custom.year') }}</td>
    <td>{{ trans('custom.pending_amount') }}</td>
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
