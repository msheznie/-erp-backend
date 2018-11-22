@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Cust Receive Payment Det Reffered History
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($custReceivePaymentDetRefferedHistory, ['route' => ['custReceivePaymentDetRefferedHistories.update', $custReceivePaymentDetRefferedHistory->id], 'method' => 'patch']) !!}

                        @include('cust_receive_payment_det_reffered_histories.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection