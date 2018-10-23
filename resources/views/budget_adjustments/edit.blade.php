@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Budget Adjustment
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($budgetAdjustment, ['route' => ['budgetAdjustments.update', $budgetAdjustment->id], 'method' => 'patch']) !!}

                        @include('budget_adjustments.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection