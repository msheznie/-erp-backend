@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Service Line
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($serviceLine, ['route' => ['serviceLines.update', $serviceLine->id], 'method' => 'patch']) !!}

                        @include('service_lines.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection