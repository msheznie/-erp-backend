@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Stock Receive Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($stockReceiveDetails, ['route' => ['stockReceiveDetails.update', $stockReceiveDetails->id], 'method' => 'patch']) !!}

                        @include('stock_receive_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection