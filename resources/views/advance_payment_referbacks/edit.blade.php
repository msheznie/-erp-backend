@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Advance Payment Referback
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($advancePaymentReferback, ['route' => ['advancePaymentReferbacks.update', $advancePaymentReferback->id], 'method' => 'patch']) !!}

                        @include('advance_payment_referbacks.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection