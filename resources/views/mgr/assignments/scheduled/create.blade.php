@extends('layouts.appuni')

@include('mgr.assignments.scheduled.createjs')

@section('content')
    @section('content_title', $title)
    <div class="row" id="scheduledAssignmentsApp">
        <div class="col-md-12">
            <form id="createForm" action="{{ route($storeRoute) }}" method="POST">
                @method('POST')
                @include('mgr.assignments.scheduled.form')
                @include('layouts.crud.create-btns')
            </form>
        </div>
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/assignments/VueScheduledAssignmentsForm.js') }}"></script>
@endsection