var app = new Vue({
    el: '#usersApp',
    data: {
        oData: oServerData,
        idUser: 0,
        pass: '',
        mail: '',
        username: '',
        userType: '',
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
        }
    },
})