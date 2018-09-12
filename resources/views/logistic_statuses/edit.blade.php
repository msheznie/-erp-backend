@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Logistic Status
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($logisticStatus, ['route' => ['logisticStatuses.update', $logisticStatus->id], 'method' => 'patch']) !!}

                        @include('logistic_statuses.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection