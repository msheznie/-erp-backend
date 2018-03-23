@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Supplier Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($supplierMaster, ['route' => ['supplierMasters.update', $supplierMaster->id], 'method' => 'patch']) !!}

                        @include('supplier_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection