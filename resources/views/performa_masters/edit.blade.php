@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Performa Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($performaMaster, ['route' => ['performaMasters.update', $performaMaster->id], 'method' => 'patch']) !!}

                        @include('performa_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection