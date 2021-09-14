<div id="dPointsModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog" style="max-width: 800px; width: 90%; margin: auto;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalle de puntos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <table id="detail_table" class="display stripe hover row-border order-column" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Puntos ganados</th>
                        <th>Puntos perdidos</th>
                        <th>T. mov</th>
                        <th>Curso</th>
                        <th>Premio</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="pointRow in lDetail">
                        <td>
                            @{{ pointRow.index }}
                        </td>
                        <td>
                            @{{ pointRow.dt_date }}
                        </td>
                        <td>
                            @{{ pointRow.increment }}
                        </td>
                        <td>
                            @{{ pointRow.decrement }}
                        </td>
                        <td>
                            @{{ pointRow.movement_type }}
                        </td>
                        <td>
                            @{{ pointRow.course }}
                        </td>
                        <td>
                            @{{ pointRow.gift }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Puntos ganados</th>
                        <th>Puntos perdidos</th>
                        <th>T. mov</th>
                        <th>Curso</th>
                        <th>Premio</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>