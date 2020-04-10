@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Customer Catalog Detail
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($customerCatalogDetail, ['route' => ['customerCatalogDetails.update', $customerCatalogDetail->id], 'method' => 'patch']) !!}

                        @include('customer_catalog_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection