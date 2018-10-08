@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Fixed Asset Category Sub
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($fixedAssetCategorySub, ['route' => ['fixedAssetCategorySubs.update', $fixedAssetCategorySub->id], 'method' => 'patch']) !!}

                        @include('fixed_asset_category_subs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection