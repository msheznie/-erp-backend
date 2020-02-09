@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Erp Document Template
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($erpDocumentTemplate, ['route' => ['erpDocumentTemplates.update', $erpDocumentTemplate->id], 'method' => 'patch']) !!}

                        @include('erp_document_templates.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection