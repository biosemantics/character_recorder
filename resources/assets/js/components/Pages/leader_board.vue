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