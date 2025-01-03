<html>
<head>
    <title>Minutes of Bid Opening Report</title>
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
            @if($employeeData && $employeeData->empName)
                <td width="40%" style="border: none">
                    <span class="font-weight-bold">Printed By :</span>  {{$employeeData->empName}}
                </td>
            @endif
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
                                <span style="font-size: 18px;font-weight: 400">Minutes of Bid Opening Report</span>
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
            <td style="width: 10%"><strong>Tender Code:</strong></td>
            <td>{{ $tenderMaster->tender_code }}</td>
            <td style="width: 15%"><strong>Envelope:</strong></td>
            <td>{{ $tenderMaster->envelop_type ? $tenderMaster->envelop_type->name : ' - ' }}</td>
            <td style="width: 18%"><strong>Number of Alternative Solutions:</strong></td>
            <td>{{ $tenderMaster->no_of_alternative_solutions }}</td>
        </tr>
        <tr>
            @if ($isNegotiation == 1)
                <td><strong>Negotiation Tender Code:</strong></td>
                <td>
                    @if ($tenderMaster->negotiation_code != null)
                        {{ $tenderMaster->negotiation_code }}
                    @endif
                </td>
            @endif

            @if ($isNegotiation == 0)
            <td><strong> Title:</strong></td>
            <td>{{ $tenderMaster->title }}</td>
            @endif
            <td><strong>Evaluation:</strong></td>
            <td>{{ $tenderMaster->evaluation_type->name }}</td>
            <td><strong>Go/No Go Enable:</strong></td>
            <td>{{ $tenderMaster->is_active_go_no_go == 1 ? 'Yes' : 'No' }}</td>
        </tr>

        <tr>
            @if ($isNegotiation == 1)
                <td><strong> Title:</strong></td>
                <td>{{ $tenderMaster->title }}</td>
            @endif
            @if ($isNegotiation == 0)
            <td><strong>Selection:</strong></td>
            <td>{{ $tenderMaster->tender_type->name }}</td>
            @endif
            <td><strong>Stage:</strong></td>
            <td>{{ $tenderMaster->stage == 1 ? 'Single Stage' : 'Two Stage' }}</td>
            <td><strong>Min no of approval for bid opening:</strong></td>
            <td>{{ $tenderMaster->min_approval_bid_opening }}</td>
        </tr>
        <tr>
            @if ($isNegotiation == 1)
                <td><strong>Selection:</strong></td>
                <td>{{ $tenderMaster->tender_type->name }}</td>
            @endif
            @if ($isNegotiation == 0)
            <td><strong>No of Bids Submitted:</strong></td>
            <td>{{ $tenderBids }}</td>
            <td><strong>Bid Opening Date:</strong></td>
            <td colspan="3">{{\Carbon\Carbon::parse($tenderMaster->bid_opening_date)->format('d/m/Y h:i A')}}</td>
            @endif
            @if ($isNegotiation == 1)
            <td><strong>No of Bids Submitted:</strong></td>
            <td>{{ $tenderBids }}</td>
            <td><strong>Bid Opening Date:</strong></td>
            <td colspan="1">{{ '-' }}</td>
            @endif
        </tr>
        </tbody>
    </table>
    <br/>
    <h4>Suppliers List</h4>
    <table style="width:100%; font-size: 12px;">
        <tr>
            <td style="text-align: center; width: 10%"><strong>#</strong></td>
            <td style="text-align: left; width: 90%">&nbsp;<strong>Supplier Name</strong></td>
        </tr>
        @forelse ($tenderBidsSupplierList as $item)
            <tr>
                <td style="text-align: center;">{{ $loop->index + 1 }}</td>
                <td> &nbsp;{{ $item->SupplierRegistrationLink->name }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" style="text-align: center;"> {{ 'No Records Found' }}</td>
            </tr>
        @endforelse
    </table>

    <br />
    <h4>Bid Opening Approver Details</h4>
    <table style="width:100%; font-size: 12px;">
        <tr>
            <td style="text-align: center; width: 40%"><strong>Employee</strong></td>
            <td style="text-align: center; width: 40%">&nbsp;<strong>Approved Date & Time</strong></td>
            <td style="text-align: center; width: 20%">&nbsp;<strong>Action</strong></td>
        </tr>
        @if(count($SrmTenderBidEmployeeDetails) > 0)
            @foreach ($SrmTenderBidEmployeeDetails as $emp)
                <tr>
                    <td style="text-align: left;">{{$emp->employee->empID}} |  {{ $emp->employee->empFullName}}</td>
                    <td style="text-align: center;">
                        @if ($emp->status != null)&nbsp;
                        {{\Carbon\Carbon::parse($emp->updated_at)->format('d/m/Y h:i A')}}
                        @endif

                        @if ($emp->status == null)
                            {{ " - " }}
                        @endif
                    </td>
                    @if ($emp->status != null)
                        <td style="text-align: center;">  &nbsp;{{ $emp->status == 1 ? 'Approved' : 'Rejected'}}</td>
                    @endif
                    @if ($emp->status == null)
                        <td style="text-align: center;"> {{  "Pending Approval "}} </td>
                    @endif
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3">No records found.</td>
            </tr>
        @endif
    </table>
</div>


