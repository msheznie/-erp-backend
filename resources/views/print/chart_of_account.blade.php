<html>
<head>
    <title>Chart Of Account</title>
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
            font-size: 14px;
        }

        h6, h3 {
            margin-top: 0px;
            margin-bottom: 0px;
            font-family: inherit;
            font-weight: bold;
            line-height: 1.2;
            color: inherit;
        }

        table > tbody > th > tr > td {
            font-size: 11px;
        }

        tr td {
            padding: 5px 0;
        }

        table {
            border-collapse: collapse;
        }

        .table th {
            border: 1px solid rgb(127, 127, 127) !important;
        }

        .table th, .table td {
            padding: 0.4rem !important;
            vertical-align: top;
            border: 1px solid rgb(127, 127, 127) !important;
        }

        .table th {
            background-color: #EBEBEB !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
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
    </style>
</head>
<body>
<div class="card-body content" id="print-section">
    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td valign="top" style="width: 30%">
                @if($chartOfAccount->primaryCompany)
                    <img src="{{$chartOfAccount->primaryCompany->logo_url}}" width="100" class="container">
                @endif
            </td>
            <td valign="top" style="width: 70%">

                <span style="font-size: 24px;font-weight: 400"> {{ $chartOfAccount->primaryCompany?$chartOfAccount->primaryCompany->CompanyName:'' }}</span>
                <br><br>
                <span style="font-weight: bold"> Account Code: {{$chartOfAccount->AccountCode}}</span>
                <br>
            </td>
        </tr>
    </table>
    <hr style="border-top: 2px solid black; height: 2px; color: black">

    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td style="text-align: center; font-weight: bold">
                <div>
                    <span style="font-size: 18px">Chart Of Account</span>
                </div>
            </td>
        </tr>
    </table>

    <table style="width: 100%">
        <tr style="width:100%">
            <td style="width: 60%">
                <table>
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">Primary Company</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{$chartOfAccount->primaryCompany?$chartOfAccount->primaryCompany->CompanyName: '-'}}
                        </td>
                    </tr>
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">Account Code</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{$chartOfAccount->AccountCode?$chartOfAccount->AccountCode: '-'}}
                        </td>
                    </tr>
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">Account Description</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{$chartOfAccount->AccountDescription?$chartOfAccount->AccountDescription: '-'}}
                        </td>
                    </tr>
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">Category</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{$chartOfAccount->accountType?$chartOfAccount->accountType->description: '-'}}
                        </td>
                    </tr>
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">Control Account</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{$chartOfAccount->controlAccount?$chartOfAccount->controlAccount->description: '-'}}
                        </td>
                    </tr>
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">Control Account YN</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            @if($chartOfAccount->controllAccountYN == 1)
                                Yes
                            @else
                                No
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">Default Template Category</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{$chartOfAccount->templateCategoryDetails?$chartOfAccount->templateCategoryDetails->description: '-'}}
                        </td>
                    </tr>
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">Confirmed By</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{$chartOfAccount->confirmedEmpName?$chartOfAccount->confirmedEmpName: '-'}}
                        </td>
                    </tr>
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">Confirmed Date</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{$chartOfAccount->confirmedEmpDate?$chartOfAccount->confirmedEmpDate: '-'}}
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%" valign="top">
                <table>
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">Is Active</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            @if($chartOfAccount->isActive == 1)
                                Yes
                            @else
                                No
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">Is Bank</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            @if($chartOfAccount->isBank == 1)
                                Yes
                            @else
                                No
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">Allocation Type</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            {{ $chartOfAccount->allocation?$chartOfAccount->allocation->Desciption:'-' }}
                        </td>
                    </tr>
                    <tr>
                        <td width="150px">
                            <span style="font-weight:bold;">Related Party YN</span>
                        </td>
                        <td width="10px">
                            <span style="font-weight:bold;">:</span>
                        </td>
                        <td>
                            @if($chartOfAccount->relatedPartyYN == 1)
                                Yes
                            @else
                                No
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
