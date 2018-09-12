@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Logistic Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($logisticDetails, ['route' => ['logisticDetails.update', $logisticDetails->id], 'method' => 'patch']) !!}

                        @include('logistic_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection