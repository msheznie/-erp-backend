@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Unit Conversion
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($unitConversion, ['route' => ['unitConversions.update', $unitConversion->id], 'method' => 'patch']) !!}

                        @include('unit_conversions.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection