@extends('layouts.appuni')

@section('scripts_section')
    <script>
        $(document).ready(function() {
            $('.select2class').select2();
        });
    </script>
@endsection

@section('content')
    @section('content_title', $title)
    <div id="appContentForm">

        <form id="createForm" method="POST" action="{{ route($storeRoute) }}" enctype="multipart/form-data">
            @method('POST')
            @include('mgr.contents.form')
            @include('layouts.crud.create-btns')
        </form>
        
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/contents/VueContentForm.js') }}"></script>
@endsection