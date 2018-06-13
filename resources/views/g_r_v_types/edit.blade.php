@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            G R V Types
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($gRVTypes, ['route' => ['gRVTypes.update', $gRVTypes->id], 'method' => 'patch']) !!}

                        @include('g_r_v_types.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection