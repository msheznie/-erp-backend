<html>
<table>
<tr>
    <td>Period From : {{ \Carbon\Carbon::parse($fromDate)->format("d/m/Y") }}</td>
    <td></td>
    <td> </td>
    <td> </td>
    <td>Period To : {{ \Carbon\Carbon::parse($toDate)->format("d/m/Y") }}</td>
</tr>
</table>
@foreach($employees as $employee)
      <table>
                 <thead>
                 <tr><td>{{ $employee->employeeName}} - {{ $employee->empID }}</td></tr>
                     <tr>
                          <th>Document Date</th>
                          <th>Document Code</th>
                          <th>Description</th>
                         @if($currencyID == 1)
                          <th>Amount ({{$currencyCodeLocal}})</th>
                         @endif
                         @if($currencyID == 2)
                             <th>Amount ({{$currencyCodeRpt}})</th>
                         @endif
                          <th>Reference Doc</th>
                          <th>Ref.Doc.Date</th>
                         @if($currencyID == 1)
                             <th>Ref.Amount ({{$currencyCodeLocal}})</th>
                             <th>Balance ({{$currencyCodeLocal}})</th>
                         @endif
                         @if($currencyID == 2)
                             <th>Ref.Amount ({{$currencyCodeRpt}})</th>
                             <th>Balance ({{$currencyCodeRpt}})</th>
                         @endif

                     </tr>
                 </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr>
                        <td>test</td>
                    </tr>
                @endforeach
                </tbody>
      </table>
@endforeach

