@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Bid Evaluation Selection
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($bidEvaluationSelection, ['route' => ['bidEvaluationSelections.update', $bidEvaluationSelection->id], 'method' => 'patch']) !!}

                        @include('bid_evaluation_selections.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection