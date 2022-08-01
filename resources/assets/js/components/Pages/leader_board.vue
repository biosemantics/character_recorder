<template>
    <div class="container">
        <center>
            <div>
                <h3>
                    Leader Board
                </h3>
            </div>
            <div>
                <router-link class="btn btn-primary" style = "float: left; padding: 2px;" :to="'/'"><span class="glyphicon glyphicon-menu-left" style="font-Size: 25px; padding-top:2px;"></span></router-link>
            </div>
            <div style="margin-left: 35px">
                <div v-for="(each, index) in resultList" :key="index">
                    <div style="width: 500px; height: 50px; border-radius: 10px; margin: 3px; padding: 7px; box-shadow: 1px 1px 1px grey" v-bind:style="{ backgroundColor: each.index == 1 && 'gold' || each.index == 2 && 'yellow' || each.index == 3 && 'lightyellow' || each.index > 3 && '#FFFFFE'}">
                        <div style="float: left" class="numberCircle" v-bind:style="{ borderColor: '#333333', color: '#333333'}">
                            {{each.index}}
                        </div>
                        <div style="float: left; font-family: Arial; font-size: 20px; padding: 4px; color: black; margin-left: 12px; font-weight: 550">
                            {{each.name}}
                        </div>
                        <div style="width: 170px; float: right; font-family: Arial; font-size: 20px; padding: 4px; color: black; text-align:left;">
                            {{each.cnt}} &nbsp; Character{{each.cnt>1?'s':''}}
                        </div>
                    </div>
                </div>
            </div>
        </center>
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

            for (var key in app.list) {
                if (key != 'allFlag') {
                    app.resultList.push({name: key, cnt: app.list[key]});
                }
            }
            app.resultList.sort( ( a, b ) => b.cnt - a.cnt );
            for (let i = 0; i < app.resultList.length; i++){
                app.resultList[i].index = (i + 1);
            }
            console.log('list', app.list);
        },
    }
</script>