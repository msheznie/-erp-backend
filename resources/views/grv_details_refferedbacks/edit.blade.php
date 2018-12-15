@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Grv Details Refferedback
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($grvDetailsRefferedback, ['route' => ['grvDetailsRefferedbacks.update', $grvDetailsRefferedback->id], 'method' => 'patch']) !!}

                        @include('grv_details_refferedbacks.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection