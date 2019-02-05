@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Quotation Details Refferedback
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($quotationDetailsRefferedback, ['route' => ['quotationDetailsRefferedbacks.update', $quotationDetailsRefferedback->id], 'method' => 'patch']) !!}

                        @include('quotation_details_refferedbacks.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection