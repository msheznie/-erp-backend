<html>
<head>
    <title>{{ __('custom.suppliers') }}</title>
    <style>
        @page {
            margin-left: 30px;
            margin-right: 30px;
            margin-top: 30px;
            margin-bottom: 0px;
        }

        body {
            font-size: 12px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }

        h3 {
            font-size: 24.5px;
        }

        h6 {
            font-size: 20px;
        }

        h6, h3 {
            margin-top: 0px;
            margin-bottom: 0px;
            font-weight: bold;
            line-height: 1.2;
            color: inherit;
        }

        table > tbody > tr > td {
            font-size: 11.5px;
        }

        .theme-tr-head {
            background-color: #DEDEDE !important;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        tr td {
            padding: 5px 0;
        }

        .table thead th {
            border-bottom: none !important;
        }

        .white-space-pre-line {
            white-space: pre-line;
            white-space: pre;
            word-wrap: normal;
        }

        .text-muted {
            color: #dedede !important;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #c2cfd6;
        }

        table.table-bordered {
            border: 1px solid #000;
        }

        .table th, .table td {
            padding: 6.4px !important;
        }

        table.table-bordered {
            border-collapse: collapse;
        }

        table.table-bordered, .table-bordered th, .table-bordered td {
            border: 1px solid #d3d9df;
            font-size:10px !important;
        }

        table > thead > tr > th {
            font-size: 11.5px;
        }

        hr {
            margin-top: 16px;
            margin-bottom: 16px;
            border: 0;
            border-top: 1px solid
        }

        hr {
            -webkit-box-sizing: content-box;
            box-sizing: content-box;
            height: 0;
            overflow: visible;
        }

        .header,
        .footer {
            width: 100%;
            text-align: left;
            position: fixed;
        }

        .header {
            top: 0px;
        }

        .footer {
            bottom: 40px;
        }

        .pagenum:before {
            content: counter(page);
        }

        #watermark {
            position: fixed;
            bottom: 0px;
            right: 0px;
            width: 200px;
            height: 200px;
            opacity: .1;
        }

        .content {
            margin-bottom: 45px;
        }
    </style>
</head>
<body onload="window.print()">
<div class="footer">
    {{--Footer Page <span class="pagenum"></span>--}}
    <span class="white-space-pre-line font-weight-bold">{!! nl2br($docRefNo) !!}</span>
</div>
<div id="watermark"></div>
<div class="card-body content">
    <table style="width: 100%">
        <tr style="width: 100%">
            <td colspan="3" class="text-center">
                <h3> {{$company->CompanyName}}</h3>
            </td>
        </tr>

        <tr style="width: 100%">
            <td colspan="3" class="text-center">
                <h6> Supplier List</h6>
            </td>
        </tr>
    </table>
    <div style="margin-top: 30px">
        <table class="table table-bordered" style="width: 100%;">
            <thead>
            <tr class="theme-tr-head">
                <th></th>
                <th>Code</th>
                <th>Supplier Name</th>
                <th>Category</th>
                <th>Currency</th>
                <th>Credit Period</th>
                <th>Country</th>
                <th>Address</th>
                <th>Telephone</th>
                <th>Email</th>
                <th>Critical YN</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($entities as $item)
                <tr style="width: 100%;">
                    <td>{{$loop->iteration}}</td>
                    <td>{{$item->primarySupplierCode}}</td>
                    <td>{{$item->supplierName}}</td>
                    <td>
                        @if($item->categoryMaster)
                            {{$item->categoryMaster->categoryDescription}}
                        @endif
                    </td>
                    <td>
                        @if(count($item->supplierCurrency) > 0 )
                            @if($item->supplierCurrency[0]->currencyMaster)
                                {{$item->supplierCurrency[0]->currencyMaster->CurrencyCode}}
                            @else
                                -
                            @endif
                        @endif
                    </td>
                    <td>{{$item->creditPeriod}}</td>
                    <td>
                        @if($item->country)
                            {{$item->country->countryName}}
                        @endif
                    </td>
                    <td>{{$item->address}}</td>
                    <td>{{$item->telephone}}</td>
                    <td>{{$item->supEmail}}</td>
                    <td>
                        @if($item->critical)
                            {{$item->critical->description}}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>

</body>
</html>