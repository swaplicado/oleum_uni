@extends('layouts.appuni')

@section('css_section')
    <link href="{{ asset('steps/bootstrap-steps.css') }}" rel="stylesheet">
@endsection

@include('uni.exams.sectionjs')

@section('content')
    @section('content_title', 'Evaluación')
    <div class="row" id="examApp">
        <div class="col-12">
            <div class="row">
                <div class="col-1"></div>
                <div class="col-11">
                    <p style="font-size: large">@{{ (indexQuestion + 1) + ". " + oQuestion.question }}</p>
                </div>
            </div>
            <div v-if="bAnswered" :class="sClassName" role="alert">
                <i v-if="sClassName == 'alert alert-success'" class='bx bxs-check-circle bx-sm'></i>
                @{{ sMessage }}
            </div>
            <br>
            <div v-if="bFeedback" class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <p><b>@{{ oQuestion.answer_feedback }}</b></p>
                </div>
            </div>
            <br v-if="bFeedback">
            <div class="row">
                <div :id="'div' + answer.id_answer" class="offset-md-1 col-md-11 col-12 form-check border-success" v-for="answer in oQuestion.lAnswers">
                    <input v-on:click="checkAnswer(answer.id_answer)" type="radio" 
                            class="form-check-input" :value="answer.id_answer" :id="answer.id_answer" v-model="picked" :disabled="bAnswered">
                    <label class="form-check-label" :for="answer.id_answer">@{{ answer.answer }}</label>
                </div>
            </div>
            <br>
            <div v-if="((oData.oTopic.sequence_id == 2) || (oData.oTopic.sequence_id == 1 && bAnswered)) && indexQuestion < (nQuestions - 1)" class="row">
                <div class="col-md-2 offset-md-10 col-5 offset-7">
                    <button v-on:click="next()" class="btn btn-success">Siguiente</button>
                </div>
            </div>
            <div v-if="indexQuestion == (nQuestions - 1)" class="row">
                <div class="col-md-2 offset-md-10 col-5 offset-7">
                    <button v-on:click="recordExam()" class="btn btn-success">Terminar</button>
                </div>
            </div>
            <br>
            <br>
            <hr>
            <div class="row">
                <ul class="steps">
                    <li :class="step.class" v-for="step in lSteps">
                      <div class="step-content">
                        <span class="step-circle">@{{ step.circle }}</span>
                        <span style="white-space: nowrap;" class="step-text">@{{ step.text }}</span>
                      </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/uni/exam/Step.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myapp/js/uni/exam/VueExam.js') }}"></script>
@endsection