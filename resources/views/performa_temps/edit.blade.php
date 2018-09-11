@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Performa Temp
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($performaTemp, ['route' => ['performaTemps.update', $performaTemp->id], 'method' => 'patch']) !!}

                        @include('performa_temps.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection