@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Customer Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($customerMaster, ['route' => ['customerMasters.update', $customerMaster->id], 'method' => 'patch']) !!}

                        @include('customer_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection