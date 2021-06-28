<!-- Modal -->
<div class="modal" tabindex="-1" id="previewModal">
    <div class="modal-dialog">
    
    <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Vista previa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div style="text-align:center;">
                {{-- Vista previa de imagen --}}
                <img v-if="fileType == 'image'" :src="fileUrl" alt="" style="vertical-align:middle;" width="80%" height="80%">

                {{-- Vista previa de video --}}
                <video id="idVideo" v-else-if="fileType == 'video'" controls="" autoplay="" name="media" width="80%" height="80%">
                    <source id="idSource" :src="fileUrl" type="video/mp4">
                </video>

                {{-- Vista previa de PDF --}}
                <embed v-else-if="fileType == 'pdf'" :src="fileUrl" width="100%" height="100%" />

                {{-- Vista previa de TXT --}}
                <div v-else-if="fileType == 'text'" class="row">
                    <div class="col-md-12">
                        <textarea v-model="sText" class="form-control" name="" id="" cols="30" rows="10"></textarea>
                    </div>
                </div>

                {{-- Previo audio --}}
                <audio id="idAudio" v-else-if="fileType == 'audio'" controls>
                    <source :src="fileUrl" type="audio/mpeg">
                </audio>

                {{-- Previo file --}}
                <a v-else-if="fileType == 'file'" :href="fileUrl" download>@{{ sFileName }}</a>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
</div>