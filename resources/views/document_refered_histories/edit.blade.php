@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Document Refered History
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($documentReferedHistory, ['route' => ['documentReferedHistories.update', $documentReferedHistory->id], 'method' => 'patch']) !!}

                        @include('document_refered_histories.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection