@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Templates G L Code
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($templatesGLCode, ['route' => ['templatesGLCodes.update', $templatesGLCode->id], 'method' => 'patch']) !!}

                        @include('templates_g_l_codes.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection