@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Customer Receive Payment
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($customerReceivePayment, ['route' => ['customerReceivePayments.update', $customerReceivePayment->id], 'method' => 'patch']) !!}

                        @include('customer_receive_payments.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection