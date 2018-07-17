@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Stock Transfer Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($stockTransferDetails, ['route' => ['stockTransferDetails.update', $stockTransferDetails->id], 'method' => 'patch']) !!}

                        @include('stock_transfer_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection