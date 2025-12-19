@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Barcode Configuration
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($barcodeConfiguration, ['route' => ['barcodeConfigurations.update', $barcodeConfiguration->id], 'method' => 'patch']) !!}

                        @include('barcode_configurations.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection