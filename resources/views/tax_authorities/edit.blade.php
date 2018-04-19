@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Tax Authority
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($taxAuthority, ['route' => ['taxAuthorities.update', $taxAuthority->id], 'method' => 'patch']) !!}

                        @include('tax_authorities.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection