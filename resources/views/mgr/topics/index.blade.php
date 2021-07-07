@extends('layouts.appuni')

@section('scripts_section')
    <script type="text/javascript">
        function GlobalData () {
            this.lTopics = <?php echo json_encode($lTopics) ?>;
            this.courseId = <?php echo json_encode($courseId) ?>;
            this.sequences = <?php echo json_encode($sequences) ?>;
            this.storeRoute = <?php echo json_encode( route($storeRoute) ) ?>;
            this.storeSubRoute = <?php echo json_encode( route($storeSubRoute) ) ?>;
        }

        var oServerData = new GlobalData();
    </script>
@endsection

@section('content')
    @section('content_title', $title)
<div class="div" id="topicsApp">
    <button id="rightnew" v-on:click="createTopic()" class="btn btn-success">Tema<i class='bx bx-plus'></i></button>
    <br>
    <br>
    <div class="row">
        <div class="accordion" id="accordionTopics">
            <div class="accordion-item" v-for="topic in oData.lTopics">
              <h2 class="accordion-header" :id="('heading') + topic.id_topic">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" :data-bs-target="'#collapse' + topic.id_topic" aria-expanded="true" :aria-controls="'collapse' + topic.id_topic">
                  @{{ topic.topic }}
                </button>
              </h2>
              <div :id="'collapse' + topic.id_topic" class="accordion-collapse collapse show" :aria-labelledby="'heading' + topic.id_topic" data-bs-parent="#accordionTopics">
                <div class="accordion-body">
                    <div class="row">
                        <div class="col-10"></div>
                        <div class="col-2">
                            <button v-on:click="createSubtopic(topic.id_topic)" class="btn btn-success btn-sm" >Subtema <i class='bx bx-list-plus'></i></button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <ul class="list-group">
                            <li v-for="subtopic in topic.lSubtopics" class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    @{{ subtopic.subtopic }}
                                    <a :href="'./' + subtopic.topic_id + '/subtopics/' + subtopic.id_subtopic + '/contents'"><i class='bx bxs-movie-play'></i></a>
                                    <a href=""><i class='bx bxs-edit-alt'></i></a>
                                    <a :href="'./' + subtopic.topic_id + '/subtopics/' + subtopic.id_subtopic + '/questions'"><i class='bx bx-question-mark'></i></a>
                                </div>
                                <span class="badge bg-primary rounded-pill">@{{ subtopic.number_questions }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
              </div>
            </div>
        </div>
    </div>
    @include('mgr.topics.topicmodal')
    @include('mgr.topics.subtopicmodal')
</div>
@endsection
@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/topics/Topic.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myapp/js/topics/VueTopics.js') }}"></script>
@endsection