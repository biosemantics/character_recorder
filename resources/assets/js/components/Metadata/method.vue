<template>
    <div class="row">
        <div class="col-md-12" style="font-size: 15px;">
            <b>1. {{method_description}}</b>
            <b v-if="edit_created_other == false">
              <div>
                <b>
                  Must fill at least one field below
                </b>
              </div>
            </b>
            <br/>
        </div>
        <div class="col-md-12" v-if="!methodEntry">
            Loading...
        </div>

        <div v-if="methodEntry">
            <!--<div class="col-md-12" v-if="noneMethod == false && methodArray.length > 0">
                Please select one illustration that matching your measurement methods.
            </div> -->
            <!-- <div v-for="(each, index) in methodEntry.resultAnnotations"
                 :key="index"
                 v-if="noneMethod == false && each.property == 'http://biosemantics.arizona.edu/ontologies/carex#elucidation'"
                 class="col-md-6"> -->
                 <!-- <label>{{ each.value.substring(1, each.value.length - 1) }}</label>
                 <label>{{ each.value.indexOf('id=') < 0 ?
                        each.value.slice(each.value.indexOf('file/d/') + 7, each.value.indexOf('/view?usp=')) :
                        each.value.split('id=')[1].substring(0, each.value.split('id=')[1].length - 1) }}</label> -->
                <!-- <img class="img-method"
                     v-bind:class="{ greenBorder: illustratorProperty[index] }"
                     v-on:click="clickedMethod(index, each.clickMethodValue)"
                     v-bind:id="'img-method-' + index" style="width: 100%;"
                     v-bind:src="'https://drive.google.com/uc?id=' + each.src"/>
            </div> -->
            <div v-if="!noneMethod && methodEntry.resultAnnotations && methodEntry.resultAnnotations.length > 0 && !edit_created_other && !editFlag" class="col-md-12 text-right">
                <a class="btn btn-primary" v-on:click="noneOfAbove()">None of above</a>
            </div>
            <div v-if="noneMethod == true || !methodEntry.resultAnnotations || methodEntry.resultAnnotations.length == 0 || methodFrom != null || methodTo != null || methodInclude != null || methodExclude != null || methodWhere != null">
                <!--<div class="col-md-12 text-right">-->
                    <!--<a v-if="methodArray.length > 0" class="btn btn-primary" v-on:click="displayImageSection()"-->
                       <!--style="padding: 3px 8px;">-->
                        <!--Open Image Segment-->
                    <!--</a>-->
                <!--</div>-->
                <div class="col-md-12">
                </div>
                <div class="col-md-12" v-if="ch_name != 'Color'"  style="margin-top: 10px;">
                    <div class="row">
                        <label class="col-md-3 text-right">From:</label>
                        <div class="col-md-8"> 
                            <div class="selectDataList">
                                <input :disabled="viewFlag || edit_created_other || editFlag" v-on:blur="userLog('From')" class="width-100" v-model="methodFrom"
                                        @keyup="saveMeasureItems" name="methodFrom"/>
                                <!-- <datalist id="method_characters">
                                    <option :value="noun_character" v-for="noun_character in noun_characters">{{noun_character}}</option>
                                </datalist> -->    
                                <ul>
                                    <li v-on:click="handleMethodFrom(nounCharacter)" :value="nounCharacter" v-for="nounCharacter in resultMethodFromQuery">{{nounCharacter}}
                                    </li>
                                </ul> 
                            </div> 
                        </div> 
                        <p class="col-md-1" v-if="fromId != null || greenTick.from == true" style="color: green;">&#10004;</p>
                    </div>
                    <div class="row"> 
                        <a v-if="fromNeedMore == true" class="red col-md-12" v-bind:class="{ green: needMoreGreen.from }"
                       v-on:mouseover="needMore('from')">Need info on new terms:</a>
                    </div>
                    <div class="row" v-if="formViewFlag.from == true">
                        <div class="col-md-12" v-if="fromSynonyms.length > 0">
                            {{ methodFrom }}: is a synonym of

                        </div>
                        <div class="col-md-12" v-if="fromSynonyms.length > 0">
                            <a class="btn btn-basic" v-on:click="addSynonym('from', each)" v-tooltip="each.tooltip"
                               v-for="(each, index) in fromSynonyms" :key="index">
                                {{ each.term }}
                            </a>
                            <a class="col-md-12 btn btn-basic" v-on:click="noneSynonym('from')">None of above, add the
                                term to Dictionary</a>
                        </div>
                        <div class="col-md-12" v-if="noneSynonymFlag.from == true">
                            <div>
                                Add the following to the dictionary:
                            </div>
                            <a class="btn btn-primary col-md-4" v-on:click="addTerm('from', methodFrom)"
                               style="padding: 3px 8px;"> {{ methodFrom }} </a>
                            <!-- <div class="col-md-7"
                                 style="border: 1px solid grey; border-radius: 4px; margin-left: 10px;">
                                <input v-model="definition.from" style="max-width: 120px;"/>
                                <a class="btn btn-primary" v-on:click="addUserSynonym('from', definition.from)"
                                   style="padding: 1px 4px;">Add</a>
                            </div> -->
                        </div>
                    </div>
                </div>

                <div class="col-md-12" v-if="ch_name != 'Color'" style="margin-top: 10px;">
                    <div class="row">
                        <label class="col-md-3 text-right">To:</label>
                        <div class="col-md-8"> 
                            <div class="selectDataList">
                                <input :disabled="viewFlag || edit_created_other || editFlag" v-on:blur="userLog('To')" class="width-100" v-model="methodTo" @keyup="saveMeasureItems" name="methodTo"/>
                                <!-- <datalist id="to_characters">
                                    <option :value="noun_character" v-for="noun_character in noun_characters">{{noun_character}}</option>
                                </datalist>  -->  
                                <ul>
                                    <li v-on:click="handleMethodTo(nounCharacter)" :value="nounCharacter" v-for="nounCharacter in resultMethodToQuery">{{nounCharacter}}
                                    </li>
                                </ul> 
                            </div>
                        </div>

                        <p class="col-md-1" v-if="toId != null || greenTick.to == true" style="color: green;">&#10004;</p>
                    </div>
                    <div class="row">
                        <a v-if="toNeedMore == true" class="red col-md-12" v-bind:class="{ green: needMoreGreen.to }"
                        v-on:mouseover="needMore('to')">Need info on new terms:</a>
                    </div>

                    <div class="col-md-12" v-if="formViewFlag.to == true">
                        <div class="row">
                            <div class="col-md-12" v-if="toSynonyms.length > 0">
                                {{ methodTo }}: is a synonym of
                            </div>
                            <div class="col-md-12" v-if="toSynonyms.length > 0">
                                <a class="btn btn-basic" v-on:click="addSynonym('to', each)" v-tooltip="each.tooltip"
                                v-for="(each, index) in toSynonyms" :key="index">
                                    {{ each.term }}
                                </a>
                                <a class="col-md-12 btn btn-basic" v-on:click="noneSynonym('to')">None of above, add the
                                    term to Dictionary</a>
                            </div>
                            <div class="col-md-12" v-if="noneSynonymFlag.to == true">
                                <div>
                                    Add the following to the dictionary:
                                </div>
                                <a class="btn btn-primary col-md-4" v-on:click="addTerm('to', methodTo)"
                                style="padding: 3px 8px;"> {{ methodTo }} </a>
                                <!-- <div class="col-md-7"
                                    style="border: 1px solid grey; border-radius: 4px; margin-left: 10px;">
                                    <input v-model="definition.to" style="max-width: 120px;"/>
                                    <a class="btn btn-primary" v-on:click="addUserSynonym('to', definition.to)"
                                    style="padding: 1px 4px;">Add</a>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12" v-if="ch_name != 'Color'" style="margin-top: 10px;">
                    <div class="row">
                        <label class="col-md-3 text-right">Include:</label>
                        <div class="col-md-8"> 
                            <div class="selectDataList">
                                <input :disabled="viewFlag || edit_created_other || editFlag" v-on:blur="userLog('Include')" class="width-100" v-model="methodInclude" @keyup="saveMeasureItems" name="methodInclude"/>
                                <!-- <datalist id="include_characters">
                                    <option :value="noun_character" v-for="noun_character in noun_characters">{{noun_character}}</option>
                                </datalist>   -->
                                <ul>
                                    <li v-on:click="handleMethodInclude(nounCharacter)" :value="nounCharacter" v-for="nounCharacter in resultMethodIncludeQuery">{{nounCharacter}}
                                    </li>
                                </ul>
                            </div> 
                        </div>
                        <p class="col-md-1" v-if="includeId != null || greenTick.include == true" style="color: green;">&#10004;</p>
                    </div>

                    <div class="row">
                        <a v-if="includeNeedMore == true" class="red col-md-12"
                        v-bind:class="{ green: needMoreGreen.include }" v-on:mouseover="needMore('include')">Need info on new
                            terms:</a>
                    </div>

                    <div class="col-md-12" v-if="formViewFlag.include == true">
                        <div class="row">
                            <div class="col-md-12" v-if="includeSynonyms.length > 0">
                                {{ methodInclude }}: is a synonym of
                            </div>
                            <div class="col-md-12" v-if="includeSynonyms.length > 0">
                                <a class="btn btn-basic" v-on:click="addSynonym('include', each)" v-tooltip="each.tooltip"
                                v-for="(each, index) in includeSynonyms" :key="index">
                                    {{ each.term }}
                                </a>
                                <a class="col-md-12 btn btn-basic" v-on:click="noneSynonym('include')">None of above, add
                                    the term to Dictionary</a>
                            </div>
                            <div class="col-md-12" v-if="noneSynonymFlag.include == true">
                                <div>
                                    Add the following to the dictionary:
                                </div>
                                <a class="btn btn-primary col-md-4" v-on:click="addTerm('include', methodInclude)"
                                style="padding: 3px 8px;"> {{ methodInclude }} </a>
                                <!-- <div class="col-md-7"
                                    style="border: 1px solid grey; border-radius: 4px; margin-left: 10px;">
                                    <input v-model="definition.include" style="max-width: 120px;"/>
                                    <a class="btn btn-primary" v-on:click="addUserSynonym('from', definition.include)"
                                    style="padding: 1px 4px;">Add</a>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12" v-if="ch_name != 'Color'" style="margin-top: 10px;">
                    <div class="row">
                        <label class="col-md-3 text-right">Exclude:</label>
                        <div class="col-md-8">
                            <div class="selectDataList">
                                <input :disabled="viewFlag || edit_created_other || editFlag" v-on:blur="userLog('Exclude')" class="width-100" v-model="methodExclude" @keyup="saveMeasureItems" name="methodExclude"/>
                                <!-- <datalist id="exclude_characters">
                                    <option :value="noun_character" v-for="noun_character in noun_characters">{{noun_character}}</option>
                                </datalist>   -->
                                <ul>
                                    <li v-on:click="handleMethodExclude(nounCharacter)" :value="nounCharacter" v-for="nounCharacter in resultMethodExcludeQuery">{{nounCharacter}}
                                    </li>
                                </ul>  
                            </div> 
                        </div>

                        <p class="col-md-1" v-if="excludeId != null || greenTick.exclude == true" style="color: green;">&#10004;</p>
                    </div>

                    <a v-if="excludeNeedMore == true" class="red col-md-12"
                       v-bind:class="{ green: needMoreGreen.exclude }" v-on:mouseover="needMore('exclude')">Need info on new
                        terms:</a>
                    <div class="col-md-12" v-if="formViewFlag.exclude == true">

                        <div class="col-md-12" v-if="excludeSynonyms.length > 0">
                            {{ methodExclude }}: is a synonym of
                        </div>

                        <div class="col-md-12" v-if="excludeSynonyms.length > 0">
                            <a class="btn btn-basic" v-on:click="addSynonym('exclude', each)" v-tooltip="each.tooltip"
                               v-for="(each, index) in excludeSynonyms" :key="index">
                                {{ each.term }}
                            </a>
                            <a class="col-md-12 btn btn-basic" v-on:click="noneSynonym('exclude')">None of above, add
                                the term to Dictionary</a>
                        </div>
                        <div class="col-md-12" v-if="noneSynonymFlag.exclude == true">
                            <div>
                                Add the following to the dictionary:
                            </div>
                            <a class="btn btn-primary col-md-4" v-on:click="addTerm('exclude', methodExclude)"
                               style="padding: 3px 8px;"> {{ methodExclude }} </a>
                            <!-- <div class="col-md-7"
                                 style="border: 1px solid grey; border-radius: 4px; margin-left: 10px;">
                                <input v-model="definition.exclude" style="max-width: 120px;"/>
                                <a class="btn btn-primary" v-on:click="addUserSynonym('from', definition.exclude)"
                                   style="padding: 1px 4px;">Add</a>
                            </div> -->
                        </div>
                    </div>
                </div>

                <div class="col-md-12" style="margin-top: 10px;">
                    <div class="row">
                        <label class="col-md-3 text-right">Where:</label>
                        <div class="selectDataList col-md-8"> 
                            <div class="selectDataList">
                                <input :disabled="viewFlag || edit_created_other || editFlag" v-on:blur="userLog('Where')" class="width-100" v-model="methodWhere" @keyup="saveMeasureItems" name="methodWhere"/>
                                <!-- <datalist id="where_characters">
                                    <option :value="noun_character" v-for="noun_character in noun_characters">{{noun_character}}</option>
                                </datalist>   -->
                                <ul>
                                    <li v-on:click="handleMethodWhere(nounCharacter)" :value="nounCharacter" v-for="nounCharacter in resultMethodWhereQuery">{{nounCharacter}}
                                    </li>
                                </ul> 
                            </div>
                        </div>  

                        <p class="col-md-1" v-if="whereId != null || greenTick.where == true" style="color: green;">&#10004;</p>
                    </div>
                    <a v-if="whereNeedMore == true" class="red col-md-12" v-bind:class="{ green: needMoreGreen.where }"
                       v-on:mouseover="needMore('where')">Need info on new terms:</a>
                    <div class="col-md-12" v-if="formViewFlag.where == true">
                        <div class="col-md-12" v-if="whereSynonyms.length > 0">
                            {{ methodWhere }}: is a synonym of
                        </div>
                        <div class="col-md-12" v-if="whereSynonyms.length > 0">
                            <a class="btn btn-basic" v-on:click="addSynonym('where', each)" v-tooltip="each.tooltip"
                               v-for="(each, index) in whereSynonyms" :key="index">
                                {{ each.term }}
                            </a>
                            <a class="col-md-12 btn btn-basic" v-on:click="noneSynonym('where')">None of above, add the
                                term to Dictionary</a>
                        </div>
                        <div class="col-md-12" v-if="noneSynonymFlag.where == true">
                            <div>
                                Add the following to the dictionary:
                            </div>
                            <a class="btn btn-primary col-md-4" v-on:click="addTerm('where', methodWhere)"
                               style="padding: 3px 8px;"> {{ methodWhere }} </a>
                            <!-- <div class="col-md-7"
                                 style="border: 1px solid grey; border-radius: 4px; margin-left: 10px;">
                                <input v-model="definition.at" style="max-width: 120px;"/>
                                <a class="btn btn-primary" v-on:click="addUserSynonym('at', definition.at)"
                                   style="padding: 1px 4px;">Add</a>
                            </div> -->
                        </div>
                    </div>
                </div>
                <!--<div class="col-md-12 text-right">-->
                    <!--<a class="btn btn-primary" v-on:click="checkDictionary()">Check</a>-->
                <!--</div>-->
                <div v-if="modalFlag == true" @close="modalFlag = false">
                    <transition name="modal">
                        <div class="modal-mask">
                            <div class="modal-wrapper">
                                <div class="modal-container">
                                    <div class="modal-header">
                                        Adding "{{ newTerm }}" to Dictionary
                                    </div>
                                    <div class="modal-body">
                                        Enter a definition for the term?
                                        <input v-model="newTermDefinition" style="min-width: 350px;"/>
                                    </div>
                                    <div class="modal-footer">
                                        <a class="btn btn-primary" v-on:click="confirmTerm(newTermDefinition)">Add</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import Vue from 'vue';
    import VTooltip from 'v-tooltip'


    Vue.use(VTooltip);
    export default {
        data: function () {
            return {
                shared: true,
                childData: [],
                character_name: null,
                ch_name: null,
                viewFlag: false,
                edit_created_other: true,
                editFlag: false,
                methodEntry: null,
                noneMethod: false,
                methodFrom: null,
                methodTo: null,
                methodInclude: null,
                methodExclude: null,
                methodWhere: null,
                fromId: null,
                fromTerm: null,
                toId: null,
                toTerm: null,
                includeTerm: null,
                includeId: null,
                excludeTerm: null,
                excludeId: null,
                whereTerm: null,
                whereId: null,
                fromNeedMore: false,
                toNeedMore: false,
                includeNeedMore: false,
                excludeNeedMore: false,
                whereNeedMore: false,
                fromSynonyms: [],
                toSynonyms: [],
                includeSynonyms: [],
                excludeSynonyms: [],
                whereSynonyms: [],
                formViewFlag: {
                    from: false,
                    to: false,
                    include: false,
                    exclude: false,
                    where: false
                },
                greenTick: {
                    from: false,
                    to: false,
                    include: false,
                    exclude: false,
                    where: false
                },
                definition: {
                    from: null,
                    to: null,
                    include: null,
                    exclude: null,
                    where: null
                },
                noneSynonymFlag: {
                    from: false,
                    to: false,
                    include: false,
                    exclude: false,
                    where: false
                },
                needMoreGreen: {
                    from: false,
                    to: false,
                    include: false,
                    exclude: false,
                    where: false
                },
                methodArray: [],
                modalFlag: false,
                newTerm: null,
                newTermDefinition: null,
                illustratorProperty: [

                ],
                method_description: '',
                sharedFlag: true,
            }
        },
        props: {
            parentData: {
                type: Array,
                default () {
                    return []
                }
            }
        },
        computed: {
          resultMethodFromQuery(){
            if(this.methodFrom){
            return this.noun_characters.filter((item)=>{
              return this.methodFrom.toLowerCase().split(' ').every(v => item.toLowerCase().includes(v))
            })
            }else{
              return this.noun_characters;
            }
          },
          resultMethodToQuery(){
            if(this.methodTo){
            return this.noun_characters.filter((item)=>{
              return this.methodTo.toLowerCase().split(' ').every(v => item.toLowerCase().includes(v))
            })
            }else{
              return this.noun_characters;
            }
          },
          resultMethodIncludeQuery(){
            if(this.methodInclude){
            return this.noun_characters.filter((item)=>{
              return this.methodInclude.toLowerCase().split(' ').every(v => item.toLowerCase().includes(v))
            })
            }else{
              return this.noun_characters;
            }
          },
          resultMethodExcludeQuery(){
            if(this.methodExclude){
            return this.noun_characters.filter((item)=>{
              return this.methodExclude.toLowerCase().split(' ').every(v => item.toLowerCase().includes(v))
            })
            }else{
              return this.noun_characters;
            }
          },
          resultMethodWhereQuery(){
            if(this.methodWhere){
            return this.noun_characters.filter((item)=>{
              return this.methodWhere.toLowerCase().split(' ').every(v => item.toLowerCase().includes(v))
            })
            }else{
              return this.noun_characters;
            }
          },
        },
        methods: {
            handleMethodFrom(val){
              this.methodFrom = val;
            },
            // maybe onchagne may onclick whatever..
            handleDataFc: function () {
                this.$emit('interface', this.childData); // handle data and give it back to parent by interface
            },
            noneSynonym: function (setting) {
                var app = this;
                app.noneSynonymFlag[setting] = true;
            },
            needMore: function (setting) {
                var app = this;
                app.formViewFlag[setting] = true; //!(app.formViewFlag[setting]);
            },
            addTerm: function (setting, value) {
                var app = this;
                app.newTerm = value;
                app.currentSetting = setting;
                app.modalFlag = true;

            },
            addUserSynonym: function (setting, definition) {
                var app = this;
                app.newTerm = definition;
                app.currentSetting = setting;
                app.modalFlag = true;
            },
            confirmTerm: function (definition) {
                var app = this;

                var jsonRequest = {
                    user: app.sharedFlag? '' : app.childData[3].name,
                    ontology: 'carex',
                    term: app.newTerm,
                    superclassIRI: "http://biosemantics.arizona.edu/ontology/carex#anatomical_structure",
                    definition: definition,
                    elucidation: '',
                    createdBy: app.childData[3].name + ' via MR',
                    examples: app.character_name,
                    creationDate: new Date(),
                    definitionSrc: app.childData[3].name,
                    logicDefinition: ''
                };

                app.greenTick[app.currentSetting] = true;
                if (!app.childData[10]){
                    app.childData[10] = {};
                }
                app.childData[10][app.currentSetting] = true;
                app.handleDataFc();
                app.formViewFlag[app.currentSetting] = false;
                app.needMoreGreen[app.currentSetting] = true;
                app.newTermDefinition = null;
                axios.post('http://shark.sbs.arizona.edu:8080/class', jsonRequest)
                    .then(async function (resp) {
                        console.log('class resp', resp);
                        axios.post('http://shark.sbs.arizona.edu:8080/save', {"user": app.sharedFlag? '': app.childData[3].name, "ontology": 'carex'})
                            .then(function (resp) {
                                console.log('save resp', resp);
                            });

                    });
                app.modalFlag = false;
            },
            addSynonym: function (setting, value) {
                var app = this;
                console.log('value', value);
                console.log("shared", app.sharedFlag);

                var jsonRequest = {
                    user: app.sharedFlag? '' : app.childData[3].name,
                    ontology: 'carex',
                };
                var temp = value.resultAnnotations.filter(function (e) {
                    return e.property == "http://www.geneontology.org/formats/oboInOwl#id";
                });

                switch (setting) {
                    case 'from':
                        jsonRequest.term = app.methodFrom;
                        break;
                    case 'to':
                        jsonRequest.term = app.methodTo;
                        break;
                    case 'include':
                        jsonRequest.term = app.methodInclude;
                        break;
                    case 'exclude':
                        jsonRequest.term = app.methodExclude;
                        break;
                    case 'where':
                        jsonRequest.term = app.methodWhere;
                        break;
                    default:
                        break;
                }
                var splitValue = value.term.split('(');
                console.log('splitValue', splitValue);
                if (splitValue.length > 1) {
                    console.log('***', splitValue[1].substring(0, 7));
                    if (splitValue[1].substring(0, 7) == 'part_of') {
                        jsonRequest.superclassIRI = "http://biosemantics.arizona.edu/ontology/carex#anatomical-structure";
                        jsonRequest.definition = '';
                        jsonRequest.elucidation = '';
                        jsonRequest.createdBy = app.childData[3].name + ' via CR';
                        jsonRequest.examples = app.character_name;
                        jsonRequest.creationDate = new Date();
                        jsonRequest.definitionSrc = app.childData[3].name;
                        jsonRequest.logicDefinition = value.term;
                        axios.post('http://shark.sbs.arizona.edu:8080/class', jsonRequest)
                            .then(function (resp) {
                                console.log('class resp', resp);
                                app.greenTick[setting] = true;
                                app.formViewFlag[setting] = false;
                                app.needMoreGreen[setting] = true;
                                axios.post('http://shark.sbs.arizona.edu:8080/save', {"user":  app.sharedFlag? '': app.childData[3].name, "ontology": 'carex'})
                                    .then(function (resp) {
                                        console.log('save resp', resp);
                                    });

                            });
                    }
                } else {
                    if (temp.length > 0) {
                        jsonRequest.classIRI = temp[0].value;
                    }

                    axios.post('http://shark.sbs.arizona.edu:8080/esynonym', jsonRequest)
                        .then(function (resp) {
                            console.log('esynonym resp', resp);
                            if (resp.data == 'SUCCESSFULLY') {
                                app.greenTick[setting] = true;
                                app.formViewFlag[setting] = false;
                                app.needMoreGreen[setting] = true;
                            }
                            axios.post('http://shark.sbs.arizona.edu:8080/save', {"user":  app.sharedFlag? '': app.childData[3].name, "ontology": 'carex'})
                                .then(function (resp) {
                                    console.log('save resp', resp);
                                });
                        });
                }
            },
            clickedMethod(index, value) {
                var app = this;
                console.log('index', index);
                console.log('value', value);
                if (app.viewFlag == false) {
                    $('.img-method').css('border', 'none');
                    $('#img-method-' + index).css('border', 'solid 1px green');
                    console.log('value', value);
                    app.childData[0] = value;
                    app.childData[1] = app.methodEntry.term;
                    app.childData[2] = app.methodEntry.resultAnnotations.filter(function (e) {
                        return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                    })[0].value;
                    app.childData[4] = app.methodFrom;
                    app.childData[5] = app.methodTo;
                    app.childData[6] = app.methodInclude;
                    app.childData[7] = app.methodExclude;
                    app.childData[8] = app.methodWhere;

                    console.log('childData', app.childData);

                    app.$emit('interface', app.childData);
                }


            },
            noneOfAbove() {
                var app = this;
                console.log(app.noneMethod);
                console.log(app.methodArray);
                console.log(app.edit_created_other);
                console.log(app.editFlag);
                app.noneMethod = true;
            },
            displayImageSection() {
                var app = this;
                app.noneMethod = false;
            },
            userLog(setting) {
                var app = this;

                app.childData[4] = app.methodFrom;
                app.childData[5] = app.methodTo;
                app.childData[6] = app.methodInclude;
                app.childData[7] = app.methodExclude;
                app.childData[8] = app.methodWhere;

                this.$emit('interface', app.childData);

                let t_action_detail = '';
                switch (setting) {
                    case 'From':
                        t_action_detail = app.methodFrom;
                        break;
                    case 'To':
                        t_action_detail = app.methodTo;
                        break;
                    case 'Include':
                        t_action_detail = app.methodInclude;
                        break;
                    case 'Exclude':
                        t_action_detail = app.methodExclude;
                        break;
                    case 'Where':
                        t_action_detail = app.methodWhere;
                        break;
                    default:
                        break;
                }
            },
//            checkDictionary() {
//                var app = this;
//                if (app.viewFlag == false) {
//                    app.childData[0] = '';
//                    app.childData[1] = '';
//                    app.childData[2] = '';
//                    app.childData[4] = app.methodFrom;
//                    app.childData[5] = app.methodTo;
//                    app.childData[6] = app.methodInclude;
//                    app.childData[7] = app.methodExclude;
//                    app.childData[8] = app.methodWhere;
//                    app.fromTerm = null;
//                    app.fromId = null;
//                    app.toTerm = null;
//                    app.toId = null;
//                    app.includeTerm = null;
//                    app.includeId = null;
//                    app.excludeTerm = null;
//                    app.excludeId = null;
//                    app.whereTerm = null;
//                    app.whereId = null;
//                    app.greenTick.from = false;
//                    app.greenTick.to = false;
//                    app.greenTick.include = false;
//                    app.greenTick.exclude = false;
//                    app.greenTick.where = false;
//                    app.fromNeedMore = false;
//                    app.toNeedMore = false;
//                    app.includeNeedMore = false;
//                    app.excludeNeedMore = false;
//                    app.whereNeedMore = false;
//                    app.needMoreGreen.from = false;
//                    app.needMoreGreen.to = false;
//                    app.needMoreGreen.include = false;
//                    app.needMoreGreen.exclude = false;
//                    app.needMoreGreen.where = false;
//                    console.log('app.methodEntry',app.methodEntry);
//                    app.childData[0] = '';
//                    app.childData[4] = app.methodFrom;
//                    app.childData[5] = app.methodTo;
//                    app.childData[6] = app.methodInclude;
//                    app.childData[7] = app.methodExclude;
//                    app.childData[8] = app.methodWhere;
//
//                    console.log('childData', app.childData);
//
//                    console.log('interface console', app.childData);
//
//                    this.$emit('interface', this.childData); // handle data and give it back to parent by interface
//
//                    axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.character_name)
//                        .then(function (resp) {
//                            console.log('search resp', resp);
//                            var tempFlag = false;
//                            for (var i = 0; i < resp.data.entries.length; i++) {
//                                if (resp.data.entries[i].term == app.character_name) {
//                                    tempFlag = true;
//                                }
//                            }
//                            if (app.methodFrom != null && app.methodFrom != '') {
//                                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.methodFrom)
//                                    .then(function (resp) {
//                                        console.log('search from resp', resp);
//                                        for (var i = 0; i < resp.data.entries.length; i++) {
//                                            if (resp.data.entries[i].score == 1) {
//                                                app.fromTerm = resp.data.entries[i].term;
//                                                app.fromId = resp.data.entries[i].resultAnnotations.filter(function (e) {
//                                                    return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
//                                                })[0].value;
//                                                console.log('fromId', app.fromId);
//                                                break;
//                                            }
//                                        }
//                                        if (app.fromId == null) {
//                                            app.fromNeedMore = true;
//                                            app.fromSynonyms = resp.data.entries;
//                                            if (app.fromSynonyms.length == 0) {
//                                                app.noneSynonymFlag.from = true;
//                                            }
//                                            for (var i = 0; i < app.fromSynonyms.length; i++) {
//                                                app.fromSynonyms[i].tooltip = '';
//                                                var temp = app.fromSynonyms[i].resultAnnotations.filter(function (e) {
//                                                    return e.property == 'http://purl.oblibrary.org/obo/IAO_0000115';
//                                                });
//                                                if (temp.length > 0) {
//                                                    app.fromSynonyms[i].tooltip = temp[0].value;
//                                                }
//                                            }
//                                        }
//                                    });
//                            }
//                            if (app.methodTo != null && app.methodTo != '') {
//                                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.methodTo)
//                                    .then(function (resp) {
//                                        console.log('search to resp', resp);
//                                        for (var i = 0; i < resp.data.entries.length; i++) {
//                                            if (resp.data.entries[i].score == 1) {
//                                                app.toTerm = resp.data.entries[i].term;
//                                                app.toId = resp.data.entries[i].resultAnnotations.filter(function (e) {
//                                                    return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
//                                                })[0].value;
//                                                console.log('toId', app.toId);
//                                                break;
//                                            }
//                                        }
//                                        if (app.toId == null) {
//                                            app.toNeedMore = true;
//                                            app.toSynonyms = resp.data.entries;
//                                            if (app.toSynonyms.length == 0) {
//                                                app.noneSynonymFlag.to = true;
//                                            }
//                                            for (var i = 0; i < app.toSynonyms.length; i++) {
//                                                app.toSynonyms[i].tooltip = '';
//                                                var temp = app.toSynonyms[i].resultAnnotations.filter(function (e) {
//                                                    return e.property == 'http://purl.oblibrary.org/obo/IAO_0000115';
//                                                });
//                                                if (temp.length > 0) {
//                                                    app.toSynonyms[i].tooltip = temp[0].value;
//                                                }
//                                            }
//                                        }
//                                    });
//                            }
//                            if (app.methodInclude != null && app.methodInclude != '') {
//                                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.methodInclude)
//                                    .then(function (resp) {
//                                        console.log('search include resp', resp);
//                                        for (var i = 0; i < resp.data.entries.length; i++) {
//                                            if (resp.data.entries[i].score == 1) {
//                                                app.includeTerm = resp.data.entries[i].term;
//                                                app.includeId = resp.data.entries[i].resultAnnotations.filter(function (e) {
//                                                    return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
//                                                })[0].value;
//                                                console.log('includeId', app.includeId);
//                                                break;
//                                            }
//                                        }
//                                        if (app.includeId == null) {
//                                            app.includeNeedMore = true;
//                                            app.includeSynonyms = resp.data.entries;
//                                            if (app.includeSynonyms.length == 0) {
//                                                app.noneSynonymFlag.include = true;
//                                            }
//                                            for (var i = 0; i < app.includeSynonyms.length; i++) {
//                                                app.includeSynonyms[i].tooltip = '';
//                                                var temp = app.includeSynonyms[i].resultAnnotations.filter(function (e) {
//                                                    return e.property == 'http://purl.oblibrary.org/obo/IAO_0000115';
//                                                });
//                                                if (temp.length > 0) {
//                                                    app.includeSynonyms[i].tooltip = temp[0].value;
//                                                }
//                                            }
//                                        }
//                                    });
//                            }
//                            if (app.methodExclude != null && app.methodExclude != '') {
//                                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.methodExclude)
//                                    .then(function (resp) {
//                                        console.log('search exclude resp', resp);
//                                        for (var i = 0; i < resp.data.entries.length; i++) {
//                                            if (resp.data.entries[i].score == 1) {
//                                                app.excludeTerm = resp.data.entries[i].term;
//                                                app.excludeId = resp.data.entries[i].resultAnnotations.filter(function (e) {
//                                                    return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
//                                                })[0].value;
//                                                console.log('excludeId', app.excludeId);
//                                                break;
//                                            }
//                                        }
//                                        if (app.excludeId == null) {
//                                            app.excludeNeedMore = true;
//                                            app.excludeSynonyms = resp.data.entries;
//                                            if (app.excludeSynonyms.length == 0) {
//                                                app.noneSynonymFlag.exclude = true;
//                                            }
//                                            for (var i = 0; i < app.excludeSynonyms.length; i++) {
//                                                app.excludeSynonyms[i].tooltip = '';
//                                                var temp = app.excludeSynonyms[i].resultAnnotations.filter(function (e) {
//                                                    return e.property == 'http://purl.oblibrary.org/obo/IAO_0000115';
//                                                });
//                                                if (temp.length > 0) {
//                                                    app.excludeSynonyms[i].tooltip = temp[0].value;
//                                                }
//                                            }
//                                        }
//                                    });
//                            }
//                            if (app.methodWhere != null && app.methodWhere != '') {
//                                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.methodWhere)
//                                    .then(function (resp) {
//                                        console.log('search at resp', resp);
//                                        for (var i = 0; i < resp.data.entries.length; i++) {
//                                            if (resp.data.entries[i].score == 1) {
//                                                app.whereTerm = resp.data.entries[i].term;
//                                                app.whereId = resp.data.entries[i].resultAnnotations.filter(function (e) {
//                                                    return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
//                                                })[0].value;
//                                                console.log('whereId', app.whereId);
//                                                break;
//                                            }
//                                        }
//                                        if (app.whereId == null) {
//                                            app.whereNeedMore = true;
//                                            app.whereSynonyms = resp.data.entries;
//                                            if (app.whereSynonyms.length == 0) {
//                                                app.noneSynonymFlag.at = true;
//                                            }
//                                            for (var i = 0; i < app.whereSynonyms.length; i++) {
//                                                app.whereSynonyms[i].tooltip = '';
//                                                var temp = app.whereSynonyms[i].resultAnnotations.filter(function (e) {
//                                                    return e.property == 'http://purl.oblibrary.org/obo/IAO_0000115';
//                                                });
//                                                if (temp.length > 0) {
//                                                    app.whereSynonyms[i].tooltip = temp[0].value;
//                                                }
//                                            }
//                                        }
//                                    });
//                            }
//
//                            if (tempFlag == false) {
//
//                                var jsonClass = {
//                                    "user":  app.sharedFlag? '': app.childData[3].name,
//                                    "ontology": 'carex',
//                                    "term": app.character_name,
//                                    "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#toreview",
//                                    "definition": '',
//                                    "createdBy": app.childData[3].name,
//                                    "creationDate": new Date(),
//                                    "definitionSrc": "tba",
//                                    "examples": "tba",
//                                    "logicDefinition": "measured_from some [" + app.methodFrom + "] and measured_to some [" + app.methodTo + "]"
//                                };
//                                if (app.methodFrom != null) {
//                                    jsonClass.definition = jsonClass.definition + 'from [' + app.methodFrom + ']';
//                                }
//                                if (app.methodTo != null) {
//                                    jsonClass.definition = jsonClass.definition + ' to [' + app.methodTo + ']';
//                                }
//                                if (app.methodInclude != null) {
//                                    jsonClass.definition = jsonClass.definition + ' include [' + app.methodInclude + ']';
//                                }
//                                if (app.methodExclude != null) {
//                                    jsonClass.definition = jsonClass.definition + ' exclude [' + app.methodExclude + ']';
//                                }
//                                if (app.methodWhere != null) {
//                                    jsonClass.definition = jsonClass.definition + ' where [' + app.methodWhere + ']';
//                                }
//                                if (app.character_name.split(' ')[0] == 'distance') {
//                                    jsonClass.superclassIRI = "http://biosemantics.arizona.edu/ontologies/carex#perceived-distance"
//                                } else if (app.character_name.split(' ')[0] == 'length') {
//                                    jsonClass.superclassIRI = "http://biosemantics.arizona.edu/ontologies/carex#perceived-length"
//
//                                } else if (app.character_name.split(' ')[0] == 'width') {
//                                    jsonClass.superclassIRI = "http://biosemantics.arizona.edu/ontologies/carex#perceived-width"
//                                }
//                                axios.post('http://shark.sbs.arizona.edu:8080/class', jsonClass)
//                                    .then(function (resp) {
//                                        console.log('class resp', resp);
//                                        axios.post('http://shark.sbs.arizona.edu:8080/save', {
//                                            "user":  app.sharedFlag? '': app.childData[3].name,
//                                            "ontology": 'carex'
//                                        })
//                                            .then(function (resp) {
//                                                console.log('save resp', resp);
//                                            });
//                                    })
//                                    .catch(function (resp) {
//                                        console.log('class error resp', resp);
//                                    });
//                            } else {
//                                var jsonClass = {
//                                    "user":  app.sharedFlag? '': app.childData[3].name,
//                                    "ontology": 'carex',
//                                    "term": app.character_name + '(' + app.childData[3].name + ')',
//                                    "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#toreview",
//                                    "definition": '',
//                                    "createdBy": app.childData[3].name,
//                                    "creationDate": new Date(),
//                                    "definitionSrc": "tba",
//                                    "examples": "tba",
//                                    "logicDefinition": "measured_from some [" + app.methodFrom + "] and measured_to some [" + app.methodTo + "]"
//                                };
//
//                                if (app.methodFrom != null) {
//                                    jsonClass.definition = jsonClass.definition + 'from [' + app.methodFrom + ']';
//                                }
//                                if (app.methodTo != null) {
//                                    jsonClass.definition = jsonClass.definition + ' to [' + app.methodTo + ']';
//                                }
//                                if (app.methodInclude != null) {
//                                    jsonClass.definition = jsonClass.definition + ' include [' + app.methodInclude + ']';
//                                }
//                                if (app.methodExclude != null) {
//                                    jsonClass.definition = jsonClass.definition + ' exclude [' + app.methodExclude + ']';
//                                }
//                                if (app.methodWhere != null) {
//                                    jsonClass.definition = jsonClass.definition + ' where [' + app.methodWhere + ']';
//                                }
//                                if (app.character_name.split(' ')[0] == 'distance') {
//                                    jsonClass.superclassIRI = "http://biosemantics.arizona.edu/ontologies/carex#perceived-distance"
//                                } else if (app.character_name.split(' ')[0] == 'length') {
//                                    jsonClass.superclassIRI = "http://biosemantics.arizona.edu/ontologies/carex#perceived-length"
//
//                                } else if (app.character_name.split(' ')[0] == 'width') {
//                                    jsonClass.superclassIRI = "http://biosemantics.arizona.edu/ontologies/carex#perceived-width"
//                                }
//
//                                axios.post('http://shark.sbs.arizona.edu:8080/class', jsonClass)
//                                    .then(function (resp) {
//                                        console.log('class resp', resp);
//                                        axios.post('http://shark.sbs.arizona.edu:8080/save', {
//                                            "user":  app.sharedFlag? '': app.childData[3].name,
//                                            "ontology": 'carex'
//                                        })
//                                            .then(function (resp) {
//                                                console.log('save resp', resp);
//                                            });
//                                    })
//                                    .catch(function (resp) {
//                                        console.log('class error resp', resp);
//                                    });
//                            }
//                        })
//                        .catch(function (resp) {
//
//                        });
//                }
//
//            },
            saveMeasureItems: function(event) {
                localStorage.setItem(event.target.name, event.target.value);
            }
        },
        beforeMount () {
            var app = this;
            this.character_name = sessionStorage.getItem("characterName");
            this.noun_characters = sessionStorage.getItem("nounCharacters").split(',');
            var ch = this.character_name.split(" ");
            if(ch[0] != undefined && ch[0] == 'Color') {
                app.ch_name = ch[0];
            }else {
                app.ch_name = '';
            }
            this.viewFlag = (sessionStorage.getItem('viewFlag') == 'true');
            this.editFlag = (sessionStorage.getItem('editFlag') == 'true');
            this.edit_created_other = (sessionStorage.getItem('edit_created_other')=='true');
            this.method_description = this.edit_created_other ? 'Method: The character is defined as' : 'Method: Please explain how you would measure a specimen for the character. e.g., from bottom to top.';
            console.log('edit created other -----------------', this.edit_created_other);
            this.childData = this.parentData; // save props data to itself's data and deal with it
            console.log('parentData', app.parentData);
            app.methodFrom = app.childData[4];
            app.methodTo = app.childData[5];
            app.methodInclude = app.childData[6];
            app.methodExclude = app.childData[7];
            app.methodWhere = app.childData[8];

            app.greenTick.from = false;
            app.greenTick.to = false;
            app.greenTick.include = false;
            app.greenTick.exclude = false;
            app.greenTick.where = false;

            app.needMoreGreen.from = false;
            app.needMoreGreen.to = false;
            app.needMoreGreen.include = false;
            app.needMoreGreen.exclude = false;
            app.needMoreGreen.where = false;

            app.fromTerm = app.childData[9].fromTerm;
            app.fromId = app.childData[9].fromId;
            app.toTerm = app.childData[9].toTerm;
            app.toId = app.childData[9].toId;
            app.includeTerm = app.childData[9].includeTerm;
            app.includeId = app.childData[9].includeId;
            app.excludeTerm = app.childData[9].excludeTerm;
            app.excludeId = app.childData[9].excludeId;
            app.whereTerm = app.childData[9].whereTerm;
            app.whereId = app.childData[9].whereId;
            app.fromNeedMore = app.childData[9].fromNeedMore;
            app.toNeedMore = app.childData[9].toNeedMore;
            app.includeNeedMore = app.childData[9].includeNeedMore;
            app.excludeNeedMore = app.childData[9].excludeNeedMore;
            app.whereNeedMore = app.childData[9].whereNeedMore;
            app.fromSynonyms = app.childData[9].fromSynonyms;
            app.toSynonyms = app.childData[9].toSynonyms;
            app.includeSynonyms = app.childData[9].includeSynonyms;
            app.excludeSynonyms = app.childData[9].excludeSynonyms;
            app.whereSynonyms = app.childData[9].whereSynonyms;
            app.noneSynonymFlag = app.childData[9].noneSynonymFlag;

            axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.character_name.toLowerCase())
                .then(function (resp) {
                    console.log('exp search resp', resp);
                    if (resp.data.entries.length > 0) {
                        app.methodEntry = resp.data.entries.filter(function(each) {
                            return each.resultAnnotations.some(e => e.property === "http://biosemantics.arizona.edu/ontologies/carex#elucidation") == true;
                        })[0];
                        if (app.methodEntry) {
                            app.methodArray = app.methodEntry.resultAnnotations.filter(function (e) {
                                return e.property == 'http://biosemantics.arizona.edu/ontologies/carex#elucidation';
                            });
                            console.log('methodArray', app.methodArray);
                            if (app.methodArray.length > 0 && app.childData[0] != null) {
                                for (var i = 0; i < app.methodEntry.resultAnnotations.length; i++) {
                                    if (app.methodEntry.resultAnnotations[i].value.substring(1,app.methodEntry.resultAnnotations[i].value.length  - 1) == app.childData[0]) {
                                        app.illustratorProperty[i] = true;
                                    }
                                }
                            }
                            for (var i = 0; i < app.methodEntry.resultAnnotations.length; i++) {
                                if (app.methodEntry.resultAnnotations[i].property === 'http://biosemantics.arizona.edu/ontologies/carex#elucidation') {
                                    app.methodEntry.resultAnnotations[i].clickMethodValue = app.methodEntry.resultAnnotations[i].value.substring(1, app.methodEntry.resultAnnotations[i].value.length - 1);
                                    if (app.methodEntry.resultAnnotations[i].value.indexOf('id=') < 0) {
                                        app.methodEntry.resultAnnotations[i].src = app.methodEntry.resultAnnotations[i].value.slice(app.methodEntry.resultAnnotations[i].value.indexOf('file/d/') + 7, app.methodEntry.resultAnnotations[i].value.indexOf('/view?usp='));
                                    } else {
                                        app.methodEntry.resultAnnotations[i].src = app.methodEntry.resultAnnotations[i].value.split('id=')[1].substring(0, app.methodEntry.resultAnnotations[i].value.split('id=')[1].length - 1);
                                    }
                                    console.log(app.methodEntry.resultAnnotations[i].src);
                                }
                            }
                            console.log('methodEntry', app.methodEntry);
                        } else {
                            app.methodEntry = true;
                            app.methodArray = resp.data.entries;
                        }

                    } else {
                        app.methodEntry = true;
                        app.methodArray = resp.data.entries;
                        console.log('methodArray', app.methodArray);
                    }
                    if (app.methodFrom != null) {
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.methodFrom)
                            .then(function (resp) {
                                console.log('search from resp', resp);
                                for (var i = 0; i < resp.data.entries.length; i++) {
                                    if (resp.data.entries[i].score == 1) {
                                        app.fromTerm = resp.data.entries[i].term;
                                        app.fromId = resp.data.entries[i].resultAnnotations.filter(function (e) {
                                            return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                        })[0].value;
                                        console.log('fromId', app.fromId);
                                        break;
                                    }
                                }
                            });
                    }
                    if (app.methodTo != null) {
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.methodTo)
                            .then(function (resp) {
                                console.log('search to resp', resp);
                                for (var i = 0; i < resp.data.entries.length; i++) {
                                    if (resp.data.entries[i].score == 1) {
                                        app.toTerm = resp.data.entries[i].term;
                                        app.toId = resp.data.entries[i].resultAnnotations.filter(function (e) {
                                            return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                        })[0].value;
                                        console.log('toId', app.toId);
                                        break;
                                    }
                                }
                            });
                    }
                    if (app.methodInclude != null) {
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.methodInclude)
                            .then(function (resp) {
                                console.log('search to resp', resp);
                                for (var i = 0; i < resp.data.entries.length; i++) {
                                    if (resp.data.entries[i].score == 1) {
                                        app.includeTerm = resp.data.entries[i].term;
                                        app.includeId = resp.data.entries[i].resultAnnotations.filter(function (e) {
                                            return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                        })[0].value;
                                        console.log('includeId', app.includeId);
                                        break;
                                    }
                                }
                            });
                    }
                    if (app.methodExclude != null) {
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.methodExclude)
                            .then(function (resp) {
                                console.log('search to resp', resp);
                                for (var i = 0; i < resp.data.entries.length; i++) {
                                    if (resp.data.entries[i].score == 1) {
                                        app.excludeTerm = resp.data.entries[i].term;
                                        app.excludeId = resp.data.entries[i].resultAnnotations.filter(function (e) {
                                            return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                        })[0].value;
                                        console.log('excludeId', app.excludeId);
                                        break;
                                    }
                                }
                            });
                    }
                    if (app.methodWhere != null) {
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.methodWhere)
                            .then(function (resp) {
                                console.log('search to resp', resp);
                                for (var i = 0; i < resp.data.entries.length; i++) {
                                    if (resp.data.entries[i].score == 1) {
                                        app.whereTerm = resp.data.entries[i].term;
                                        app.whereId = resp.data.entries[i].resultAnnotations.filter(function (e) {
                                            return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                        })[0].value;
                                        console.log('whereId', app.whereId);
                                        break;
                                    }
                                }
                            });
                    }
                })
                .catch(function (resp) {
                    console.log('exp search resp error', resp);
                });
        },
        mounted() {
        }
    }
</script>

