@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Gpos Payment Gl Config Detail
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($gposPaymentGlConfigDetail, ['route' => ['gposPaymentGlConfigDetails.update', $gposPaymentGlConfigDetail->id], 'method' => 'patch']) !!}

                        @include('gpos_payment_gl_config_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection