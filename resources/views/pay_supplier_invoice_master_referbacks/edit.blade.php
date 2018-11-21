@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Pay Supplier Invoice Master Referback
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($paySupplierInvoiceMasterReferback, ['route' => ['paySupplierInvoiceMasterReferbacks.update', $paySupplierInvoiceMasterReferback->id], 'method' => 'patch']) !!}

                        @include('pay_supplier_invoice_master_referbacks.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection