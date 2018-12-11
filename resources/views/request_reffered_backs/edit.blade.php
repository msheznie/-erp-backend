@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Request Reffered Back
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($requestRefferedBack, ['route' => ['requestRefferedBacks.update', $requestRefferedBack->id], 'method' => 'patch']) !!}

                        @include('request_reffered_backs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection