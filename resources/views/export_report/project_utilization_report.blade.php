
<?php 
use Carbon\Carbon;
use Carbon\CarbonPeriod;
?>


<html>
	<table>
	        <thead>
            <tr></tr>
            <tr>
              <th colspan="7" align='center' style="font-size:50px">{{$reportTittle}}</th>
            </tr>
            <tr>
              <th colspan="7" align='center' style="font-size:50px">{{$companyName}}</th>
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
                <td colspan="2">Project Description - {{$projectDetail->description}}</td>
                <td colspan="2">Project Currency - {{$projectDetail->currency->CurrencyCode}}/{{$projectDetail->currency->CurrencyName}}</td>
                <td colspan="3"></td>
            </tr>
            <tr>
              <td  colspan="2">Segment - {{$projectDetail->service_line->ServiceLineCode}}/{{$projectDetail->service_line->ServiceLineDes}}</td>
              <td  colspan="2">Reporting  Currency - {{$CurrencyName}}</td>
              <td  colspan="3">Total Estimated Amount - {{ round($projectAmount, $decimalPoint)}}({{$CurrencyCode}})</td>
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
                <th  style="background-color:#e4e5e6">Document Narration</th>
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
                  <th></th>
                  <th>{{round($openingBalance, $decimalPoint)}}</th>
                </tr>
                @foreach ($detailsPOWise as $item)
                <?php 
                if ($item->documentSystemID == 2) {
                  $date = explode(' ',$item->purchase_order_detail->approvedDate);
                } elseif ($item->documentSystemID == 15) {
                  $date = explode(' ',$item->debit_note_detail->approvedDate);
                } elseif ($item->documentSystemID == 19) {
                  $date = explode(' ',$item->credit_note_detail->approvedDate);
                } elseif ($item->documentSystemID == 4) {
                  $date = explode(' ',$item->direct_payment_voucher_detail->approvedDate);
                }
                  $date = (new Carbon($date[0]))->format('d/m/Y');
                ?>
                <tr>
                  <td>{{$item->GLCode}}</td>
                  <td>{{$item->chart_of_account->AccountDescription}}</td>
                  <td>{{$item->documentCode}}</td>
                  <td>{{$date }}</td>
                  <td>-</td>
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
                    <td></td>
                    <th>{{round($budgetConsumptionAmount, $decimalPoint)}}</th>
                  </tr>
                  <tr>
                    <th>Closing Balance</th>
                    <th></th>
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