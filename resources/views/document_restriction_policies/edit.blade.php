@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Document Restriction Policy
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($documentRestrictionPolicy, ['route' => ['documentRestrictionPolicies.update', $documentRestrictionPolicy->id], 'method' => 'patch']) !!}

                        @include('document_restriction_policies.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection