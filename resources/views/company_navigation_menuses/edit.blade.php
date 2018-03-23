@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Company Navigation Menus
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($companyNavigationMenus, ['route' => ['companyNavigationMenuses.update', $companyNavigationMenus->id], 'method' => 'patch']) !!}

                        @include('company_navigation_menuses.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection