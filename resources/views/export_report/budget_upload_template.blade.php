<html>
	<table>
	    <thead>
	    	<tr>
		    	<th>Main Category</th>
		    	<th>Sub Category</th>
		    	<th>Account Code</th>
		    	<th>Account Description</th>
		    	<th>January</th>
		    	<th>February</th>
		    	<th>March</th>
		    	<th>April</th>
		    	<th>May</th>
		    	<th>June</th>
		    	<th>July</th>
		    	<th>August</th>
		    	<th>September</th>
		    	<th>October</th>
		    	<th>November</th>
		    	<th>December</th>
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
			 			<td></td>
			 			<td></td>
			 			<td></td>
			 			<td></td>
			 			<td></td>
			 			<td></td>
			 			<td></td>
			 			<td></td>
			 			<td></td>
			 			<td></td>
			 			<td></td>
			 			<td></td>
			 		</tr>
		 		@endforeach
	 		@endforeach
	 	</tbody>
 	</table>
</html>