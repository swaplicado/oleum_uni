<!-- Modal -->
<div class="modal fade" id="reviewsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Quiero saber tu opinión</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('reviews.store') }}" method="post">
          @csrf
          <input type="hidden" name="id_course" value="{{ $oCourse->id_course }}">
          <div class="row">
            <div class="col-12">
              @foreach ($lReviews as $oReviewCfg)
                @if ($oReviewCfg->review_form == "stars")
                    <div class="row">
                      <div class="row">
                        <b class="col-12">{{ $oReviewCfg->question }}</b>
                      </div>
                      <div class="row">
                        <div class="col-11">
                          <div class="rate">
                            <input type="radio" id="{{ $oReviewCfg->id_configuration."star5" }}" name="{{ "rate".$oReviewCfg->id_configuration }}" value="5" />
                            <label for="{{ $oReviewCfg->id_configuration."star5" }}" title="text">5 stars</label>
                            <input type="radio" id="{{ $oReviewCfg->id_configuration."star4" }}" name="{{ "rate".$oReviewCfg->id_configuration }}" value="4" />
                            <label for="{{ $oReviewCfg->id_configuration."star4" }}" title="text">4 stars</label>
                            <input type="radio" id="{{ $oReviewCfg->id_configuration."star3" }}" name="{{ "rate".$oReviewCfg->id_configuration }}" value="3" />
                            <label for="{{ $oReviewCfg->id_configuration."star3" }}" title="text">3 stars</label>
                            <input type="radio" id="{{ $oReviewCfg->id_configuration."star2" }}" name="{{ "rate".$oReviewCfg->id_configuration }}" value="2" />
                            <label for="{{ $oReviewCfg->id_configuration."star2" }}" title="text">2 stars</label>
                            <input type="radio" id="{{ $oReviewCfg->id_configuration."star1" }}" name="{{ "rate".$oReviewCfg->id_configuration }}" value="1" />
                            <label for="{{ $oReviewCfg->id_configuration."star1" }}" title="text">1 star</label>
                          </div>
                        </div>
                      </div>
                    </div>
                @else
                    <div>
                      <div class="mb-3 row">
                        <div class="col-sm-11">
                          <b>{{ $oReviewCfg->question }}</b>
                        </div>
                      </div>
                      <div class="mb-3 row">
                        <div class="col-sm-11">
                          <textarea class="form-control" name="{{ "textArea".$oReviewCfg->id_configuration }}" rows="3" placeholder="Escribe tu respuesta..." required>
                          </textarea>
                        </div>
                      </div>
                    </div>
                @endif
                <br>
              @endforeach
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-info">Enviar</button>
        </form>
        </div>
    </div>
  </div>
</div>