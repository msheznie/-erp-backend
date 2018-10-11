@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Insurance Policy Type
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($insurancePolicyType, ['route' => ['insurancePolicyTypes.update', $insurancePolicyType->id], 'method' => 'patch']) !!}

                        @include('insurance_policy_types.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection