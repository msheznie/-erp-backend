@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Year
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($year, ['route' => ['years.update', $year->id], 'method' => 'patch']) !!}

                        @include('years.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection