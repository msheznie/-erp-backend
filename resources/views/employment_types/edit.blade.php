@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Employment Type
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($employmentType, ['route' => ['employmentTypes.update', $employmentType->id], 'method' => 'patch']) !!}

                        @include('employment_types.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection