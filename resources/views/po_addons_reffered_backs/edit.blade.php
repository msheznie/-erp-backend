@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Po Addons Reffered Back
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($poAddonsRefferedBack, ['route' => ['poAddonsRefferedBacks.update', $poAddonsRefferedBack->id], 'method' => 'patch']) !!}

                        @include('po_addons_reffered_backs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection