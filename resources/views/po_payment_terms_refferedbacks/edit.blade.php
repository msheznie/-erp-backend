@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Po Payment Terms Refferedback
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($poPaymentTermsRefferedback, ['route' => ['poPaymentTermsRefferedbacks.update', $poPaymentTermsRefferedback->id], 'method' => 'patch']) !!}

                        @include('po_payment_terms_refferedbacks.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection