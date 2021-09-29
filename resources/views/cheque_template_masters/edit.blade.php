@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Cheque Template Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($chequeTemplateMaster, ['route' => ['chequeTemplateMasters.update', $chequeTemplateMaster->id], 'method' => 'patch']) !!}

                        @include('cheque_template_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection