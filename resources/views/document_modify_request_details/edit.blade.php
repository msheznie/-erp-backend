@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Document Modify Request Detail
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($documentModifyRequestDetail, ['route' => ['documentModifyRequestDetails.update', $documentModifyRequestDetail->id], 'method' => 'patch']) !!}

                        @include('document_modify_request_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection