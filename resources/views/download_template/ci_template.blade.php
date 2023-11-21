<div class="row">
    <div>
        <div class="table">
            <table>
                <head>
                    <tr><th></th></tr>
                    <tr>
                        <th></th>
                        <th colspan="15">
                            "M" refers to Mandatory field. If mandatory field is empty in any row, the upload will not be successful
                        </th>
                    </tr>
                    <tr>
                        <th></th>
                        <th colspan="15">
                            Do not delete any of the columns in this excel sheet
                        </th>
                    </tr>
                    <tr>
                        <th></th>
                        <th colspan="15">
                            If you want to add multiple details to a single invoice use Customer invoice number column and repeat the same invoice number multiple times to have more than one details in a single invoice
                        </th>
                    </tr>
                    <tr>
                        <th></th>
                        <th colspan="15">
                            Confirmed by and Approved by columns are optional; if not updated, the system will automatically use the uploader's name.
                        </th>
                    </tr>
                    <tr>
                        <th></th>
                        <th colspan="15">
                            In Customer Code, CR Number at least one field should have a value in any given row. Both fields cannot be blank. 
                        </th>
                    </tr>
                        
                    </tr>
                </head>
            </table>
        </div>
    </div>

</div>
<div class="row">
    <div>
        <div class="table-responsive">
            @if ($isProjectBase && $isVATEligible)
                {{$detailColumns = 8}}
            @elseif ((!$isProjectBase && $isVATEligible) || ($isProjectBase && !$isVATEligible))
                {{$detailColumns = 7}}
            @else 
                {{$detailColumns = 6}}
            @endif
            <table class="table table-sm table-striped hover table-bordered">
                <thead>
                <tr>
                    <th colspan="12" style="text-align: center">Header</th>
                    <th colspan="{{$detailColumns}}" style="text-align: center">Details</th>
                </tr>
                <tr>
                    <th></th>
                    <th>M</th>
                    <th></th>
                    <th>M</th>
                    <th>M</th>
                    <th>M</th>
                    <th>M</th>
                    <th>M</th>
                    <th>M</th>
                    <th>M</th>
                    <th></th>
                    <th></th>
                    <th>M</th>
                    @if ($isProjectBase)
                        <th></th>
                    @endif
                    <th>M</th>
                    <th>M</th>
                    <th>M</th>
                    <th>M</th>
                    <th></th>
                    @if ($isVATEligible)
                        <th></th>
                    @endif
                </tr>
                <tr>
                    <th></th>
                    <th>Customer Code</th>
                    <th>CR Number</th>
                    <th>Currency</th>
                    <th>Comments</th>
                    <th>Document Date</th>
                    <th>Invoice Due Date</th>
                    <th>Customer Invoice No</th>
                    <th>Bank</th>
                    <th>Account No</th>
                    <th>Confirmed By</th>
                    <th>Approved By</th>
                    <th>GL Account</th>
                    @if ($isProjectBase)
                        <th>Project</th>
                    @endif
                    <th>Segment</th>
                    <th>UOM</th>
                    <th>Qty</th>
                    <th>Sales Price</th>
                    <th>Discount Amount</th>
                    @if ($isVATEligible)
                        <th>VAT Amount</th>
                    @endif
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
