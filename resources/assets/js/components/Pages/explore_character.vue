<template>
    <div class="container" style="width: 90%">
        <center>
            <div>
                <h3>
                    Explore Character Data
                </h3>
            </div>
            <div>
                <a class="btn btn-primary" style = "float: left; padding: 2px;" href="/chrecorder/public"><span class="glyphicon glyphicon-menu-left" style="font-Size: 25px; padding-top:2px;"></span></a>
            </div>
            <div style="margin-left: 35px">
                <div class="row">
                    <div class="col-md-3">
                        <a v-on:click="handleUsedBy()" class="btn btn-primary" style="width: 100%; margin: 10px" :class="{disabled: searchType == 0}">Find characters used by ...</a><br>
                        <div v-if="searchType == 0" style="margin-left: 10px; width: 100%;">
                            <input :disabled="bSearching" class="" v-model="username" style="width: 100%;" placeholder="Please enter an author name" v-on:keyup.enter="$event.target.blur(); exploreCharacter()"/>
                            <br>
                            <a :disabled="bSearching" v-on:click="exploreCharacter()" class="btn btn-primary" style="width: 60%; margin: 10px" :class="{disabled: username == ''}">Go</a><br>
                        </div>
                        <a v-on:click="handleAboutTaxon()" class="btn btn-primary" style="width: 100%; margin: 10px" :class="{disabled: searchType == 1}">Find characters about taxon ...</a><br>
                        <div v-if="searchType == 1" style="margin-left: 10px; width: 100%;">
                            <input :disabled="bSearching" class="" v-model="taxonName" style="width: 100%;" placeholder="Please enter a taxon" v-on:keyup.enter="$event.target.blur(); exploreCharacter()"/>
                            <br>
                            <a :disabled="bSearching" v-on:click="exploreCharacter()" class="btn btn-primary" style="width: 60%; margin: 10px" :class="{disabled: taxonName == ''}">Go</a><br>
                        </div>
                        <a v-on:click="handleOfStructure()" class="btn btn-primary" style="width: 100%; margin: 10px" :class="{disabled: searchType == 2}">Find characters of structure ...</a><br>
                        <div v-if="searchType == 2" style="margin-left: 10px; width: 100%;">
                            <input :disabled="bSearching" class="" v-model="structure" style="width: 100%;" placeholder="Please enter a structure" v-on:keyup.enter="$event.target.blur(); exploreCharacter()"/>
                            <br>
                            <tree v-if="treeFlag"
                                :data="structureTreeData"
                                :options="treeOption"
                                :filter="filterFlag?structure:null"
                                ref="nonColorTree"
                                @node:selected="onTextureTreeNodeSelected"
                                style="max-height: 300px;"
                            >
                                <div slot-scope="{ node }" class="node-container">
                                    <div class="node-text" v-tooltip="node.text">{{ node.text }}
                                    </div>
                                </div>
                            </tree>
                            <a :disabled="bSearching" v-on:click="exploreCharacter()" class="btn btn-primary" style="width: 60%; margin: 10px" :class="{disabled: (structure == '' || findCharacterByStructure)}">Go</a><br>
                        </div>
                        <a v-on:click="handleWithCharacter()" class="btn btn-primary" style="width: 100%; margin: 10px" :class="{disabled: searchType == 3}">Find structures with character ...</a><br>
                        <div v-if="searchType == 3" style="margin-left: 10px; width: 100%;">
                            <select :disabled="bSearching" style="height: 26px; width: 100%;" v-model="characterType">
                                <option value="Length">Length</option>
                                <option value="Width">Width</option>
                                <option value="Depth">Depth</option>
                                <option value="Diameter">Diameter</option>
                                <option value="Number">Number</option>
                                <!-- <option value="Distance">Distance</option> -->
                                <option value="Color">Color</option>
                                <option value="Shape">Shape</option>
                                <option value="Texture">Texture</option>
                                <option value="Growth Form">Growth Form</option>
                                <option value="Orientation">Orientation</option>
                                <option value="Pubescence">Pubescence</option>
                                <option value="Relationship">Relationship</option>
                                <option value="Duration">Duration</option>
                            </select>
                            <div v-if="checkHaveUnit(characterType)">
                                <div style="width: 100%; text-align: left; margin-top: 6px;">Value:</div>
                                <div>
                                    <select style="width: 25%; height: 29px;"
                                            v-model="compare1">
                                        <option value=">">{{">"}}</option>
                                        <option value=">=">{{">="}}</option>
                                    </select>
                                    <input type="number" :disabled="bSearching" class="" v-model="value1" style="width: 70%; margin-top: 5px" placeholder="Please enter a value"/>
                                </div>
                                <div>
                                    <select style="width: 25%; height: 29px;"
                                            v-model="compare2">
                                        <option value="<">&lt;</option>
                                        <option value="<=">&lt;=</option>
                                    </select>
                                    <input type="number" :disabled="bSearching" class="" v-model="value2" style="width: 70%; margin-top: 5px" placeholder="Please enter a value"/>
                                </div>
                                <div v-if="checkHaveUnit(characterType) && !characterType.startsWith('Count') && !characterType.startsWith('Ratio') && !characterType.startsWith('Number')">
                                    <div style="width: 100%; text-align: left; margin-top: 5px;">Unit:
                                        <div class="btn-group">
                                            <a class="btn btn-add dropdown-toggle"
                                                style="line-height: 20px; color: #3097D1;"
                                                data-toggle="dropdown">
                                                <span><b>{{ unit }}</b></span>
                                                <span class="glyphicon glyphicon-chevron-down"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </a>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a v-on:click="unit = 'm'">m</a></li>
                                                <li><a v-on:click="unit = 'dm'">dm</a></li>
                                                <li><a v-on:click="unit = 'cm'">cm</a></li>
                                                <li><a v-on:click="unit = 'mm'">mm</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="characterType && !checkHaveUnit(characterType)" v-on:click="focusValue()">
                                <div>
                                    <div style="width: 100%; text-align: left; margin-top: 6px;">negation: <b>{{(characterType == 'Color' ? currentColorValue : currentNonColorValue).negation}}</b></div>
                                </div>
                                <div>
                                    <div style="width: 100%; text-align: left; margin-top: 6px;">pre_constraint: <b>{{(characterType == 'Color' ? currentColorValue : currentNonColorValue).pre_constraint}}</b></div>
                                </div>
                                <div>
                                    <div style="width: 100%; text-align: left; margin-top: 6px;">certainty_constraint: <b>{{(characterType == 'Color' ? currentColorValue : currentNonColorValue).certainty_constraint}}</b></div>
                                </div>
                                <div>
                                    <div style="width: 100%; text-align: left; margin-top: 6px;">degree_constraint: <b>{{(characterType == 'Color' ? currentColorValue : currentNonColorValue).degree_constraint}}</b></div>
                                </div>
                                <div v-if="characterType != 'Color'">
                                    <div style="width: 100%; text-align: left; margin-top: 6px;">{{characterType}}: <b>{{currentNonColorValue.main_value}}</b></div>
                                </div>
                                <div v-if="characterType == 'Color'">
                                    <div style="width: 100%; text-align: left; margin-top: 6px;">brightness: <b>{{currentColorValue.brightness}}</b></div>
                                    
                                    <div style="width: 100%; text-align: left; margin-top: 6px;">reflectance: <b>{{currentColorValue.reflectance}}</b></div>
                                    
                                    <div style="width: 100%; text-align: left; margin-top: 6px;">saturation: <b>{{currentColorValue.saturation}}</b></div>
                                    
                                    <div style="width: 100%; text-align: left; margin-top: 6px;">color: <b> {{currentColorValue.color}}</b></div>
                                    
                                    <div style="width: 100%; text-align: left; margin-top: 6px;">pattern: <b> {{currentColorValue.pattern}}</b></div>
                                    
                                </div>
                                <div>
                                    <div style="width: 100%; text-align: left; margin-top: 6px;">post_constraint: <b> {{(characterType == 'Color' ? currentColorValue : currentNonColorValue).post_constraint}}</b></div>
                                </div>
                            </div>
                            <a :disabled="bSearching" v-on:click="exploreCharacter()" class="btn btn-primary" style="width: 60%; margin: 10px" v-if="characterType != '' && characterType != null">Go</a><br>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div v-if="characterData[searchType]" class="table-responsive" style="max-height: calc(100vh - 200px);">
                            <table class="table table-bordered cr-table">
                                <thead>
                                    <th v-for="(header,index) in characterData[searchType].names" :key="'header'+index" style="min-width: 100px; height: 43px; line-height: 43px; text-align: center;">{{header}}</th>
                                </thead>
                                <tbody>
                                    <tr v-for="(row,index) in characterData[searchType].values" :key="'row'+index">
                                        <td v-for="(val,index) in characterData[searchType].names" :key="'data'+index" style="padding: 3px; padding-left: 10px">
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
        <div v-if="nonColorDetailsFlag" @close="nonColorDetailsFlag = false">
            <transition name="modal">
                <div class="modal-mask">
                    <div class="modal-wrapper">
                        <div class="modal-container">
                            <div style="max-height:80vh; overflow-y: auto;">
                                <div class="modal-header">
                                    <b style="text-align: left">Explore Non-Color Characters</b>
                                    <br/>
                                </div>
                                <div class="modal-body">
                                    <div style="border-radius: 5px; border: 1px solid; padding: 15px;">
                                        <div>
                                            <div style="display: inline-block;">
                                                <select style="width: 90px; height: 26px;"
                                                        v-on:focus="treeShowFlag = false;"
                                                        v-model="currentNonColorValue.negation">
                                                    <option value=""></option>
                                                    <option value="not">not</option>
                                                </select>
                                                <h5>
                                                    negation
                                                </h5>
                                            </div>
                                            <div style="display: inline-block;">
                                                <input style="width: 90px; height: 26px;"
                                                    v-on:focus="treeShowFlag = false;"
                                                    v-model="currentNonColorValue.pre_constraint"
                                                    list="pre_list">
                                                <datalist id="pre_list">
                                                    <option v-for="(each, index) in preList" :value="each" :key="index">{{ each }}
                                                    </option>
                                                </datalist>
                                                <h5>
                                                    pre-constraint
                                                </h5>
                                            </div>
                                            <div style="display: inline-block;">
                                                <select style="width: 90px; height: 26px;"
                                                        v-on:focus="treeShowFlag = false;"
                                                        v-model="currentNonColorValue.certainty_constraint">
                                                    <option value=""></option>
                                                    <option value="doubtfully">doubtfully</option>
                                                    <option value="possibly">possibly</option>
                                                    <option value="presumably">presumably</option>
                                                    <option value="approximately">approximately</option>
                                                    <option value="definitely">definitely</option>
                                                </select>
                                                <h5>
                                                    certainty-constraint
                                                </h5>
                                            </div>
                                            <div style="display: inline-block;">
                                                <select style="width: 90px; height: 26px;"
                                                        v-on:focus="treeShowFlag = false;"
                                                        v-model="currentNonColorValue.degree_constraint">
                                                    <option value=""></option>
                                                    <option value="imperceptibly">imperceptibly</option>
                                                    <option value="scarcely">scarcely</option>
                                                    <option value="moderately">moderately</option>
                                                    <option value="considerably">considerably</option>
                                                    <option value="extremely">extremely</option>
                                                </select>
                                                <h5>
                                                    degree-constraint
                                                </h5>
                                            </div>
                                            <div style="display: inline-block;">
                                                <input v-on:focus="focusNonColorValue()"
                                                    v-on:keyup.enter="$event.target.blur();confirmNonColorValue();"
                                                    style="width: 90px; border:none; border-bottom: 1px solid; text-align:left;"
                                                    v-model="currentNonColorValue.main_value"
                                                    >
                                                <h5>
                                                    {{ characterType.toLowerCase() }}
                                                </h5>
                                            </div>
                                            <div style="display: inline-block;">
                                                <input style="width: 90px;"
                                                    v-on:focus="treeShowFlag = false;"
                                                    v-model="currentNonColorValue.post_constraint"
                                                    list="post_list">
                                                <datalist id="post_list" v-if="postList.length > 0">
                                                    <option v-for="(each, index) in postList" :value="each" :key="index">{{ each }}
                                                    </option>
                                                </datalist>
                                                <h5>
                                                    post-constraint
                                                </h5>
                                            </div>
                                        </div>
                                        <div v-if="allTreeData[characterType.toLowerCase()] && treeShowFlag" style="margin-top: 10px;">
                                            <tree
                                                    :data="treeShowFlag? allTreeData[characterType.toLowerCase()] : null"
                                                    :options="treeOption"
                                                    :filter="filterFlag?currentNonColorValue.main_value:null"
                                                    ref="nonColorTree"
                                                    @node:selected="onNonColorTreeNodeSelected"
                                                    style="max-height: 300px;">
                                                <div slot-scope="{ node }" class="node-container">
                                                    <div class="node-text" v-tooltip="node.text">{{ node.text }}
                                                    </div>
                                                </div>
                                            </tree>
                                        </div>
                                        <div v-if="!nonColorValueExist"
                                            style="margin-top: 10px;">
                                            <!-- <b>There is not '{{currentNonColorValue.main_value}}' in the {{characterType}} term tree.</b> -->
                                            <b>No characters have this value. Select a value from given term set</b>
                                        </div>
                                        <div class="row">
                                            <div style="float: right; margin-right: 20px">
                                                <a class="btn btn-primary ok-btn"
                                                v-on:click="confirmNonColorValue()">
                                                    OK </a>
                                                <a v-on:click="nonColorDetailsFlag = false;currentNonColorValue.main_value='';currentNonColorValue.negation = '';currentNonColorValue.pre_constraint = '';currentNonColorValue.certainty_constraint = '';currentNonColorValue.degree_constraint = '';currentNonColorValue.post_constraint = '';"
                                                class="btn btn-danger">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
        <div v-if="colorDetailsFlag" @close="colorDetailsFlag = false">
            <transition name="modal">
                <div class="modal-mask">
                    <div class="modal-wrapper">
                        <div class="modal-container">
                            <div style="max-height:80vh; overflow-y: auto;">
                                <div class="modal-header">
                                    <b style="text-align: left">Explore Color Characters</b>
                                    <br/>
                                </div>
                                <div class="modal-body">
                                    <div style="border-radius: 5px; border: 1px solid; padding: 15px;">
                                        <div>
                                            <div style="display: inline-block;">
                                                <select style="width: 90px; height: 26px;"
                                                        v-on:focus="treeShowFlag = false;"
                                                        v-model="currentColorValue.negation">
                                                    <option value=""></option>
                                                    <option value="not">not</option>
                                                </select>
                                                <h5>
                                                    negation
                                                </h5>
                                            </div>
                                            <div style="display: inline-block;">
                                                <input style="width: 90px; height: 26px;"
                                                    v-on:focus="treeShowFlag = false;"
                                                    v-model="currentColorValue.pre_constraint"
                                                    list="pre_list">
                                                <datalist id="pre_list">
                                                    <option v-for="(each, index) in preList" :value="each" :key="index">{{ each }}
                                                    </option>
                                                </datalist>
                                                <h5>
                                                    pre-constraint
                                                </h5>
                                            </div>
                                            <div style="display: inline-block;">
                                                <select style="width: 90px; height: 26px;"
                                                        v-on:focus="treeShowFlag = false;"
                                                        v-model="currentColorValue.certainty_constraint">
                                                    <option value=""></option>
                                                    <option value="doubtfully">doubtfully</option>
                                                    <option value="possibly">possibly</option>
                                                    <option value="presumably">presumably</option>
                                                    <option value="approximately">approximately</option>
                                                    <option value="definitely">definitely</option>
                                                </select>
                                                <h5>
                                                    certainty-constraint
                                                </h5>
                                            </div>
                                            <div style="display: inline-block;">
                                                <select style="width: 90px; height: 26px;"
                                                        v-on:focus="treeShowFlag = false;"
                                                        v-model="currentColorValue.degree_constraint">
                                                    <option value=""></option>
                                                    <option value="imperceptibly">imperceptibly</option>
                                                    <option value="scarcely">scarcely</option>
                                                    <option value="moderately">moderately</option>
                                                    <option value="considerably">considerably</option>
                                                    <option value="extremely">extremely</option>
                                                </select>
                                                <h5>
                                                    degree-constraint
                                                </h5>
                                            </div>
                                            <div style="display: inline-block;">
                                                <input v-on:focus="focusColorValue('brightness')"
                                                    v-on:keyup.enter="$event.target.blur();confirmColorValue();"
                                                    style="width: 90px; border:none; border-bottom: 1px solid; text-align:left;"
                                                    v-model="currentColorValue.brightness"
                                                    class="color-input">
                                                <h5>
                                                    brightness
                                                </h5>
                                            </div>
                                            <div style="display: inline-block;">
                                                <input v-on:focus="focusColorValue('reflectance')"
                                                    v-on:keyup.enter="$event.target.blur();confirmColorValue();"
                                                    style="width: 90px; border:none; border-bottom: 1px solid; text-align:left;"
                                                    v-model="currentColorValue.reflectance"
                                                    class="color-input">
                                                <h5>
                                                    reflectance
                                                </h5>
                                            </div>
                                            <div style="display: inline-block;">
                                                <input v-on:focus="focusColorValue('saturation')"
                                                    v-on:keyup.enter="$event.target.blur();confirmColorValue();"
                                                    style="width: 90px; border:none; border-bottom: 1px solid; text-align:left;"
                                                    v-model="currentColorValue.saturation"
                                                    class="color-input">
                                                <h5>
                                                    saturation
                                                </h5>
                                            </div>
                                            <div style="display: inline-block;">
                                                <input v-on:focus="focusColorValue('color')"
                                                    v-on:keyup.enter="$event.target.blur();confirmColorValue();"
                                                    style="width: 90px; border:none; border-bottom: 1px solid; text-align:left;"
                                                    v-model="currentColorValue.color"
                                                    class="color-input">
                                                <h5>
                                                    color
                                                </h5>
                                            </div>
                                            <div style="display: inline-block;">
                                                <input v-on:focus="focusColorValue('pattern')"
                                                    v-on:keyup.enter="$event.target.blur();confirmColorValue();"
                                                    style="width: 90px; border:none; border-bottom: 1px solid; text-align:left;"
                                                    v-model="currentColorValue.pattern"
                                                    class="color-input">
                                                <h5>
                                                    pattern
                                                </h5>
                                            </div>
                                            <div style="display: inline-block;">
                                                <input style="width: 90px;"
                                                    v-on:focus="treeShowFlag = false;"
                                                    v-model="currentColorValue.post_constraint"
                                                    list="post_list">
                                                <datalist id="post_list" v-if="postList.length > 0">
                                                    <option v-for="(each, index) in postList" :value="each" :key="index">{{ each }}
                                                    </option>
                                                </datalist>
                                                <h5>
                                                    post-constraint
                                                </h5>
                                            </div>
                                        </div>
                                        <div v-if="allTreeData[currentColorValue.detailFlag] && treeShowFlag" style="margin-top: 10px;">
                                            <tree
                                                    :data="treeShowFlag ? allTreeData[currentColorValue.detailFlag] : null"
                                                    :options="treeOption"
                                                    :filter="filterFlag?currentColorValue[currentColorValue.detailFlag]:null"
                                                    ref="colorTree"
                                                    @node:selected="onColorTreeNodeSelected"
                                                    style="max-height: 300px;">
                                                <div slot-scope="{ node }" class="node-container">
                                                    <div class="node-text" v-tooltip="node.text">{{ node.text }}
                                                    </div>
                                                </div>
                                            </tree>
                                        </div>
                                        <div v-if="colorValueExist.brightness"
                                            style="margin-top: 10px;">
                                            <b>The term {{currentColorValue.brightness}} is not in brightness term tree.</b>
                                        </div>
                                        <div v-if="colorValueExist.reflectance"
                                            style="margin-top: 10px;">
                                            <b>The term {{currentColorValue.reflectance}} is not in reflectance term tree.</b>
                                        </div>
                                        <div v-if="colorValueExist.saturation"
                                            style="margin-top: 10px;">
                                            <b>The term {{currentColorValue.saturation}} is not in saturation term tree.</b>
                                        </div>
                                        <div v-if="colorValueExist.color"
                                            style="margin-top: 10px;">
                                            <b>The term {{currentColorValue.color}} is not in color term tree.</b>
                                        </div>
                                        <div v-if="colorValueExist.pattern"
                                            style="margin-top: 10px;">
                                            <b>The term {{currentColorValue.pattern}} is not in pattern term tree.</b>
                                        </div>
                                        <div class="row">
                                            <div style="float: right; margin-right: 20px">
                                                <a class="btn btn-primary ok-btn"
                                                v-on:click="confirmColorValue()">
                                                    OK </a>
                                                <a v-on:click="colorDetailsFlag = false;currentColorValue.brightness='';currentColorValue.reflectance='';currentColorValue.saturation='';currentColorValue.color='';currentColorValue.pattern='';currentColorValue.negation = '';currentColorValue.pre_constraint = '';currentColorValue.certainty_constraint = '';currentColorValue.degree_constraint = '';currentColorValue.post_constraint = '';"
                                                class="btn btn-danger">Cancel</a>
                                            </div>
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
    import LiquorTree from 'liquor-tree';
    
    Vue.use(LiquorTree);
    
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
                searchType: 0,
                username: '',
                taxonName: '',
                characterType: '',
                bSearching: false,
                characterData: [],
                structureTreeData: null,
                unit: 'cm',
                value1: 0,
                value2: 0,
                compare1: '>',
                compare2: '<',
                preList: [],
                postList: [],

                currentNonColorValue: {
                    main_value: '',
                },
                currentColorValue: {
                    brightness: '',
                    color: '',
                    saturation: '',
                    reflectance: '',
                    pattern: '',
                    detailFlag: '',
                },
                nonColorDetailsFlag: false,
                colorDetailsFlag: false,
                structureTreeData: {},

                structure: '',

                treeOption: {
                    multiple: true,
                    autoCheckChildren: false,
                    parentSelect: false,
                    filter: {
                        matcher(query, node) {
                            return node.data.text.startsWith(query);
                        },
                        showChildren: true
                    }
                },
                filterFlag: false,
                treeFlag: false,
                treeShowFlag: false,
                nonColorValueExist: true,
                colorValueExist: {
                },
                treeData: null,
                allTreeData: {},
                findCharacterByStructure: true,
            }
        },
        components: {
        },
        methods: {
            focusValue() {
                var app = this;

                if (app.characterType == 'Color') {
                    app.colorDetailsFlag = true;
                }
                else {
                    app.nonColorDetailsFlag = true;
                }
                app.treeShowFlag = false;
                app.nonColorValueExist = true;
                app.colorValueExist = null;
                app.colorValueExist = {};
            },
            async focusNonColorValue() {
                var app = this;
                app.nonColorValueExist = true;
                const characterType = app.characterType.toLowerCase();
                if (!app.allTreeData[characterType]) {
                    app.treeShowFlag = false;
                    await axios.get('http://shark.sbs.arizona.edu:8080/carex/getSubclasses?baseIri=http://biosemantics.arizona.edu/ontologies/carex&term='+characterType)
                    .then(function(resp){
                        console.log(resp.data);
                        app.allTreeData[characterType] = resp.data;
                        app.treeShowFlag = true;
                        if (app.nonColorDetailsFlag){
                            app.nonColorDetailsFlag = false;
                            app.nonColorDetailsFlag = true;
                        }
                        setTimeout(function(){
                            app.filterFlag = true;
                        }, 20);
                    });
                }
                else {
                    if (app.nonColorDetailsFlag && !app.treeShowFlag){
                        app.treeShowFlag = true;
                        app.nonColorDetailsFlag = false;
                        app.nonColorDetailsFlag = true;
                        setTimeout(function(){
                            app.filterFlag = true;
                        }, 20);
                    }
                }
                console.log(app.allTreeData[characterType]);
                console.log(app.treeShowFlag);
            },
            getTreeRootName(detailFlag) {
                var roots={
                    brightness: 'color brightness',
                    reflectance: 'reflectance',
                    saturation: 'saturation',
                    color: 'colored',
                    pattern: 'multi-colored',
                }
                if (roots[detailFlag]) return roots[detailFlag];
                return detailFlag;
            },
            async focusColorValue(detailFlag) {
                var app = this;
                app.colorValueExist = null;
                app.colorValueExist = {};
                if (app.currentColorValue.detailFlag != detailFlag) {
                    if (!app.allTreeData[detailFlag]){
                        app.filterFlag = false;
                        app.treeShowFlag = false;
                        app.currentColorValue.detailFlag = detailFlag;
                        await axios.get('http://shark.sbs.arizona.edu:8080/carex/getSubclasses?baseIri=http://biosemantics.arizona.edu/ontologies/carex&term='+app.getTreeRootName(detailFlag))
                        .then(function(resp){
                            console.log(resp.data);
                            app.allTreeData[detailFlag] = resp.data;
                            app.treeShowFlag = true;
                            if (app.colorDetailsFlag){
                                app.colorDetailsFlag = false;
                                app.colorDetailsFlag = true;
                            }
                            setTimeout(function(){
                                app.filterFlag = true;
                            }, 20);
                        });
                    }
                    else {
                        app.currentColorValue.detailFlag = detailFlag;
                        app.colorDetailsFlag = false;
                        app.treeShowFlag = false;
                        app.filterFlag = false;
                        app.colorDetailsFlag = true;
                        setTimeout(function() {
                            app.treeShowFlag = true;
                        }, 20);
                        setTimeout(function() {
                            app.filterFlag = true;
                        }, 40);
                    }
                }
                console.log(app.allTreeData[detailFlag]);
                console.log(app.treeShowFlag);
            },
            onNonColorTreeNodeSelected(node) {
                var app = this;
                app.currentNonColorValue.main_value = node.data.text;
                console.log(node.data.text);
            },
            onColorTreeNodeSelected(node) {
                var app = this;
                app.currentColorValue[app.currentColorValue.detailFlag] = node.data.text;
            },
            confirmNonColorValue() {
                var app = this;
                app.nonColorValueExist = app.searchTreeData(app.allTreeData[app.characterType.toLowerCase()], app.currentNonColorValue.main_value);
                app.treeShowFlag = false;
                if (app.nonColorValueExist) {
                    app.nonColorDetailsFlag = false;
                }
            },
            confirmColorValue() {
                var app = this;
                app.treeShowFlag = false;
                app.colorValueExist.brightness = !app.searchTreeData(app.allTreeData.brightness, app.currentColorValue.brightness);
                app.colorValueExist.reflectance = !app.searchTreeData(app.allTreeData.reflectance, app.currentColorValue.reflectance);
                app.colorValueExist.saturation = !app.searchTreeData(app.allTreeData.saturation, app.currentColorValue.saturation);
                app.colorValueExist.color = !app.searchTreeData(app.allTreeData.color, app.currentColorValue.color);
                app.colorValueExist.pattern = !app.searchTreeData(app.allTreeData.pattern, app.currentColorValue.pattern);
                if (!app.colorValueExist.brightness &&
                  !app.colorValueExist.reflectance &&
                  !app.colorValueExist.saturation &&
                  !app.colorValueExist.color &&
                  !app.colorValueExist.pattern){
                    app.colorDetailsFlag = false;
                }
            },
            handleUsedBy() {
                var app = this;
                app.searchType = 0;
            },
            handleAboutTaxon() {
                var app = this;
                app.searchType = 1;
            },
            handleOfStructure() {
                var app = this;
                app.searchType = 2;
                app.filterFlag = false;
                setTimeout(()=>{
                    app.filterFlag = true;
                },10);
            },
            handleWithCharacter() {
                var app = this;
                app.searchType = 3;
            },
            searchTreeData(tData, txt) {
                var app = this;
                if (txt == '') return true;
                if (!tData)return true;
                if (tData.text == txt){
                    return true;
                }
                for (let i = 0; tData.children && i < tData.children.length; i ++){
                    if (app.searchTreeData(tData.children[i],txt)){
                        return true;
                    }
                }
                return false;
            },
            checkHaveUnit(string) {
                var app = this;

                if (!string){
                    return false;
                }

                if (string.startsWith('Length')
                    || string.startsWith('Width')
                    || string.startsWith('Number')
                    || string.startsWith('Depth')
                    || string.startsWith('Diameter')
                    || string.startsWith('Distance')
                    || string.startsWith('Ratio')
                    || string.startsWith('Count')) {
                    return true;
                } else {
                    return false;
                }
            },
            calcValue(val, unit) {
                let obj = {
                    mm: 1,
                    cm: 10,
                    dm: 100,
                    m: 1000,
                };
                return val * obj[unit];
            },
            onTextureTreeNodeSelected(node) {
                var app = this;
                app.structure = node.data.text;
                // app.exploreCharacter();
            },
            getColorDetailText() {
                var app = this;
                let str = (app.currentColorValue.pre_constraint ? (app.currentColorValue.pre_constraint + ' ') : '') +
                    (app.currentColorValue.certainty_constraint ? (app.currentColorValue.certainty_constraint + ' ') : '') +
                    (app.currentColorValue.degree_constraint ? (app.currentColorValue.degree_constraint + ' ') : '') +
                    (app.currentColorValue.brightness ? (app.currentColorValue.brightness + ' ') : '') +
                    (app.currentColorValue.reflectance ? (app.currentColorValue.reflectance + ' ') : '') +
                    (app.currentColorValue.saturation ? (app.currentColorValue.saturation + ' ') : '') +
                    (app.currentColorValue.color ? (app.currentColorValue.color + ' ') : '') +
                    (app.currentColorValue.pattern ? (app.currentColorValue.pattern + ' ') : '') +
                    (app.currentColorValue.post_constraint ? (app.currentColorValue.post_constraint + ' ') : '');

                str = str.slice(0, -1);
                return str;
            },
            getNonColorDetailText() {
                var app = this;
                let str = (app.currentNonColorValue.pre_constraint ? (app.currentNonColorValue.pre_constraint + ' ') : '') +
                    (app.currentNonColorValue.certainty_constraint ? (app.currentNonColorValue.certainty_constraint + ' ') : '') +
                    (app.currentNonColorValue.degree_constraint ? (app.currentNonColorValue.degree_constraint + ' ') : '') +
                    (app.currentNonColorValue.main_value ? (app.currentNonColorValue.main_value + ' ') : '') +
                    (app.currentNonColorValue.post_constraint ? (app.currentNonColorValue.post_constraint + ' ') : '');
                str = str.slice(0, -1);
                return str;
            },
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
            exploreCharacter() {
                var app = this;
                app.bSearching = true;
                let query = ``;
                switch(app.searchType){
                    case 0:
                        query=`PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
                                PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                PREFIX owl: <http://www.w3.org/2002/07/owl#>
                                PREFIX iao: <http://purl.obolibrary.org/obo/iao.owl#>
                                PREFIX dc: <http://purl.org/dc/terms/>
                                PREFIX obi:<http://purl.obolibrary.org/obo/obi.owl#>
                                PREFIX uo: <http://purl.obolibrary.org/obo/uo.owl#>
                                PREFIX ncbi: <https://www.ncbi.nlm.nih.gov/Taxonomy#>
                                PREFIX mo:<http://biosemantics.arizona.edu/ontologies/modifierlist#>
                                PREFIX :<http://biosemantics.arizona.edu/ontologies/carex#>
                                PREFIX kb:<http://biosemantics.arizona.edu/kb/carex#>
                                PREFIX data:<http://biosemantics.arizona.edu/kb/data#>
                                PREFIX app:<http://shark.sbs.arizona.edu/chrecorder#>

                                select distinct ?graph ?character 
                                where {
                                        ?graph dc:creator app:${app.username}.
                                        GRAPH ?graph{
                                            ?structure :has_quality ?character.
                                        }
                        }`;
                        app.api(query, data => {
                            console.log(data);
                            app.bSearching = false;
                            let tmp = {};
                            tmp.names = data.head.vars;
                            tmp.values = data.results.bindings;
                            app.characterData[app.searchType] = tmp;
                        })
                        break;
                    case 1:
                        $.get('https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=taxonomy&term='+app.taxonName.toLowerCase().replace(' ','%20'),{},function(resp){
                            let idNode = resp.getElementsByTagName("Id")[0];
                            let id = idNode ? idNode.childNodes[0].nodeValue : "unknown";

                            if (id == "unknown") {
                                alert('Invalid taxon');
                                app.bSearching = false;
                                return;
                            }
                            query=`PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
                                PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                PREFIX owl: <http://www.w3.org/2002/07/owl#>
                                PREFIX iao: <http://purl.obolibrary.org/obo/iao.owl#>
                                PREFIX dc: <http://purl.org/dc/terms/>
                                PREFIX obi:<http://purl.obolibrary.org/obo/obi.owl#>
                                PREFIX uo: <http://purl.obolibrary.org/obo/uo.owl#>
                                PREFIX ncbi: <https://www.ncbi.nlm.nih.gov/Taxonomy#>
                                PREFIX mo:<http://biosemantics.arizona.edu/ontologies/modifierlist#>
                                PREFIX :<http://biosemantics.arizona.edu/ontologies/carex#>
                                PREFIX kb:<http://biosemantics.arizona.edu/kb/carex#>
                                PREFIX data:<http://biosemantics.arizona.edu/kb/data#>
                                PREFIX app:<http://shark.sbs.arizona.edu/chrecorder#>

                                # View triples
                                SELECT distinct ?graph ?character
                                where {
                                    GRAPH ?graph {
                                        ?sample iao:is_about ncbi:txid${id}.
                                        ?sample :has_part ?part.
                                        ?part :has_quality ?iCharacter.
                                        ?iCharacter a ?character.
                                    }
                            }`;
                            
                            app.api(query, data => {
                                console.log(data);
                                app.bSearching = false;
                                let tmp = {};
                                tmp.names = data.head.vars;
                                tmp.values = data.results.bindings;
                                app.characterData[app.searchType] = tmp;
                            })
                        });
                        break;
                    case 2:
                        query=`BASE <http://biosemantics.arizona.edu/ontologies/carex#>
                            PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
                            PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                            PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                            PREFIX owl: <http://www.w3.org/2002/07/owl#>
                            PREFIX iao: <http://purl.obolibrary.org/obo/iao.owl#>
                            PREFIX dc: <http://purl.org/dc/terms/>
                            PREFIX obi:<http://purl.obolibrary.org/obo/obi.owl#>
                            PREFIX uo: <http://purl.obolibrary.org/obo/uo.owl#>
                            PREFIX ncbi: <https://www.ncbi.nlm.nih.gov/Taxonomy#>
                            PREFIX mo:<http://biosemantics.arizona.edu/ontologies/modifierlist#>
                            PREFIX :<http://biosemantics.arizona.edu/ontologies/carex#>
                            PREFIX kb:<http://biosemantics.arizona.edu/kb/carex#>
                            PREFIX data:<http://biosemantics.arizona.edu/kb/data#>
                            PREFIX app:<http://shark.sbs.arizona.edu/chrecorder#>

                            select distinct ?graph ?icharacter ?structure
                            where {
                                        GRAPH ?graph {
                                            ?structure :has_quality ?icharacter.
                                            ?structure a :${app.structure.replace(' ', '_').replace('-','_')}.
                                        }
                        }`;
                        app.api(query, data => {
                            console.log(data);
                            app.bSearching = false;
                            let tmp = {};
                            tmp.names = data.head.vars;
                            tmp.values = data.results.bindings;
                            app.characterData[app.searchType] = tmp;
                        })
                        break;
                    case 3:
                        if (app.checkHaveUnit(app.characterType)){
                            query=` BASE <http://biosemantics.arizona.edu/ontologies/carex#>
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

                                    select distinct ?graph ?character ?value ${app.characterType!='Number' && '?unit'}
                                    where {
                                        ?structure :has_quality ?character.
                                        ?character a ?str.
                                        ?str rdfs:subClassOf :perceived_${app.characterType.toLowerCase().replace(' ', '_')}.
                                        ?character :has_value ?value.
                                        ?character :has_unit ?unit.
                                        GRAPH ?graph {
                                            ?structure :has_quality ?character.
                                        }
                            }`;

                            app.api(query, resp => {
                                console.log(resp);
                                for (let i = 0 ; i < resp.results.bindings.length ; i ++) {
                                    resp.results.bindings[i].value.value = parseFloat(resp.results.bindings[i].value.value);
                                    if (app.characterType!='Number') {
                                        resp.results.bindings[i].unit.value = resp.results.bindings[i].unit.value.split('owl#')[1];
                                        if (!eval(app.calcValue(resp.results.bindings[i].value.value,resp.results.bindings[i].unit.value) + app.compare1 + app.calcValue(app.value1,app.unit) + '&&' + app.calcValue(resp.results.bindings[i].value.value,resp.results.bindings[i].unit.value) + app.compare2 + app.calcValue(app.value2,app.unit))){
                                        resp.results.bindings.splice(i, 1);
                                        i --;
                                    }
                                    }
                                    else if (!eval(resp.results.bindings[i].value.value + app.compare1 + app.value1 + '&&' + resp.results.bindings[i].value.value + app.compare2 + app.value2)){
                                        resp.results.bindings.splice(i, 1);
                                        i --;
                                    }
                                }
                                app.characterData[app.searchType] = {names: resp.head.vars, values: resp.results.bindings};
                                app.bSearching = false;
                            })
                        }
                        else 
                        {
                            if (app.characterType == 'Color') {
                                if (app.currentColorValue.negation && app.currentColorValue.negation == 'not') {
                                    query=` BASE <http://biosemantics.arizona.edu/ontologies/carex#>
                                            PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
                                            PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                            PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                            PREFIX owl: <http://www.w3.org/2002/07/owl#>
                                            PREFIX iao: <http://purl.obolibrary.org/obo/iao.owl#>
                                            PREFIX dc: <http://purl.org/dc/terms/>
                                            PREFIX obi:<http://purl.obolibrary.org/obo/obi.owl#>
                                            PREFIX uo: <http://purl.obolibrary.org/obo/uo.owl#>
                                            PREFIX ncbi: <https://www.ncbi.nlm.nih.gov/Taxonomy#>
                                            PREFIX mo:<http://biosemantics.arizona.edu/ontologies/modifierlist#>
                                            PREFIX :<http://biosemantics.arizona.edu/ontologies/carex#>
                                            PREFIX kb:<http://biosemantics.arizona.edu/kb/carex#>
                                            PREFIX data:<http://biosemantics.arizona.edu/kb/data#>
                                            PREFIX app:<http://shark.sbs.arizona.edu/chrecorder#>

                                            select distinct ?graph ?character ?structure
                                            where {
                                                GRAPH ?graph {
                                                    ?negation a owl:NegativePropertyAssertion ;
                                                                owl:sourceIndividual   ?structure;  
                                                                owl:targetIndividual   ?character.    
                                                    
                                                    ${app.currentColorValue.color!='' && app.currentColorValue.color ? '?character :has_hue_value :' + app.currentColorValue.color.replace(' ', '_').replace('-','_') + '.' : ''}
                                                    ${app.currentColorValue.brightness!='' && app.currentColorValue.brightness ? '?character :has_brightness_value :' + app.currentColorValue.brightness.replace(' ', '_').replace('-','_') + '.' : ''}
                                                    ${app.currentColorValue.reflectance!='' && app.currentColorValue.reflectance ? '?character :has_reflectance_value :' + app.currentColorValue.reflectance.replace(' ', '_').replace('-','_') + '.' : ''}
                                                    ${app.currentColorValue.saturation!='' && app.currentColorValue.saturation ? '?character :has_saturation_value :' + app.currentColorValue.saturation.replace(' ', '_').replace('-','_') + '.' : ''}
                                                    ${app.currentColorValue.pattern !='' && app.currentColorValue.pattern  ? '?character :has_pattern_value :' + app.currentColorValue.pattern .replace(' ', '_').replace('-','_') + '.' : ''}
                                                    ${app.currentColorValue.certainty_constraint!='' && app.currentColorValue.certainty_constraint ? '?character :has_certainty_value_modifier :' + app.currentColorValue.certainty_constraint.replace(' ', '_').replace('-','_') + '.' : ''}
                                                    ${app.currentColorValue.degree_constraint!='' && app.currentColorValue.degree_constraint ? '?character :has_degree_value_modifier :' + app.currentColorValue.degree_constraint.replace(' ', '_').replace('-','_') + '.' : ''}
                                                    ${app.currentColorValue.pre_constraint && app.currentColorValue.pre_constraint != '' || app.currentColorValue.post_constraint && app.currentColorValue.post_constraint != '' ? '?character :has_value "' + app.getColorDetailText() + '".' : ''}
                                                }
                                    }`;
                                }
                                else {
                                    query = `   BASE <http://biosemantics.arizona.edu/ontologies/carex#>
                                                PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
                                                PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                                PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                                PREFIX owl: <http://www.w3.org/2002/07/owl#>
                                                PREFIX iao: <http://purl.obolibrary.org/obo/iao.owl#>
                                                PREFIX dc: <http://purl.org/dc/terms/>
                                                PREFIX obi:<http://purl.obolibrary.org/obo/obi.owl#>
                                                PREFIX uo: <http://purl.obolibrary.org/obo/uo.owl#>
                                                PREFIX ncbi: <https://www.ncbi.nlm.nih.gov/Taxonomy#>
                                                PREFIX mo:<http://biosemantics.arizona.edu/ontologies/modifierlist#>
                                                PREFIX :<http://biosemantics.arizona.edu/ontologies/carex#>
                                                PREFIX kb:<http://biosemantics.arizona.edu/kb/carex#>
                                                PREFIX data:<http://biosemantics.arizona.edu/kb/data#>
                                                PREFIX app:<http://shark.sbs.arizona.edu/chrecorder#>

                                                select distinct ?graph ?character ?structure
                                                where {
                                                    ?icharacter rdfs:subClassOf :perceived_color.
                                                    GRAPH ?graph {
                                                        ?structure :has_quality ?character.
                                                        ?character a ?icharacter.
                                                        
                                                        ${app.currentColorValue.color!='' && app.currentColorValue.color ? '?character :has_hue_value :' + app.currentColorValue.color.replace(' ', '_').replace('-','_') + '.' : ''}
                                                        ${app.currentColorValue.brightness!='' && app.currentColorValue.brightness ? '?character :has_brightness_value :' + app.currentColorValue.brightness.replace(' ', '_').replace('-','_') + '.' : ''}
                                                        ${app.currentColorValue.reflectance!='' && app.currentColorValue.reflectance ? '?character :has_reflectance_value :' + app.currentColorValue.reflectance.replace(' ', '_').replace('-','_') + '.' : ''}
                                                        ${app.currentColorValue.saturation!='' && app.currentColorValue.saturation ? '?character :has_saturation_value :' + app.currentColorValue.saturation.replace(' ', '_').replace('-','_') + '.' : ''}
                                                        ${app.currentColorValue.pattern !='' && app.currentColorValue.pattern  ? '?character :has_pattern_value :' + app.currentColorValue.pattern .replace(' ', '_').replace('-','_') + '.' : ''}
                                                        ${app.currentColorValue.certainty_constraint!='' && app.currentColorValue.certainty_constraint ? '?character :has_certainty_value_modifier :' + app.currentColorValue.certainty_constraint.replace(' ', '_').replace('-','_') + '.' : ''}
                                                        ${app.currentColorValue.degree_constraint!='' && app.currentColorValue.degree_constraint ? '?character :has_degree_value_modifier :' + app.currentColorValue.degree_constraint.replace(' ', '_').replace('-','_') + '.' : ''}
                                                        ${app.currentColorValue.pre_constraint && app.currentColorValue.pre_constraint != '' || app.currentColorValue.post_constraint && app.currentColorValue.post_constraint != '' ? '?character :has_value "' + app.getColorDetailText() + '".' : ''}
                                                    }
                                    }`;
                                }
                            }
                            else {
                                if (app.currentNonColorValue.negation && app.currentNonColorValue.negation == 'not'){
                                    query=` BASE <http://biosemantics.arizona.edu/ontologies/carex#>
                                            PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
                                            PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                            PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                            PREFIX owl: <http://www.w3.org/2002/07/owl#>
                                            PREFIX iao: <http://purl.obolibrary.org/obo/iao.owl#>
                                            PREFIX dc: <http://purl.org/dc/terms/>
                                            PREFIX obi:<http://purl.obolibrary.org/obo/obi.owl#>
                                            PREFIX uo: <http://purl.obolibrary.org/obo/uo.owl#>
                                            PREFIX ncbi: <https://www.ncbi.nlm.nih.gov/Taxonomy#>
                                            PREFIX mo:<http://biosemantics.arizona.edu/ontologies/modifierlist#>
                                            PREFIX :<http://biosemantics.arizona.edu/ontologies/carex#>
                                            PREFIX kb:<http://biosemantics.arizona.edu/kb/carex#>
                                            PREFIX data:<http://biosemantics.arizona.edu/kb/data#>
                                            PREFIX app:<http://shark.sbs.arizona.edu/chrecorder#>

                                            select distinct ?graph ?character ?structure
                                            where {
                                                GRAPH ?graph {
                                                    ?negation a owl:NegativePropertyAssertion ;
                                                            owl:sourceIndividual   ?structure;  
                                                            owl:targetIndividual   ?character.
                                                    
                                                    ${app.currentNonColorValue.main_value!='' && app.currentNonColorValue.main_value ? '?character :has_value :' + app.currentNonColorValue.main_value.replace(' ', '_').replace('-','_') + '.' : ''}
                                                    ${app.currentNonColorValue.certainty_constraint!='' && app.currentNonColorValue.certainty_constraint ? '?character :has_certainty_value_modifier mo:' + app.currentNonColorValue.certainty_constraint.replace(' ', '_').replace('-','_') + '.' : ''}
                                                    ${app.currentNonColorValue.degree_constraint!='' && app.currentNonColorValue.degree_constraint ? '?character :has_degree_value_modifier mo:' + app.currentNonColorValue.degree_constraint.replace(' ', '_').replace('-','_') + '.' : ''}
                                                    ${app.currentNonColorValue.pre_constraint && app.currentNonColorValue.pre_constraint != '' || app.currentNonColorValue.post_constraint && app.currentNonColorValue.post_constraint != '' ? '?character :has_value "' + app.getNonColorDetailText() + '".' : ''}
                                                }
                                    }`;
                                }
                                else {
                                    query = `   BASE <http://biosemantics.arizona.edu/ontologies/carex#>
                                                PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
                                                PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                                PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                                PREFIX owl: <http://www.w3.org/2002/07/owl#>
                                                PREFIX iao: <http://purl.obolibrary.org/obo/iao.owl#>
                                                PREFIX dc: <http://purl.org/dc/terms/>
                                                PREFIX obi:<http://purl.obolibrary.org/obo/obi.owl#>
                                                PREFIX uo: <http://purl.obolibrary.org/obo/uo.owl#>
                                                PREFIX ncbi: <https://www.ncbi.nlm.nih.gov/Taxonomy#>
                                                PREFIX mo:<http://biosemantics.arizona.edu/ontologies/modifierlist#>
                                                PREFIX :<http://biosemantics.arizona.edu/ontologies/carex#>
                                                PREFIX kb:<http://biosemantics.arizona.edu/kb/carex#>
                                                PREFIX data:<http://biosemantics.arizona.edu/kb/data#>
                                                PREFIX app:<http://shark.sbs.arizona.edu/chrecorder#>

                                                select distinct ?graph ?character ?structure
                                                where {
                                                    ?icharacter rdfs:subClassOf :perceived_${app.characterType.toLowerCase().replace(' ', '_')}.
                                                    GRAPH ?graph {
                                                        ?structure :has_quality ?character.
                                                        ?character a ?icharacter.
                                                        
                                                        ${app.currentNonColorValue.main_value!='' && app.currentNonColorValue.main_value ? '?character :has_value :' + app.currentNonColorValue.main_value.replace(' ', '_').replace('-','_') + '.' : ''}
                                                        ${app.currentNonColorValue.certainty_constraint!='' && app.currentNonColorValue.certainty_constraint ? '?character :has_certainty_value_modifier mo:' + app.currentNonColorValue.certainty_constraint.replace(' ', '_').replace('-','_') + '.' : ''}
                                                        ${app.currentNonColorValue.degree_constraint!='' && app.currentNonColorValue.degree_constraint ? '?character :has_degree_value_modifier mo:' + app.currentNonColorValue.degree_constraint.replace(' ', '_').replace('-','_') + '.' : ''}
                                                        ${app.currentNonColorValue.pre_constraint && app.currentNonColorValue.pre_constraint != '' || app.currentNonColorValue.post_constraint && app.currentNonColorValue.post_constraint != '' ? '?character :has_value "' + app.getNonColorDetailText() + '".' : ''}
                                                    }
                                    }`;
                                }
                            }
                            app.api(query, (resp) => {
                                console.log(resp);
                                app.characterData[app.searchType] = {names: resp.head.vars, values: resp.results.bindings};
                                app.bSearching = false;
                            })
                        }
                        break;
                }
            },
        },
        watch: {
            structure: function(val) {
                this.findCharacterByStructure = !this.searchTreeData(this.structureTreeData, val);
            }
        },
        mounted() {
            var app = this;
            
            console.log(app.user);

            app.user.name = app.user.email.split('@')[0];
            sessionStorage.setItem('userId', app.user.id);

            axios.post('/chrecorder/public/api/v1/get-default-constraint').then(function(resp){
                app.preList = resp.data.preList;
                app.postList = resp.data.postList;
                console.log(app.preList);
                console.log(app.postList);
            });
            axios.get('http://shark.sbs.arizona.edu:8080/carex/getSubclasses?baseIri=http://biosemantics.arizona.edu/ontologies/carex&term=anatomical%20structure').then(function(resp){
                console.log(resp.data);
                app.structureTreeData = resp.data;
                app.treeFlag = true;
                app.filterFlag = false;
                setTimeout(()=>{
                    app.filterFlag = true;
                },10);
            });
            axios.get('http://shark.sbs.arizona.edu:8080/carex/getSubclasses?baseIri=http://biosemantics.arizona.edu/ontologies/carex&term=quality')
            .then(function(resp){
                console.log(resp.data);
                for (let i = 0 ; i < resp.data.children.length ; i ++ ) {
                    if (resp.data.children[i].text == 'shape'){
                        app.allTreeData.shape = resp.data.children[i];
                    }
                    else if (resp.data.children[i].text == 'color brightness'){
                        app.allTreeData.brightness = resp.data.children[i];
                    }
                    else if (resp.data.children[i].text == 'reflectance'){
                        app.allTreeData.reflectance = resp.data.children[i];
                    }
                    else if (resp.data.children[i].text == 'color saturation'){
                        app.allTreeData.saturation = resp.data.children[i];
                    }
                    else if (resp.data.children[i].text == 'coloration'){
                        app.allTreeData.color = resp.data.children[i].children[0];
                        app.allTreeData.pattern = resp.data.children[i].children[1];
                    }
                    else if (resp.data.children[i].text == 'surface feature'){
                        app.allTreeData.texture = resp.data.children[i].children[0];
                        app.allTreeData.pubescence = resp.data.children[i].children[3];
                    }
                    else if (resp.data.children[i].text == 'growth form'){
                        app.allTreeData['growth form'] = resp.data.children[i];
                    }
                    else if (resp.data.children[i].text == 'orientation'){
                        app.allTreeData.orientation = resp.data.children[i];
                    }
                    else if (resp.data.children[i].text == 'duration'){
                        app.allTreeData.duration = resp.data.children[i];
                    }
                    // else if (resp.data.children[i].text == 'relational quality'){
                    //     app.allTreeData.relationship = resp.data.children[i];
                    // }
                }
            });
        },
    }
</script>