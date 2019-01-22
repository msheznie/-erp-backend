@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Sales Person Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($salesPersonMaster, ['route' => ['salesPersonMasters.update', $salesPersonMaster->id], 'method' => 'patch']) !!}

                        @include('sales_person_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection