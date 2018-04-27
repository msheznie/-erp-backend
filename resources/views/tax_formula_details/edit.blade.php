@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Tax Formula Detail
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($taxFormulaDetail, ['route' => ['taxFormulaDetails.update', $taxFormulaDetail->id], 'method' => 'patch']) !!}

                        @include('tax_formula_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection