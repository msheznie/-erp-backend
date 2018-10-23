@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Audit Trail
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($auditTrail, ['route' => ['auditTrails.update', $auditTrail->id], 'method' => 'patch']) !!}

                        @include('audit_trails.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection