const app = new Vue({
    el: "#app",
    data: {
        message: "",
        selectedLang: "en-us",
        languages: [
            {
                code: "en-us",
                name: "English (United States)",
            },
            {
                code: "id-id",
                name: "Indonesian",
            },
        ],
    },
    methods: {
        send: function () {
            VoiceRSS.speech({
                key: '6ab723513b7149f7bdba5f984ee5e5df',
                src: this.message,
                hl: this.selectedLang,
                v: 'Linda',
                r: 0,
                c: 'mp3',
                f: '44khz_16bit_stereo',
                ssml: false
            });
        },

        selected: function (event) {
            this.selectedLang = event.target.value;
        }
    }
})