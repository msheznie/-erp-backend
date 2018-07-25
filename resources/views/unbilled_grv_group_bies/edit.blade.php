@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Unbilled Grv Group By
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($unbilledGrvGroupBy, ['route' => ['unbilledGrvGroupBies.update', $unbilledGrvGroupBy->id], 'method' => 'patch']) !!}

                        @include('unbilled_grv_group_bies.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection