<html>
<center>
    <table>
        <thead>
        <tr></tr>
        <tr>
            <td colspan="2"></td>
            <td><h1>Tax Details Report</h1></td>
        </tr>
        <tr></tr>
        <tr style="font-weight: bold">
            <td>Start Date : {{ $fromDate }} </td>
            <td>End Date :  {{ $toDate }}</td>
        </tr>
        <tr></tr>
        <tr>
            @if(in_array(1, $selectedColumns))
                <th>Company ID</th>
            @endif

            @if(in_array(2, $selectedColumns))
                <th>Document Code</th>
            @endif

            @if(in_array(3, $selectedColumns))
                <th>Document Date</th>
            @endif

            @if(in_array(34, $selectedColumns))
                <th>Document Type</th>
            @endif

            @if(in_array(15, $selectedColumns))
                <th>Reverse Charge Mechanism</th>
            @endif

            @if(in_array(4, $selectedColumns))
                <th>Invoice No</th>
            @endif

            @if(in_array(5, $selectedColumns))
                <th>Invoice Date</th>
            @endif

            @if(in_array(11, $selectedColumns))
                <th>Posted Date</th>
            @endif

            @if(in_array(6, $selectedColumns))
                <th>Narration</th>
            @endif

            @if(in_array(7, $selectedColumns))
                <th>Supplier Code</th>
            @endif

            @if(in_array(8, $selectedColumns))
                <th>Supplier Name</th>
            @endif

            @if(in_array(33, $selectedColumns))
                <th>Party Code</th>
            @endif

            @if(in_array(32, $selectedColumns))
                <th>Party Name</th>
            @endif

            @if(in_array(12, $selectedColumns))
                <th>Customer Code</th>
            @endif

            @if(in_array(13, $selectedColumns))
                <th>Customer Short Code</th>
            @endif

            @if(in_array(14, $selectedColumns))
                <th>Customer Name</th>
            @endif

            @if(in_array(16, $selectedColumns))
                <th>VAT IN</th>
            @endif

            @if(in_array(17, $selectedColumns))
                <th>Country</th>
            @endif

            @if(in_array(26, $selectedColumns))
                <th>Freezone</th>
            @endif

            @if(in_array(28, $selectedColumns))
                <th>Transaction</th>
            @endif

            @if(in_array(27, $selectedColumns))
                <th>Goods or Services</th>
            @endif

            @if(in_array(9, $selectedColumns))
                <th>Currency</th>
            @endif

            @if(in_array(29, $selectedColumns))
                <th>VAT Type</th>
            @endif

            @if(in_array(21, $selectedColumns))
                <th>Line Item No</th>
            @endif

            @if(in_array(23, $selectedColumns))
                <th>VAT Category</th>
            @endif

            @if(in_array(30, $selectedColumns))
                <th>VAT %</th>
            @endif

            <th>Value</th>
            <th>Discount</th>
            <th>Net Value</th>
            <th>VAT</th>

            @if(in_array(24, $selectedColumns))
                <th>Exempt VAT Portion</th>
            @endif

            @if(in_array(31, $selectedColumns))
                <th>Retention Amount</th>
            @endif

            <th>Due Amount</th>

            @if(in_array(20, $selectedColumns))
                <th>Amount In Reporting Currency ({{$reporingCurrencyCode}})</th>
            @endif

            @if(in_array(19, $selectedColumns))
                <th>Exchange Rate</th>
            @endif


        </tr>
        </thead>
        <tbody>
        @foreach($reportData as $data)
            <tr style="border-top: {{ isset($data->borderTop) ? '2px solid #000' : '1px solid #ccc' }}">
                @if(in_array(1, $selectedColumns)) <td>{{ $data->companyID }}</td> @endif
                @if(in_array(2, $selectedColumns)) <td>{{ $data->DocumentCode }}</td> @endif
                @if(in_array(3, $selectedColumns)) <td>{{ $data->DocumentDate }}</td> @endif
                @if(in_array(34, $selectedColumns)) <td>{{ $data->documentType }}</td> @endif
                @if(in_array(15, $selectedColumns))
                    <td>{{ (isset($data->rcmActivated) && $data->rcmActivated == 1) ? 'Yes' : 'No' }}</td>
                @endif
                @if(in_array(4, $selectedColumns)) <td>{{ $data->invoiceNo }}</td> @endif
                @if(in_array(5, $selectedColumns)) <td>{{ $data->invoiceDate }}</td> @endif
                @if(in_array(11, $selectedColumns)) <td>{{ $data->postedDate }}</td> @endif
                @if(in_array(6, $selectedColumns))
                    <td class="word-wrap">
                        @if(!empty($data->comments))
                            {{ $data->comments }}
                        </span>
                        @endif
                    </td>
                @endif
                @if(in_array(7, $selectedColumns)) <td>{{ $data->primarySupplierCode }}</td> @endif
                @if(in_array(8, $selectedColumns)) <td>{{ $data->supplierName }}</td> @endif
                @if(in_array(33, $selectedColumns)) <td>{{ $data->primarySupplierCode }}</td> @endif
                @if(in_array(32, $selectedColumns)) <td>{{ $data->supplierName }}</td> @endif
                @if(in_array(12, $selectedColumns)) <td>{{ $data->CutomerCode }}</td> @endif
                @if(in_array(13, $selectedColumns)) <td>{{ $data->customerShortCode }}</td> @endif
                @if(in_array(14, $selectedColumns)) <td>{{ $data->CustomerName }}</td> @endif
                @if(in_array(16, $selectedColumns)) <td>{{ $data->vatNumber }}</td> @endif
                @if(in_array(17, $selectedColumns)) <td>{{ $data->countryName }}</td> @endif
                @if(in_array(26, $selectedColumns)) <td></td> @endif
                @if(in_array(28, $selectedColumns)) <td>{{ $data->transcation }}</td> @endif
                @if(in_array(27, $selectedColumns)) <td>{{ $data->goodORService }}</td> @endif
                @if(in_array(9, $selectedColumns)) <td>{{ $data->CurrencyCode }}</td> @endif
                @if(in_array(29, $selectedColumns)) <td>{{ $data->vatCategory }}</td> @endif

                @if(in_array(21, $selectedColumns))
                    <td>
                        @if(!empty($data->itemPrimaryCode) && $reportViewID == 2)
                            {{ $data->itemPrimaryCode }}
                        @elseif(empty($data->itemPrimaryCode))
                            @if($reportViewID == 1)
                                {{ $data->lineItemNumberALL }}
                            @elseif($reportViewID == 2)
                                {{ $data->lineItemNumber }}
                            @endif
                        @elseif(empty($data->glCode) && $reportViewID == 2)
                            {{ $data->glCode }}
                        @endif
                    </td>
                @endif

                @if(in_array(23, $selectedColumns))
                    <td>
                        {{ $reportViewID == 1 ? $data->subCategoryDescriptionALL : $data->subCategoryDescription }}
                    </td>
                @endif

                @if(in_array(30, $selectedColumns))
                    <td>
                        {{ $reportViewID == 1 ? $data->VATPercentageALL : $data->VATPercentage }}
                    </td>
                @endif

                {{-- Amount --}}
                <td class="text-right">
                    {{ ($reportViewID == 1 ? $data->bookingAmountTrans : $data->value) }}
                </td>

                {{-- Discount --}}
                <td class="text-right">
                    {{ $reportViewID == 1 ? $data->discountAmount : $data->discount}}
                </td>

                {{-- Net --}}
                <td class="text-right">
                    {{ (
                        ($reportViewID == 1
                            ? $data->bookingAmountTrans - $data->discountAmount
                            : $data->value - $data->discount)
                    ) }}
                </td>

                {{-- VAT --}}
                <td class="text-right">
                    {{ (
                        $reportViewID == 1 ? $data->taxTotalAmount : $data->VATAmount
                    ) }}
                </td>

                @if(in_array(24, $selectedColumns))
                    <td>
                        {{ $reportViewID == 1
                            ? $data->exemptVATPortionALL
                            : ($data->exempt_vat_portion ?? null) }}
                    </td>
                @endif

                @if(in_array(31, $selectedColumns))
                    <td
                            @if($reportViewID == 2 && (isset($data->rowSpan) && $data->rowSpan) > 1)
                                rowspan="{{ $data->rowSpan }}"
                            style="vertical-align: middle; text-align: center"
                            @endif
                    >
                        {{ ($data->retentionAmount ?? 0 ) }}
                    </td>
                @endif

                {{-- Total Amount --}}
                <td class="text-right">
                    @php
                        $totalAmount = 0;
                        if ($reportViewID == 1) {
                            if (in_array($tempType, [1, 2])) {
                                $totalAmount = $data->bookingAmountTrans - $data->discountAmount + ((isset($data->rcmActivated) && $data->rcmActivated) ? 0 : $data->taxTotalAmount) - ($data->retentionAmount ?? 0);
                            } else {
                                $totalAmount = $data->bookingAmountTrans + $data->discountAmount + $data->taxTotalAmount;
                            }
                        } else {
                            if (in_array($tempType, [1, 2])) {
                                $totalAmount = $data->value - $data->discount + ((isset($data->rcmActivated) && $data->rcmActivated) ? 0 : $data->VATAmount) + (isset($data->documentTypeID) && in_array($data->documentTypeID,[0,2]) ? $data->exempt_vat_portion : 0) - ($data->retentionAmount ?? 0);
                            } else {
                                $totalAmount = $data->value + $data->discount + $data->VATAmount;
                            }
                        }
                    @endphp
                    {{ ($totalAmount) }}
                </td>

                {{-- Reporting Amount --}}
                @if(in_array(20, $selectedColumns))
                    <td>
                        @php
                            $reportingVal = 0;
                            if ($reportViewID == 1) {
                                if (in_array($tempType, [1, 2])) {
                                    $reportingVal = (
                                        ($data->bookingAmountTrans - $data->discountAmount + ((isset($data->rcmActivated) && $data->rcmActivated) ? 0 : $data->taxTotalAmount) - ($data->retentionAmount ?? 0))/
                                        $data->companyReportingER
                                    );
                                } else {
                                    $reportingVal = (
                                       ( $data->bookingAmountTrans + $data->discountAmount + $data->taxTotalAmount)/
                                        $data->companyReportingER
                                    );
                                }
                            } else {
                                if (in_array($tempType, [1, 2])) {
                                    $reportingVal = (
                                        ($data->value - $data->discount + ((isset($data->rcmActivated) && $data->rcmActivated) ? 0 : $data->VATAmount) + (isset($data->documentTypeID) && in_array($data->documentTypeID,[0,2]) ? $data->exempt_vat_portion : 0) - ($data->retentionAmount ?? 0))/
                                        $data->companyReportingER
                                    );
                                } else {
                                    $reportingVal = (
                                        ($data->value + $data->discount + $data->VATAmount)/
                                        $data->companyReportingER
                                    );
                                }
                            }
                        @endphp
                        {{ ($reportingVal) }}
                    </td>
                @endif

                @if(in_array(19, $selectedColumns)) <td>{{ $data->companyReportingER }}</td> @endif
            </tr>
        @endforeach
        </tbody>

    </table>
</center>
</html>
