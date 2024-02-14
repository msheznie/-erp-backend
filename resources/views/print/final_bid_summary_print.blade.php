<html>
<head>
    <title>Supplier Ranking Summary Report</title>
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
                        class="font-weight-bold">Printed By :</span> {{ optional($employeeData)->empName ?? '' }}
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
                @if ($tenderCompany->company)
                    {{$tenderCompany->company->CompanyName}}
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
                @if($tenderCompany->company)
                    <img src="{{$tenderCompany->company->logo_url}}" width="180px" height="60px" class="container">
                @endif
            </td>
            <td valign="top" style="width: 80%; border: none">
                @if($tenderCompany->company)
                    <span style="font-size: 26px;font-weight: 400"> {{$tenderCompany->company->CompanyName}}</span>
                @endif
                    <br>
                    <table style="border: none">
                        <tr>
                            <td width="100px" style="border: none">
                                <span style="font-size: 18px;font-weight: 400">Supplier Ranking Summary Report</span>
                            </td>
                        </tr>
                    </table>
            </td>
        </tr>
    </table>
    <hr style="color: #d3d9df">
    <br>
    <br>
<table style="width:100%" class="bit-tender-summary-report">
    <tbody>
    <tr>
        <td><strong>Tender Code:</strong></td>
        <td colspan="2">{{ $tenderMaster->tender_code }}</td>
        <td colspan="2"><strong>Tender Title:</strong></td>
        <td colspan="4">{{ $tenderMaster->title }}</td>
    </tr>
    <tr>
        @if ($isNegotiation == 1)
            <td colspan="1"><strong>
                    @if($isNegotiation == 1)
                        Negotiation Tender Code:
                    @endif
                </strong></td>
            <td colspan="2">
                @if ($tenderMaster->negotiation_code && $isNegotiation == 1)
                    {{ $tenderMaster->negotiation_code }}
                @endif
            </td>
        @endif
        @if ($tenderMaster->stage == 1 && $isNegotiation == 1)
            <td colspan="2">
                @if ($tenderMaster->stage == 1 )
                    <strong>Bid Opening Date:</strong>
                @endif
            </td>
            <td colspan="4">
                @if ($tenderMaster->stage == 1)
                    @if ($tenderMaster->bid_opening_date && $isNegotiation == 0)
                        {{\Carbon\Carbon::parse($tenderMaster->bid_opening_date)->format('d/m/Y h:i A')}}
                    @endif
                    @if (empty($tenderMaster->bid_opening_date) || $isNegotiation == 1)
                        -
                    @endif
                @endif
            </td>
        @endif
        @if ($tenderMaster->stage == 1 && $isNegotiation == 0)
            <td colspan="1">
                @if ($tenderMaster->stage == 1 )
                    <strong>Bid Opening Date:</strong>
                @endif
            </td>
            <td colspan="8">
                @if ($tenderMaster->stage == 1)
                    @if ($tenderMaster->bid_opening_date && $isNegotiation == 0)
                        {{\Carbon\Carbon::parse($tenderMaster->bid_opening_date)->format('d/m/Y h:i A')}}
                    @endif
                    @if (empty($tenderMaster->bid_opening_date) || $isNegotiation == 1)
                        -
                    @endif
                @endif
            </td>
        @endif

        @if ($tenderMaster->stage == 2)
          @if ($isNegotiation == 1)
            <td><strong>Technical Bid Opening Date:</strong></td>
            <td colspan="5">
                    -
            </td>
           @endif
           @if ($isNegotiation == 0)
            <td><strong>Technical Bid Opening Date:</strong></td>
            <td colspan="2">
                @if ($tenderMaster->technical_bid_opening_date)
                    {{\Carbon\Carbon::parse($tenderMaster->technical_bid_opening_date)->format('d/m/Y h:i A')}}
                @endif

                @if (empty($tenderMaster->technical_bid_opening_date))
                    -
                @endif
            </td>
           @endif
        @if ($isNegotiation == 0)
          <td colspan="2"><strong>Commercial Bid Opening Date:</strong></td>
            <td colspan="4">

                @if ($tenderMaster->commerical_bid_opening_date)
                    {{\Carbon\Carbon::parse($tenderMaster->commerical_bid_opening_date)->format('d/m/Y h:i A')}}
                @endif
                @if (empty($tenderMaster->commerical_bid_opening_date))
                    -
                @endif
            </td>
        @endif
        @endif
    </tr>
        <tr>
        @if ($isNegotiation == 0)
            <td><strong>Comment:</strong></td>
            <td colspan="8">
                {{ $tenderMaster->award_comment }}
            </td>
        @endif
        @if ($isNegotiation == 1)
             @if ($tenderMaster->stage == 2)
                 <td><strong>Commercial Bid Opening Date:</strong></td>
                 <td colspan="2">
                    -
                </td>
             @endif
             @if ($tenderMaster->stage == 1)
                <td><strong>Comment:</strong></td>
                <td colspan="8">
                    {{ $tenderMaster->negotiation_award_comment }}
                </td>
             @endif
             @if ($tenderMaster->stage == 2)
                <td><strong>Comment:</strong></td>
                <td colspan="5">
                    {{ $tenderMaster->negotiation_award_comment }}
                </td>
             @endif
        @endif
    </tr>
    </tbody>
</table>
    <br/>
<table style="width:100%" class="bit-tender-summary-report">
    <tr>
        <td style="text-align: center;"><strong>Sr. No</strong></td>
        <td style="text-align: center;"><strong>Bid Submission Code</strong></td>
        <td style="text-align: center;"><strong>Bid Submission Date</strong></td>
        <td style="text-align: center;"><strong>Supplier Name</strong></td>
        <td style="text-align: center;"><strong>Total Commercial</strong></td>
        <td style="text-align: center;"><strong>Commercial Weightage</strong></td>
        <td style="text-align: center;"><strong>Technical Weightage</strong></td>
        <td style="text-align: center;"><strong>Total Weightage</strong></td>
        <td style="text-align: center;"><strong>Ranking</strong></td>
        <td style="text-align: center;"><strong>Awarding</strong></td>
    </tr>
    <tbody>
    @foreach ($awardSummary as $item)
        <tr>
            <td style="text-align: center;">{{ $loop->index+1 }}</td>
            <td>{{ $item->bidSubmissionCode }}</td>
            <td style="text-align: center;width: 90px">{{ \Carbon\Carbon::parse($item->bidSubmittedDatetime)->format('d/m/Y h:i A') }}</td>
            <td style="text-align: center;">{{ $item->name }}</td>
            <td style="text-align: right;"> {{ number_format($item->line_item_total, 3) }} </td>
            <td style="text-align: right;">{{ $item->com_weightage }}</td>
            <td style="text-align: right;">{{ $item->tech_weightage }}</td>
            <td style="text-align: right;">{{ $item->total_weightage }}</td>
            <td style="text-align: center;">{{ $item->combined_ranking }}</td>
            <td style="text-align: center;">
              @if ($item->award == 1)
                  Awarded
              @else
                Not Awarded
              @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>