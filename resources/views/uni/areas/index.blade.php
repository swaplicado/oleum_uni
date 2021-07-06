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
        <div class="col-3" v-for="assign in oData.lAssignments">
            <div class="card border-primary text-dark bg-light mb-3" style="max-width: 18rem;">
              <div class="card-header">
                <a :href="'{{ route('uni.modules.index') }}' + '/' + assign.knowledge_area_id">
                  @{{ assign.knowledge_area }}
                </a>
              </div>
              <div class="card-body">
                <h5 class="card-title">@{{ assign.knowledge_area }}</h5>
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