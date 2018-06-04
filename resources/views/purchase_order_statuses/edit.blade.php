@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Purchase Order Status
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($purchaseOrderStatus, ['route' => ['purchaseOrderStatuses.update', $purchaseOrderStatus->id], 'method' => 'patch']) !!}

                        @include('purchase_order_statuses.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection