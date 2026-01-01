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

        #supplier_code {
            position: absolute;
            left: 1.016cm;
            top: 5.334cm;
            font-size: 12pt;
        }

        #supplier_name_2 {
            position: absolute;
            left: 4.318cm;
            top: 5.334cm;
            font-size: 12pt;
        }

        #line_comments {
            position: absolute;
            left: 9.906cm;
            top: 5.334cm;
            font-size: 12pt;
            width: 4cm;
            max-width: 4cm;
            line-height: 1.3;
            white-space: normal;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        .line-item-amount {
            position: absolute;
            left: 16.002cm;
            font-size: 11pt;
            text-align: right;
            width: 4cm;
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
    $supplierCode = optional($supplier)->uniqueTextcode ?? '';
    $pvDate = \App\helper\Helper::dateFormat($entity->BPVdate);
    $pvNumber = $entity->BPVcode;
    $chequeDate = \App\helper\Helper::dateFormat($entity->BPVchequeDate);
    $totalAmount = number_format($entity->payAmountBank, $entity->decimalPlaces);
    $amountWords = $entity->amount_word;
    
    $lineItemStartTop = 5.334;
    $lineItemSpacing = 0.6;
@endphp

    <div id="supplier_name_1">{{ $supplierName }}</div>

    <div id="pv_date">{{ $pvDate }}</div>

    <div id="pv_number">{{ $pvNumber }}</div>

    <div id="supplier_code">{{ $supplierCode }}</div>

    <div id="supplier_name_2">{{ $supplierName }}</div>

    @php
        $instruction = '';
        if(isset($entity->details) && count($entity->details) > 0) {
            $firstItem = $entity->details->first();
            $instruction = $firstItem->instruction ?? '';
            $instruction = str_replace(["\r\n", "\r", "\n"], ' ', $instruction);
        }
    @endphp
    <div id="line_comments">{{ $instruction }}</div>

    @if(isset($entity->details) && count($entity->details) > 0)
        @foreach ($entity->details as $index => $item)
            @php
                $ref = '';
                $desc = '';
                $amount = number_format($item->netAmount ?? 0, $entity->decimalPlaces ?? 2);
                
                if($entity->invoiceType == 2) {
                    $ref = $item->bookingInvDocCode ?? '';
                    $desc = $item->supplierInvoiceNo ?? '';
                } elseif($entity->invoiceType == 3) {
                    $ref = $item->glCode ?? '';
                    $desc = $item->glCodeDes ?? '';
                } elseif($entity->invoiceType == 5) {
                    $ref = $item->purchaseOrderCode ?? '';
                    $desc = $item->itemDescription ?? '';
                }
                
                $itemInstruction = $item->instruction ?? $entity->BPVNarration ?? '';
                if($itemInstruction) {
                    $itemInstruction = str_replace(["\r\n", "\r", "\n"], ' ', $itemInstruction);
                    $desc = $desc . ' - ' . $itemInstruction;
                }
                $itemTop = $lineItemStartTop + ($index * $lineItemSpacing);
            @endphp
            <div style="position: absolute; left: 1.016cm; top: {{ $itemTop }}cm; font-size: 11pt; width: 3cm;">{{ $ref }}</div>
            <div style="position: absolute; left: 9.906cm; top: {{ $itemTop }}cm; font-size: 11pt; width: 4cm; max-width: 4cm; overflow-wrap: break-word; line-height: 1.3; white-space: normal; display: block; box-sizing: border-box;">{{ $desc }}</div>
            <div class="line-item-amount" style="top: {{ $itemTop }}cm;">{{ $amount }}</div>
        @endforeach
    @endif

    <div id="total_words">{{ $amountWords }}</div>

    <div id="total_amount">{{ $totalAmount }}</div>

    <div id="payee_name">{{ $supplierName }}</div>

    <div id="cheque_date">{{ $chequeDate }}</div>

    <div id="amount_words">{{ $amountWords }}</div>

    <div id="amount_numbers">{{ $totalAmount }}</div>

</body>
</html>
