@extends('layouts.appuni')

<script type="text/javascript">
    function GlobalData () {
        this.lModules = <?php echo json_encode( $lModules ) ?>;
    }
    
    var oServerData = new GlobalData();
</script>

@section('content')
    @section('content_title', 'Módulos de '.$knowledgeArea)
    <div class="row" id="indexModulesApp">
        <div class="col-lg-3 col-md-6 col-12" v-for="module in oData.lModules">
            <div class="card border-primary text-dark bg-light mb-3" style="max-width: 18rem;">
              <div class="card-header">
                <a :href="'{{ route('uni.courses.index') }}' + '/' + module.id_module">
                  @{{ module.module }}
                </a>
              </div>
              <div class="card-body">
                <h5 class="card-title">@{{ module.module }}</h5>
                <p class="card-text">@{{ module.description }}</p>
                <p class="card-text">@{{ module.objectives }}</p>
              </div>
              <div class="card-footer text-muted">
                <b>Footer</b>
              </div>
          </div>
        </div>
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/uni/VueModulesIndex.js') }}"></script>
@endsection