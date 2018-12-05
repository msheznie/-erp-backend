@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Jv Details Referredback
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($jvDetailsReferredback, ['route' => ['jvDetailsReferredbacks.update', $jvDetailsReferredback->id], 'method' => 'patch']) !!}

                        @include('jv_details_referredbacks.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection