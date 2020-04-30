@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Fcm Token
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('fcm_tokens.show_fields')
                    <a href="{{ route('fcmTokens.index') }}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
