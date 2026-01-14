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
            left: 2.1cm;
            top: 3.07cm;
            font-size: 12pt;
        }

        #pv_date {
            position: absolute;
            left: 15.34cm;
            top: 3.2cm;
            font-size: 12pt;
        }

        #pv_number {
            position: absolute;
            left: 15.34cm;
            top: 4cm;
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
            margin-left: 1.8cm;
        }

        #supplier_name_2 {
            font-size: 12pt;
            width: 5cm;
            text-align: left;
        }

        #BPVNarration {
            font-size: 12pt;
            width: 6cm;
            line-height: 1.3;
            word-wrap: break-word;
        }

        .line-item-amount {
            font-size: 12pt;
            width: 4cm;
            text-align: right;
            margin-left: auto;
            margin-right: 1cm;
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
            left: 15cm;
            top: 14.478cm;
            font-size: 12pt;
            text-align: right;
            width: 4cm;
        }

        #payee_name {
            position: absolute;
            left: 2.2cm;
            top: 22cm;
            font-size: 12pt;
            width: 12cm;
        }

        #amount_words {
            position: absolute;
            left: 2.2cm;
            top: 23cm;
            font-size: 12pt;
            width: 12cm;
        }

        #cheque_date {
            position: absolute;
            left: 16cm;
            top: 21.11cm;
            font-size: 12pt;
        }

        #amount_numbers {
            position: absolute;
            left: 14.7cm;
            top: 23.2cm;
            font-size: 12pt;
            text-align: right;
            width: 4cm;
        }
    </style>
</head>
<body onload="window.print();window.close()">
@php
    $supplier = optional($entity->details->first())->supplier;

    $supplierName = optional($supplier)->supplierName ?? $entity->nameOnCheque ?? '';
    $supplierCode = optional($supplier)->supplierCode ?? 'S03';

    $pvDate = \App\helper\Helper::dateFormat($entity->BPVdate);
    $pvNumber = $entity->BPVcode;
    $chequeDate = \App\helper\Helper::dateFormat($entity->BPVchequeDate);

    $totalAmount = number_format($entity->totalAmount ?? 0, $entity->decimalPlaces ?? 2);
    $amountWords = $entity->amount_word;

    $BPVNarration = str_replace(["\r\n", "\r", "\n"], ' ', $entity->BPVNarration ?? '');
@endphp


<!-- Header -->
<div id="supplier_name_1">{{ $supplierName }}</div>
<div id="pv_date">{{ $pvDate }}</div>
<div id="pv_number">{{ $pvNumber }}</div>

<!-- Single Supplier row with BPVNarration and total amount -->
<div id="supplier_row_container">
    <div id="supplier_code">{{ $supplierCode }}</div>
    <div id="supplier_name_2">{{ $supplierName }}</div>
    <div id="BPVNarration">{{ $BPVNarration }}</div>
    <div class="line-item-amount">{{ $totalAmount }}</div>
</div>

<!-- Totals -->
<div id="total_words">{{ $amountWords }}&nbsp;{{ trans('custom.only') }}</div>
<div id="total_amount">{{ $totalAmount }}</div>

<!-- Cheque -->
<div id="payee_name">{{ $supplierName }}</div>
<div id="cheque_date">{{ $chequeDate }}</div>
<div id="amount_words">{{ $amountWords }}&nbsp;{{ trans('custom.only') }}</div>
<div id="amount_numbers">{{ $totalAmount }}</div>

</body>
</html>
 