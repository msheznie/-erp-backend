
<?php 
use Carbon\Carbon;
use Carbon\CarbonPeriod;
?>


<html>
	<table>
	        <thead>
            <tr></tr>
            <tr>
              <th colspan="6" align='center' style="font-size:50px">{{$reportTittle}}</th>
            </tr>
            <tr>
              <th colspan="6" align='center' style="font-size:50px">{{$companyName}}</th>
            </tr>
            <tr></tr>
            <tr>
              <th colspan="2" style="font-size:15px;">Date From: {{$fromDate}}</th>
              <th colspan="2" style="font-size:15px;">Date To: {{$toDate}}</th>
            </tr>
            <tr></tr>
            <tr>
              <th>Project Details</th>
            </tr>
          </thead>
          <tbody>
            <?php 

            if(isset($companyReportingCurrency))
            {
              $decimalPoint = $companyReportingCurrency->DecimalPlaces;
              $CurrencyName =$companyReportingCurrency->CurrencyName;
              $CurrencyCode = $companyReportingCurrency->CurrencyCode;
            }
            else
            {
              $decimalPoint = 2;
              $CurrencyName ='N/A';
              $CurrencyCode = 'N/A';
            } 
            ?>
                <tr>
                  <td>Project Description</td>
                  <td> -  {{$projectDetail->description}}</td>
                  <td>Project Currency</td>
                  <td > -  {{$projectDetail->currency->CurrencyName}}</td>
                  <td>Project Budget</td>
                  <td > :-  {{ round($projectAmount, $decimalPoint)}}({{$CurrencyCode}})</td>
                </tr>
                <tr>
                  <td>Segment</td>
                  <td > -  {{$projectDetail->service_line->ServiceLineDes}}</td>
                  <td>Reporting  Currency</td>
                  <td > -  {{$CurrencyName}}</td>
                  <td>Balance Amount</td>
                  <td > :-  {{ round(($closingBalance), $decimalPoint)}}({{$CurrencyCode}})</td>
                </tr>
            <tr></tr>
          </tbody>
          <tbody>
            <tr>
              <thead>
                <th  style="background-color:#e4e5e6">GL Code</th>
                <th  style="background-color:#e4e5e6">GL Description</th>
                <th  style="background-color:#e4e5e6">Document Number</th>
                <th  style="background-color:#e4e5e6">Document Date</th>
                <th  style="background-color:#e4e5e6">Segment</th>
                <th  style="background-color:#e4e5e6">Amount</th>
              </thead>
              <tbody>
                <tr>
                  <th>Opening Balance</th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th>{{round($openingBalance, $decimalPoint)}}</th>
                </tr>
                @foreach ($detailsPOWise as $item)
                <?php 
                $date = '';
                if ($item->documentSystemID == 2 && isset($item->purchase_order_detail->approvedDate)) {
                  $date = explode(' ',$item->purchase_order_detail->approvedDate);
                } elseif ($item->documentSystemID == 15 && isset($item->debit_note_detail->debitNoteDate)) {
                  $date = explode(' ',$item->debit_note_detail->debitNoteDate);
                } elseif ($item->documentSystemID == 19 && isset($item->credit_note_detail->creditNoteDate)) {
                  $date = explode(' ',$item->credit_note_detail->creditNoteDate);
                } elseif ($item->documentSystemID == 4 && isset($item->direct_payment_voucher_detail->postedDate)) {
                  $date = explode(' ',$item->direct_payment_voucher_detail->postedDate);
                } elseif ($item->documentSystemID == 3 && isset($item->grv_master_detail->grvDate)) {
                  $date = explode(' ',$item->grv_master_detail->grvDate);
                } elseif ($item->documentSystemID == 17 && isset($item->jv_master_detail->JVdate)) {
                  $date = explode(' ',$item->jv_master_detail->JVdate);
                }
                  $date = $date?  (new Carbon($date[0]))->format('d/m/Y'):'';
                ?>
                <tr>
                  <td style="text-align: left;">{{$item->GLCode}}</td>
                  <td>{{$item->chart_of_account->AccountDescription}}</td>
                  <td>{{$item->documentCode}}</td>
                  <td>{{$date }}</td>
                  <td>{{$item->segment_by->ServiceLineDes}}</td>
                  <td>{{round($item->consumedRptAmount, $decimalPoint)}}</td>
                </tr>
                @endforeach

                <tr>
                    <th>Total Consumption</th>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <th>{{round($budgetConsumptionAmount, $decimalPoint)}}</th>
                  </tr>
                  <tr>
                    <th>Closing Balance</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>{{round($closingBalance, $decimalPoint)}}</th>
                  </tr>
              </tbody>
            </tr>
          </tbody>

 	</table>
</html>