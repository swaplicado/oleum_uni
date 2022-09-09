<!-- Modal -->
<div class="modal fade" id="editDateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Cuadrante:</b> @{{ karea }} <b> asignado a:</b> @{{AssignBy}} - @{{AssignByName}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <label for="dt_assignment" class="form-label"><b>Fecha Inicio*:</b></label>
                        <input type="date" class="form-control" id="dt_assignment" v-model="dateIni"
                            name="dt_assignment" required>
                    </div>
                    <div class="col-md-3">
                        <label for="duration" class="form-label"><b>Extender (días):</b></label>
                        <input type="number" class="form-control" style="width: 70%; display: inline;"
                            v-model="durationDays" id="duration" name="duration" required>
                        <a class="btn btn-success" style="padding-left: 10px;" v-on:click="setDurationDaysArea();"><i
                                class='bx bxs-right-arrow-circle'></i></a>
                    </div>
                    <div class="col-md-4">
                        <label for="dt_end" class="form-label"><b>Fecha límite*:</b></label>
                        <input type="date" class="form-control" id="dt_end" v-model="dateEnd" name="dt_end"
                            required>
                    </div>
                </div>
                <br>
                <br>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item" v-for="(oModule, index) in lModules">
                        <div class="row">
                            <div class="col-md-3" style="display: table; vertical-align: middle;">
                                <label class="form-label" style="display: table-row;"><b>Modulo:</b></label>
                                <label class="form-label" style="display: table-row;">@{{ oModule.module }}</label>
                            </div>
                            <div class="col-md-3">
                                <label :for="'dt_open_' + index" class="form-label" style="width: 99%;"><b>Fecha inicio*:</b></label>
                                <input type="date" class="form-control" :id="'dt_open_'.index"
                                    v-model="oModule.dt_ini" :name="'dt_open_' + index" required
                                    style="width: 70%; display: inline;">
                                <a class="btn btn-success" style="padding-left: 10px;"
                                    v-on:click="setStartDayModulo(index);"><i class='bx bx-check'></i></a>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label"><b>Extender (días):</b></label>
                                <input type="number" class="form-control" style="width: 50%; display: inline;"
                                    v-model="oModule.addDays" id="duration" name="duration" required>
                                <a class="btn btn-success" style="padding-left: 10px;"
                                    v-on:click="setDurationDaysModulo(index);"><i
                                        class='bx bxs-right-arrow-circle'></i></a>
                            </div>
                            <div class="col-md-3">
                                <label :for="'dt_end_' + index" class="form-label" style="width: 99%;"><b>Fecha
                                        fin*:</b></label>
                                <input type="date" class="form-control" :id="'dt_end_'.index"
                                    v-model="oModule.dt_end" :name="'dt_end_' + index" required
                                    style="width: 70%; display: inline;">
                                <a class="btn btn-success" style="padding-left: 10px;"
                                    v-on:click="setDatesCourses(index);"><i class='bx bx-check'></i></a>
                            </div>
                            <div class="col-md-1">
                                <label :for="'courses' + index" class="form-label"><b>Cursos:</b></label>
                                <a class="btn btn-primary" :name="'courses' + index" data-bs-toggle="collapse"
                                    :href="'#collapsemodule' + index" role="button" aria-expanded="false"
                                    :aria-controls="'collapsemodule' + index" v-on:click="rotat('icon'+index)">
                                    <i class='bx bxs-down-arrow' :id="'icon' + index"></i>
                                </a>
                            </div>
                            <br>
                            <div class="collapse" :id="'collapsemodule' + index">
                                <div class="card card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item" v-for="(oCourse, cIndex) in oModule.courses">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="form-label"
                                                        style="width: 99%;"><b>Curso:</b></label>
                                                    <label class="form-label">@{{ oCourse.course }}</label>
                                                </div>
                                                <div class="col-md-3">
                                                    <label :for="'course_dt_open_' + cIndex"
                                                        class="form-label"><b>Fecha
                                                            inicio*:</b></label>
                                                    <input type="date" class="form-control"
                                                        :id="'course_dt_open_'.cIndex" v-model="oCourse.dt_ini"
                                                        :name="'course_dt_open_' + cIndex" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="duration" class="form-label"><b>Extender
                                                            (días):</b></label>
                                                    <input type="number" class="form-control"
                                                        style="width: 70%; display: inline;" v-model="oCourse.addDays"
                                                        id="duration" name="duration" required>
                                                    <a class="btn btn-success" style="padding-left: 10px;"
                                                        v-on:click="setDurationDaysCourse(index, cIndex);"><i
                                                            class='bx bxs-right-arrow-circle'></i></a>
                                                </div>
                                                <div class="col-md-3">
                                                    <label :for="'course_dt_end_' + cIndex"
                                                        class="form-label"><b>Fecha
                                                            fin*:</b></label>
                                                    <input type="date" class="form-control"
                                                        :id="'course_dt_end_'.cIndex" v-model="oCourse.dt_end"
                                                        :name="'course_dt_end_' + cIndex" required>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" v-on:click="updateAssign();">Actualizar</button>
            </div>
        </div>
    </div>
</div>
