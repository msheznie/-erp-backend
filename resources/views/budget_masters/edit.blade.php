@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Budget Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($budgetMaster, ['route' => ['budgetMasters.update', $budgetMaster->id], 'method' => 'patch']) !!}

                        @include('budget_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection