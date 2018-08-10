<table class="table table-responsive" id="fieldMasters-table">
    <thead>
        <tr>
            <th>Fieldshortcode</th>
        <th>Fieldname</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
        <th>Companyid</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($fieldMasters as $fieldMaster)
        <tr>
            <td>{!! $fieldMaster->fieldShortCode !!}</td>
            <td>{!! $fieldMaster->fieldName !!}</td>
            <td>{!! $fieldMaster->createdUserGroup !!}</td>
            <td>{!! $fieldMaster->createdPcID !!}</td>
            <td>{!! $fieldMaster->createdUserID !!}</td>
            <td>{!! $fieldMaster->modifiedPc !!}</td>
            <td>{!! $fieldMaster->modifiedUser !!}</td>
            <td>{!! $fieldMaster->createdDateTime !!}</td>
            <td>{!! $fieldMaster->timeStamp !!}</td>
            <td>{!! $fieldMaster->companyId !!}</td>
            <td>
                {!! Form::open(['route' => ['fieldMasters.destroy', $fieldMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('fieldMasters.show', [$fieldMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('fieldMasters.edit', [$fieldMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>