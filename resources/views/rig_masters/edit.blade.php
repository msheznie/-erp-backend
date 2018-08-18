@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Rig Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($rigMaster, ['route' => ['rigMasters.update', $rigMaster->id], 'method' => 'patch']) !!}

                        @include('rig_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection