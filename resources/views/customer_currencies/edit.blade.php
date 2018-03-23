@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Customer Currency
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($customerCurrency, ['route' => ['customerCurrencies.update', $customerCurrency->id], 'method' => 'patch']) !!}

                        @include('customer_currencies.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection