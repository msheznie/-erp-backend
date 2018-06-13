@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Materiel Request Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($materielRequestDetails, ['route' => ['materielRequestDetails.update', $materielRequestDetails->id], 'method' => 'patch']) !!}

                        @include('materiel_request_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection