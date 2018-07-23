@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Stock Receive
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($stockReceive, ['route' => ['stockReceives.update', $stockReceive->id], 'method' => 'patch']) !!}

                        @include('stock_receives.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection