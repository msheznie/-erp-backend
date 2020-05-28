<div class="table-responsive">
    <table class="table" id="tenants-table">
        <thead>
            <tr>
                <th>Name</th>
        <th>Sub Domain</th>
        <th>Database</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($tenants as $tenant)
            <tr>
                <td>{{ $tenant->name }}</td>
            <td>{{ $tenant->sub_domain }}</td>
            <td>{{ $tenant->database }}</td>
                <td>
                    {!! Form::open(['route' => ['tenants.destroy', $tenant->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('tenants.show', [$tenant->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('tenants.edit', [$tenant->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
