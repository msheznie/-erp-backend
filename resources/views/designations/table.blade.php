<table class="table table-responsive" id="designations-table">
    <thead>
        <tr>
            <th>Designation</th>
        <th>Designation O</th>
        <th>Localname</th>
        <th>Jobcode</th>
        <th>Jobdecipline</th>
        <th>Businessfunction</th>
        <th>Appraisaltemplateid</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($designations as $designation)
        <tr>
            <td>{!! $designation->designation !!}</td>
            <td>{!! $designation->designation_O !!}</td>
            <td>{!! $designation->localName !!}</td>
            <td>{!! $designation->jobCode !!}</td>
            <td>{!! $designation->jobDecipline !!}</td>
            <td>{!! $designation->businessFunction !!}</td>
            <td>{!! $designation->appraisalTemplateID !!}</td>
            <td>{!! $designation->createdPCid !!}</td>
            <td>{!! $designation->createdUserID !!}</td>
            <td>{!! $designation->modifiedUser !!}</td>
            <td>{!! $designation->modifiedPc !!}</td>
            <td>{!! $designation->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['designations.destroy', $designation->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('designations.show', [$designation->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('designations.edit', [$designation->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>