@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Report Template
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($reportTemplate, ['route' => ['reportTemplates.update', $reportTemplate->id], 'method' => 'patch']) !!}

                        @include('report_templates.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection