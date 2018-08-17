@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Fixed Asset Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($fixedAssetMaster, ['route' => ['fixedAssetMasters.update', $fixedAssetMaster->id], 'method' => 'patch']) !!}

                        @include('fixed_asset_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection