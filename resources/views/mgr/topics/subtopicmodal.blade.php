<div id="subTopicsModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Subtema</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label for="subtopic" class="form-label">Nombre del subtema</label>
                <input v-model="oSubTopic.subtopic" type="text" class="form-control" id="subtopic" placeholder="Nombre del subtema">
            </div>
            <div class="mb-3">
              <label for="number_q" class="form-label">Número de preguntas</label>
              <input v-model="oSubTopic.number_questions" min="2" type="number" class="form-control" id="number_q" placeholder="Número de preguntas">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="button" v-on:click="storeSubTopic()" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </div>
  </div>