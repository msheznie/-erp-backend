@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Tax Formula Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($taxFormulaMaster, ['route' => ['taxFormulaMasters.update', $taxFormulaMaster->id], 'method' => 'patch']) !!}

                        @include('tax_formula_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection