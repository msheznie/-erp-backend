@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Purchase Order Process Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($purchaseOrderProcessDetails, ['route' => ['purchaseOrderProcessDetails.update', $purchaseOrderProcessDetails->id], 'method' => 'patch']) !!}

                        @include('purchase_order_process_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection