@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Supplier Currency
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($supplierCurrency, ['route' => ['supplierCurrencies.update', $supplierCurrency->id], 'method' => 'patch']) !!}

                        @include('supplier_currencies.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection