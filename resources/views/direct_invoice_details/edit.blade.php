@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Direct Invoice Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($directInvoiceDetails, ['route' => ['directInvoiceDetails.update', $directInvoiceDetails->id], 'method' => 'patch']) !!}

                        @include('direct_invoice_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection