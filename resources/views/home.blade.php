@extends('layouts.appuni')

@section('content')
    @section('content_title', $title)
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                          <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                          <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
                          <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        </div>
                        <div class="carousel-inner">
                          <div class="carousel-item active">
                            <img src="{{ asset('img/morelia.jpg') }}" class="d-block w-100" alt="">
                            <div class="carousel-caption d-none d-md-block">
                              <h5>First slide label</h5>
                              <p>Some representative placeholder content for the first slide.</p>
                            </div>
                          </div>
                          <div class="carousel-item">
                            <img src="{{ asset('img/morelia.jpg') }}" class="d-block w-100" alt="">
                            <div class="carousel-caption d-none d-md-block">
                              <h5>Second slide label</h5>
                              <p>Some representative placeholder content for the second slide.</p>
                            </div>
                          </div>
                          <div class="carousel-item">
                            <img src="{{ asset('img/morelia.jpg') }}" class="d-block w-100" alt="">
                            <div class="carousel-caption d-none d-md-block">
                              <h5>Third slide label</h5>
                              <p>Some representative placeholder content for the third slide.</p>
                            </div>
                          </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-12">
                <a href="{{ route('areas.index') }}"><h2 style="color: #426BC2">Áreas de competencia...</h2></a>
              </div>
            </div>
            <div class="row">
              @foreach($lAssignments as $ka)
                <div class="col-3">
                  <a href="{{ route('uni.modules.index', $ka->id_knowledge_area) }}">
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
                <h2 style="color: #426BC2">Cursando actualmente...</h2>
              </div>
            </div>
            <div class="row">
              @foreach ($lCourses as $course)
                <div class="col-3">
                  <a href="{{ route('uni.courses.course', $course->id_course) }}">
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
