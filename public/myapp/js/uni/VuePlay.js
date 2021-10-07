var app = new Vue({
    el: '#playApp',
    data: {
        oData: oServerData,
        fileType: "",
        fileUrl: "#",
        oContent: [],
        nContents: oServerData.lContents.length,
        indexContent: 0,
        sFileName: "",
        sVideoId: ""
    },
    mounted() {
        this.setContent(this.oData.iContent);
    },
    methods: {
        setContent(idContent = 0) {
            if (idContent > 0) {
                let index = 0;
                for (const content of this.oData.lContents) {
                    if (content.id_content == idContent) {
                        this.indexContent = index;
                        break;
                    }
                    index++;
                }
            }

            this.oContent = this.oData.lContents[this.indexContent];

            this.fileType = this.oContent.file_type;
            this.fileUrl = JSON.parse(this.oContent.view_path);

            try {
                switch (this.fileType) {
                    case 'video':
                        let video = document.getElementById('idVideo');
                        video.load();
                        break;
                    case 'text':
                        this.sText = this.fileUrl;
                        break;
                    case 'audio':
                        let audio = document.getElementById('idAudio');
                        audio.load();
                        break;
                    case 'file':
                        this.sFileName = this.oContent.file_name;
                        break;
                    case 'youtube':
                        this.sVideoId = this.oContent.file_path;
                        break;

                    default:
                        break;
                }
            } catch (error) {
                console.log(error);
            }

            if (idContent == 0) {
                this.registryContent();
            }
        },
        next() {
            this.indexContent++;
            this.setContent();
        },
        previous() {
            this.indexContent--;
            this.setContent();
        },
        registryContent(bClose = false) {
            /**
             * Petición al Controlador
             */
            axios.post(this.oData.registryContentRoute, {
                    'content': this.oContent.id_content,
                    'take_control': this.oData.idSubtopicTaken,
                    'is_close': bClose,
                })
                .then(response => {
                    console.log(response.data);
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        }
    },
})