var app = new Vue({
    el: '#playApp',
    data: {
        oData: oServerData,
        fileType: "",
        fileUrl: "#",
        oContent: [],
        nContents: oServerData.lContents.length,
        indexContent: 0,
        sFileName: ""
    },
    mounted() {
        this.setContent();
    },
    methods: {
        setContent() {
            this.oContent = this.oData.lContents[this.indexContent];
            console.log(this.oContent);

            this.fileType = this.oContent.file_type;
            this.fileUrl = JSON.parse(this.oContent.view_path);

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

                default:
                    break;
            }
        },
        next() {
            this.indexContent++;
            this.setContent();
        },
        previous() {
            this.indexContent--;
            this.setContent();
        }
    },
})