@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Logistic Mode Of Import
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($logisticModeOfImport, ['route' => ['logisticModeOfImports.update', $logisticModeOfImport->id], 'method' => 'patch']) !!}

                        @include('logistic_mode_of_imports.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection