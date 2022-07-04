<div class="modal fade" style="width: 100%;" id="modalEditGeneral" tabindex="-1" aria-labelledby="editGeneralModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGeneralModalLabel">Edición general de elementos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#">
                    @csrf
                    <div class="row">
                        <div class="col-2">
                            <label for="cuadrante" class="form-label">Cuadrante:</label>
                        </div>
                        <div class="col-10">
                            <select class="form-select" id="sel_cuadrante" style="width: 80%;" required></select>
                            <button type="button" class="btn-close" v-on:click="cleanCuadrante();"></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <label for="module" class="form-label">Modulo:</label>
                        </div>
                        <div class="col-10">
                            <select class="form-select" id="sel_modules" style="width: 80%;" required></select>
                            <button type="button" class="btn-close" v-on:click="cleanModule();"></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <label for="course" class="form-label">Curso:</label>
                        </div>
                        <div class="col-10">
                            <select class="form-select" id="sel_courses" style="width: 80%;" required></select>
                            <button type="button" class="btn-close" v-on:click="cleanCourse();"></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <label for="topic" class="form-label">Tema:</label>
                        </div>
                        <div class="col-10">
                            <select class="form-select" id="sel_topics" style="width: 80%;" required></select>
                            <button type="button" class="btn-close" v-on:click="cleanTopic();"></button>
                        </div>
                    </div>
                    {{-- <div class="form-group">
                        <label for="subtopic" class="form-label">Subtema:</label>
                        <select class="form-select" id="sel_subtopics" style="width: 80%;" required></select>
                    </div> --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" v-on:click="getEdit();">Ir a editar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>