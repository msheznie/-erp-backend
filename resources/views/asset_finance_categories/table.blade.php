<table class="table table-responsive" id="assetFinanceCategories-table">
    <thead>
        <tr>
            <th>Financecatdescription</th>
        <th>Costglcode</th>
        <th>Accdepglcode</th>
        <th>Depglcode</th>
        <th>Dispoglcode</th>
        <th>Isactive</th>
        <th>Sortorder</th>
        <th>Createdpcid</th>
        <th>Createdusergroup</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($assetFinanceCategories as $assetFinanceCategory)
        <tr>
            <td>{!! $assetFinanceCategory->financeCatDescription !!}</td>
            <td>{!! $assetFinanceCategory->COSTGLCODE !!}</td>
            <td>{!! $assetFinanceCategory->ACCDEPGLCODE !!}</td>
            <td>{!! $assetFinanceCategory->DEPGLCODE !!}</td>
            <td>{!! $assetFinanceCategory->DISPOGLCODE !!}</td>
            <td>{!! $assetFinanceCategory->isActive !!}</td>
            <td>{!! $assetFinanceCategory->sortOrder !!}</td>
            <td>{!! $assetFinanceCategory->createdPcID !!}</td>
            <td>{!! $assetFinanceCategory->createdUserGroup !!}</td>
            <td>{!! $assetFinanceCategory->createdUserID !!}</td>
            <td>{!! $assetFinanceCategory->createdDateTime !!}</td>
            <td>{!! $assetFinanceCategory->modifiedPc !!}</td>
            <td>{!! $assetFinanceCategory->modifiedUser !!}</td>
            <td>{!! $assetFinanceCategory->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['assetFinanceCategories.destroy', $assetFinanceCategory->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('assetFinanceCategories.show', [$assetFinanceCategory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('assetFinanceCategories.edit', [$assetFinanceCategory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>