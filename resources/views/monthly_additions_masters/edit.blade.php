@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Monthly Additions Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($monthlyAdditionsMaster, ['route' => ['monthlyAdditionsMasters.update', $monthlyAdditionsMaster->id], 'method' => 'patch']) !!}

                        @include('monthly_additions_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection