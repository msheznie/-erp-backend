@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Customer Invoice Direct Detail
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($customerInvoiceDirectDetail, ['route' => ['customerInvoiceDirectDetails.update', $customerInvoiceDirectDetail->id], 'method' => 'patch']) !!}

                        @include('customer_invoice_direct_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection