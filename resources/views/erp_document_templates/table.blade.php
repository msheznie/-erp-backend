<div class="table-responsive">
    <table class="table" id="erpDocumentTemplates-table">
        <thead>
            <tr>
                <th>Documentid</th>
        <th>Companyid</th>
        <th>Printtemplateid</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($erpDocumentTemplates as $erpDocumentTemplate)
            <tr>
                <td>{!! $erpDocumentTemplate->documentID !!}</td>
            <td>{!! $erpDocumentTemplate->companyID !!}</td>
            <td>{!! $erpDocumentTemplate->printTemplateID !!}</td>
                <td>
                    {!! Form::open(['route' => ['erpDocumentTemplates.destroy', $erpDocumentTemplate->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('erpDocumentTemplates.show', [$erpDocumentTemplate->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('erpDocumentTemplates.edit', [$erpDocumentTemplate->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
