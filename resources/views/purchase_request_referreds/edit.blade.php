@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Purchase Request Referred
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($purchaseRequestReferred, ['route' => ['purchaseRequestReferreds.update', $purchaseRequestReferred->id], 'method' => 'patch']) !!}

                        @include('purchase_request_referreds.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection