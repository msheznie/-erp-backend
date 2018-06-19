<style type="text/css">
    .font-weight-bold {
        font-weight: 700 !important;
    }
</style>
<div class="footer">
    <table style="width:100%;">
        <tr>
            <td colspan="2" style="width:100%">
                <span class="font-weight-bold">Kindly confirm the balance and settle the pending invoices at the earliest.</span>
            </td>
        </tr>
        <tr>
            <td style="width:50%;font-size: 10px;vertical-align: top;">
                <span style="">Printed Date : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
            <td style="width:50%; text-align: center;font-size: 10px;vertical-align: top;">
                <span style="text-align: center">Page <span class="pagenum"></span></span><br>
            </td>
        </tr>
    </table>
</div>
<div class="content">
    <div class="row">
        <table style="width:100%;" class="table table-bordered table-striped table-sm">
            @foreach ($reportData as $key => $val)
                <thead>
                <tr style="border-top: 1px solid black;">
                    <th width="10%">Document Code</th>
                    <th width="10%">Posted Date</th>
                    <th width="5%">Contract</th>
                    <th width="5%">PO Number</th>
                    <th width="10%">Invoice Date</th>
                    <th width="15%">Narration</th>
                    <th width="5%">Currency</th>
                    <th width="10%">Invoice Amount</th>
                    <th width="5%">Receipt/CN Code</th>
                    <th width="5%">Receipt/CN Date</th>
                    <th width="10%">Receipt Amount</th>
                    <th width="10%">Balance Amount</th>
                </tr>
                </thead>
                <tbody>
                {{ $subInvoiceAmount = 0 }}
                {{ $subReceiptAmount = 0 }}
                {{ $subBalanceAmount = 0 }}
                @foreach ($val as $det)
                    {{ $subInvoiceAmount += $det->invoiceAmount }}
                    {{ $subReceiptAmount += $det->receiptAmount }}
                    {{ $subBalanceAmount += $det->balanceAmount }}
                    <tr style="border-bottom: 1px solid black;">
                        <td>{{ $det->documentCode  }}</td>
                        <td>{{$det->postedDate}}</td>
                        <td>{{$det->clientContractID}}</td>
                        <td>{{$det->invoiceDate}}</td>
                        <td>{{$det->documentNarration}}</td>
                        <td>{{$det->documentCurrency}}</td>
                        <td class="text-right">{{number_format($det->invoiceAmount, $det->balanceDecimalPlaces)}}</td>
                        <td>{{$det->ReceiptCode}}</td>
                        <td>{{$det->ReceiptDate}}</td>
                        <td class="text-right">{{number_format($det->receiptAmount, $det->balanceDecimalPlaces)}}</td>
                        <td class="text-right">{{number_format($det->balanceAmount, $det->balanceDecimalPlaces)}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7" class="text-right"><b>Sub Total:</b></td>
                    <td class="text-right">{{number_format($subInvoiceAmount, $reportData->$key[0]->balanceDecimalPlaces)}}</td>
                    <td colspan="2" style="border-bottom-color:white !important;border-left-color:white !important"
                        class="text-right"></td>
                    <td class="text-right">{{number_format($subReceiptAmount, $reportData->$key[0]->balanceDecimalPlaces)}}</td>
                    <td class="text-right">{{number_format($subBalanceAmount, $reportData->$key[0]->balanceDecimalPlaces)}}</td>
                </tr>
                </tbody>
            @endforeach
            <tr>
                <td colspan="7" class="text-right"><b>Grand Total:</b></td>
                <td class="text-right">{{number_format($invoiceAmount, $currencyDecimalPlace)}}</td>
                <td colspan="2" style="border-bottom-color:white !important;border-left-color:white !important"
                    class="text-right"></td>
                <td class="text-right">{{number_format($receiptAmount, $currencyDecimalPlace)}}</td>
                <td class="text-right">{{number_format($balanceAmount, $currencyDecimalPlace)}}</td>
            </tr>
        </table>

    </div>
</div>