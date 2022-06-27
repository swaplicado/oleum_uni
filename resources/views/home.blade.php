@extends('layouts.appuni')

@section('content')
    @section('content_title', $title)
    <div class="row">
        <div class="col-12">
            @include('carousel')
            </div>
            <hr>
            <div class="row">
              <div class="col-12">
                <a href="{{ route('areas.index') }}"><h2 style="color: #05887F">Áreas de competencia...</h2></a>
              </div>
            </div>
            <div class="row">
              @if (count($lAssignments) == 0)
                  <h5>No tienes competencias asignadas</h5>
              @endif
              @foreach($lAssignments as $ka)
                <div class="col-lg-3 col-md-6 col-12">
                  {{-- <a href="{{ route('uni.modules.index', [$ka->id_assignment, $ka->id_knowledge_area]) }}"> --}}
                    <div class="card border-primary text-dark bg-light mb-3" style="max-width: 18rem;">
                      <div class="card-header text-header-blue" style="height: 5rem;">
                        {{ $ka->knowledge_area }}
                      </div>
                      <div class="card-body" style="height: 16rem;">
                        <p class="card-text">{{ $ka->description }}</p>
                      </div>
                      <div class="card-footer text-muted">
                        <a style="width: 95%" href="{{ route('uni.modules.index', [$ka->id_assignment, $ka->id_knowledge_area]) }}" class="btn btn-info" type="button">Tomar módulos</a>
                        <br>
                        {{ "Termina ".(\Carbon\Carbon::parse($ka->dt_end)->diffForHumans()) }}
                      </div>
                    </div>
                  {{-- </a> --}}
                </div>
              @endforeach
            </div>
            <hr>
            <div class="row">
              <div class="col-12">
                <h2 style="color: #05887F">Cursando actualmente...</h2>
              </div>
            </div>
            <div class="row">
              @if (count($lCourses) == 0)
                <h5>No hay nada por aquí...</h5>
              @endif
              @foreach ($lCourses as $course)
                <div class="col-lg-3 col-md-6 col-12">
                  <a href="{{ route('uni.courses.course', [$course->id_course, $course->assignment_id]) }}">
                    <div class="card border-success">
                      <img src="{{ asset('img/curso-capacitacion-snb.png') }}" class="card-img-top" alt="">
                      <div class="card-body">
                        <h5 class="card-title">{{ $course->course }}</h5>
                        <p class="card-text">{{ $course->description }}</p>
                        <p class="card-text"><small class="text-muted">{{ "Comenzado ".(\Carbon\Carbon::parse($course->dtt_take)->diffForHumans()) }}</small></p>
                      </div>
                      <div class="card-footer text-muted">
                        <div class="progress">
                          <div class="progress-bar" role="progressbar" style="{{ "width: ".$course->completed_percent."%" }}" aria-valuenow="{{ $course->completed_percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                      </div>
                    </div>
                  </a>
                </div>
              @endforeach
            </div>
        </div>
    </div>
@endsection
