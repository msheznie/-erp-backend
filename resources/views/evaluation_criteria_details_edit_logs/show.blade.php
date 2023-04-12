@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Evaluation Criteria Details Edit Log
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('evaluation_criteria_details_edit_logs.show_fields')
                    <a href="{{ route('evaluationCriteriaDetailsEditLogs.index') }}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
