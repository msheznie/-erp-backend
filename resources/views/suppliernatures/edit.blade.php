@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Suppliernature
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($suppliernature, ['route' => ['suppliernatures.update', $suppliernature->id], 'method' => 'patch']) !!}

                        @include('suppliernatures.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection