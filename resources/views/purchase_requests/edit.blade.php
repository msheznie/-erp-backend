@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Purchase Request
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($purchaseRequest, ['route' => ['purchaseRequests.update', $purchaseRequest->id], 'method' => 'patch']) !!}

                        @include('purchase_requests.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection