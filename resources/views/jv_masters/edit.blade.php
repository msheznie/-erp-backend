@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Jv Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($jvMaster, ['route' => ['jvMasters.update', $jvMaster->id], 'method' => 'patch']) !!}

                        @include('jv_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection