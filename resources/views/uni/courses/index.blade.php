@extends('layouts.appuni')

@section('scripts_section')

<script type="text/javascript">
    function GlobalData () {
        this.lCourses = <?php echo json_encode( $lCourses ) ?>
    }
    
    var oServerData = new GlobalData();
</script>
@endsection

@section('content')
    @section('content_title', 'Cursos de '.$module)
    <div id="indexCoursesApp">
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <div class="col" v-for="course in oData.lCourses">
                <div class="card border-warning">
                    <img src="{{ asset('img/aceites.jpg') }}" class="card-img-top" alt="">
                    <div class="card-body">
                        <h4 class="card-title">@{{ course.course }}</h4>
                        <p class="card-text">@{{ course.description }}</p>
                        <div class="row">
                            <div class="col-9"></div>
                            <div class="col-3">
                                <a :href="'{{ route('uni.courses.course') }}' + '/' + course.id_course" class="btn btn-success">Ver Curso</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-warning">
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/uni/VueCoursesIndex.js') }}"></script>
@endsection