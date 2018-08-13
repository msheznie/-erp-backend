@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Pay Supplier Invoice Detail
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($paySupplierInvoiceDetail, ['route' => ['paySupplierInvoiceDetails.update', $paySupplierInvoiceDetail->id], 'method' => 'patch']) !!}

                        @include('pay_supplier_invoice_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection