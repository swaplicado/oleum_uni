<script type="text/javascript">
    function GlobalData () {
        this.lContents = <?php echo json_encode( $lContents ) ?>;
        this.oSubtopic = <?php echo json_encode( $oSubtopic ) ?>;
        this.iContent = <?php echo json_encode( $iContent ) ?>;
        this.idSubtopicTaken = <?php echo json_encode( $idSubtopicTaken ) ?>;
        this.takeGrouper = <?php echo json_encode( $takeGrouper ) ?>;
        this.registryContentRoute = <?php echo json_encode( route($registryContentRoute) ) ?>;
    }
    
    var oServerData = new GlobalData();
</script>