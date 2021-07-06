<div id="questionModal"  class="modal fade" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Pregunta y respuestas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div>
            <div>
              <div class="mb-3">
                <label for="question" class="form-label">Pregunta*</label>
                <textarea v-model="oQuestion.question" class="form-control" id="question" name="question" rows="3"></textarea>
              </div>
              <div class="mb-3">
                <label for="n_answers" class="form-label">Número de respuestas*</label>
                <input v-model="oQuestion.number_answers" class="form-control" type="number" min="2" id="n_answers" name="n_answers"/>
              </div>
            </div>
            <hr>
            <div>
              <div class="row">
                <div class="col-12">
                  <label for="answer_id" class="form-label">Retroalimentación de respuesta*</label>
                  <textarea v-model="oQuestion.answer_feedback" class="form-control" id="answer_feedback" name="answer_feedback" rows="2"></textarea>
                </div>
              </div>
              <br>
              <div class="row">
                <div class="col-12">
                  <label for="answer_id" class="form-label">Respuesta correcta*</label>
                  <select v-model="idPicked" style="width: 100%" class="form-control" id="answer_id" name="answer_id" placeholder="Respuesta...">
                    <option v-for="oAnswer in oQuestion.lAnswers" :value="oAnswer.id_aux">@{{ oAnswer.answer }}</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-10"></div>
                <div class="col-1">
                  <button v-on:click="newAnswer()" class="btn btn-success"><i class='bx bx-list-plus'></i></button>
                </div>
              </div>
              <div class="row" v-for="oAnswer in oQuestion.lAnswers">
                <label for="">Indique respuesta*</label>
                <div class="col-10">
                  <input v-model="oAnswer.answer" class="form-control" type="text">
                </div>
                <div class="col-1">
                  <button class="btn btn-danger"><i class='bx bx-x'></i></button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" v-on:click="saveQuestion()">Guardar</button>
        </div>
      </div>
    </div>
  </div>