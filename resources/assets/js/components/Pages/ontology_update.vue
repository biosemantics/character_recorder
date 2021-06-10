<template>
    <div class="container" style="width: 90%">
        <center>
            <div>
                <h3>
                    Ontology Update:  {{currentTermType == 1 ? 'Deprecated terms' : (currentTermType == 2 ? 'Terms with new superclass' : 'Terms with new definition')}}
                </h3>
            </div>
            <div>
                <a class="btn btn-primary" style = "float: left; padding: 2px;" href="/chrecorder/public"><span class="glyphicon glyphicon-menu-left" style="font-Size: 25px; padding-top:2px;"></span></a>
            </div>
            <div style="margin-left: 35px">
                <div class="row">
                    <div class="col-md-3">
                        <a v-on:click="handleDeprecatedterm()" class="btn btn-primary" :class="{active: currentTermType == 1}" style="width: 100%; margin: 10px" >Deprecated Terms</a><br>
                        <a v-on:click="handleNewSuperclass()" class="btn btn-primary" :class="{active: currentTermType == 2}" style="width: 100%; margin: 10px" >Terms with new superclass</a><br>
                        <a v-on:click="handleNewDefinition()" class="btn btn-primary" :class="{active: currentTermType == 3}" style="width: 100%; margin: 10px" >Terms with new definition</a><br>
                    </div>
                    <div class="col-md-8">
                        <div v-if="currentTermType == 1" style="margin-left: 15px; width: 100%;">
                            <div class="row" v-for="(eachTerm, index) in deprecatedTerms" :key="index" style="text-align: left">
                                <div class="col-md-10">
                                    <div v-if="eachTerm['tagName'] && eachTerm['tagName'] != ''">
                                        <a v-on:click="handleClickDeprecatedTerm(eachTerm['tagName'])"><b>{{eachTerm['deprecate term']}}</b></a>
                                    </div>
                                    <div v-else>
                                        <b>{{eachTerm['deprecate term']}}</b>
                                    </div>
                                    <div style="margin-left:20px;">
                                        <div>
                                            Deprecated Reason: <span style="font-style: italic;font-weight:500;word-break: break-all;"> {{eachTerm['deprecated reason']}}</span>
                                        </div>
                                        <div v-if="eachTerm['replacement term'] && eachTerm['replacement term'] != ''">
                                            Replacement Term: <span style="font-style: italic;font-weight:500;word-break: break-all;"> {{eachTerm['replacement term']}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <a v-on:click="handleMessageDeprecated(eachTerm)"><span >Dispute This</span></a>
                                </div>
                            </div>
                        </div>
                        <div v-if="currentTermType == 2" style="margin-left: 10px; width: 100%;">
                            <!-- <div v-if="newSuperClassTerms.length == 0" style="font-size: xx-large;font-weight: bold;margin-top: 20%;">
                                No terms with new superclass
                            </div> -->
                            <div class="row" v-for="(eachTerm, index) in newSuperClassTerms" :key="index" style="text-align: left">
                                <div class="col-md-10">
                                    <div>
                                        <b>{{eachTerm['moved term']}}</b>
                                    </div>
                                    <div style="margin-left:20px;">
                                        <div v-if="eachTerm['moved term'] && eachTerm['moved term'] != ''">
                                            Moved term: <span style="font-style: italic;font-weight:500;word-break: break-all;"> {{eachTerm['moved term']}}</span>
                                        </div>
                                        <div v-if="eachTerm['moved term IRI'] && eachTerm['moved term IRI'] != ''">
                                            Moved term IRI: <span style="font-style: italic;font-weight:500;word-break: break-all;"> {{eachTerm['moved term IRI']}}</span>
                                        </div>
                                        <div v-if="eachTerm['editor_note'] && eachTerm['editor_note'] != ''">
                                            Note: <span style="font-style: italic;font-weight:500;word-break: break-all;"> {{eachTerm['editor_note']}}</span>
                                        </div>
                                        <div v-if="eachTerm['example_of_usage'] && eachTerm['example_of_usage'] != ''">
                                            Example of usage: <span style="font-style: italic;font-weight:500;word-break: break-all;"> {{eachTerm['example_of_usage']}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <a v-on:click="handleMessageSuperclass(eachTerm)"><span >Dispute This</span></a>
                                </div>
                            </div>
                        </div>
                        <div v-if="currentTermType == 3" style="margin-left: 10px; width: 100%;">
                            <!-- <div v-if="newDefinitionTerms.length == 0" style="font-size: xx-large;font-weight: bold;margin-top: 20%;">
                                No terms with new definition
                            </div> -->
                            <div class="row" v-for="(eachTerm, index) in newDefinitionTerms" :key="index" style="text-align: left">
                                <div class="col-md-10">
                                    <div>
                                        <b>{{eachTerm['term with new definition']}}</b>
                                    </div>
                                    <div style="margin-left:20px;">
                                        <div v-if="eachTerm['term with new definition'] && eachTerm['term with new definition'] != ''">
                                            Term with new definition: <span style="font-style: italic;font-weight:500;word-break: break-all;"> {{eachTerm['term with new definition']}}</span>
                                        </div>
                                        <div v-if="eachTerm['term with new def IRI'] && eachTerm['term with new def IRI'] != ''">
                                            Term with new definition IRI: <span style="font-style: italic;font-weight:500;word-break: break-all;"> {{eachTerm['term with new def IRI']}}</span>
                                        </div>
                                        <div v-if="eachTerm['definition'] && eachTerm['definition'] != ''">
                                            Definition: <span style="font-style: italic;font-weight:500;word-break: break-all;"> {{eachTerm['definition']}}</span>
                                        </div>
                                        <div v-if="eachTerm['editor_note'] && eachTerm['editor_note'] != ''">
                                            Note: <span style="font-style: italic;font-weight:500;word-break: break-all;"> {{eachTerm['editor_note']}}</span>
                                        </div>
                                        <div v-if="eachTerm['example_of_usage'] && eachTerm['example_of_usage'] != ''">
                                            Example of usage: <span style="font-style: italic;font-weight:500;word-break: break-all;"> {{eachTerm['example_of_usage']}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <a v-on:click="handleMessageDefinition(eachTerm)"><span >Dispute This</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </center>
        <div v-if="messageDialogFlag" @close="messageDialogFlag = false">
            <transition name="modal">
                <div class="modal-mask">
                    <div class="modal-wrapper">
                        <div class="modal-container">
                            <div style="max-height:80vh; overflow-y: auto;">
                                <div class="modal-header">
                                    <b style="text-align: left">Dispute <font style="color: orange; font-style: italic">{{disputedTerm}}</font> in Carex Ontology</b>
                                    <br/>
                                </div>
                                <div class="modal-body">
                                    <div style="margin-top: 0px;">
                                        <div v-if="deprecatedReason != ''" style="border-bottom: gray; padding 2px; font-size: 11pt">
                                            <span style="width: 20%;">Deprecated Reason:</span>
                                            <b><span>{{deprecatedReason}}</span></b>
                                        </div>
                                        <div v-if="currentTermType == 1">
                                            <div v-if="replacementTerm != ''" style="border-bottom: gray; padding 2px; font-size: 11pt">
                                                <hr style="margin-top: 8px; margin-bottom: 8px; border-top-color: #ddd;">
                                                <span style="width: 20%;">Replacement Term:</span>
                                                <b><span>{{replacementTerm}}</span></b>
                                            </div>
                                            <div v-else style="border-bottom: gray; padding 2px; font-size: 11pt">
                                                <hr style="margin-top: 8px; margin-bottom: 8px; border-top-color: #ddd;">
                                                <span style="width: 20%;">No replacement term was provided.</span>
                                            </div>
                                        </div>
                                        <div v-if="movedNote != ''" style="border-bottom: gray; padding 2px; font-size: 11pt">
                                            <span style="width: 20%;">Note:</span>
                                            <b><span>{{movedNote}}</span></b>
                                        </div>
                                        <div v-if="newDefinition != ''" style="border-bottom: gray; padding 2px; font-size: 11pt">
                                            <span style="width: 20%;">Definition:</span>
                                            <b><span>{{newDefinition}}</span></b>
                                        </div>
                                        <div v-if="definitionNote != ''" style="border-bottom: gray; padding 2px; font-size: 11pt">
                                            <hr style="margin-top: 8px; margin-bottom: 8px; border-top-color: #ddd;">
                                            <span style="width: 20%;">Note:</span>
                                            <b><span>{{definitionNote}}</span></b>
                                        </div>
                                        <div v-if="exampleUsage != ''" style="border-bottom: gray; padding 2px; font-size: 11pt">
                                            <hr style="margin-top: 8px; margin-bottom: 8px; border-top-color: #ddd;">
                                            <span style="width: 20%;">Example of usage:</span>
                                            <b><span>{{exampleUsage}}</span></b>
                                        </div>
                                        <textarea v-bind:placeholder="promptText" style="margin-top:15px;border-radius: 5px; border: 1px solid; padding: 15px; width: 100%;" rows="8" v-model="disputeMessage"></textarea>                                </div>
                                    </div>
                                <div class="modal-footer">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <a class="btn btn-primary ok-btn"
                                                v-bind:class="{disabled: disputeMessage == ''}"
                                                v-on:click="onDisputeTerm">
                                                Dispute </a>
                                            <a v-on:click="messageDialogFlag=false"
                                                class="btn btn-danger">Close</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    
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
                currentTermType: 0,
                messageDialogFlag: false,
                deprecatedTerms: [],
                newSuperClassTerms: [],
                newDefinitionTerms: [],
                userCharacters: [],
                headers: [],
                values: [],
                allColorValues: [],
                allNonColorValues: [],
                userTags: [],
                disputedTerm: '',
                deprecatedReason: '',
                replacementTerm: '',
                movedNote: '',
                newDefinition: '',
                definitionNote: '',
                exampleUsage: '',
                disputeMessage: '',
                promptText: ''
            }
        },
        components: {
        },
        methods: {
            handleDeprecatedterm() {
                var app = this;
                app.currentTermType = 1;
                axios.get("http://shark.sbs.arizona.edu:8080/carex/getDeprecatedClasses")
                    .then(function (resp) {
                        app.deprecatedTerms = resp.data['deprecated classes'];
                        console.log('app.deprecatedTerms', app.deprecatedTerms);
                        for (var i = 0; i < app.deprecatedTerms.length; i ++) {
                            for (var j = 0; j < app.userTags.length; j ++) {
                                for (var k = 0; k < app.userCharacters.length; k ++) {
                                    if (app.userCharacters[k].standard_tag == app.userTags[j].tag_name) {
                                        if (app.userCharacters[k].IRI == app.deprecatedTerms[i]['deprecated IRI']) {
                                            app.deprecatedTerms[i]['tagName'] = app.userTags[j].tag_name;
                                            break;
                                        }
                                        var rows = app.values.find(value => value[0].character_id == app.userCharacters[k].id);
                                        if (!app.checkHaveUnit(rows.find(v => v.header_id == 1).value)) {
                                            for (var l = 0; l < rows.length; l ++) {
                                                if (rows[l].header_id != 1) {
                                                    for (var ind = 0; ind < app.allColorValues.length; ind ++) {
                                                        if (app.allColorValues[ind].value_id == rows[l].id) {
                                                            var colorValue = app.allColorValues[ind];
                                                            if (app.deprecatedTerms[i]['deprecated IRI'] == colorValue.brightness_IRI) {
                                                                app.deprecatedTerms[i]['tagName'] = app.userTags[j].tag_name;
                                                                break;              
                                                            }
                                                            if (app.deprecatedTerms[i]['deprecated IRI'] == colorValue.reflectance_IRI) {
                                                                app.deprecatedTerms[i]['tagName'] = app.userTags[j].tag_name;
                                                                break;              
                                                            }
                                                            if (app.deprecatedTerms[i]['deprecated IRI'] == colorValue.saturation_IRI) {
                                                                app.deprecatedTerms[i]['tagName'] = app.userTags[j].tag_name;
                                                                break;              
                                                            }
                                                            if (app.deprecatedTerms[i]['deprecated IRI'] == colorValue.colored_IRI) {
                                                                app.deprecatedTerms[i]['tagName'] = app.userTags[j].tag_name;
                                                                break;              
                                                            }
                                                            if (app.deprecatedTerms[i]['deprecated IRI'] == colorValue.multi_colored_IRI) {
                                                                app.deprecatedTerms[i]['tagName'] = app.userTags[j].tag_name;
                                                                break;              
                                                            }
                                                        }
                                                    }
                                                    for (var ind = 0; ind < app.allNonColorValues.length; ind ++) {
                                                        if (app.allNonColorValues[ind].value_id == rows[l].id) {                              
                                                            var nonColorValue = app.allNonColorValues[ind];
                                                            if (app.deprecatedTerms[i]['deprecated IRI'] == nonColorValue.main_value_IRI) {
                                                                app.deprecatedTerms[i]['tagName'] = app.userTags[j].tag_name;
                                                                break;              
                                                            }
                                                        }
                                                    }
                                                    if (app.deprecatedTerms[i]['tagName'] && app.deprecatedTerms[i]['tagName'] != '') {
                                                        break;
                                                    }
                                                }
                                            }
                                            if (app.deprecatedTerms[i]['tagName'] && app.deprecatedTerms[i]['tagName'] != '') {
                                                break;
                                            }
                                        }
                                    }
                                }
                                if (app.deprecatedTerms[i]['tagName'] && app.deprecatedTerms[i]['tagName'] != '') {
                                    break;
                                }
                            }
                        }
                        app.deprecatedTerms.sort((a, b) => (b.tagName ? b.tagName.length : 0) - (a.tagName ? a.tagName.length : 0));
                        console.log(app.deprecatedTerms);
                    });
            },
            handleNewSuperclass() {
                var app = this;
                app.currentTermType = 2;
                axios.get("http://shark.sbs.arizona.edu:8080/carex/getMovedClasses?dateString=2020-01-01")
                    .then(function (resp) {
                        app.newSuperClassTerms = resp.data['moved classes'];
                        console.log('app.newSuperClass', app.newSuperClassTerms);
                    });                
            },
            handleNewDefinition() {
                var app = this;
                app.currentTermType = 3;
                axios.get("http://shark.sbs.arizona.edu:8080/carex/getClassesWithNewDefinition?dateString=2020-01-01")
                    .then(function (resp) {
                        app.newDefinitionTerms = resp.data['classes with new definition'];
                        console.log("app.newDefinitions", app.newDefinitionTerms);
                    });
            },
            handleClickDeprecatedTerm(tagName) {
                console.log(tagName);
                sessionStorage.setItem('depreCatedTagName', tagName);
                window.location.href = '/chrecorder/public';
            },
            checkHaveUnit(string) {
                if (string.startsWith('Length of')
                    || string.startsWith('Width of')
                    || string.startsWith('Number of')
                    || string.startsWith('Depth of')
                    || string.startsWith('Diameter of')
                    || string.startsWith('Distance between')
                    || string.startsWith('Count of')) {
                    return true;
                } else {
                    return false;
                }
            },
            handleMessageDeprecated(eachTerm) {
                var app = this;
                app.disputeMessage = "";
                app.movedNote = "";
                app.newDefinition = "";
                app.exampleUsage = "";
                app.definitionNote = "";
                app.disputedTerm = eachTerm['deprecate term'];
                app.deprecatedReason = eachTerm['deprecated reason'];
                app.replacementTerm = eachTerm['replacement term'] ? eachTerm['replacement term'] : '';
                app.promptText = "To dispute the deprecation decision on the term, please provide your reason(s) as detailed as possible";
                app.messageDialogFlag = true;
            },
            handleMessageSuperclass(eachTerm) {
                var app = this;
                app.disputeMessage = "";
                app.deprecatedReason = "";
                app.replacementTerm = "";
                app.newDefinition = "";
                app.exampleUsage = "";
                app.definitionNote = "";
                app.disputedTerm = eachTerm['moved term'];
                app.movedNote = eachTerm['editor_note'];
                app.promptText = "To dispute new superclass decision on the term, please provide your reason(s) as detailed as possible";
                app.messageDialogFlag = true;
            },
            handleMessageDefinition(eachTerm) {
                var app = this;
                app.disputeMessage = "";
                app.deprecatedReason = "";
                app.replacementTerm = "";
                app.movedNote = "";
                app.disputedTerm = eachTerm['term with new definition'];
                app.newDefinition = eachTerm['definition'];
                app.exampleUsage = eachTerm['example_of_usage']
                app.definitionNote = eachTerm['editor_note'];
                app.promptText = "To dispute new definition decision on the term, please provide your reason(s) as detailed as possible"
                app.messageDialogFlag = true;
            },
            onDisputeTerm() {
                var app = this;
                var postValue = {
                    'name': app.user.name,
                    'email': app.user.email,
                    'subject': 'Dispute ' + app.disputedTerm + ' in Carex Ontology',
                    'message': app.disputeMessage
                };
                console.log(postValue);
                axios.post('/chrecorder/public/send-mail', postValue);
                app.messageDialogFlag = false;

            }
        },
        watch: {
        },
        async created() {
            var app = this;
            await axios.get("/chrecorder/public/api/v1/character/" + app.user.id)
                .then(function (resp) {
                    console.log('resp character', resp.data);
                    app.userCharacters = resp.data.characters;
                    app.headers = resp.data.headers;
                    app.values = resp.data.values;
                    app.allColorValues = resp.data.allColorValues;
                    app.allNonColorValues = resp.data.allNonColorValues;
                });
            await axios.get("/chrecorder/public/api/v1/user-tag/" + app.user.id)
                .then(function (resp) {
                    app.userTags = resp.data;
                    console.log('userTags', app.userTags);
                });
            app.currentTermType = 1;
            axios.get("http://shark.sbs.arizona.edu:8080/carex/getDeprecatedClasses")
                .then(function (resp) {
                    app.deprecatedTerms = resp.data['deprecated classes'];
                    console.log('app.deprecatedTerms', app.deprecatedTerms);
                    for (var i = 0; i < app.deprecatedTerms.length; i ++) {
                        for (var j = 0; j < app.userTags.length; j ++) {
                            for (var k = 0; k < app.userCharacters.length; k ++) {
                                if (app.userCharacters[k].standard_tag == app.userTags[j].tag_name) {
                                    if (app.userCharacters[k].IRI == app.deprecatedTerms[i]['deprecated IRI']) {
                                        app.deprecatedTerms[i]['tagName'] = app.userTags[j].tag_name;
                                        break;
                                    }
                                    var rows = app.values.find(value => value[0].character_id == app.userCharacters[k].id);
                                    if (!app.checkHaveUnit(rows.find(v => v.header_id == 1).value)) {
                                        for (var l = 0; l < rows.length; l ++) {
                                            if (rows[l].header_id != 1) {
                                                for (var ind = 0; ind < app.allColorValues.length; ind ++) {
                                                    if (app.allColorValues[ind].value_id == rows[l].id) {
                                                        var colorValue = app.allColorValues[ind];
                                                        if (app.deprecatedTerms[i]['deprecated IRI'] == colorValue.brightness_IRI) {
                                                            app.deprecatedTerms[i]['tagName'] = app.userTags[j].tag_name;
                                                            break;              
                                                        }
                                                        if (app.deprecatedTerms[i]['deprecated IRI'] == colorValue.reflectance_IRI) {
                                                            app.deprecatedTerms[i]['tagName'] = app.userTags[j].tag_name;
                                                            break;              
                                                        }
                                                        if (app.deprecatedTerms[i]['deprecated IRI'] == colorValue.saturation_IRI) {
                                                            app.deprecatedTerms[i]['tagName'] = app.userTags[j].tag_name;
                                                            break;              
                                                        }
                                                        if (app.deprecatedTerms[i]['deprecated IRI'] == colorValue.colored_IRI) {
                                                            app.deprecatedTerms[i]['tagName'] = app.userTags[j].tag_name;
                                                            break;              
                                                        }
                                                        if (app.deprecatedTerms[i]['deprecated IRI'] == colorValue.multi_colored_IRI) {
                                                            app.deprecatedTerms[i]['tagName'] = app.userTags[j].tag_name;
                                                            break;              
                                                        }
                                                    }
                                                }
                                                for (var ind = 0; ind < app.allNonColorValues.length; ind ++) {
                                                    if (app.allNonColorValues[ind].value_id == rows[l].id) {                              
                                                        var nonColorValue = app.allNonColorValues[ind];
                                                        if (app.deprecatedTerms[i]['deprecated IRI'] == nonColorValue.main_value_IRI) {
                                                            app.deprecatedTerms[i]['tagName'] = app.userTags[j].tag_name;
                                                            break;              
                                                        }
                                                    }
                                                }
                                                if (app.deprecatedTerms[i]['tagName'] && app.deprecatedTerms[i]['tagName'] != '') {
                                                    break;
                                                }
                                            }
                                        }
                                        if (app.deprecatedTerms[i]['tagName'] && app.deprecatedTerms[i]['tagName'] != '') {
                                            break;
                                        }
                                    }
                                }
                            }
                            if (app.deprecatedTerms[i]['tagName'] && app.deprecatedTerms[i]['tagName'] != '') {
                                break;
                            }
                        }
                    }
                    app.deprecatedTerms.sort((a, b) => (b.tagName ? b.tagName.length : 0) - (a.tagName ? a.tagName.length : 0));
                    console.log(app.deprecatedTerms);
                });
        },
        mounted() {
            var app = this;
            
            console.log(app.user);

            app.user.name = app.user.email.split('@')[0];
            sessionStorage.setItem('userId', app.user.id);

        },
    }
</script>