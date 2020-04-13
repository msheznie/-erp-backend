@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Report Column Template Detail
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($reportColumnTemplateDetail, ['route' => ['reportColumnTemplateDetails.update', $reportColumnTemplateDetail->id], 'method' => 'patch']) !!}

                        @include('report_column_template_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection