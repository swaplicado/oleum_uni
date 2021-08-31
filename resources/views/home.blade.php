@extends('layouts.appuni')

@section('content')
    @section('content_title', $title)
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div id="homeCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                          @for ($i = 0; $i < count($lCarousel); $i++)
                            <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i == 0 ? 'active' : '' }}" aria-current="{{ $i == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $i+1 }}"></button>
                          @endfor
                        </div>
                        <div class="carousel-inner">
                          @for ($i = 0; $i < count($lCarousel); $i++)
                            <div class="carousel-item {{ $i == 0 ? 'active' : '' }}">
                              <a href="{{ $lCarousel[$i]->url }}" target="_blank">
                                @if ($lCarousel[$i]->content_n_id == null)
                                  <img src="{{ asset($lCarousel[$i]->image) }}" class="d-block w-100" alt="">
                                  <div class="carousel-caption d-none d-md-block">
                                    <h5 style="color: {{ $lCarousel[$i]->text_color }}"><b>{{ $lCarousel[$i]->title }}</b></h5>
                                    <p style="color: {{ $lCarousel[$i]->text_color }}">{{ $lCarousel[$i]->text }}</p>
                                  </div>
                                @else
                                  <video controls="" autoplay="" name="media" width="100%" height="100%">
                                    <source id="idSource" src="{{ $lCarousel[$i]->path }}" type="video/mp4">
                                  </video>
                                @endif
                              </a>
                            </div>
                          @endfor
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
                          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                </div>
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
                  <a href="{{ route('uni.modules.index', [$ka->id_assignment, $ka->id_knowledge_area]) }}">
                    <div class="card border-primary text-dark bg-light mb-3" style="max-width: 18rem;">
                      <div class="card-header">{{ $ka->knowledge_area }}</div>
                      <div class="card-body">
                        <h5 class="card-title">{{ $ka->knowledge_area }}</h5>
                        <p class="card-text">{{ $ka->description }}</p>
                      </div>
                      <div class="card-footer text-muted">
                        {{ "Termina ".(\Carbon\Carbon::parse($ka->dt_end)->diffForHumans()) }}
                      </div>
                    </div>
                  </a>
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
