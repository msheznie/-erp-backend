<div class="table-responsive">
    <table class="table" id="erpPrintTemplateMasters-table">
        <thead>
            <tr>
                <th>Printtemplatename</th>
        <th>Printtemplateblade</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($erpPrintTemplateMasters as $erpPrintTemplateMaster)
            <tr>
                <td>{!! $erpPrintTemplateMaster->printTemplateName !!}</td>
            <td>{!! $erpPrintTemplateMaster->printTemplateBlade !!}</td>
                <td>
                    {!! Form::open(['route' => ['erpPrintTemplateMasters.destroy', $erpPrintTemplateMaster->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('erpPrintTemplateMasters.show', [$erpPrintTemplateMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('erpPrintTemplateMasters.edit', [$erpPrintTemplateMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
