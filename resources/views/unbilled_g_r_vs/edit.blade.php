@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Unbilled G R V
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($unbilledGRV, ['route' => ['unbilledGRVs.update', $unbilledGRV->id], 'method' => 'patch']) !!}

                        @include('unbilled_g_r_vs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection