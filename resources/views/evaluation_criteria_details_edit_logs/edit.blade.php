@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Evaluation Criteria Details Edit Log
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($evaluationCriteriaDetailsEditLog, ['route' => ['evaluationCriteriaDetailsEditLogs.update', $evaluationCriteriaDetailsEditLog->id], 'method' => 'patch']) !!}

                        @include('evaluation_criteria_details_edit_logs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection