@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Salary Process Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($salaryProcessMaster, ['route' => ['salaryProcessMasters.update', $salaryProcessMaster->id], 'method' => 'patch']) !!}

                        @include('salary_process_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection