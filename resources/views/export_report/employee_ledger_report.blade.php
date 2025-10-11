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
            <th>{{ __('custom.period_from') }} : {{ \Carbon\Carbon::parse($fromDate)->format("d/m/Y") }}</th>
        </tr>
        <tr>
            <th>{{ __('custom.period_to') }} : {{ \Carbon\Carbon::parse($toDate)->format("d/m/Y") }} </th>
        </tr>
    </thead>
</table>
@php
     function isPositive($num, $type){
        $modNum = 0;

        if($type == 7 || $type == 2 || $type == 5 || $type == 6 || $type == 3)
        {
          $modNum = $num * -1;
        }
        else {
          $modNum = ($num < 0) ? $num * -1 : $num;
        }
        return $modNum;
     }

     function isRefPositive($num, $type, $refType){
        $modNum = 0;

        if($type == 7 || $type == 2 || $type == 5 || $type == 6 || $type == 3)
        {
            if($refType == 1){
                $modNum = $num;
            }
            else{
                $modNum = $num * -1;
            }
        }
        else {
          $modNum = ($num < 0) ? $num * -1 : $num;
        }
        return $modNum;
     }
@endphp

@foreach($employees as $employee)
    <table>
        <thead>
            <tr>
                <td><B>{{ $employee->employeeName}} - {{ $employee->empID }}</B></td>
            </tr>
            <tr>
                <th>{{ __('custom.document_date') }}</th>
                <th>{{ __('custom.document_code') }}</th>
                <th>{{ __('custom.description') }}</th>
                @if($currencyID == 1)
                    <th>{{ __('custom.amount') }} ({{$currencyCodeLocal}})</th>
                @endif
                @if($currencyID == 2)
                    <th>{{ __('custom.amount') }} ({{$currencyCodeRpt}})</th>
                @endif
                <th>{{ __('custom.reference_doc') }}</th>
                <th>{{ __('custom.ref_doc_date') }}</th>
                @if($currencyID == 1)
                    <th>{{ __('custom.ref_amount') }} ({{$currencyCodeLocal}})</th>
                    <th>{{ __('custom.balance') }} ({{$currencyCodeLocal}})</th>
                @endif
                @if($currencyID == 2)
                    <th>{{ __('custom.ref_amount') }} ({{$currencyCodeRpt}})</th>
                    <th>{{ __('custom.balance') }} ({{$currencyCodeRpt}})</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if($employee->openingBalanceLocal != 0 || $employee->openingBalanceLocal != 0)
                <tr>
                    <td>-</td>
                    <td>{{ __('custom.opening_balance') }}</td>
                    <td>-</td>
                    @if($currencyID == 1)
                        <td style="text-align: right">{{number_format($employee->openingBalanceLocal,$currencyDecimalLocal)}}</td>
                    @endif
                    @if($currencyID == 2)
                        <td style="text-align: right">{{number_format($employee->openingBalanceRpt,$currencyDecimalRpt)}}</td>
                    @endif
                    <td>-</td>
                    <td>-</td>
                    @if($currencyID == 1)
                        <td style="text-align: right">{{number_format(0,$currencyDecimalLocal)}}</td>
                    @endif
                    @if($currencyID == 2)
                        <td style="text-align: right">{{number_format(0,$currencyDecimalRpt)}}</td>
                    @endif
                    @if($currencyID == 1)
                        <td style="text-align: right">{{number_format($employee->openingBalanceLocal,$currencyDecimalLocal)}}</td>
                    @endif
                    @if($currencyID == 2)
                        <td style="text-align: right">{{number_format($employee->openingBalanceRpt,$currencyDecimalRpt)}}</td>
                    @endif
                </tr>
            @endif
            @foreach($datas as $data)
                @if (($data->type != 2) && ($employee->employeeID == $data->employeeID))
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($data->documentDate)->format("d/m/Y") }}</td>
                        <td>{{ $data->documentCode }}</td>
                        <td>{{ $data->description }}</td>
                        @if($currencyID == 1)
                            <td  style="text-align: right">{{ number_format(isPositive($data->amountLocal,$data->type),$data->localCurrencyDecimals) }}</td>
                        @endif
                        @if($currencyID == 2)
                            <td  style="text-align: right">{{ number_format(isPositive($data->amountRpt,$data->type),$data->rptCurrencyDecimals) }}</td>
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
                        @if(isset($data->referenceAmountLocal) && $data->referenceAmountLocal != null && $currencyID == 1)
                            <td style="text-align: right">{{ number_format(isRefPositive($data->referenceAmountLocal,$data->type,$data->refType), $data->localCurrencyDecimals) }}</td>
                        @endif
                        @if(isset($data->referenceAmountRpt) && $data->referenceAmountRpt != null && $currencyID == 2)
                            <td  style="text-align: right">{{ number_format(isRefPositive($data->referenceAmountRpt, $data->type, $data->refType), $data->rptCurrencyDecimals) }}</td>
                        @endif
                        @if($data->referenceAmountRpt == null && $data->referenceAmountLocal == null)
                            <td  style="text-align: right">0</td>
                        @endif
                        @if($data->referenceAmountLocal != null && $data->amountLocal != null && $currencyID == 1)
                            <td style="text-align: right">{{ number_format(isPositive($data->amountLocal, $data->type) - isRefPositive($data->referenceAmountLocal, $data->type, $data->refType), $data->localCurrencyDecimals) }}</td>
                        @endif
                        @if($data->referenceAmountRpt != null && $data->amountRpt != null && $currencyID == 2)
                            <td  style="text-align: right">{{ number_format(isPositive($data->amountRpt, $data->type) - isRefPositive($data->referenceAmountRpt, $data->type, $data->refType), $data->rptCurrencyDecimals) }}</td>
                        @endif
                        @if($data->referenceAmountLocal == null && $data->amountLocal != null && $currencyID == 1)
                            <td style="text-align: right">{{ number_format(isPositive($data->amountLocal, $data->type), $data->localCurrencyDecimals) }}</td>
                        @endif
                        @if($data->referenceAmountRpt == null && $data->amountRpt != null && $currencyID == 2)
                            <td  style="text-align: right">{{ number_format(isPositive($data->amountRpt, $data->type), $data->rptCurrencyDecimals) }}</td>
                        @endif
                        @if($data->referenceAmountLocal != null && $data->amountLocal == null && $currencyID == 1)
                            <td style="text-align: right">{{ number_format(isRefPositive($data->referenceAmountLocal, $data->type, $data->refType), $data->localCurrencyDecimals) }}</td>
                        @endif
                        @if($data->referenceAmountRpt != null && $data->amountRpt == null && $currencyID == 2)
                            <td  style="text-align: right">{{ number_format(isRefPositive($data->referenceAmountRpt, $data->type, $data->refType), $data->rptCurrencyDecimals) }}</td>
                        @endif
                        @if($data->referenceAmountRpt == null && $data->referenceAmountLocal == null && $data->amountRpt == null && $data->amountLocal == null)
                            <td  style="text-align: right">0</td>
                        @endif
                    </tr>
                @endif
            @endforeach
            @if($employee->totalSumLocal != 0 || $employee->totalSumRpt != 0)
                <tr>
                    <td colspan="3" style="border-bottom-color:white !important;border-left-color:white !important; text-align: right !important;"><b>{{ __('custom.total') }}</b></td>
                    @if($currencyID == 1)
                        <td style="text-align: right"><b>{{number_format($employee->totalSumLocal,$currencyDecimalLocal)}}</b></td>
                    @endif
                    @if($currencyID == 2)
                        <td style="text-align: right"><b>{{number_format($employee->totalSumRpt,$currencyDecimalRpt)}}</b></td>
                    @endif
                    <td colspan="2" class="text-right" style="border-bottom-color:white !important;border-left-color:white !important; text-align: right !important;"><b>{{ __('custom.total') }}</b></td>
                    @if($currencyID == 1)
                        <td style="text-align: right"><b>{{number_format((isset($employee->totalSumRefReferenceAmountLocal) ? $employee->totalSumRefReferenceAmountLocal : 0),$currencyDecimalLocal)}}</b></td>
                    @endif
                    @if($currencyID == 2)
                        <td style="text-align: right"><b>{{number_format((isset($employee->totalSumRefReferenceAmountRpt) ? $employee->totalSumRefReferenceAmountRpt : 0),$currencyDecimalRpt)}}</b></td>
                    @endif
                    @if($currencyID == 1)
                        <td style="text-align: right"><b>{{number_format($employee->totalSumLocal - (isset($employee->totalSumRefReferenceAmountLocal) ? $employee->totalSumRefReferenceAmountLocal : 0),$currencyDecimalLocal)}}</b></td>
                    @endif
                    @if($currencyID == 2)
                        <td style="text-align: right"><b>{{number_format($employee->totalSumRpt - (isset($employee->totalSumRefReferenceAmountRpt) ? $employee->totalSumRefReferenceAmountRpt : 0),$currencyDecimalRpt)}}</b></td>
                    @endif
                </tr>
            @endif
            <tr>
                <td></td>
            </tr>
        </tbody>
    </table>
    <br>
    @php $isPV = 0 @endphp
    @foreach($datas as $data)
        @if($employee->employeeID == $data->employeeID)
            @if ($data->type == 2)
                @php $isPV = 1 @endphp
            @endif
        @endif
    @endforeach
    @if ($isPV == 1)
        <table>
            <thead>
                <tr>
                    <td><B>{{ __('custom.direct_payment_voucher') }}</B></td>
                </tr>
                <tr>
                    <th>{{ __('custom.document_date') }}</th>
                    <th>{{ __('custom.document_code') }}</th>
                    <th>{{ __('custom.description') }}</th>
                    @if($currencyID == 1)
                        <th>{{ __('custom.amount') }} ({{$currencyCodeLocal}})</th>
                    @endif
                    @if($currencyID == 2)
                        <th>{{ __('custom.amount') }} ({{$currencyCodeRpt}})</th>
                    @endif
                    <th>{{ __('custom.reference_doc') }}</th>
                    <th>{{ __('custom.ref_doc_date') }}</th>
                    @if($currencyID == 1)
                        <th>{{ __('custom.ref_amount') }} ({{$currencyCodeLocal}})</th>
                        <th>{{ __('custom.balance') }} ({{$currencyCodeLocal}})</th>
                    @endif
                    @if($currencyID == 2)
                        <th>{{ __('custom.ref_amount') }} ({{$currencyCodeRpt}})</th>
                        <th>{{ __('custom.balance') }} ({{$currencyCodeRpt}})</th>
                    @endif
                </tr>
            </thead>
            <tbody>
            @foreach($datas as $data)
                @if(($employee->employeeID == $data->employeeID) && ($data->type == 2))
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($data->documentDate)->format("d/m/Y") }}</td>
                        <td>{{ $data->documentCode }}</td>
                        <td>{{ $data->description }}</td>
                        @if($currencyID == 1)
                            <td  style="text-align: right">{{ number_format(isPositive($data->amountLocal,$data->type),$data->localCurrencyDecimals) }}</td>
                        @endif
                        @if($currencyID == 2)
                            <td  style="text-align: right">{{ number_format(isPositive($data->amountRpt,$data->type),$data->rptCurrencyDecimals) }}</td>
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
                        @if(isset($data->referenceAmountLocal) &&$data->referenceAmountLocal != null && $currencyID == 1)
                            <td style="text-align: right">{{ number_format(isRefPositive($data->referenceAmountLocal,$data->type,$data->refType), $data->localCurrencyDecimals) }}</td>
                        @endif
                        @if(isset($data->referenceAmountRpt) && $data->referenceAmountRpt != null && $currencyID == 2)
                            <td  style="text-align: right">{{ number_format(isRefPositive($data->referenceAmountRpt, $data->type, $data->refType), $data->rptCurrencyDecimals) }}</td>
                        @endif
                        @if($data->referenceAmountRpt == null && $data->referenceAmountLocal == null)
                            <td  style="text-align: right">0</td>
                        @endif
                        @if($data->referenceAmountLocal != null && $data->amountLocal != null && $currencyID == 1)
                            <td style="text-align: right">{{ number_format(isPositive($data->amountLocal, $data->type) - isRefPositive($data->referenceAmountLocal, $data->type, $data->refType), $data->localCurrencyDecimals) }}</td>
                        @endif
                        @if($data->referenceAmountRpt != null && $data->amountRpt != null && $currencyID == 2)
                            <td  style="text-align: right">{{ number_format(isPositive($data->amountRpt, $data->type) - isRefPositive($data->referenceAmountRpt, $data->type, $data->refType), $data->rptCurrencyDecimals) }}</td>
                        @endif
                        @if($data->referenceAmountLocal == null && $data->amountLocal != null && $currencyID == 1)
                            <td style="text-align: right">{{ number_format(isPositive($data->amountLocal, $data->type), $data->localCurrencyDecimals) }}</td>
                        @endif
                        @if($data->referenceAmountRpt == null && $data->amountRpt != null && $currencyID == 2)
                            <td  style="text-align: right">{{ number_format(isPositive($data->amountRpt, $data->type), $data->rptCurrencyDecimals) }}</td>
                        @endif
                        @if($data->referenceAmountLocal != null && $data->amountLocal == null && $currencyID == 1)
                            <td style="text-align: right">{{ number_format(isRefPositive($data->referenceAmountLocal, $data->type, $data->refType), $data->localCurrencyDecimals) }}</td>
                        @endif
                        @if($data->referenceAmountRpt != null && $data->amountRpt == null && $currencyID == 2)
                            <td  style="text-align: right">{{ number_format(isRefPositive($data->referenceAmountRpt, $data->type, $data->refType), $data->rptCurrencyDecimals) }}</td>
                        @endif
                        @if($data->referenceAmountRpt == null && $data->referenceAmountLocal == null && $data->amountRpt == null && $data->amountLocal == null)
                            <td  style="text-align: right">0</td>
                        @endif
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    @endif
@endforeach
<table>
    <tbody>
    <tr>
        <td colspan="3" style="border-bottom-color:white !important;border-left-color:white !important; text-align: right !important;"><b>{{ __('custom.grand_total') }}</b></td>
        @if($currencyID == 1)
            <td style="text-align: right"><b>{{number_format($grandSumData['grandSumLocal'],$currencyDecimalLocal)}}</b></td>
        @endif
        @if($currencyID == 2)
            <td style="text-align: right"><b>{{number_format($grandSumData['grandSumRpt'],$currencyDecimalRpt)}}</b></td>
        @endif
        <td colspan="2" class="text-right" style="border-bottom-color:white !important;border-left-color:white !important; text-align: right !important;"></td>
        @if($currencyID == 1)
            <td style="text-align: right"><b>{{number_format($grandSumData['grandRefSumLocal'],$currencyDecimalLocal)}}</b></td>
        @endif
        @if($currencyID == 2)
            <td style="text-align: right"><b>{{number_format($grandSumData['grandRefSumRpt'],$currencyDecimalRpt)}}</b></td>
        @endif
        @if($currencyID == 1)
            <td style="text-align: right"><b>{{number_format($grandSumData['grandSumLocal'] - $grandSumData['grandRefSumLocal'],$currencyDecimalLocal)}}</b></td>
        @endif
        @if($currencyID == 2)
            <td style="text-align: right"><b>{{number_format($grandSumData['grandSumRpt'] - $grandSumData['grandRefSumRpt'],$currencyDecimalRpt)}}</b></td>
        @endif
    </tr>
    </tbody>
</table>
