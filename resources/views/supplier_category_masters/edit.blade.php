@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Supplier Category Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($supplierCategoryMaster, ['route' => ['supplierCategoryMasters.update', $supplierCategoryMaster->id], 'method' => 'patch']) !!}

                        @include('supplier_category_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection