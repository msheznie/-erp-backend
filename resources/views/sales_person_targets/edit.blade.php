@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Sales Person Target
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($salesPersonTarget, ['route' => ['salesPersonTargets.update', $salesPersonTarget->id], 'method' => 'patch']) !!}

                        @include('sales_person_targets.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection