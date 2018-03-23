<table class="table table-responsive" id="financeItemCategoryMasters-table">
    <thead>
        <tr>
            <th>Categorydescription</th>
        <th>Itemcodedef</th>
        <th>Numberofdigits</th>
        <th>Lastserialorder</th>
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
    @foreach($financeItemCategoryMasters as $financeItemCategoryMaster)
        <tr>
            <td>{!! $financeItemCategoryMaster->categoryDescription !!}</td>
            <td>{!! $financeItemCategoryMaster->itemCodeDef !!}</td>
            <td>{!! $financeItemCategoryMaster->numberOfDigits !!}</td>
            <td>{!! $financeItemCategoryMaster->lastSerialOrder !!}</td>
            <td>{!! $financeItemCategoryMaster->timeStamp !!}</td>
            <td>{!! $financeItemCategoryMaster->createdUserGroup !!}</td>
            <td>{!! $financeItemCategoryMaster->createdPcID !!}</td>
            <td>{!! $financeItemCategoryMaster->createdUserID !!}</td>
            <td>{!! $financeItemCategoryMaster->modifiedPc !!}</td>
            <td>{!! $financeItemCategoryMaster->modifiedUser !!}</td>
            <td>{!! $financeItemCategoryMaster->createdDateTime !!}</td>
            <td>
                {!! Form::open(['route' => ['financeItemCategoryMasters.destroy', $financeItemCategoryMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('financeItemCategoryMasters.show', [$financeItemCategoryMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('financeItemCategoryMasters.edit', [$financeItemCategoryMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>