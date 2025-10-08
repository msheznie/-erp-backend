<html>
	<table>
	    <thead>
	    	<tr>
			<th>Main Category</th>
			<th>Sub Category</th>
			<th>Account Code</th>
			<th>Account Description</th>
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
