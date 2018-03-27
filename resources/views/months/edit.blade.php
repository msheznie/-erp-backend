@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Months
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($months, ['route' => ['months.update', $months->id], 'method' => 'patch']) !!}

                        @include('months.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection