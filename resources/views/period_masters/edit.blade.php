@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Period Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($periodMaster, ['route' => ['periodMasters.update', $periodMaster->id], 'method' => 'patch']) !!}

                        @include('period_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection