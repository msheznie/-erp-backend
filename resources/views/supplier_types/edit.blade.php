@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Supplier Type
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($supplierType, ['route' => ['supplierTypes.update', $supplierType->id], 'method' => 'patch']) !!}

                        @include('supplier_types.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection