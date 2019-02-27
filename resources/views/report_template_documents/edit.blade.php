@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Report Template Document
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($reportTemplateDocument, ['route' => ['reportTemplateDocuments.update', $reportTemplateDocument->id], 'method' => 'patch']) !!}

                        @include('report_template_documents.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection