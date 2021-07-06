var app = new Vue({
    el: '#examApp',
    data: {
        oData: oServerData,
        oQuestion: [],
        indexQuestion: 0,
        nQuestions: oServerData.lQuestions.length,
        picked: 0,
        bFeedback: false,
        bAnswered: false,
        sClassName: "",
        sMessage: "",
        lRecordAnswers: []
    },
    mounted() {
        this.setQuestion();
    },
    methods: {
        next() {
            if (this.indexQuestion == (this.nQuestions - 1)) {
                return;
            }

            this.indexQuestion++;
            this.setQuestion();
        },
        setQuestion() {
            this.oQuestion = this.oData.lQuestions[this.indexQuestion];
            this.bAnswered = false;
            this.picked = 0;
            this.bFeedback = false;
            this.bAnswered = false;
            this.sClassName = "";
            this.sMessage = "";
        },
        checkAnswer(idAnswer) {
            if (this.bAnswered) {
                return;
            }

            let isCorrect = idAnswer == this.oQuestion.answer_id;
            this.bFeedback = !isCorrect;

            if (isCorrect) {
                this.sClassName = "alert alert-success";
                this.sMessage = '¡La respuesta es CORRECTA!'
            } else {
                this.sClassName = "alert alert-danger";
                this.sMessage = '¡La respuesta es INCORRECTA!'
            }

            this.picked = idAnswer;
            this.bAnswered = true;

            await this.recordAnswer(this.oQuestion.id_question, idAnswer);
        },
        async recordAnswer(idQuestion, idAnswer) {
            axios
                .post(this.oData.sRecordRoute, {
                    params: {
                        'id_question': idQuestion,
                        'id_answer': idAnswer
                    }
                })
                .then(response => {
                    this.lRecordAnswers = response.data;
                })
                .catch(err => {
                    SGui.showError(err);
                });
        }
    }
})