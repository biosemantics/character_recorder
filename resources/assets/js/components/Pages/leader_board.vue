<template>
    <div class="container">
        <div>
            <h3>
                Leader Board
            </h3>
        </div>
        <div>
            <div v-for="each in resultList">
                {{ each }}
            </div>
        </div>
        <div>
            <a class="btn btn-primary" href="/">Go to Matrix Page</a>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';

    export default {
        props: [
            'user',
            'list'
        ],
        computed: {

        },
        data: function () {
            return {
                resultList: [],
            }
        },
        methods: {

        },
        created() {
            var channel = Echo.channel('my-channel');
            var app = this;
            channel.listen('.my-event', function(data) {
                console.log('data.leaderBoard', data.leaderBoard);

                app.resultList = [];
                var list = data['leaderBoard'];

                var lastWeek = '';
                if (list['allFlag'] == false) {
                    lastWeek = ' last week';
                }
                for (var key in list) {
                    if (key != 'allFlag') {
                        app.resultList.push(key + ' recorded ' + list[key] + ' characters' + lastWeek);
                    }
                }
                console.log('list', list);

            });
        },
        mounted() {
            var app = this;

            var lastWeek = '';
            if (app.list['allFlag'] == false) {
                lastWeek = ' last week';
            }
            for (var key in app.list) {
                if (key != 'allFlag') {
                    app.resultList.push(key + ' recorded ' + app.list[key] + ' characters' + lastWeek);
                }
            }
            console.log('list', app.list);
        },
    }
</script>