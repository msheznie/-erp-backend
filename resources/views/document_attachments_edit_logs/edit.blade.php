@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Document Attachments Edit Log
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($documentAttachmentsEditLog, ['route' => ['documentAttachmentsEditLogs.update', $documentAttachmentsEditLog->id], 'method' => 'patch']) !!}

                        @include('document_attachments_edit_logs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection