@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Purchase Order Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($purchaseOrderDetails, ['route' => ['purchaseOrderDetails.update', $purchaseOrderDetails->id], 'method' => 'patch']) !!}

                        @include('purchase_order_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection