@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Field Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($fieldMaster, ['route' => ['fieldMasters.update', $fieldMaster->id], 'method' => 'patch']) !!}

                        @include('field_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection