<html>
<center>
    <tr>
        <td colspan="3"> </td>
        <td><h1>Details</h1>  </td>
        <td colspan="3"> </td>

    <tr>
</center>

<table>
    <thead>
    <tr>
    <td>Company ID</td>
    <td>Department</td>
    <td>GL Code</td>
    <td>Document Code</td>
    <td>Year</td>
    <td>Pending Amount</td>
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
            <td>Total</td>
            <td>{{ number_format($total,2) }}</td>
        </tr>
    </tfoot>
</table>
