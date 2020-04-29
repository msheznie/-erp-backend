<div class="table-responsive">
    <table class="table" id="fcmTokens-table">
        <thead>
            <tr>
                <th>Userid</th>
        <th>Fcm Token</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($fcmTokens as $fcmToken)
            <tr>
                <td>{{ $fcmToken->userID }}</td>
            <td>{{ $fcmToken->fcm_token }}</td>
                <td>
                    {!! Form::open(['route' => ['fcmTokens.destroy', $fcmToken->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('fcmTokens.show', [$fcmToken->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('fcmTokens.edit', [$fcmToken->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
