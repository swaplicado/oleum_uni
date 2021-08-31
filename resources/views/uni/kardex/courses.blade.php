@extends('layouts.appuni')

@section('content')
    @section('content_title', 'Mi avance')

    <div class="row">
        <div class="col-12">
            <h5>Cursos, temas y subtemas:</h5>
            <br>
            <ol class="list-group list-group-numbered">
                @foreach ($lCourses as $course)
                    <div class="card">
                        <h5 class="card-header d-flex justify-content-between align-items-start" style="color: black; background-color: #6FC1E1">
                            {{ $course->course }}
                            <div>
                                <span class="badge bg-primary rounded-pill">{{ $course->grade[1] == null || $course->grade[1] == 0 ? "-" : $course->grade[1] }}</span>
                                <br>
                                @if ($course->elem_status_id == 3 && $course->grade[0])
                                    <a href="{{ route('certificate', [config('csys.elem_type.COURSE'), $course->id_course, $course->id_assignment]) }}" target="_blank">
                                        <i class='bx bx-paperclip'></i>
                                    </a>
                                @endif
                            </div>
                        </h5>
                        @foreach ($course->lTopics as $topic)
                            <li class="list-group-item d-flex justify-content-between align-items-start" style="color: black; background-color: #4CC4D0">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">{{ $topic->topic }}</div>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $topic->grade[1] == null || $topic->grade[1] == 0 ? "-" : $topic->grade[1] }}</span>
                            </li>
                            <div class="card-body">
                                <ul class="list-group">
                                    @foreach ($topic->lSubTopics as $subtopic)    
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $subtopic->subtopic }}
                                        <span class="badge bg-primary rounded-pill">{{ $subtopic->grade[1] == null || $subtopic->grade[1] == 0 ? "-" : $subtopic->grade[1] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </ol>
        </div>
    </div>
@endsection