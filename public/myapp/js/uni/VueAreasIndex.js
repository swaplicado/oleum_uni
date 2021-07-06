var app = new Vue({
    el: '#indexAreasApp',
    data: {
        oData: oServerData
    },
    beforeMount() {
        console.log(this.oData);
        for (let assign of this.oData.lAssignments) {
            assign.sAgo = moment(assign.dt_assignment, "YYYY-MM-DD").fromNow();
        }
    },
})