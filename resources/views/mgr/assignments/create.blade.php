@extends('layouts.appuni')

@section('scripts_section')
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2class').select2();
    });
</script>
<script type="text/javascript">
    function GlobalData () {
            this.studentsRoute = <?php echo json_encode( route($studentsRoute) ) ?>;
            this.storeRoute = <?php echo json_encode( route($storeRoute) ) ?>;
            this.indexRoute = <?php echo json_encode( route($indexRoute) ) ?>;
    }

    var oServerData = new GlobalData();
</script>

@endsection

@section('content')
    @section('content_title', $title)
    <div id="appAssignmentsForm">
        <form id="createForm" method="POST">
            @method('POST')
            @include('mgr.assignments.form')
        </form>
        @include('mgr.assignments.studentsmodal')
        <div class="row">
            <div class="col-9"></div>
            <div class="col-3">
                <button class="btn btn-success" v-on:click="getStudents()">
                    Ver estudiantes por asignar
                </button>
            </div>
        </div>
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/assignments/Assignment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myapp/js/assignments/VueAssignmentsForm.js') }}"></script>
@endsection