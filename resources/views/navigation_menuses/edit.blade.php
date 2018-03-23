@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Navigation Menus
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($navigationMenus, ['route' => ['navigationMenuses.update', $navigationMenus->id], 'method' => 'patch']) !!}

                        @include('navigation_menuses.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection