<html>
<head>
    <title>Item - Supplier</title>
    <style>
        .sup-item-summary-report, th, td{
            border: 1px solid black;
            border-collapse: collapse;
        }

        .sup-item-summary-report {
            font-size: 12px;
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
<table style="border: none">
    <tr style="border: hidden">
        <td style="border: hidden">Tender ID:</td>
        <td style="border: hidden"></td>
    </tr>
    <tr>
        <td style="border: hidden">Tender Description:</td>
        <td style="border: hidden"></td>
    </tr>
    <tr>
        <td style="border: hidden">Commercial Bid Opening Date:</td>
        <td style="border: hidden"></td>
    </tr>
</table>
<br>
<br>
<table style="width:100%;" class="sup-item-summary-report">
       <tr class="data-row">
           <th>Item</th>
           @foreach ($srm_bid_submission_master as $doc)
               <th style="text-align: center;">{{$doc['SupplierRegistrationLink']['name']}}</th>
           @endforeach
       </tr>

      @foreach ($supplier_list[0]['pricing_shedule_details'] as $doc)
        @if($doc['boq_applicable'] != 1)
            <tr class="data-row">
              <td>{{$doc['label']}}</td>
              <td style="text-align: center;">{{$doc['bid_main_work']['total_amount']}}</td>
            </tr>
        @endif
      @endforeach
      @foreach ($supplier_list[0]['pricing_shedule_details'] as $doc)
        @if($doc['boq_applicable'] == 1)
            @if(sizeof($doc['tender_boq_items']) > 0)
              <tr class="data-row">
                  <td><strong>{{$doc['label']}}</strong></td>
                  <td></td>
              </tr>
            @endif
              @foreach ($doc['tender_boq_items'] as $doc2)
                  <tr class="data-row">
                      <td>{{ $doc2['item_name'] }}</td>
                      @foreach ($srm_bid_submission_master as $doc3)
                          @if (($doc3['SupplierRegistrationLink']['id']  == $doc2['bid_boq']['supplier_registration_id']))
                              <td style="text-align: center;">{{$doc2['bid_boq']['total_amount']}}</td>
                          @endif
                      @endforeach
                      @endforeach
                  </tr>
                  @endif
      @endforeach
        <tr class="data-row">
            <td><strong>Total</strong></td>
            @foreach ($srm_bid_submission_master as $doc)
                <td style="text-align: center;"><strong></strong></td>
            @endforeach
        </tr>
</table>
</html>

