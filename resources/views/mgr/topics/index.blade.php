@extends('layouts.appuni')

@section('css_section')
    <style>
        .select2-container--open{
            z-index:9999999;
        }
    </style>
@endsection

@section('scripts_section')
    <script>
        $(document).ready(function() {
            $('.select2class').select2();
            $('#sel2').select2({
                dropdownParent: $('#topicsModal')
            });
            $('#selTopicSecuence').on('select2:select', function (e) {
                var data = e.params.data;
                appTopics.oTopic.secuence_id = data.id;
            });
        });
    </script>
    <script type="text/javascript">
        function GlobalData () {
            this.lTopics = <?php echo json_encode($lTopics) ?>;
            this.courseId = <?php echo json_encode($courseId) ?>;
            this.sequences = <?php echo json_encode($sequences) ?>;
            this.storeRoute = <?php echo json_encode( route($storeRoute) ) ?>;
            this.storeSubRoute = <?php echo json_encode( route($storeSubRoute) ) ?>;
            this.lAreas = <?php echo json_encode($lAreas) ?>;
            this.modulesRoute = <?php echo json_encode( route('kareas.getModule') ) ?>;
            this.coursesRoute = <?php echo json_encode( route('kareas.getCourse') ) ?>;
            this.topicsRoute = <?php echo json_encode( route('kareas.getTopic') ) ?>;
            this.copyRoute = <?php echo json_encode( route('copyElement') ) ?>;
        }

        var oServerData = new GlobalData();
    </script>
@endsection

@section('content')
    @section('content_title', $title)
<form id="form_delete" class="d-inline" method="POST" style="display: none;">
    @csrf @method("delete")
</form>

<div class="div" id="topicsApp">

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">@{{editType}}: @{{oEdit.name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="mform" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="estatus" class="form-label">Editar nombre:</label>
                        <input type="text" name="name" :value="oEdit.name">
                    </div>
                    <div class="form-group">
                        <label for="numQuestions" class="form-label">Número de preguntas:</label>
                        <input type="number" name="number_questions" :value="oEdit.number_questions">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


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
                        <div class="col-7"></div>
                        <div class="col-5">
                            <button v-on:click="showCopyElementModal(topic.id_topic, 'topic');" class="btn btn-info btn-sm" >Copiar tema<i class='bx bx-export'></i></button>
                            <button v-on:click="createSubtopic(topic.id_topic)" class="btn btn-success btn-sm" >Subtema <i class='bx bx-list-plus'></i></button>
                            <button v-on:click="editTopic(topic.id_topic, topic.topic, '{{route('topics.edit', ':id')}}')" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal">Editar tema <i class='bx bxs-edit-alt'></i></button>
                            <button v-on:click="topicDelete(topic.id_topic, topic.topic, '{{route('topics.delete', ':id')}}');" class="btn btn-danger btn-sm" >Eliminar tema <i class='bx bxs-trash'></i></button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <ul class="list-group">
                            <li v-for="subtopic in topic.lSubtopics" class="list-group-item d-flex justify-content-between align-items-center">
                                <div style="width:80%">
                                    @{{ subtopic.subtopic }}
                                    <a :href="'./' + subtopic.topic_id + '/subtopics/' + subtopic.id_subtopic + '/contents'"><i class='bx bxs-movie-play'></i></a>
                                    <a :href="'./' + subtopic.topic_id + '/subtopics/' + subtopic.id_subtopic + '/questions'"><i class='bx bx-question-mark'></i></a>
                                </div>
                                <div style="width: 5%">
                                    <span class="badge bg-primary rounded-pill">@{{ subtopic.number_questions }}</span>
                                </div>
                                <div style="width: 5%">
                                    <a href="#" class = "bx bxs-edit-alt" v-on:click="editSubtopic(subtopic.id_subtopic, subtopic.subtopic, subtopic.number_questions, '{{route('subtopics.edit', ':id')}}');" data-bs-toggle="modal" data-bs-target="#editModal"></a>
                                </div>
                                <div style="width: 5%">
                                    <a href="#" title="Copiar subtema" class = "bx bxs-copy" v-on:click="showCopyElementModal(subtopic.id_subtopic, 'subtopic');"></a>
                                </div>
                                <div style="width: 5%">
                                    <a href="#" class = "bx bxs-trash" style="color: red;" v-on:click="subtopicDelete(subtopic.id_subtopic, subtopic.subtopic, '{{route('subtopics.delete', ':id')}}');"></a>
                                </div>
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
    @include('mgr.modalCopyElement')
</div>
@endsection
@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/copyElementClass.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myapp/js/topics/Topic.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myapp/js/topics/VueTopics.js') }}"></script>
    <script>
        var appVue = appTopics;
    </script>
@endsection