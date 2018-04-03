@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            G R V Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($gRVMaster, ['route' => ['gRVMasters.update', $gRVMaster->id], 'method' => 'patch']) !!}

                        @include('g_r_v_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection