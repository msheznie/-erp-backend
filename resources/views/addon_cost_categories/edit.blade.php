@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Addon Cost Categories
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($addonCostCategories, ['route' => ['addonCostCategories.update', $addonCostCategories->id], 'method' => 'patch']) !!}

                        @include('addon_cost_categories.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection