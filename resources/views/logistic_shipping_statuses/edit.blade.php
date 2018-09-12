@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Logistic Shipping Status
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($logisticShippingStatus, ['route' => ['logisticShippingStatuses.update', $logisticShippingStatus->id], 'method' => 'patch']) !!}

                        @include('logistic_shipping_statuses.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection