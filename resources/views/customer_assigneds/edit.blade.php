@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Customer Assigned
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($customerAssigned, ['route' => ['customerAssigneds.update', $customerAssigned->id], 'method' => 'patch']) !!}

                        @include('customer_assigneds.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection