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
             
                    <tr>
                        <td>Description - {{$projectDetail->description}}</td>
                    </tr>
                    <tr>
                        <td>Segment - {{$projectDetail->service_line->ServiceLineCode}}/{{$projectDetail->service_line->ServiceLineDes}}</td>
                    </tr>
                    <tr>
                        <td>Currency - {{$projectDetail->currency->CurrencyCode}}/{{$projectDetail->currency->CurrencyName}}</td>
                    </tr>
                    <tr>
                        <td>Amount - {{round($projectAmount, 3)}}</td>
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
              <th>{{round($openingBalance, 3)}}</th>
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
                ?>
                <tr>
                  <td></td>
                  <td>{{$item->documentCode}}</td>
                  <td>{{$date[0] }}</td>
                  <td>{{round($item->documentAmount, 3)}}</td>
                </tr>
                @endforeach

                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <th>{{round($budgetConsumptionAmount, 3)}}</th>
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
                  <th>{{round($closingBalance, 3)}}</th>
                </tr>
              </thead>

            </tr>
          </tbody>

 	</table>
</html>