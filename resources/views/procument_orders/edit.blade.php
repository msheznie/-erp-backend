@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Procument Order
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($procumentOrder, ['route' => ['procumentOrders.update', $procumentOrder->id], 'method' => 'patch']) !!}

                        @include('procument_orders.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection