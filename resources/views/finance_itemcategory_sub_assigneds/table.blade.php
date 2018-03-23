<table class="table table-responsive" id="financeItemcategorySubAssigneds-table">
    <thead>
        <tr>
            <th>Mainitemcategoryid</th>
        <th>Itemcategorysubid</th>
        <th>Categorydescription</th>
        <th>Financeglcodebbssystemid</th>
        <th>Financeglcodebbs</th>
        <th>Financeglcodeplsystemid</th>
        <th>Financeglcodepl</th>
        <th>Includeplforgrvyn</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Isactive</th>
        <th>Isassigned</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($financeItemcategorySubAssigneds as $financeItemcategorySubAssigned)
        <tr>
            <td>{!! $financeItemcategorySubAssigned->mainItemCategoryID !!}</td>
            <td>{!! $financeItemcategorySubAssigned->itemCategorySubID !!}</td>
            <td>{!! $financeItemcategorySubAssigned->categoryDescription !!}</td>
            <td>{!! $financeItemcategorySubAssigned->financeGLcodebBSSystemID !!}</td>
            <td>{!! $financeItemcategorySubAssigned->financeGLcodebBS !!}</td>
            <td>{!! $financeItemcategorySubAssigned->financeGLcodePLSystemID !!}</td>
            <td>{!! $financeItemcategorySubAssigned->financeGLcodePL !!}</td>
            <td>{!! $financeItemcategorySubAssigned->includePLForGRVYN !!}</td>
            <td>{!! $financeItemcategorySubAssigned->companySystemID !!}</td>
            <td>{!! $financeItemcategorySubAssigned->companyID !!}</td>
            <td>{!! $financeItemcategorySubAssigned->isActive !!}</td>
            <td>{!! $financeItemcategorySubAssigned->isAssigned !!}</td>
            <td>{!! $financeItemcategorySubAssigned->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['financeItemcategorySubAssigneds.destroy', $financeItemcategorySubAssigned->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('financeItemcategorySubAssigneds.show', [$financeItemcategorySubAssigned->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('financeItemcategorySubAssigneds.edit', [$financeItemcategorySubAssigned->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>