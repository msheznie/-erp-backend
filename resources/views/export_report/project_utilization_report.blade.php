
<?php 
use Carbon\Carbon;
use Carbon\CarbonPeriod;
?>


<html>
	<table>
	        <thead>
            <tr></tr>
            <tr>
              <th></th>
              <th></th>
              <th align='center' style="font-size:50px">{{$reportTittle}}</th>
            </tr>
            <tr></tr>
            <tr>
              <th></th>
              <th style="font-size:15px;">Date From: {{$fromDate}}</th>
              <th style="font-size:15px;">Date To: {{$toDate}}</th>
              <th></th>
              <th></th>
            </tr>
            <tr></tr>
            <tr></tr>
            <tr>
              <th>Project Details</th>
            </tr>
          </thead>
	 	<tbody>
                      <?php 
                        $decimalPoint = $companyReportingCurrency->DecimalPlaces;
                      ?>
             
                    <tr>
                        <td>Description - {{$projectDetail->description}}</td>
                    </tr>
                    <tr>
                        <td>Segment - {{$projectDetail->service_line->ServiceLineCode}}/{{$projectDetail->service_line->ServiceLineDes}}</td>
                    </tr>
                    <tr>
                        <td>Project Currency - {{$projectDetail->currency->CurrencyCode}}/{{$projectDetail->currency->CurrencyName}}</td>
                    </tr>
                    <tr>
                      <td>Reporting  Currency :- {{$companyReportingCurrency->CurrencyName}}</td>
                    </tr>
                    <tr>
                        <td>Amount - {{ round($projectAmount, $decimalPoint)}}({{$companyReportingCurrency->CurrencyCode}})</td>
                    </tr>
              
	 	</tbody>
        <thead>
            <tr></tr>
            <tr></tr>
            <tr>
              <th>Opening Balance</th>
              <th></th>
              <th></th>
              <th></th>
              <th>{{round($openingBalance, $decimalPoint)}}</th>
            </tr>
            <tr></tr>
            <tr></tr>
            <tr>
              <th>Consumption</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <thead>
                <th></th>
                <th>Document Code</th>
                <th>Document Date</th>
                <th>Document Amount</th>
              </thead>
              <tbody>
                @foreach ($detailsPOWise as $item)
                <?php 
                  $date = explode(' ',$item->purchase_order_detail->approvedDate);
                  $date = (new Carbon($date[0]))->format('d/m/Y');
                ?>
                <tr>
                  <td></td>
                  <td>{{$item->documentCode}}</td>
                  <td>{{$date }}</td>
                  <td>{{round($item->documentAmount, $decimalPoint)}}</td>
                  <td></td>
                </tr>
                @endforeach

                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <th>{{round($budgetConsumptionAmount, $decimalPoint)}}</th>
                  </tr>

              </tbody>

              <thead>
                <tr></tr>
                <tr></tr>
                <tr>
                  <th>Closing Balance</th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th>{{round($closingBalance, $decimalPoint)}}</th>
                </tr>
              </thead>

            </tr>
          </tbody>

 	</table>
</html>