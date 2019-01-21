@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Customer Master Category
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($customerMasterCategory, ['route' => ['customerMasterCategories.update', $customerMasterCategory->id], 'method' => 'patch']) !!}

                        @include('customer_master_categories.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection