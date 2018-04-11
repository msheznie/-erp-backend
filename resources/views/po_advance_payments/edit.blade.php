@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Po Advance Payment
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($poAdvancePayment, ['route' => ['poAdvancePayments.update', $poAdvancePayment->id], 'method' => 'patch']) !!}

                        @include('po_advance_payments.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection