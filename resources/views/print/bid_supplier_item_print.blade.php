<html>
<head>
    <title>{{ __('custom.supplier_item_summary') }}</title>
    <style>
        .sup-item-summary-report, th, td{
            border: 1px solid black;
            border-collapse: collapse;
        }

        .sup-item-summary-report {
            font-size: 13px;
        }

        .sup-item-summary-report-head {
            font-size: 14px;
        }

        .sup-item-summary-report thead th {
            border-bottom: none !important;
        }

        .sup-item-summary-report thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #c2cfd6;
        }

    </style>
</head>
<h1><center>Commercial Bid Item Wise Evaluation Report</center></h1>
<table style="width:100%;" style="border: none" class="sup-item-summary-report-head">
    <tr>
        <td style="border: hidden"><strong>
        @if($documentType == 0)
                Tender ID:
        @elseif($documentType == 1)
                RFQ ID:
        @elseif($documentType == 2)
                RFI ID:
        @elseif($documentType == 3)
                RFP ID:
        @endif   
        </strong> {{$tender_code}}</td>
        <td style="border: hidden"></td>
    </tr>
    <tr>
        <td style="border: hidden"></td>
        <td style="border: hidden"></td>
    </tr>
    <tr>
        <td style="border: hidden"><strong>
        @if($documentType == 0)
                Tender Description:
        @elseif($documentType == 1)
                RFQ Description:
        @elseif($documentType == 2)
                RFI Description:
        @elseif($documentType == 3)
                RFP Description:
        @endif    
        
        </strong> {{$tender_description}}</td>
        <td style="border: hidden"></td>
    </tr>
    <tr>
        <td style="border: hidden"></td>
        <td style="border: hidden"></td>
    </tr>
    <tr>
        <td style="border: hidden"><strong>Commercial Bid Opening Date: </strong>
        @if ($commerical_bid_opening_date)
            {{\Carbon\Carbon::parse($commerical_bid_opening_date)->format('d/m/Y')}}
        @endif

        @if (empty($commerical_bid_opening_date))
                   -
        @endif 
    
    </td>
        <td style="border: hidden"></td>
    </tr>
</table>
<br>
<table style="width:100%;" class="sup-item-summary-report">
       <tr class="data-row">
           <th>Item</th>
           @foreach ($supplier_list as $bid)
               <th style="text-align: center;">{{$bid['name']}}</th>
           @endforeach
       </tr>
    <tbody>
    @foreach ($item_list[0]['pricing_shedule_details'] as $item)
        @if($item['boq_applicable'] != 1)
            <tr class="data-row">
                <td>{{$item['label']}}</td>
                @foreach ($item['bid_main_works'] as $bid_main_work)
                    <td style="text-align: right;">{{number_format($bid_main_work['total_amount'], 2, '.', ',')}}</td>
                @endforeach
            </tr>
        @endif

        @if($item['boq_applicable'] == 1)
            <tr class="data-row">
                <td><strong>{{$item['label']}}</strong></td>
                @foreach ($supplier_list as $bid)
                    <td></td>
                @endforeach
            </tr>
        @endif

        @foreach ($item['tender_boq_items'] as $boq)
            <tr class="data-row">
                <td>{{ $boq['item_name'] }}</td>
                @foreach ($boq['bid_boqs'] as $bid_boq)
                   <td style="text-align: right;">{{ number_format($bid_boq['total_amount'], 2, '.', ',')}}</td>
                @endforeach
            </tr>
        @endforeach
    @endforeach
    <tr class="data-row">
        <td><strong>Total</strong></td>
        @foreach ($supplier_list as $bid)
            @foreach ($totalItemsCount as $item)
                @if($item['id'] == $bid['id'])
                    <td style="text-align: right;"><strong>{{ number_format($item['value'], 2, '.', ',') }}</strong></td>
                @endif
            @endforeach
        @endforeach
    </tr>
    </tbody>
</table>
</html>

