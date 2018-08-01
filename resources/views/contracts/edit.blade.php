@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Contract
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($contract, ['route' => ['contracts.update', $contract->id], 'method' => 'patch']) !!}

                        @include('contracts.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection