<html>
<head>
    <title>Minutes of Tender Awarding Report</title>
    <style>
        @page {
            margin-left: 30px;
            margin-right: 30px;
            margin-top: 30px;
        }

        .footer {
            position: absolute;
        }

        .footer {
            bottom: 0;
            height: 100px;
        }

        .footer {
            width: 100%;
            text-align: center;
            position: fixed;
            font-size: 10px;
            padding-top: -20px;
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

        table > tbody > tr > td {
            font-size: 11.5px;
        }

        .font-weight-bold {
            font-weight: 700 !important;
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
            border: 1px solid #e2e3e5;
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
        .pagenum:before {
            content: counter(page);
        }
        .content {
            margin-bottom: 45px;
        }
        .container
        {
            display: block;
            max-width:230px;
            max-height:95px;
            width: auto;
            height: auto;
        }

        .table_height
        {
            max-height: 60px !important;
        }

        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }

    </style>
</head>
<body>
<div class="footer">
    <table style="width:100%; border: none">
        <tr>
            <td width="40%" style="border: none"><span
                        class="font-weight-bold">Printed By :</span> {{$employeeData->empName}}
            </td>
        </tr>
    </table>
    <table style="width:100%; border: none">
        <tr>
            <td style="border: none"><span class="font-weight-bold">Printed Date & Time :</span>{{date('d/m/Y h:i A')}}</span><br></td>
        </tr>
        <tr>
            &nbsp;
        </tr>
    </table>
    <table style="width:100%; border: none">
        <tr>
            <td colspan="3" style="width:100%; border: none">
                <hr style="background-color: black">
            </td>
        </tr>
        <tr>
            <td style="width:33%;font-size: 10px;vertical-align: top; border: none; border: none">
            </td>
            <td style="width:33%; text-align: center;font-size: 10px;vertical-align: top; border: none">
                <span style="text-align: center">Page <span class="pagenum"></span></span><br>
                @if ($company)
                    {{$company->CompanyName}}
                @endif
            </td>
            <td style="width:33%;font-size: 10px;vertical-align: top; border: none">
            </td>
        </tr>
    </table>
</div>
<div class="card-body content" id="print-section">
    <table style="width: 100%; border: none" class="table_height">
        <tr style="width: 100%; border: none">
            <td valign="top" style="width: 20%; border: none">
                @if($company)
                    <img src="{{$company->logo_url}}" width="180px" height="60px" class="container">
                @endif
            </td>
            <td valign="top" style="width: 80%; border: none">
                @if($company)
                    <span style="font-size: 26px;font-weight: 400"> {{$company->CompanyName}}</span>
                @endif
                    <br>
                    <table style="border: none">
                        <tr>
                            <td width="100px" style="border: none">
                                <span style="font-size: 18px;font-weight: 400">Minutes of Tender Awarding Report</span>
                            </td>
                        </tr>
                    </table>
            </td>
        </tr>
    </table>
    <hr style="color: #d3d9df">
    <br>
    <br>
    <table style="width:100%; font-size: 12px;">
        <tbody>
        <tr>
            <td><strong>Tender Code:</strong></td>
            <td>{{ $tenderMaster->tender_code }}</td>
            <td><strong>Tender Title:</strong></td>
            <td>{{ $tenderMaster->title }}</td>
        </tr>
        @if ($tenderMaster->negotiation_code != null)
        <tr>
            <td><strong>Negotiation Tender Code:</strong></td>
            <td colspan="3">
                @if ($tenderMaster->negotiation_code != null)
                    {{ $tenderMaster->negotiation_code }}
                @endif
            </td>
        </tr>
        @endif
        <tr>
            @if ($tenderMaster->stage == 1 && $tenderMaster->negotiation_code == null)
                <td><strong>Bid Opening Date:</strong></td>
                <td>
                    @if ($tenderMaster->bid_opening_date)
                        {{\Carbon\Carbon::parse($tenderMaster->bid_opening_date)->format('d/m/Y h:i A')}}
                    @endif
                    @if (empty($tenderMaster->bid_opening_date))
                        -
                    @endif
                </td>
            @endif
            @if ($tenderMaster->stage == 2 && $tenderMaster->negotiation_code == null)
                <td><strong>Technical Bid Opening Date:</strong></td>
                <td>
                    @if ($tenderMaster->technical_bid_opening_date)
                        {{\Carbon\Carbon::parse($tenderMaster->technical_bid_opening_date)->format('d/m/Y h:i A')}}
                    @endif
                    @if (empty($tenderMaster->technical_bid_opening_date))
                        -
                    @endif
                </td>
            @endif
            @if ($tenderMaster->negotiation_code != null)
                <td><strong>Bid Opening Date:</strong></td>
                <td>-</td>
            @endif
            <td><strong>Committee Minimum Approval:</strong></td>
            <td>{{ $tenderMaster->min_approval_bid_opening }}</td>
        </tr>
        <tr>
            <td><strong>Tender Awarded Supplier Name:</strong></td>
            <td>{{ $tenderMaster->ranking_supplier->supplier->name }}</td>
            <td><strong>Tender Awarding Comment:</strong></td>
            <td>{{ $tenderMaster->final_tender_award_comment }}</td>
        </tr>
        </tbody>
    </table>
    <br/>
    <table style="width:100%; font-size: 12px;">
        <tr>
            <td style="text-align: center;"><strong>Committee Members</strong></td>
            <td style="text-align: center;"><strong>Approved Date & Time</strong></td>
            <td style="text-align: center;"><strong>Approved Status</strong></td>
        </tr>
        <tbody>
        @foreach ($employeeDetails as $item)
            <tr>
                <td>{{ $item->employee->empID }} | {{$item->employee->empName}}</td>
                <td style="text-align: center;">
                    @if ($item->tender_award_commite_mem_status != 0)
                    {{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y h:i A') }}
                    @else
                        {{'-'}}
                    @endif
                </td>
                <td style="text-align: center;">
                    @if ($item->tender_award_commite_mem_status == 1)
                        Approved
                    @endif
                    @if ($item->tender_award_commite_mem_status == 2)
                        Rejected
                    @endif
                    @if ($item->tender_award_commite_mem_status == 0)
                         -
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>


