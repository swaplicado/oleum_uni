<!-- Modal -->
<div class="modal fade" id="studentsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Estudiantes por asignar:</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div v-for="oElem in lAssignments">
                <div class="card border-primary">
                    <div class="card-header">
                      @{{ oElem.auxName }}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <input v-model="oElem.dt_assignment" type="date" class="form-control">
                            </div>
                            <div class="col-5">
                                <input v-model="oElem.dt_end" type="date" class="form-control">
                            </div>
                            <div class="col-1">
                                <button v-on:click="discardAssignment(oElem)" class="btn btn-danger"><i class='bx bx-x'></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="button" v-on:click="assignArea()" class="btn btn-primary">Asignar cuadrante</button>
        </div>
      </div>
    </div>
  </div>