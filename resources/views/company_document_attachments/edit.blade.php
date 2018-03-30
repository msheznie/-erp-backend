@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Company Document Attachment
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($companyDocumentAttachment, ['route' => ['companyDocumentAttachments.update', $companyDocumentAttachment->id], 'method' => 'patch']) !!}

                        @include('company_document_attachments.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection