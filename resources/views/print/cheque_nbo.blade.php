<html>
<head>
    <title>{{ __('custom.cheque') }}</title>
    <style>
        @page {
            size: 200mm 280mm;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Courier New', monospace;
            font-size: 12pt;
            width: 200mm;
            height: 280mm;
            position: relative;
        }

        #supplier_name_1 {
            position: absolute;
            left: 1.524cm;
            top: 3.302cm;
            font-size: 12pt;
        }

        #pv_date {
            position: absolute;
            left: 15.24cm;
            top: 3.302cm;
            font-size: 12pt;
        }

        #pv_number {
            position: absolute;
            left: 15.24cm;
            top: 3.81cm;
            font-size: 12pt;
        }

        #supplier_row_container {
            position: absolute;
            left: 0;
            top: 5.334cm;
            width: 100%;
            display: flex;
            align-items: flex-start;
        }

        #supplier_code {
            font-size: 12pt;
            width: 3cm;
            text-align: left;
        }

        #supplier_name_2 {
            font-size: 12pt;
            width: 5cm;
            text-align: left;
        }

        #line_comments {
            font-size: 12pt;
            width: 4cm;
            line-height: 1.3;
            word-wrap: break-word;
        }

        .line-item-amount {
            font-size: 12pt;
            width: 4cm;
            text-align: right;
            margin-left: auto; /* push to right */
        }

        #total_words {
            position: absolute;
            left: 2.54cm;
            top: 14.478cm;
            font-size: 12pt;
            width: 12cm;
        }

        #total_amount {
            position: absolute;
            left: 15.24cm;
            top: 14.478cm;
            font-size: 12pt;
            text-align: right;
            width: 4cm;
        }

        #payee_name {
            position: absolute;
            left: 2.032cm;
            top: 21.59cm;
            font-size: 12pt;
            width: 12cm;
        }

        #amount_words {
            position: absolute;
            left: 1.778cm;
            top: 22.606cm;
            font-size: 12pt;
            width: 12cm;
        }

        #cheque_date {
            position: absolute;
            left: 15.494cm;
            top: 20.574cm;
            font-size: 12pt;
        }

        #amount_numbers {
            position: absolute;
            left: 16.002cm;
            top: 22.606cm;
            font-size: 12pt;
            text-align: right;
            width: 4cm;
        }
    </style>
</head>

<body onload="window.print();window.close()">
@php
    $firstDetail = optional($entity->details)->first();
    $supplier = optional($firstDetail)->supplier;

    $supplierName = optional($supplier)->supplierName ?? $entity->nameOnCheque ?? '';
    $supplierCode = optional($supplier)->supplierCode ?? 'S03';

    $pvDate = \App\helper\Helper::dateFormat($entity->BPVdate);
    $pvNumber = $entity->BPVcode;
    $chequeDate = \App\helper\Helper::dateFormat($entity->BPVchequeDate);

    $totalAmount = number_format($entity->payAmountBank, $entity->decimalPlaces);
    $amountWords = $entity->amount_word;
@endphp

<!-- Header -->
<div id="supplier_name_1">{{ $supplierName }}</div>
<div id="pv_date">{{ $pvDate }}</div>
<div id="pv_number">{{ $pvNumber }}</div>

<!-- Supplier row + line comments + amount (flex container) -->
@if(!empty($entity->details))
    @foreach ($entity->details as $index => $item)
        @php
            $amount = number_format($item->netAmount ?? 0, $entity->decimalPlaces ?? 2);
            $comment = $item->instruction ?? $item->comments ?? '';
            $comment = str_replace(["\r\n", "\r", "\n"], ' ', $comment);
        @endphp
        <div id="supplier_row_container" style="top: {{ 5.334 + $index * 0.6 }}cm;">
            <div id="supplier_code">{{ $supplierCode }}</div>
            <div id="supplier_name_2">{{ $supplierName }}</div>
            <div id="line_comments">{{ $comment }}</div>
            <div class="line-item-amount">{{ $amount }}</div>
        </div>
    @endforeach
@endif

<!-- Totals -->
<div id="total_words">{{ $amountWords }}&nbsp;{{ trans('custom.only') }}</div>
<div id="total_amount">{{ trans('custom.total') }}&nbsp;{{ $totalAmount }}</div>

<!-- Cheque -->
<div id="payee_name">{{ $supplierName }}</div>
<div id="cheque_date">{{ $chequeDate }}</div>
<div id="amount_words">{{ $amountWords }}&nbsp;{{ trans('custom.only') }}</div>
<div id="amount_numbers">{{ $totalAmount }}</div>

</body>
</html>
