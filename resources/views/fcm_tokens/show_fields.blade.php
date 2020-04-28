<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{{ $fcmToken->id }}</p>
</div>

<!-- Userid Field -->
<div class="form-group">
    {!! Form::label('userID', 'Userid:') !!}
    <p>{{ $fcmToken->userID }}</p>
</div>

<!-- Fcm Token Field -->
<div class="form-group">
    {!! Form::label('fcm_token', 'Fcm Token:') !!}
    <p>{{ $fcmToken->fcm_token }}</p>
</div>

