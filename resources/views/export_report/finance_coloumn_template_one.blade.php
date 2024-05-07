<html>
 	<table>
        <thead>

			<tr>
				<th colspan="5" align="center">{{$template->reportName}}</th>
			</tr>
			<tr>
				<th colspan="5" align="center">{{$company->CompanyName}}</th>
			</tr>
			<tr></tr>
			@if ($month != null)
				<tr>
					<th>As of - {{$month}}</th>
				</tr>
			@endif

			@if ($from_date != null && $to_date != null)
				<tr>
					<th>Period From - {{$from_date}}</th>
				</tr>
				<tr>
					<th>Period To - {{$to_date}} </th>
				</tr>
			@endif
			<tr><th>Currency: {{$currencyCode}}</th></tr>
			<tr></tr>
			<tr></tr>
			@if($columnTemplateID == 2)
				<tr>
		            @if($fourthLevel)
		            	<th colspan="5"></th>
		            @elseif($thirdLevel)
		            	<th colspan="4"></th>
		            @elseif($secondLevel)
		            	<th colspan="3"></th>
		            @elseif($firstLevel)
		            	<th colspan="2"></th>
		            @else
		            	<th></th>
		            @endif
		            @foreach ($companyHeaderData as $company)
		            	<th style="text-align: center;" colspan="{{sizeof($columnHeader)}}">
	            			{{$segmentParentData[$company['companyCode']]}}
		            	</th>
		            @endforeach
		            <th></th>
		        </tr>
			@endif
            <tr>
                @if($fourthLevel)
	            	<th colspan="5"></th>
	            @elseif($thirdLevel)
	            	<th colspan="4"></th>
	            @elseif($secondLevel)
	            	<th colspan="3"></th>
	            @elseif($firstLevel)
	            	<th colspan="2"></th>
	            @else
	            	<th></th>
	            @endif
	            @foreach ($companyHeaderData as $company)
	            	<th style="text-align: center;" colspan="{{sizeof($columnHeader)}}">
	            		@if($columnTemplateID == 1)
	            			{{$company['companyCode']}}
	            		@else 
	            			{{$serviceLineDescriptions[$company['companyCode']]}}
	            		@endif
	            	</th>
	            @endforeach
	            @if($columnTemplateID == 2)
	            	<th></th>
        		@endif
            </tr>
            <tr>
                @if($fourthLevel)
	            	<th colspan="5">Description</th>
	            @elseif($thirdLevel)
	            	<th colspan="4">Description</th>
	            @elseif($secondLevel)
	            	<th colspan="3">Description</th>
	            @elseif($firstLevel)
	            	<th colspan="2">Description</th>
	            @else
	            	<th>Description</th>
	            @endif
	            @foreach ($companyHeaderData as $company)
	            	 @foreach ($columnHeader as $column)
		            	<th>{{$column['description']}}</th>
		            @endforeach
	            @endforeach
	            @if($columnTemplateID == 2)
	            	<th>Total</th>
        		@endif
            </tr>
        </thead>
        <tbody>
        	@foreach ($reportData as $header)
		        @if($header['hideHeader'] == 0)
		        <tr>
		            @if($header['itemType'] == 1 || $header['itemType'] == 4)
		            <td>
		                <strong>{{$header['detDescription']}}</strong>
		            </td>
		            @if($firstLevel)
		            <td></td>
		            @endif
		            @if($secondLevel)
		            <td></td>
		            @endif
		            @if($thirdLevel)
		            <td></td>
		            @endif
		            @if($fourthLevel)
		            <td></td>
		            @endif
		            @foreach ($columns as $column)
		            <td></td>
		            @endforeach
		            @endif
		            @if($header['itemType'] == 3)
		            <td>
		                <strong>{{$header['detDescription']}}</strong>
		            </td>
		            @if($firstLevel)
		            <td></td>
		            @endif
		            @if($secondLevel)
		            <td></td>
		            @endif
		            @if($thirdLevel)
		            <td></td>
		            @endif
		            @if($fourthLevel)
		            <td></td>
		            @endif
		            @endif
	            	@foreach ($companyHeaderData as $company)
			            @foreach ($columns as $column)
				            @if($header['itemType'] == 3)
				            <td style="font-weight: bold;">
					                @if(isset($header['columnData'][$company['companyCode']][$column]))
					                	{{round($header['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
					                @else
					                	0
					                @endif
				            </td>
				            @endif
			            @endforeach
	            	@endforeach
	            	@if($columnTemplateID == 2 && $header['itemType'] == 3)
		            	<td>{{round(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $header), $decimalPlaces)}}</td>
	        		@endif
		        </tr>
		        @endif
		        @if(isset($header['detail']))
		        @foreach ($header['detail'] as $data)
			        <tr>
			            @if($data['isFinalLevel'] == 1)
				            <td></td>
				            @if($data['itemType'] == 3)
				            <td style="font-weight: bold;">
				                {{$data['detDescription']}}
				            </td>
				            @else
				            <td>
				                {{$data['detDescription']}}
				            </td>
				            @endif
				            @if($secondLevel)
				            <td></td>
				            @endif
				            @if($thirdLevel)
				            <td></td>
				            @endif
				            @if($fourthLevel)
				            <td></td>
				            @endif
				            @foreach ($companyHeaderData as $company)
					            @foreach ($columns as $column)
					            @if($data['itemType'] == 3)
					            <td style="font-weight: bold;">
					                @if(isset($data['columnData'][$company['companyCode']][$column]))
					                {{round($data['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
					                @else
					                0
					                @endif
					            </td>
					            @else
					            <td>
					                @if(isset($data['columnData'][$company['companyCode']][$column]))
					                {{round($data['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
					                @else
					                0
					                @endif
					            </td>
					            @endif
					            @endforeach
				            @endforeach
				            @if($columnTemplateID == 2)
				            	<td>{{round(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $data), $decimalPlaces)}}</td>
			        		@endif
			            @endif
			            @if($data['isFinalLevel'] == 0)
			            <td></td>
			            <td>
			                {{$data['detDescription']}}
			            </td>
			            @if($secondLevel)
			            <td></td>
			            @endif
			            @if($thirdLevel)
			            <td></td>
			            @endif
			            @if($fourthLevel)
			            <td></td>
			            @endif
			            @foreach ($columns as $column)
			            <td></td>
			            @endforeach
			            @if($columnTemplateID == 2)
			            	<td></td>
		        		@endif
			            @endif
			        </tr>
			        @if($data['isFinalLevel'] == 1)
					@if($data['glCodes'] != null)
					@foreach ($data['glCodes'] as $data2)
			        @if($data['expanded'])
			        <tr>
			            <td></td>
			            <td style="padding-left: 10px">
			                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$data2['glCode']}} - {{$data2['glDescription']}}
			            </td>
			            @if($secondLevel)
			            <td></td>
			            @endif
			            @if($thirdLevel)
			            <td></td>
			            @endif
			            @if($fourthLevel)
			            <td></td>
			            @endif
			             @foreach ($companyHeaderData as $company)
			            @foreach ($columns as $column)
			            <td>
			                @if(isset($data2['columnData'][$company['companyCode']][$column]))
			                {{round($data2['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
			                @else
			                0
			                @endif
			            </td>
			            @endforeach
			            @endforeach
			            @if($columnTemplateID == 2)
			            	<td>{{round(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $data2), $decimalPlaces)}}</td>
		        		@endif
			        </tr>
			        @endif
			        @endforeach
			        @endif
			        @endif
			        @if(isset($data['detail']))
			        @foreach ($data['detail'] as $dataSubTwo)
			        <tr>
			            @if($dataSubTwo['isFinalLevel'] == 1)
			            <td></td>
			            <td></td>
			            @if($dataSubTwo['itemType'] == 3)
			            <td style="font-weight: bold;">
			                {{$dataSubTwo['detDescription']}}
			            </td>
			            @else
			            <td>
			                {{$dataSubTwo['detDescription']}}
			            </td>
			            @endif
			            @if($thirdLevel)
			            <td></td>
			            @endif
			            @if($fourthLevel)
			            <td></td>
			            @endif
			             @foreach ($companyHeaderData as $company)
			            @foreach ($columns as $column)
			            @if($dataSubTwo['itemType'] == 3)
			            <td style="font-weight: bold;">
			                @if(isset($dataSubTwo['columnData'][$company['companyCode']][$column]))
			                {{round($dataSubTwo['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
			                @else
			                0
			                @endif
			            </td>
			            @else
			            <td>
			                @if(isset($dataSubTwo['columnData'][$company['companyCode']][$column]))
			                {{round($dataSubTwo['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
			                @else
			                0
			                @endif
			            </td>
			            @endif
			            @endforeach
			            @endforeach
			            @if($columnTemplateID == 2)
			            	<td>{{round(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $dataSubTwo), $decimalPlaces)}}</td>
		        		@endif
			            @endif
			            @if($dataSubTwo['isFinalLevel'] == 0)
			            <td></td>
			            <td></td>
			            <td>
			                {{$dataSubTwo['detDescription']}}
			            </td>
			            @if($thirdLevel)
			            <td></td>
			            @endif
			            @if($fourthLevel)
			            <td></td>
			            @endif
			            @foreach ($columns as $column)
			            <td></td>
			            @endforeach
			            @if($columnTemplateID == 2)
			            	<td></td>
		        		@endif
			            @endif
			        </tr>
			        @if($dataSubTwo['isFinalLevel'] == 1)
			        @foreach ($dataSubTwo['glCodes'] as $data23)
			        @if($dataSubTwo['expanded'])
			        <tr>
			            <td></td>
			            <td></td>
			            <td style="padding-left: 10px">
			                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$data23['glCode']}} - {{$data23['glDescription']}}
			            </td>
			             @if($thirdLevel)
			            <td></td>
			            @endif
			            @if($fourthLevel)
			            <td></td>
			            @endif
			             @foreach ($companyHeaderData as $company)
			            @foreach ($columns as $column)
			            <td>
			                @if(isset($data23['columnData'][$company['companyCode']][$column]))
			                {{round($data23['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
			                @else
			                0
			                @endif
			            </td>
			            @endforeach
			            @endforeach
			            @if($columnTemplateID == 2)
			            	<td>{{round(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $data23), $decimalPlaces)}}</td>
		        		@endif
			        </tr>
			        @endif
			        @endforeach
			        @endif
			        @if(isset($dataSubTwo['detail']))
			        @foreach ($dataSubTwo['detail'] as $dataSubThree)
			        <tr>
			            @if($dataSubThree['isFinalLevel'] == 1)
			            <td></td>
			            <td></td>
			            <td></td>
			            @if($dataSubThree['itemType'] == 3)
			            <td style="font-weight: bold;">
			                {{$dataSubThree['detDescription']}}
			            </td>
			            @else
			            <td>
			                {{$dataSubThree['detDescription']}}
			            </td>
			            @endif
			            @if($fourthLevel)
			            <td></td>
			            @endif
			             @foreach ($companyHeaderData as $company)
			            @foreach ($columns as $column)
			            @if($dataSubThree['itemType'] == 3)
			            <td style="font-weight: bold;">
			                @if(isset($dataSubThree['columnData'][$company['companyCode']][$column]))
			                {{round($dataSubThree['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
			                @else
			                0
			                @endif
			            </td>
			            @else
			            <td>
			                @if(isset($dataSubThree['columnData'][$company['companyCode']][$column]))
			                {{round($dataSubThree['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
			                @else
			                0
			                @endif
			            </td>
			            @endif
			            @endforeach
			            @endforeach
			            @if($columnTemplateID == 2)
			            	<td>{{round(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $dataSubThree), $decimalPlaces)}}</td>
		        		@endif
			            @endif
			            @if($dataSubThree['isFinalLevel'] == 0)
			            <td></td>
			            <td></td>
			            <td></td>
			            <td>
			                {{$dataSubThree['detDescription']}}
			            </td>
			            @if($fourthLevel)
			            <td></td>
			            @endif
			            @foreach ($columns as $column)
			            <td></td>
			            @endforeach
			            @if($columnTemplateID == 2)
			            	<td></td>
		        		@endif
			            @endif
			        </tr>
			        @if($dataSubThree['isFinalLevel'] == 1)
			        @foreach ($dataSubThree['glCodes'] as $data24)
			        @if($dataSubThree['expanded'])
			        <tr>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td style="padding-left: 10px">
			                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$data24['glCode']}} - {{$data24['glDescription']}}
			            </td>
			            @if($fourthLevel)
			            <td></td>
			            @endif
			             @foreach ($companyHeaderData as $company)
			            @foreach ($columns as $column)
			            <td>
			                @if(isset($data24['columnData'][$company['companyCode']][$column]))
			                {{round($data24['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
			                @else
			                0
			                @endif
			            </td>
			            @endforeach
			            @endforeach
			            @if($columnTemplateID == 2)
			            	<td>{{round(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $data24), $decimalPlaces)}}</td>
		        		@endif
			        </tr>
			        @endif
			        @endforeach
			        @endif
			        @if(isset($dataSubThree['detail']))
			        @foreach ($dataSubThree['detail'] as $dataSubFour)
			        <tr>
			            @if($dataSubFour['isFinalLevel'] == 1)
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>
			            @if($dataSubFour['itemType'] == 3)
			            <td style="font-weight: bold;">
			                {{$dataSubFour['detDescription']}}
			            </td>
			            @else
			            <td>
			                {{$dataSubFour['detDescription']}}
			            </td>
			            @endif
			             @foreach ($companyHeaderData as $company)
			            @foreach ($columns as $column)
			            @if($dataSubFour['itemType'] == 3)
			            <td style="font-weight: bold;">
			                @if(isset($dataSubFour['columnData'][$company['companyCode']][$column]))
			                {{round($dataSubFour['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
			                @else
			                0
			                @endif
			            </td>
			            @else
			            <td>
			                @if(isset($dataSubFour['columnData'][$company['companyCode']][$column]))
			                {{round($dataSubFour['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
			                @else
			                0
			                @endif
			            </td>
			            @endif
			            @endforeach
			            @endforeach
			            @if($columnTemplateID == 2)
			            	<td>{{round(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $dataSubFour), $decimalPlaces)}}</td>
		        		@endif
			            @endif
			            @if($dataSubFour['isFinalLevel'] == 0)
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td>
			                {{$dataSubFour['detDescription']}}
			            </td>
			            @foreach ($columns as $column)
			            <td></td>
			            @endforeach
			            @if($columnTemplateID == 2)
			            	<td></td>
		        		@endif
			            @endif
			        </tr>
			        @if($dataSubFour['isFinalLevel'] == 1)
			        @foreach ($dataSubFour['glCodes'] as $data25)
			        @if($dataSubFour['expanded'])
			        <tr>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td style="padding-left: 10px">
			                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$data25['glCode']}} - {{$data25['glDescription']}}
			            </td>
			            @foreach ($companyHeaderData as $company)
			            @foreach ($columns as $column)
			            <td>
			                @if(isset($data25['columnData'][$company['companyCode']][$column]))
			                {{round($data25['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
			                @else
			                0
			                @endif
			            </td>
			            @endforeach
			            @endforeach
			            @if($columnTemplateID == 2)
			            	<td>{{round(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $data25), $decimalPlaces)}}</td>
		        		@endif
			        </tr>
			        @endif
			        @endforeach
			        @endif
			        @endforeach
			        @endif
			        @endforeach
			        @endif
			        @endforeach
			        @endif
		        @endforeach
		        @endif
		        @if($accountType == 3 && $loop->last)
			        <tr>
			            <td><strong>Opening Balance</strong></td>
			            @if($firstLevel)
			            <td></td>
			            @endif
			            @if($secondLevel)
			            <td></td>
			            @endif
			            @if($thirdLevel)
			            <td></td>
			            @endif
			            @if($fourthLevel)
			            <td></td>
			            @endif
			            @foreach ($companyHeaderData as $company)
			            	{{$x=0}}
			            	@foreach ($columns as $column)
				            <td style="font-weight: bold;">
				                {{round($openingBalance[$company['companyCode']][$x], $decimalPlaces)}}
				            </td>
				            {{ $x++ }}
				            @endforeach
			            @endforeach
			            @if($columnTemplateID == 2)
			            	<td>{{round(\Helper::rowTotalOfReportTemplateBalance($companyHeaderData, $columns, $openingBalance), $decimalPlaces)}}</td>
		        		@endif
			        </tr>
			        <tr>
			            <td><strong>Closing Balance</strong></td>
			            @if($firstLevel)
			            <td></td>
			            @endif
			            @if($secondLevel)
			            <td></td>
			            @endif
			            @if($thirdLevel)
			            <td></td>
			            @endif
			            @if($fourthLevel)
			            <td></td>
			            @endif
			            @foreach ($companyHeaderData as $company)
			            	{{$j=0}}
			            	@foreach ($columns as $column)
					            <td style="font-weight: bold;">
					                {{round($closingBalance[$company['companyCode']][$j], $decimalPlaces)}}
					            </td>
					            {{ $j++ }}
				            @endforeach
			            @endforeach
			            @if($columnTemplateID == 2)
			            	<td>{{round(\Helper::rowTotalOfReportTemplateBalance($companyHeaderData, $columns, $closingBalance), $decimalPlaces)}}</td>
		        		@endif
			        </tr>
		        @endif
		        @if($accountType == 2 && $loop->last && $isUncategorize)
		        <tr>
		            <td><strong>Uncategorized</strong></td>
		            @if($firstLevel)
		            <td></td>
		            @endif
		            @if($secondLevel)
		            <td></td>
		            @endif
		            @if($thirdLevel)
		            <td></td>
		            @endif
		            @if($fourthLevel)
		            <td></td>
		            @endif
		            @foreach ($companyHeaderData as $company)
			            @foreach ($columns as $column)
			            <td style="font-weight: bold;">
			                @if(isset($uncategorize['columnData'][$company['companyCode']][$column]))
			                	{{round($uncategorize['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
			                @else
			                	0
			                @endif
			            </td>
			            @endforeach
		            @endforeach
		            @if($columnTemplateID == 2)
		            	<td>{{round(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $uncategorize), $decimalPlaces)}}</td>
	        		@endif
		        </tr>
		        @endif
		        @if($accountType == 2 && $loop->last)
		        <tr>
		            <td><strong>Grand Total</strong></td>
		            @if($firstLevel)
		            <td></td>
		            @endif
		            @if($secondLevel)
		            <td></td>
		            @endif
		            @if($thirdLevel)
		            <td></td>
		            @endif
		            @if($fourthLevel)
		            <td></td>
		            @endif
		            @foreach ($companyHeaderData as $company)
			            @foreach ($columns as $column)
			            <td style="font-weight: bold;">
		                	{{round(\Helper::grandTotalValueOfReportTemplate($company['companyCode'], $column, $grandTotalUncatArr), $decimalPlaces)}}
			            </td>
			            @endforeach
		            @endforeach
		             @if($columnTemplateID == 2)
		            	<td>{{round(\Helper::rowTotalOfReportTemplateGrandTotal($companyHeaderData, $columns, $grandTotalUncatArr), $decimalPlaces)}}</td>
	        		@endif
		        </tr>
		        @endif
		        @if($accountType == 1 && $loop->last)
		        <tr>
		            <td><strong>Uncategorized</strong></td>
		            @if($firstLevel)
		            <td></td>
		            @endif
		            @if($secondLevel)
		            <td></td>
		            @endif
		            @if($thirdLevel)
		            <td></td>
		            @endif
		            @if($fourthLevel)
		            <td></td>
		            @endif
		            @foreach ($companyHeaderData as $company)
		            @foreach ($columns as $column)
		            <td style="font-weight: bold;">
		                @if(isset($uncategorize['columnData'][$company['companyCode']][$column]))
		                {{round($uncategorize['columnData'][$company['companyCode']][$column], $decimalPlaces)}}
		                @else
		                0
		                @endif
		            </td>
		            @endforeach
		            @endforeach
		            @if($columnTemplateID == 2)
		            	<td>{{round(\Helper::rowTotalOfReportTemplate($companyHeaderData, $columns, $uncategorize), $decimalPlaces)}}</td>
	        		@endif
		        </tr>
		        @endif
	        @endforeach
            @if(sizeof($reportData) == 0)
		        <tr>
		            <td colspan="{{sizeof($columnHeader) + sizeof($companyHeaderData)}}">No Records Found</td>
		        </tr>
	        @endif
        </tbody>
    </table>
</html>
