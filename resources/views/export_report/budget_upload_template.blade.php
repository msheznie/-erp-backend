<html>
	<table>
	    <thead>
	    	<tr>
			<th>{{ trans('custom.main_category') }}</th>
			<th>{{ trans('custom.sub_category') }}</th>
			<th>{{ trans('custom.account_code') }}</th>
			<th>{{ trans('custom.account_description') }}</th>
	 			@foreach ($monthArray as $month)
			    	<th>{{$month['monthName']}} - {{$month['year']}}</th>
		    	@endforeach
	    	</tr>
	    </thead>
	 	<tbody>
	 		@foreach ($reportData as $rowLevel)
		 		@foreach ($rowLevel->gllink as $row)
			 		<tr>
			 			<td>
			 				{{\App\helper\Helper::headerCategoryOfReportTemplate($row->templateDetailID)['description'] }}
			 			</td>
			 			<td>
		 					{{$rowLevel->description}}
			 			</td>
			 			<td>
		 					{{$row->glCode}}
			 			</td>
			 			<td>
			 				{{$row->glDescription}}
			 			</td>
			 			<td>0</td>
			 			<td>0</td>
			 			<td>0</td>
			 			<td>0</td>
			 			<td>0</td>
			 			<td>0</td>
			 			<td>0</td>
			 			<td>0</td>
			 			<td>0</td>
			 			<td>0</td>
			 			<td>0</td>
			 			<td>0</td>
			 		</tr>
		 		@endforeach
	 		@endforeach
	 	</tbody>
 	</table>
</html>
