@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Quotation Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($quotationMaster, ['route' => ['quotationMasters.update', $quotationMaster->id], 'method' => 'patch']) !!}

                        @include('quotation_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection