<!-- Modal -->
<div class="modal fade" id="previousRequisites" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Requisitos previos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Tipo</label>
            <input type="text" class="form-control" v-model="texTypeElement" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" v-model="textName" readonly>
          </div>
          <br>
          <div class="row">
            <label for=""><b>Nuevo Requisito:</b></label>
            <div class="col-12 col-md-6">
              <label for="">Por:</label>
              <select class="form-control" v-model="iRowType">
                <option value="1">Cuadrante</option>
                <option value="2">Módulo</option>
                <option value="3">Curso</option>
              </select>
            </div>
            <div class="col-12 col-md-6" v-if="iRowType == 1">
              <label for="">Seleccione cuadrante:</label>
              <select class="form-control" v-model="iReferenceId">
                <option v-for="oArea in lAreas" :value="oArea.id_knowledge_area">@{{ oArea.knowledge_area }}</option>
              </select>
            </div>
            <div class="col-12 col-md-6" v-if="iRowType == 2">
              <label for="">Seleccione módulo:</label>
              <select class="form-control" v-model="iReferenceId">
                <option v-for="oModule in lModules" :value="oModule.id_module">@{{ oModule.module }}</option>
              </select>
            </div>
            <div class="col-12 col-md-6" v-if="iRowType == 3">
              <label for="">Seleccione curso:</label>
              <select class="form-control" v-model="iReferenceId">
                <option v-for="oCourse in lCourses" :value="oCourse.id_course">@{{ oCourse.course }}</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-10"></div>
            <div class="col-2">
              <button v-on:click="createPrerequisite()" class="btn btn-success"><i class='bx bx-plus-circle'></i></button>
            </div>
          </div>
          <br>
          <div v-for="oPre in lPrerrequisites">
            <div class="row" >
              <div class="col-10">
                <input class="form-control" type="text" :value="getRowText(oPre)" readonly>
              </div>
              <div class="col-2">
                <button title="Quitar" v-on:click="deletePrerequisiteRow(oPre)" class="btn btn-danger"><i class='bx bx-x'></i></button>
              </div>
            </div>
            <br>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </div>
</div>

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/mgr/Prerequisite.js') }}"></script>
    <script>
      function GlobalData () {
            this.lAreas = <?php echo (isset($lAreas) ? json_encode($lAreas) : json_encode([])); ?>;
            this.modulesRoute = <?php echo json_encode( route('kareas.getModule') ) ?>;
            this.coursesRoute = <?php echo json_encode( route('kareas.getCourse') ) ?>;
            this.topicsRoute = <?php echo json_encode( route('kareas.getTopic') ) ?>;
            this.subtopicsRoute = <?php echo json_encode( route('kareas.getSubtopic') ) ?>;
            this.routeTopic = <?php echo json_encode( route('topics.index', ":course") ) ?>;
            this.routeCourse = <?php echo json_encode( route('courses.edit', ":id") ) ?>;
            this.routeModule = <?php echo json_encode( route('modules.edit', ":id") ) ?>;
            this.routeArea = <?php echo json_encode( route('kareas.edit', ":karea") ) ?>;
            this.sGetRoute = <?php echo json_encode( route('get.pre.data') ) ?>;
            this.storeRoute = <?php echo json_encode( route('store.pre.data') ) ?>;
            this.deleteRoute = <?php echo json_encode( route('delete.pre.row') ) ?>;
        }

        var oGlobalData = new GlobalData();
    </script>
    <script type="text/javascript" src="{{ asset('myapp/js/mgr/VuePre.js') }}"></script>
    @if (isset($lAreas))
    <script src="{{ asset('myapp/js/mgr/editGeneral.js') }}"></script>
    @endif
@endsection