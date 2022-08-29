@extends('layouts.appuni')

@section('scripts_section')
    <script>
        $(document).ready(function() {
            $('.select2class').select2();
        });
    </script>
    <script>
        function hasPoints(){
            var has_points = document.getElementById('has_points');
            if (has_points.checked == true) {
                var points = document.getElementById('university_points');
                points.value = 1;
                points.removeAttribute('readonly');
            }
            else {
                var points = document.getElementById('university_points');
                points.value = 0;
                points.setAttribute('readonly', 'true');
            }
        }
    </script>
@endsection

@section('content')
    @section('content_title', $title)

    <form id="createForm" method="POST" action="{{ route($storeRoute) }}">
        @method('POST')
        @include('mgr.courses.form')
        @include('layouts.crud.create-btns')
    </form>
@endsection