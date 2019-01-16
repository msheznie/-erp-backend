@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Document Email Notification Detail
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'documentEmailNotificationDetails.store']) !!}

                        @include('document_email_notification_details.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
