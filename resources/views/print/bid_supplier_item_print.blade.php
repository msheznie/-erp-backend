<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    .bit-tender-summary-report {
        font-size: 12px;
    }
</style>


<table style="width:100%" class="bit-tender-summary-report">
        <tr>
            <th></th>
            @foreach ($srm_bid_submission_master as $doc)
            <th style="text-align: center;"><strong>{{$doc['SupplierRegistrationLink']['name']}}</strong></th>
            @endforeach

        </tr>
    <tbody>
        @foreach ($bidData[0]['pricing_shedule_details'] as $doc)
            <tr>
            <td style="text-align: center;">{{$doc['label']}}</td>
            <td style="text-align: center;">{{$doc['bid_main_work']['amount']}}</td>
            </tr>
        @endforeach
        @foreach ($bidData[0]['pricing_shedule_details'] as $doc)
                @foreach ($doc['tender_boq_items'] as $doc2)
                    <tr>
                        <td style="text-align: center;">{{$doc2['item_name']}} - {{$doc2['bid_boq']['supplier_registration_id']}}</td>
                        @foreach ($srm_bid_submission_master as $doc3)
                            @if ($doc3['SupplierRegistrationLink']['id']  == $doc2['bid_boq']['supplier_registration_id'])
                                <td style="text-align: center;">{{$doc3['SupplierRegistrationLink']['id']}}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
        @endforeach
    </tbody>
</table>
