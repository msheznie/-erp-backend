<table class="table table-responsive" id="supplierCategorySubs-table">
    <thead>
        <tr>
            <th>Supmastercategoryid</th>
        <th>Subcategorycode</th>
        <th>Categorydescription</th>
        <th>Timestamp</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($supplierCategorySubs as $supplierCategorySub)
        <tr>
            <td>{!! $supplierCategorySub->supMasterCategoryID !!}</td>
            <td>{!! $supplierCategorySub->subCategoryCode !!}</td>
            <td>{!! $supplierCategorySub->categoryDescription !!}</td>
            <td>{!! $supplierCategorySub->timeStamp !!}</td>
            <td>{!! $supplierCategorySub->createdUserGroup !!}</td>
            <td>{!! $supplierCategorySub->createdPcID !!}</td>
            <td>{!! $supplierCategorySub->createdUserID !!}</td>
            <td>{!! $supplierCategorySub->modifiedPc !!}</td>
            <td>{!! $supplierCategorySub->modifiedUser !!}</td>
            <td>{!! $supplierCategorySub->createdDateTime !!}</td>
            <td>
                {!! Form::open(['route' => ['supplierCategorySubs.destroy', $supplierCategorySub->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('supplierCategorySubs.show', [$supplierCategorySub->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('supplierCategorySubs.edit', [$supplierCategorySub->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>