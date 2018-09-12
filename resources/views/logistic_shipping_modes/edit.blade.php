@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Logistic Shipping Mode
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($logisticShippingMode, ['route' => ['logisticShippingModes.update', $logisticShippingMode->id], 'method' => 'patch']) !!}

                        @include('logistic_shipping_modes.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection