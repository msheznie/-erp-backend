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
	 		@foreach ($reportData as $row)
		 		<tr>
		 			<td>
		 				{{\App\helper\Helper::headerCategoryOfReportTemplate($row->templateDetailID) }}
		 			</td>
		 			<td>
		 				@if(isset($row->template_category->description))
		 					{{$row->template_category->description}}
		 				@endif
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
	 	</tbody>
 	</table>
</html>