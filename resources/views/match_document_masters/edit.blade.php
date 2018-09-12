@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Match Document Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($matchDocumentMaster, ['route' => ['matchDocumentMasters.update', $matchDocumentMaster->id], 'method' => 'patch']) !!}

                        @include('match_document_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection