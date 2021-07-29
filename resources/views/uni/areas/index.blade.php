@extends('layouts.appuni')

@section('scripts_section')

<script type="text/javascript">
    function GlobalData () {
        this.lAssignments = <?php echo json_encode( $lAssignments ) ?>;
    }
    
    var oServerData = new GlobalData();
</script>
@endsection

@section('content')
    @section('content_title', 'Mis áreas de competencia')
    <div class="row" id="indexAreasApp">
        <div class="col-lg-3 col-md-6 col-12" v-for="assign in oData.lAssignments">
            <div class="card border-primary text-dark bg-light mb-3" style="max-width: 18rem;">
              <div class="card-header">
                <a :href="'{{ route('uni.modules.index') }}' + '/' + assign.knowledge_area_id">
                  @{{ assign.knowledge_area }}
                </a>
              </div>
              <div class="card-body">
                <h6>
                  <b>@{{ assign.dt_assignment + " al " + assign.dt_end }}</b>
                </h6>
                <br>
                <h5 class="card-title"><b>@{{ assign.knowledge_area }}</b></h5>
                <p class="card-text">@{{ assign.description }}</p>
                <p class="card-text">@{{ assign.objectives }}</p>
              </div>
              <div class="card-footer text-muted">
                @{{ assign.sAgo }}
              </div>
          </div>
        </div>
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/uni/VueAreasIndex.js') }}"></script>
@endsection