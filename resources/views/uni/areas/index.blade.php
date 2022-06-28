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
      @foreach($lAssignments as $assign)
     {{--   v-for="assign in oData.lAssignments" --}}
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card border-primary text-dark bg-light mb-3" style="max-width: 18rem;">
              <div class="card-header text-header-blue" style="height: 4rem;">
                {{-- <a href="{{ route('uni.modules.index', [$assign->id_assignment, $assign->knowledge_area_id]) }}">{{ $assign->knowledge_area }}</a> --}}
                {{ $assign->knowledge_area }}
              </div>
              <div class="card-body" style="height: 20rem;">
                <h6>
                  <b>{{ $assign->dt_assignment." al ".$assign->dt_end }}</b>
                </h6>
                <br>
                <p class="card-text">{{ $assign->description }}</p>
                <p class="card-text">{{ $assign->objectives }}</p>
              </div>
              <div class="card-footer text-muted">
                <a href="{{ route('uni.modules.index', [$assign->id_assignment, $assign->knowledge_area_id]) }}" style="width: 95%;" class="btn btn-info">Ver módulos</a>
                <hr>
                {{ "Termina ".(\Carbon\Carbon::parse($assign->dt_end)->diffForHumans()) }}
              </div>
          </div>
          <div class="progress">
              <div class="progress-bar bg-success" role="progressbar" style="width:{{$assign->completed_percent}}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
      @endforeach
    </div>
@endsection

@section('bottom_scripts')
  {{--   <script type="text/javascript" src="{{ asset('myapp/js/uni/VueAreasIndex.js') }}"></script>  --}}
@endsection