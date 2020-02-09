@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Erp Print Template Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($erpPrintTemplateMaster, ['route' => ['erpPrintTemplateMasters.update', $erpPrintTemplateMaster->id], 'method' => 'patch']) !!}

                        @include('erp_print_template_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection