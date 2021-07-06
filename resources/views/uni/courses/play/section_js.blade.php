<script type="text/javascript">
    function GlobalData () {
        this.lContents = <?php echo json_encode( $lContents ) ?>;
        this.oSubtopic = <?php echo json_encode( $oSubtopic ) ?>;
    }
    
    var oServerData = new GlobalData();
</script>