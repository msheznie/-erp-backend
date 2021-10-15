<div class="table-responsive">
    <table class="table" id="chequeTemplateBanks-table">
        <thead>
            <tr>
                <th>Cheque Template Master Id</th>
        <th>Bank Id</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($chequeTemplateBanks as $chequeTemplateBank)
            <tr>
                <td>{{ $chequeTemplateBank->cheque_template_master_id }}</td>
            <td>{{ $chequeTemplateBank->bank_id }}</td>
                <td>
                    {!! Form::open(['route' => ['chequeTemplateBanks.destroy', $chequeTemplateBank->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('chequeTemplateBanks.show', [$chequeTemplateBank->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('chequeTemplateBanks.edit', [$chequeTemplateBank->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
