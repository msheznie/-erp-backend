@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Payment Bank Transfer Detail Reffered Back
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($paymentBankTransferDetailRefferedBack, ['route' => ['paymentBankTransferDetailRefferedBacks.update', $paymentBankTransferDetailRefferedBack->id], 'method' => 'patch']) !!}

                        @include('payment_bank_transfer_detail_reffered_backs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection