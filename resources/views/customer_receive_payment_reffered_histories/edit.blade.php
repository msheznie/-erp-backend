@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Customer Receive Payment Reffered History
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($customerReceivePaymentRefferedHistory, ['route' => ['customerReceivePaymentRefferedHistories.update', $customerReceivePaymentRefferedHistory->id], 'method' => 'patch']) !!}

                        @include('customer_receive_payment_reffered_histories.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection