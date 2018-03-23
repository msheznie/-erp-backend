@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Department Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($departmentMaster, ['route' => ['departmentMasters.update', $departmentMaster->id], 'method' => 'patch']) !!}

                        @include('department_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection