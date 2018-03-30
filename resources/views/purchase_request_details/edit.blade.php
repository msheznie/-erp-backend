@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Purchase Request Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($purchaseRequestDetails, ['route' => ['purchaseRequestDetails.update', $purchaseRequestDetails->id], 'method' => 'patch']) !!}

                        @include('purchase_request_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection