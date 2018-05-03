<table class="table table-responsive" id="accessTokens-table">
    <thead>
        <tr>
            <th>User Id</th>
        <th>Client Id</th>
        <th>Name</th>
        <th>Scopes</th>
        <th>Revoked</th>
        <th>Expires At</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($accessTokens as $accessTokens)
        <tr>
            <td>{!! $accessTokens->user_id !!}</td>
            <td>{!! $accessTokens->client_id !!}</td>
            <td>{!! $accessTokens->name !!}</td>
            <td>{!! $accessTokens->scopes !!}</td>
            <td>{!! $accessTokens->revoked !!}</td>
            <td>{!! $accessTokens->expires_at !!}</td>
            <td>
                {!! Form::open(['route' => ['accessTokens.destroy', $accessTokens->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('accessTokens.show', [$accessTokens->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('accessTokens.edit', [$accessTokens->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>