<table class="table table-responsive" id="financeItemCategorySubs-table">
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
    @foreach($financeItemCategorySubs as $financeItemCategorySub)
        <tr>
            <td>{!! $financeItemCategorySub->categoryDescription !!}</td>
            <td>{!! $financeItemCategorySub->itemCategoryID !!}</td>
            <td>{!! $financeItemCategorySub->financeGLcodebBSSystemID !!}</td>
            <td>{!! $financeItemCategorySub->financeGLcodebBS !!}</td>
            <td>{!! $financeItemCategorySub->financeGLcodePLSystemID !!}</td>
            <td>{!! $financeItemCategorySub->financeGLcodePL !!}</td>
            <td>{!! $financeItemCategorySub->includePLForGRVYN !!}</td>
            <td>{!! $financeItemCategorySub->createdDateTime !!}</td>
            <td>{!! $financeItemCategorySub->createdUserGroup !!}</td>
            <td>{!! $financeItemCategorySub->createdPcID !!}</td>
            <td>{!! $financeItemCategorySub->createdUserID !!}</td>
            <td>{!! $financeItemCategorySub->modifiedPc !!}</td>
            <td>{!! $financeItemCategorySub->modifiedUser !!}</td>
            <td>{!! $financeItemCategorySub->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['financeItemCategorySubs.destroy', $financeItemCategorySub->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('financeItemCategorySubs.show', [$financeItemCategorySub->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('financeItemCategorySubs.edit', [$financeItemCategorySub->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>