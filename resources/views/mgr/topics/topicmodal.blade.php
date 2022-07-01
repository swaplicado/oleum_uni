<div id="topicsModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tema</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label for="topic" class="form-label">Nombre del tema</label>
                <input v-model="oTopic.topic" type="text" class="form-control" id="topic" placeholder="Nombre del tema">
            </div>
            <div class="mb-3">
                <label for="sequence" class="form-label">Secuencia</label>
                <div>
                    <select class="select2class form-control" id="selTopicSecuence" :value="oTopic.secuence_id" name="sequence" style="width: 75%;">
                        <option v-for="seq in oData.sequences" :value="seq.id_sequence">@{{ seq.seq }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="button" v-on:click="storeTopic()" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </div>
  </div>