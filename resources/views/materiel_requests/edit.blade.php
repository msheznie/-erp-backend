@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Materiel Request
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($materielRequest, ['route' => ['materielRequests.update', $materielRequest->id], 'method' => 'patch']) !!}

                        @include('materiel_requests.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection