@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Quotation Version Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($quotationVersionDetails, ['route' => ['quotationVersionDetails.update', $quotationVersionDetails->id], 'method' => 'patch']) !!}

                        @include('quotation_version_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection