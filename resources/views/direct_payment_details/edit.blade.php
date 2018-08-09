@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Direct Payment Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($directPaymentDetails, ['route' => ['directPaymentDetails.update', $directPaymentDetails->id], 'method' => 'patch']) !!}

                        @include('direct_payment_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection