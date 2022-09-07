var app = new Vue({
    el: '#usersApp',
    data: {
        oData: oServerData,
        lAreas: oServerData.lAreas,
        selArea: null,
        idUser: 0,
        pass: '',
        mail: '',
        username: '',
        userType: '',
    },
    mounted(){
        let self = this;
        $('#selArea')
            .select2({
                placeholder: 'selecciona área',
                data: self.lAreas,
            })
            .on('select2:select', function (e){
                self.selArea = e.params.data.id;
            });
    },
    methods: {
        editPassword(id) {
            this.idUser = id;
            this.pass = "";
            $('#passModal').modal('show');
        },
        updatePassword() {
            SGui.showWaiting(3000);

            /**
             * Petición al Controlador
             */
            axios.put(this.oData.passroute, {
                    'id_user': this.idUser,
                    'new_pss': this.pass,
                })
                .then(response => {
                    let res = response.data;
                    $('#passModal').modal('hide');
                    SGui.showOk();
                    location.reload();
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },
        editUsername(id, username) {
            this.idUser = id;
            this.username = username;

            $('#usernameModal').modal('show');
        },
        updateUsername() {
            SGui.showWaiting(3000);

            /**
             * Petición al Controlador
             */
            axios.put(this.oData.userroute, {
                    'id_user': this.idUser,
                    'username': this.username,
                })
                .then(response => {
                    let res = response.data;
                    $('#usernameModal').modal('hide');
                    SGui.showOk();
                    location.reload();
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },
        editMail(id, mail) {
            this.idUser = id;
            this.mail = mail;

            $('#mailModal').modal('show');

        },
        updateMail() {
            SGui.showWaiting(3000);

            /**
             * Petición al Controlador
             */
            axios.put(this.oData.mailroute, {
                    'id_user': this.idUser,
                    'mail': this.mail,
                })
                .then(response => {
                    let res = response.data;
                    $('#mailModal').modal('hide');
                    SGui.showOk();
                    location.reload();
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },
        updateUserType() {
            SGui.showWaiting(3000);

            /**
             * Petición al Controlador
             */
            axios.put(this.oData.userroute, {
                    'user_type': this.userType,
                })
                .then(response => {
                    let res = response.data;

                    SGui.showOk();
                    location.reload();
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },

        editArea(user_id, user, area_id) {
            this.idUser = user_id;
            this.username = user;
            this.selArea = area_id;
            $('#selArea').val(area_id).trigger('change');
            $('#userArea').modal('show');
        },

        updateUserarea() {
            SGui.showWaiting(3000);

            /**
             * Petición al Controlador
             */
            axios.post(this.oData.arearoute, {
                    'id_user': this.idUser,
                    'area_id': this.selArea,
                })
                .then(response => {
                    let res = response.data;
                    if(res.success){
                        $('#usernameModal').modal('hide');
                        SGui.showOk();
                        location.reload();
                    }else{
                        SGui.showError(res.message);
                    }
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },
    },
})