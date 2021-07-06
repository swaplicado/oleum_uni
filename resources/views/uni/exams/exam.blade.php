@extends('layouts.appuni')

@include('uni.exams.sectionjs')

@section('content')
    @section('content_title', 'Evaluación')
    <div class="row" id="examApp">
        <div class="col-12">
            <div class="row">
                <div class="col-1"></div>
                <div class="col-11">
                    <p style="font-size: large">@{{ oQuestion.question }}</p>
                </div>
            </div>
            <div v-if="bAnswered" :class="sClassName" role="alert">
                @{{ sMessage }}
            </div>
            <br>
            <div v-if="bFeedback" class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <p><b>@{{ oQuestion.feedback }}</b></p>
                </div>
            </div>
            <br v-if="bFeedback">
            <div class="row">
                <div class="col-1"></div>
                <div :id="'div' + answer.id_answer" class="col-3 form-check border-success" v-for="answer in oQuestion.lAnswers">
                    <input v-on:click="checkAnswer(answer.id_answer)" type="radio" 
                            class="form-check-input" :value="answer.id_answer" :id="answer.id_answer" v-model="picked" :disabled="bAnswered">
                    <label class="form-check-label" :for="answer.id_answer">@{{ answer.answer }}</label>
                </div>
            </div>
            <br>
            <div v-if="((oData.oTopic.sequence_id == 2) || (oData.oTopic.sequence_id == 1 && bAnswered)) && indexQuestion < (nQuestions - 1)" class="row">
                <div class="col-10"></div>
                <div class="col-2">
                    <button v-on:click="next()" class="btn btn-success">Siguiente</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/uni/VueExam.js') }}"></script>
@endsection