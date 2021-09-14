var app = new Vue({
    el: '#pointsApp',
    data: {
        oData: oGlobalData,
        lDetail: [],
        movClass: 1,
        lMovTypes: oGlobalData.inMovTypes,
        movType: 0,
        points: 0,
        idStudent: 0,
        comments: ""
    },
    methods: {
        viewDetail(idStudent) {
            SGui.showWaiting(4000);
            this.lDetail = [];

            axios
                .get(this.oData.sGetRoute + "/" + idStudent)
                .then(response => {
                    let data = response.data;

                    this.lDetail = data;

                    oDetailTable.draw();

                    $('#dPointsModal').modal('show');
                })
                .catch(err => {
                    console.log(err);
                    SGui.showError(err);
                });
        },
        adjustPoints(idStudent) {
            this.idStudent = idStudent;
            this.comments = "";
            $('#modPointsModal').modal('show');
        },
        onClassChange() {
            if (parseInt(this.movClass, 10) == 1) {
                this.lMovTypes = this.oData.inMovTypes;
            } else {
                this.lMovTypes = this.oData.outMovTypes;
            }

            this.movType = 0;
        },
        storeMovement() {
            SGui.showWaiting(4000);

            axios
                .post(this.oData.sStoreRoute, {
                    'mov_class': this.movClass,
                    'mov_type': this.movType,
                    'points': this.points,
                    'comments': this.comments,
                    'id_student': this.idStudent
                })
                .then(response => {
                    let res = response.data;
                    SGui.showOk();
                    location.reload();
                })
                .catch(err => {
                    SGui.showError(err);
                });
        }
    },
});