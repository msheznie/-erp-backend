@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Erp Location
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($erpLocation, ['route' => ['erpLocations.update', $erpLocation->id], 'method' => 'patch']) !!}

                        @include('erp_locations.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection