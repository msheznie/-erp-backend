@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Document Email Notification Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($documentEmailNotificationMaster, ['route' => ['documentEmailNotificationMasters.update', $documentEmailNotificationMaster->id], 'method' => 'patch']) !!}

                        @include('document_email_notification_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection