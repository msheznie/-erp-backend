@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Jv Master Referredback
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($jvMasterReferredback, ['route' => ['jvMasterReferredbacks.update', $jvMasterReferredback->id], 'method' => 'patch']) !!}

                        @include('jv_master_referredbacks.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection