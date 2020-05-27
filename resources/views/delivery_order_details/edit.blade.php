@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Delivery Order Detail
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($deliveryOrderDetail, ['route' => ['deliveryOrderDetails.update', $deliveryOrderDetail->id], 'method' => 'patch']) !!}

                        @include('delivery_order_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection