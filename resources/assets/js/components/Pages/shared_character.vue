<template>
    <div class="container" style="width: 90%">
        <center>
            <div>
                <h3>
                    Character Shared by All Specimens
                </h3>
            </div>
            <div>
                <router-link class="btn btn-primary" style = "float: left; padding: 2px;" :to="'/'"><span class="glyphicon glyphicon-menu-left" style="font-Size: 25px; padding-top:2px;"></span></router-link>
            </div>
            <div style="margin-left: 35px">
                <div class="row">
                    <div class="col-md-offset-2 col-md-8">
                        <div v-if="characterData" class="table-responsive" style="max-height: calc(100vh - 200px);">
                            <table class="table table-bordered cr-table">
                                <thead>
                                    <th v-for="(header,index) in characterData.names" :key="'header'+index" style="min-width: 100px; height: 43px; line-height: 43px; text-align: center;">{{header == 'icharacter' ? 'character': header}}</th>
                                </thead>
                                <tbody>
                                    <tr v-for="(row,index) in characterData.values" :key="'row'+index">
                                        <td v-for="(val,index) in characterData.names" :key="'data'+index" style="padding: 3px; padding-left: 10px">
                                            {{row[val].value}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </center>
    </div>
</template>

<script>
    import Vue from 'vue';
    
    const url = 'https://shark.sbs.arizona.edu:8443/blazegraph/namespace/kb/sparql';
    function makeBaseAuth(user, pswd){ 
        var token = user + ':' + pswd;
        var hash = "";
        if (btoa) {
            hash = btoa(token);
        }
        return "Basic " + hash;
    }

    export default {
        props: [
            'user',
        ],
        computed: {
        },
        data: function () {
            return {
                username: '',
                taxonName: '',
                characterData: {},
                allTreeData: {},
            }
        },
        components: {
        },
        methods: {
            api(query, success) {
                var settings = {
                    method: 'POST',
                    data: { 'query': query ,'format': 'json'},
                    withCredentials: true,
                    headers: { 'Authorization': makeBaseAuth('blazegraph', 'dDhc5XwGtg9vZWDjGb1r') },
                    success: success,
                    error: function error(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR.responseText);
                        app.bSearching = false;
                    }
                };

                $.ajax(url, settings);
            },
        },
        mounted() {
            var app = this;
            
            console.log(app.user);

            app.user.name = app.user.email.split('@')[0];
            sessionStorage.setItem('userId', app.user.id);
            var graphName = 'graph_' + app.user.taxon.toLowerCase().replaceAll(' ', '_') + '_' + app.user.name;
            console.log(graphName);

            var query=` BASE <http://biosemantics.arizona.edu/ontologies/carex#>
                        ##foundation namespaces
                        PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                        PREFIX owl: <http://www.w3.org/2002/07/owl#>
                        PREFIX iao: <http://purl.obolibrary.org/obo/iao.owl#>
                        PREFIX dc: <http://purl.org/dc/terms/>
                        #utility namespaces
                        PREFIX obi:<http://purl.obolibrary.org/obo/obi.owl#>
                        PREFIX uo: <http://purl.obolibrary.org/obo/uo.owl#>
                        PREFIX ncbi: <https://www.ncbi.nlm.nih.gov/Taxonomy#>
                        PREFIX mo:<http://biosemantics.arizona.edu/ontologies/modifierlist#>
                        #domain namespaces
                        PREFIX :<http://biosemantics.arizona.edu/ontologies/carex#>
                        PREFIX kb:<http://biosemantics.arizona.edu/kb/carex#>
                        PREFIX data:<http://biosemantics.arizona.edu/kb/data#>
                        PREFIX app:<http://shark.sbs.arizona.edu/chrecorder#>

                        SELECT $character
                        WHERE{

                        GRAPH <http://biosemantics.arizona.edu/kb/data#${graphName}> {
                            {
                                SELECT (count(distinct ?sp) as ?snumber)
                                WHERE{
                                    ?sp a obi:specimen.
                                }
                            }
                            {
                                SELECT $character (count(distinct ?sp) as ?cnumber)
                                WHERE{
                                    ?sp a obi:specimen.
                                    ?sp :has_part $structure.
                                    $structure :has_quality $iCharacter.
                                    $iCharacter a $character.
                                }
                                GROUP BY $character
                            }
                            FILTER($snumber = $cnumber)
                        }
                    }`;
            app.api(query, data => {
                console.log(data);
                let tmp = {};
                tmp.names = [];
                data.head.vars.forEach((val) => {
                    if (val != 'graph') {
                        tmp.names.push(val);
                    }
                });
                tmp.values = [];
                data.results.bindings.forEach((val) =>{
                    let subVal = {};
                    Object.keys(val).forEach((item) => {
                        if (item != 'graph') {
                            subVal[item] = val[item];
                            if (item == 'character') {
                                // subVal[item].value = subVal[item].value.slice(35, subVal[item].value.length);
                                subVal[item].value = subVal[item].value.split('ontologies/')[1];
                            }
                        }
                    })
                    tmp.values.push(subVal);
                });
                app.characterData = tmp;
            });
        },
    }
</script>