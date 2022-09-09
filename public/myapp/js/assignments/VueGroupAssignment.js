var app = new Vue({
    el: '#groupAssignmentsApp',
    data: {
        oData: oServerData,
        dateIni: null,
        dateEnd: null,
        assignId: null,
        kaId: null,
        karea: null,
        durationDays: null,
        lModules: [],
        AssignBy: null,
        AssignByName: null,
    },
    mounted() {
        
    },
    methods: {
        editDate(fIni, fEnd, assignId, ka_id, ka, type, name) {
            this.lModules = [];
            this.dateIni = fIni;
            this.dateEnd = fEnd;
            this.assignId = assignId;
            this.kaId = ka_id;
            this.karea = ka;
            this.durationDays = null;
            this.AssignBy = type;
            this.AssignByName = name;
            this.getModules();
            $('#editDateModal').modal('show');
        },

        getModules(){
            SGui.showWaiting(4000);

            axios
                .get(this.oData.getModulesRoute, {
                    params: {
                        'assign_id': this.assignId,
                        'kaId': this.kaId,
                        'dateIni': this.dateIni,
                    }
                })
                .then(response => {
                    var data = response.data;
                    this.lModules = data;
                })
                .catch(err => {
                    console.log(err);
                    SGui.showError(err);
                });
        },

        setDurationDaysArea(){
            this.dateEnd = moment(this.dateEnd, 'YYYY-MM-DD').add((this.durationDays > 1 ? this.durationDays - 1 : this.durationDays), 'days').format('YYYY-MM-DD');
        },

        setDurationDaysModulo(index){
            this.lModules[index].dt_end = moment(this.lModules[index].dt_end, 'YYYY-MM-DD').add((this.lModules[index].addDays > 1 ? this.lModules[index].addDays - 1 : this.lModules[index].addDays), 'days').format('YYYY-MM-DD');
            if(!this.lModules[index].havePreCourses){
                this.setDatesCourses(index);
            }else{
                SGui.showMessage('', 'Debe insertar el cambio de fecha en los cursos manualmente ya que estos presentan secuencia.', 'warning');
            }
        },

        setStartDayModulo(index){
            if(!this.lModules[index].havePreCourses){
                this.setstartDateCourses(index);
            }else{
                SGui.showMessage('', 'Debe insertar el cambio de fecha en los cursos manualmente ya que estos presentan secuencia.', 'warning');
            }
        },

        setDurationDaysCourse(mIndex, cIndex){
            this.lModules[mIndex].courses[cIndex].dt_end = moment(this.lModules[mIndex].courses[cIndex].dt_end, 'YYYY-MM-DD').add((this.lModules[mIndex].courses[cIndex].addDays > 1 ? this.lModules[mIndex].courses[cIndex].addDays - 1 : this.lModules[mIndex].courses[cIndex].addDays), 'days').format('YYYY-MM-DD');
        },

        setstartDateCourses(index){
            for (let i = 0; i < this.lModules[index].courses.length; i++) {
                this.lModules[index].courses[i].dt_ini = this.lModules[index].dt_ini;
            }
        },

        setDatesCourses(index){
            for (let i = 0; i < this.lModules[index].courses.length; i++) {
                this.lModules[index].courses[i].dt_end = this.lModules[index].dt_end;
            }
        },

        rotat(id){
            var icon = document.getElementById(id);
            if(!icon.classList.contains('bx-rotate-180')){
                icon.classList.add('bx-rotate-180');
            }else{
                icon.classList.remove('bx-rotate-180');
            }
        },

        updateAssign(){
            SGui.showWaiting(10000);

            axios
                .post(this.oData.updateRoute, {
                    'assign_id': this.assignId,
                    'dateIni': this.dateIni,
                    'dateEnd': this.dateEnd,
                    'lModules': this.lModules,
                })
                .then(response => {
                    var data = response.data;
                    if(data.success){
                        SGui.showOk();
                        location.reload();
                    }else{
                        SGui.showMessage('Error', data.message, data.icon);
                    }
                })
                .catch(err => {
                    console.log(err);
                    SGui.showError(err);
                });
        },

        deleteAssign(id_control, karea, type, name ,dt_assignment, dt_end){
            Swal.fire({
                title: 'Desea eliminar la asignación?',
                text: karea+' asignado a '+type+': '+name+' con fecha inicio: '+dt_assignment+' y fecha fin: '+dt_end,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    SGui.showWaiting(10000);
                    axios
                        .post(this.oData.deleteRoute, {
                            'assign_id': id_control,
                        })
                        .then(response => {
                            var data = response.data;
                            if(data.success){
                                SGui.showOk();
                                location.reload();
                            }else{
                                SGui.showMessage('Error', data.message, data.icon);
                            }
                        })
                        .catch(err => {
                            console.log(err);
                            SGui.showError(err);
                        });
                }
            });
        }
    },
})