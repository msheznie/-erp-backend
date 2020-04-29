<!-- Userid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('userID', 'Userid:') !!}
    {!! Form::number('userID', null, ['class' => 'form-control']) !!}
</div>

<!-- Fcm Token Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fcm_token', 'Fcm Token:') !!}
    {!! Form::text('fcm_token', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('fcmTokens.index') }}" class="btn btn-default">Cancel</a>
</div>
