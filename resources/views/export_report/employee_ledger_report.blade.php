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
                 <tr><td><B>{{ $employee->employeeName}} - {{ $employee->empID }}</B></td></tr>
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
                    @if($employee->employeeID == $data->employeeID)
                    <tr>

                        <td>{{ \Carbon\Carbon::parse($data->documentDate)->format("d/m/Y") }}</td>
                        <td>{{ $data->documentCode }}</td>
                        <td>{{ $data->description }}</td>
                        @if($currencyID == 1)
                        <td  style="text-align: right">{{ number_format($data->amountLocal,$data->localCurrencyDecimals) }}</td>
                        @endif
                        @if($currencyID == 2)
                            <td  style="text-align: right">{{ number_format($data->amountRpt,$data->rptCurrencyDecimals) }}</td>
                        @endif
                        @if($data->referenceDoc != null)
                        <td>{{ $data->referenceDoc }}</td>
                        @endif
                        @if($data->referenceDoc == null)
                            <td>-</td>
                        @endif
                        @if($data->referenceDocDate != null)
                            <td>{{ $data->referenceDocDate }}</td>
                        @endif
                        @if($data->referenceDocDate == null)
                            <td>-</td>
                        @endif

                        @if(isset($data->referenceAmountLocal) && isset($data->referenceAmountRpt))

                            @if($data->referenceAmountLocal != null && $currencyID == 1)
                                <td style="text-align: right">{{ number_format(ABS($data->referenceAmountLocal), $data->localCurrencyDecimals) }}</td>
                            @endif
                            @if($data->referenceAmountRpt != null && $currencyID == 2)
                                <td  style="text-align: right">{{ number_format(ABS($data->referenceAmountRpt), $data->rptCurrencyDecimals) }}</td>
                            @endif
                            @if($data->referenceAmountRpt == null && $data->referenceAmountLocal == null)
                                <td  style="text-align: right">0</td>
                            @endif
                            @if($data->referenceAmountLocal != null && $data->amountLocal != null && $currencyID == 1)
                                <td style="text-align: right">{{ number_format($data->amountLocal - ABS($data->referenceAmountLocal), $data->localCurrencyDecimals) }}</td>
                            @endif
                            @if($data->referenceAmountRpt != null && $data->amountRpt != null && $currencyID == 2)
                                <td  style="text-align: right">{{ number_format(ABS($data->referenceAmountRpt) - $data->amountRpt, $data->rptCurrencyDecimals) }}</td>
                            @endif

                            @if($data->referenceAmountLocal == null && $data->amountLocal != null && $currencyID == 1)
                                    <td style="text-align: right">{{ number_format($data->amountLocal, $data->localCurrencyDecimals) }}</td>
                            @endif
                            @if($data->referenceAmountRpt == null && $data->amountRpt != null && $currencyID == 2)
                                    <td  style="text-align: right">{{ number_format($data->amountRpt, $data->rptCurrencyDecimals) }}</td>
                            @endif
                            @if($data->referenceAmountLocal != null && $data->amountLocal == null && $currencyID == 1)
                                    <td style="text-align: right">{{ number_format(ABS($data->referenceAmountLocal), $data->localCurrencyDecimals) }}</td>
                            @endif
                            @if($data->referenceAmountRpt != null && $data->amountRpt == null && $currencyID == 2)
                                    <td  style="text-align: right">{{ number_format(ABS($data->referenceAmountRpt), $data->rptCurrencyDecimals) }}</td>
                            @endif
                            @if($data->referenceAmountRpt == null && $data->referenceAmountLocal == null && $data->amountRpt == null && $data->amountLocal == null)
                                <td  style="text-align: right">0</td>
                            @endif
                        @else
                            <td>0</td>
                            <td>0</td>
                        @endif

                    </tr>
                    @endif
                @endforeach
                </tbody>
      </table>
@endforeach

