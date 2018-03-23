@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Supplier Importance
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($supplierImportance, ['route' => ['supplierImportances.update', $supplierImportance->id], 'method' => 'patch']) !!}

                        @include('supplier_importances.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection