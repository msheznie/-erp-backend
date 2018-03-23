<table class="table table-responsive" id="financeItemcategorySubs-table">
    <thead>
        <tr>
            <th>Categorydescription</th>
        <th>Itemcategoryid</th>
        <th>Financeglcodebbssystemid</th>
        <th>Financeglcodebbs</th>
        <th>Financeglcodeplsystemid</th>
        <th>Financeglcodepl</th>
        <th>Includeplforgrvyn</th>
        <th>Createddatetime</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($financeItemcategorySubs as $financeItemcategorySub)
        <tr>
            <td>{!! $financeItemcategorySub->categoryDescription !!}</td>
            <td>{!! $financeItemcategorySub->itemCategoryID !!}</td>
            <td>{!! $financeItemcategorySub->financeGLcodebBSSystemID !!}</td>
            <td>{!! $financeItemcategorySub->financeGLcodebBS !!}</td>
            <td>{!! $financeItemcategorySub->financeGLcodePLSystemID !!}</td>
            <td>{!! $financeItemcategorySub->financeGLcodePL !!}</td>
            <td>{!! $financeItemcategorySub->includePLForGRVYN !!}</td>
            <td>{!! $financeItemcategorySub->createdDateTime !!}</td>
            <td>{!! $financeItemcategorySub->createdUserGroup !!}</td>
            <td>{!! $financeItemcategorySub->createdPcID !!}</td>
            <td>{!! $financeItemcategorySub->createdUserID !!}</td>
            <td>{!! $financeItemcategorySub->modifiedPc !!}</td>
            <td>{!! $financeItemcategorySub->modifiedUser !!}</td>
            <td>{!! $financeItemcategorySub->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['financeItemcategorySubs.destroy', $financeItemcategorySub->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('financeItemcategorySubs.show', [$financeItemcategorySub->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('financeItemcategorySubs.edit', [$financeItemcategorySub->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>