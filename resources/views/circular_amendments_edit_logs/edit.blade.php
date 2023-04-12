@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Circular Amendments Edit Log
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($circularAmendmentsEditLog, ['route' => ['circularAmendmentsEditLogs.update', $circularAmendmentsEditLog->id], 'method' => 'patch']) !!}

                        @include('circular_amendments_edit_logs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection