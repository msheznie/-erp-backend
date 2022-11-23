<div class="table-responsive">
    <table class="table" id="thirdPartyIntegrationKeys-table">
        <thead>
            <tr>
                <th>Company Id</th>
        <th>Third Party System Id</th>
        <th>Api Key</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($thirdPartyIntegrationKeys as $thirdPartyIntegrationKeys)
            <tr>
                <td>{{ $thirdPartyIntegrationKeys->company_id }}</td>
            <td>{{ $thirdPartyIntegrationKeys->third_party_system_id }}</td>
            <td>{{ $thirdPartyIntegrationKeys->api_key }}</td>
                <td>
                    {!! Form::open(['route' => ['thirdPartyIntegrationKeys.destroy', $thirdPartyIntegrationKeys->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('thirdPartyIntegrationKeys.show', [$thirdPartyIntegrationKeys->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('thirdPartyIntegrationKeys.edit', [$thirdPartyIntegrationKeys->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
