<html>
<head>
    <title>{{ __('custom.item_supplier') }}</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        .sup-item-summary-report {
            font-size: 12px;
        }

        .sup-item-summary-report-body {
            font-size: 12px;
        }

        .table thead th {
            border-bottom: none !important;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #c2cfd6;
        }

        .sup-item-summary-report-head {
            border: 0px solid black !important; 
        }

    </style>
</head>
<h1><center>Commercial Bid Schedule Wise Evaluation Report</center></h1>
<table style="width:100%;" class="sup-item-summary-report-head">
    <tr>
        <td style="border: hidden"><strong>

        @if($data[0]['tender']['document_type'] == 0)
                Tender ID:
        @elseif($data[0]['tender']['document_type'] == 1)
                RFQ ID:
        @elseif($data[0]['tender']['document_type'] == 2)
                RFI ID:
        @elseif($data[0]['tender']['document_type'] == 3)
                RFP ID:
        @endif     
        </strong> {{ $data[0]['tender']['tender_code'] }} </td>
        <td style="border: hidden"></td>
    </tr>
    <tr>
        <td style="border: hidden"></td>
        <td style="border: hidden"></td>
    </tr>
    <tr>
        <td style="border: hidden"><strong>
        @if($data[0]['tender']['document_type'] == 0)
                Tender Title:
        @elseif($data[0]['tender']['document_type'] == 1)
                RFQ Title:
        @elseif($data[0]['tender']['document_type'] == 2)
                RFI Title:
        @elseif($data[0]['tender']['document_type'] == 3)
                RFP Title:
        @endif       
        </strong> {{ $data[0]['tender']['title'] }}</td>
        <td style="border: hidden"></td>
    </tr>
    <tr>
        <td style="border: hidden"></td>
        <td style="border: hidden"></td>
    </tr>
    <tr>
        <td style="border: hidden"><strong>Commercial Bid Opening Date: </strong>
            @if ($data[0]['tender']['commerical_bid_opening_date'])
            {{\Carbon\Carbon::parse($data[0]['tender']['commerical_bid_opening_date'])->format('d/m/Y')}}
            @endif

            @if (empty($data[0]['tender']['commerical_bid_opening_date']))
            -
            @endif 
        </td>
        <td style="border: hidden"></td>
    </tr>
</table>
<br>

<table style="width:100%; margin-top:5%;" class="sup-item-summary-report">
   <tr>
    <th>Bid Submission Code</th>
    <th>Supplier Name</th>
    <th>Grand Sum</th>
    <th>Ranking</th>
   </tr>

   @foreach($data as $item)
   <tr>
    <td width="15%">{{$item['bidSubmissionCode']}}</td>
    <td width="35%">{{$item['supplier']}}</td>
    <td style="text-align:right">{{number_format($item['total'],3)}}</td>
    <td style="text-align:right">{{$item['ranking']}}</td>
   </tr>
   @endforeach

</table>



</html>
