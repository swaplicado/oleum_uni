<div class="modal fade" style="width: 100%;" id="modalCopyElement" tabindex="-1" aria-labelledby="copyElement" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="copyElement">Copiar elemento en:</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#">
                    @csrf
                    <div class="row" v-show="type == 'area'">
                        <div class="col-12" style="text-align: center;">
                            <h3>¿Deseas copiar el cuadrante?</h3>
                        </div>
                    </div>
                    <div class="row" v-show="type == 'module' || type == 'course' || type == 'topic' || type == 'subtopic'">
                        <div class="col-2">
                            <label for="cuadrante" class="form-label">Cuadrante:</label>
                        </div>
                        <div class="col-10">
                            <select class="form-select" id="sel_cuadrante" style="width: 80%;"></select>
                            <button type="button" class="btn-close" v-on:click="cleanCuadrante();"></button>
                        </div>
                    </div>
                    <div class="row" v-show="type == 'course' || type == 'topic' || type == 'subtopic'">
                        <div class="col-2">
                            <label for="module" class="form-label">Módulo:</label>
                        </div>
                        <div class="col-10">
                            <select class="form-select" id="sel_modules" style="width: 80%;"></select>
                            <button type="button" class="btn-close" v-on:click="cleanModule();"></button>
                        </div>
                    </div>
                    <div class="row" v-show="type == 'topic' || type == 'subtopic'">
                        <div class="col-2">
                            <label for="course" class="form-label">Curso:</label>
                        </div>
                        <div class="col-10">
                            <select class="form-select" id="sel_courses" style="width: 80%;"></select>
                            <button type="button" class="btn-close" v-on:click="cleanCourse();"></button>
                        </div>
                    </div>
                    <div class="row" v-show="type == 'subtopic'">
                        <div class="col-2">
                            <label for="topic" class="form-label">Tema:</label>
                        </div>
                        <div class="col-10">
                            <select class="form-select" id="sel_topics" style="width: 80%;"></select>
                            <button type="button" class="btn-close" v-on:click="cleanTopic();"></button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" v-on:click="copyElement();" :disabled="disabledCopy">Copiar elemento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>