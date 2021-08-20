class SGui {
    constructor() {}

    static showWaiting(iTimer) {
        Swal.fire({
            title: 'Espera...',
            timer: iTimer,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    static showOk() {
        Swal.fire({
            title: '¡Realizado!',
            timer: 1500,
            icon: 'success'
        });
    }

    static showError(sError) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: sError,
        });
    }

    static showMessage(sTitle, sMessage, sIcon = 0) {
        Swal.fire({
            icon: sIcon == 0 ? 'info' : sIcon,
            title: sTitle,
            text: sMessage
        })
    }

    /**
     * Muestra un mensaje con fondo de animación con confetti
     * 
     * @param {String} sTitle
     * @param {String} sMessage
     * @param {String} sIcon
     */
    static showSuccess(sTitle, sMessage, sIcon = 0) {
        let base_url = window.location.origin
        let url = location.pathname.split('/');
        let img = base_url + "/" + url[1] + "/" + url[2] + "/images/success/source.gif";
        Swal.fire({
            icon: sIcon == 0 ? 'info' : sIcon,
            title: sTitle,
            text: sMessage,
            width: 600,
            padding: '3em',
            backdrop: `
              rgba(0,0,123,0.4)
              url(` + img + `)
              left top
              no-repeat
            `
        })
    }
}