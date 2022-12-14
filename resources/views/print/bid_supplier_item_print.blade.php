<html>
<head>
    <title>Item - Supplier</title>
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

    </style>
</head>

<table style="width:100%;" class="sup-item-summary-report">
       <tr>
           <th></th>
           @foreach ($srm_bid_submission_master as $doc)
               <th style="text-align: center;"><strong>{{$doc['SupplierRegistrationLink']['name']}}</strong></th>
           @endforeach
       </tr>

      @foreach ($bidData[0]['pricing_shedule_details'] as $doc)
        @if($doc['boq_applicable'] != 1)
            <tr>
              <td>{{$doc['label']}}</td>
              <td style="text-align: center;">{{$doc['bid_main_work']['amount']}}</td>
            </tr>
        @endif
      @endforeach
      @foreach ($bidData[0]['pricing_shedule_details'] as $doc)
        @if($doc['boq_applicable'] == 1)
            @if(sizeof($doc['tender_boq_items']) > 0)
              <tr>
                  <td><strong>{{$doc['label']}}</strong></td>
                  <td></td>
              </tr>
            @endif
              @foreach ($doc['tender_boq_items'] as $doc2)
                  <tr>
                      <td>{{ $doc2['item_name'] }}</td>
                      @foreach ($srm_bid_submission_master as $doc3)
                          @if (($doc3['SupplierRegistrationLink']['id']  == $doc2['bid_boq']['supplier_registration_id']))
                              <td style="text-align: center;">{{$doc2['bid_boq']['unit_amount']}}</td>
                          @endif
                      @endforeach
                      @endforeach
                  </tr>
                  @endif
      @endforeach
        <tr>
            <td><strong>Total</strong></td>
            @foreach ($srm_bid_submission_master as $doc)
                <td style="text-align: center;"><strong>{{$doc['SupplierRegistrationLink']['name']}}</strong></td>
            @endforeach
        </tr>
</table>
</html>

