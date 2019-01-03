@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Outlet Users
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($outletUsers, ['route' => ['outletUsers.update', $outletUsers->id], 'method' => 'patch']) !!}

                        @include('outlet_users.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection