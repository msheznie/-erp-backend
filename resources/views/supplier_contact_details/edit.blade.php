@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Supplier Contact Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($supplierContactDetails, ['route' => ['supplierContactDetails.update', $supplierContactDetails->id], 'method' => 'patch']) !!}

                        @include('supplier_contact_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection