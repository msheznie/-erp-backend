<html>
<table>
    <thead>
        <tr>
            <th colspan="5" align="center">{{$report_tittle}}</th>
        </tr>
        <tr>
            <th colspan="5" align="center">{{$companyName}}</th>
        </tr>
        <tr></tr>
        <tr>
            <th>Period From : {{ \Carbon\Carbon::parse($fromDate)->format("d/m/Y") }}</th>
        </tr>
        <tr>
            <th>Period To : {{ \Carbon\Carbon::parse($toDate)->format("d/m/Y") }} </th>
        </tr>
    </thead>
</table>
@php
     function isPositive($num, $type){
        $modNum = 0;
        $modNum  = ($num < 0) ? $num * -1 : $num;

        if($type == 2|| $type == 5 || $type == 6 || $type == 3)
        {
          $modNum = $modNum * -1;
        }
        return $modNum;

    }
@endphp

@foreach($employees as $employee)
    @php
        $sumLocal = 0;
        $sumRpt = 0;
        $sumRefLocal = 0;
        $sumRefRpt = 0;
        $sumBalLocal = 0;
        $sumBalRpt = 0;
    @endphp
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
                            <td  style="text-align: right">{{ number_format(isPositive($data->amountLocal,$data->type),$data->localCurrencyDecimals) }}</td>
                            @php $sumLocal += isPositive($data->amountLocal, $data->type) @endphp

                        @endif
                        @if($currencyID == 2)
                            <td  style="text-align: right">{{ number_format(isPositive($data->amountRpt,$data->type),$data->rptCurrencyDecimals) }}</td>
                            @php $sumRpt += isPositive($data->amountRpt, $data->type) @endphp

                        @endif
                        @if($data->referenceDoc != null)
                        <td>{{ $data->referenceDoc }}</td>
                        @endif
                        @if($data->referenceDoc == null)
                            <td>-</td>
                        @endif
                        @if($data->referenceDocDate != null)
                            @if($data->isLine == 1)
                            <td>{{ $data->referenceDocDate }}</td>
                            @endif
                            @if($data->isLine == 0)
                            <td>{{ \Carbon\Carbon::parse($data->referenceDocDate)->format("d/m/Y") }}</td>
                            @endif
                        @endif
                        @if($data->referenceDocDate == null)
                            <td>-</td>
                        @endif

                        @php
                            $data->referenceAmountLocal = isset($data->referenceAmountLocal) ? $data->referenceAmountLocal : null;
                            $data->referenceAmountRpt = isset($data->referenceAmountRpt) ? $data->referenceAmountRpt : null;
                        @endphp
                            @if(isset($data->referenceAmountLocal) &&$data->referenceAmountLocal != null && $currencyID == 1)
                                <td style="text-align: right">{{ number_format(isPositive($data->referenceAmountLocal,$data->type), $data->localCurrencyDecimals) }}</td>
                            @php $sumRefLocal += isPositive($data->referenceAmountLocal, $data->type) @endphp

                        @endif
                            @if(isset($data->referenceAmountRpt) && $data->referenceAmountRpt != null && $currencyID == 2)
                                <td  style="text-align: right">{{ number_format(isPositive($data->referenceAmountRpt, $data->type), $data->rptCurrencyDecimals) }}</td>
                            @php $sumRefRpt += isPositive($data->referenceAmountRpt, $data->type) @endphp
                            @endif
                            @if($data->referenceAmountRpt == null && $data->referenceAmountLocal == null)
                                <td  style="text-align: right">0</td>
                            @endif

                            @if($data->referenceAmountLocal != null && $data->amountLocal != null && $currencyID == 1)
                                <td style="text-align: right">{{ number_format(isPositive($data->amountLocal, $data->type) - isPositive($data->referenceAmountLocal, $data->type), $data->localCurrencyDecimals) }}</td>
                            @php $sumBalLocal += isPositive($data->amountLocal,$data->type) - isPositive($data->referenceAmountLocal, $data->type) @endphp
                            @endif

                            @if($data->referenceAmountRpt != null && $data->amountRpt != null && $currencyID == 2)
                                <td  style="text-align: right">{{ number_format(isPositive($data->amountRpt, $data->type) - isPositive($data->referenceAmountRpt, $data->type), $data->rptCurrencyDecimals) }}</td>
                            @php $sumBalRpt += isPositive($data->amountRpt, $data->type) - isPositive($data->referenceAmountRpt, $data->type) @endphp
                            @endif

                            @if($data->referenceAmountLocal == null && $data->amountLocal != null && $currencyID == 1)
                                    <td style="text-align: right">{{ number_format(isPositive($data->amountLocal, $data->type), $data->localCurrencyDecimals) }}</td>
                            @php $sumBalLocal += isPositive($data->amountLocal, $data->type) @endphp
                            @endif

                        @if($data->referenceAmountRpt == null && $data->amountRpt != null && $currencyID == 2)
                                    <td  style="text-align: right">{{ number_format(isPositive($data->amountRpt, $data->type), $data->rptCurrencyDecimals) }}</td>
                            @php $sumBalRpt += isPositive($data->amountRpt, $data->type) @endphp
                            @endif

                        @if($data->referenceAmountLocal != null && $data->amountLocal == null && $currencyID == 1)
                                    <td style="text-align: right">{{ number_format(isPositive($data->referenceAmountLocal, $data->type), $data->localCurrencyDecimals) }}</td>
                            @php $sumBalLocal += isPositive($data->referenceAmountLocal, $data->type) @endphp

                        @endif
                            @if($data->referenceAmountRpt != null && $data->amountRpt == null && $currencyID == 2)
                                    <td  style="text-align: right">{{ number_format(isPositive($data->referenceAmountRpt, $data->type), $data->rptCurrencyDecimals) }}</td>
                            @php $sumBalRpt += isPositive($data->referenceAmountRpt, $data->type) @endphp

                        @endif
                            @if($data->referenceAmountRpt == null && $data->referenceAmountLocal == null && $data->amountRpt == null && $data->amountLocal == null)
                                <td  style="text-align: right">0</td>
                            @endif


                    </tr>
                    @endif
                @endforeach
                <tr>
                    <td colspan="3" style="border-bottom-color:white !important;border-left-color:white !important; text-align: right !important;"><b>Total</b></td>

                    @if($currencyID == 1)
                        <td style="text-align: right"><b>{{number_format($sumLocal,$currencyDecimalLocal)}}</b></td>
                    @endif
                    @if($currencyID == 2)
                        <td style="text-align: right"><b>{{number_format($sumRpt,$currencyDecimalRpt)}}</b></td>
                    @endif

                    <td colspan="2" class="text-right" style="border-bottom-color:white !important;border-left-color:white !important; text-align: right !important;"><b>Total</b></td>
                    @if($currencyID == 1)
                        <td style="text-align: right"><b>{{number_format($sumRefLocal,$currencyDecimalLocal)}}</b></td>
                    @endif
                    @if($currencyID == 2)
                        <td style="text-align: right"><b>{{number_format($sumRefRpt,$currencyDecimalRpt)}}</b></td>
                    @endif
                    @if($currencyID == 1)
                        <td style="text-align: right"><b>{{number_format($sumBalLocal,$currencyDecimalLocal)}}</b></td>
                    @endif
                    @if($currencyID == 2)
                        <td style="text-align: right"><b>{{number_format($sumBalRpt,$currencyDecimalRpt)}}</b></td>
                    @endif
                </tr>
                </tbody>
      </table>
@endforeach


