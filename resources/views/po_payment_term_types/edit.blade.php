@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Po Payment Term Types
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($poPaymentTermTypes, ['route' => ['poPaymentTermTypes.update', $poPaymentTermTypes->id], 'method' => 'patch']) !!}

                        @include('po_payment_term_types.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection