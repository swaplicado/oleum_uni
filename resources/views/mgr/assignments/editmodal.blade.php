<!-- Modal -->
<div class="modal fade" id="editAssignmentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-12">
                    <input type="text" class="form-control" v-model="student" readonly>
                </div>
            </div>
            <br>
            <input type="hidden" v-model="idAssignment" name="id_assignment">
            <div class="row">
                <div class="col-6">
                    <input type="date" class="form-control" v-model="dtStart" name="dt_assignment" required>
                </div>
                <div class="col-6">
                    <input type="date" class="form-control" v-model="dtEnd" name="dt_end" required>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" v-on:click="updateAssignment()" class="btn btn-primary">Modificar</button>
        </div>
      </div>
    </div>
  </div>