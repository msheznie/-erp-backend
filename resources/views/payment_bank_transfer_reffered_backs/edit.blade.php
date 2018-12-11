@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Payment Bank Transfer Reffered Back
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($paymentBankTransferRefferedBack, ['route' => ['paymentBankTransferRefferedBacks.update', $paymentBankTransferRefferedBack->id], 'method' => 'patch']) !!}

                        @include('payment_bank_transfer_reffered_backs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection