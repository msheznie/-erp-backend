@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Supplier Critical
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($supplierCritical, ['route' => ['supplierCriticals.update', $supplierCritical->id], 'method' => 'patch']) !!}

                        @include('supplier_criticals.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection