@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Fixed Asset Category
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($fixedAssetCategory, ['route' => ['fixedAssetCategories.update', $fixedAssetCategory->id], 'method' => 'patch']) !!}

                        @include('fixed_asset_categories.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection