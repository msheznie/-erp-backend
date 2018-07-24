@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Purchase Order Adv Payment Refferedback
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($purchaseOrderAdvPaymentRefferedback, ['route' => ['purchaseOrderAdvPaymentRefferedbacks.update', $purchaseOrderAdvPaymentRefferedback->id], 'method' => 'patch']) !!}

                        @include('purchase_order_adv_payment_refferedbacks.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection