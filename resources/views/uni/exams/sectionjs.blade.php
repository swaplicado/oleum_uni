@section('scripts_section')
    <script type="text/javascript">
        function GlobalData () {
            this.lQuestions = <?php echo json_encode( $lQuestions ) ?>;
            this.oSubtopic = <?php echo json_encode( $oSubtopic ) ?>;
            this.oTopic = <?php echo json_encode( $oTopic ) ?>;
            this.idSubtopicTaken = <?php echo json_encode( $idSubtopicTaken ) ?>;
            this.takenGrouper = <?php echo json_encode( $takenGrouper ) ?>;
            this.idAssignment = <?php echo json_encode( $idAssignment ) ?>;
            this.takeEvaluation = <?php echo json_encode( $takeEvaluation ) ?>;
            this.sRecordRoute = <?php echo json_encode( route($sRecordRoute) ) ?>;
            this.sRecordExam = <?php echo json_encode( route($sRecordExam) ) ?>;
            this.sSuccessRoute = <?php echo json_encode( route($sSuccessRoute, [$idCourse, $idAssignment]) ) ?>;
            this.sFailRoute = <?php echo json_encode( route($sFailRoute, [$oSubtopic->id_subtopic, $takenGrouper, $idAssignment]) ) ?>;
            this.sImageRoute = <?php echo json_encode( asset('images/success/source.gif') ) ?>;
        }
        
        var oServerData = new GlobalData();
    </script>
@endsection