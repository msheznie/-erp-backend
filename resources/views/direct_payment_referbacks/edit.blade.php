@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Direct Payment Referback
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($directPaymentReferback, ['route' => ['directPaymentReferbacks.update', $directPaymentReferback->id], 'method' => 'patch']) !!}

                        @include('direct_payment_referbacks.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection