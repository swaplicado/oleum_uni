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
                <div class="card border-warning h-100">
                    <img v-if="course.cover == undefined" src="{{ asset('img/aceites.jpg') }}" class="card-img-top" alt="">
                    <video id="idVideo" v-else-if="course.cover.file_type == 'video'" controls="" autoplay="" name="media" width="100%" height="100%">
                        <source id="idSource" :src="course.cover.view_path" type="video/mp4">
                    </video>
                    <img v-else :src="course.cover.view_path" alt="" class="card-img-top" width="100%" height="100%">
                    <div class="card-body">
                        <h4 class="card-title"><b>@{{ course.course }}</b></h4>
                        <p class="card-text">@{{ course.description }}</p>
                        <div class="row">
                            <div class="col-9"></div>
                            <div class="col-3">
                                <a :href="'{{ route('uni.courses.course') }}' + '/' + course.id_course + '/' + course.id_assignment" class="btn btn-success">Iniciar Curso</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-warning">
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" :style="'width: ' + course.completed_percent + '%'" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
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