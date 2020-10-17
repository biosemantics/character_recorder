<template>
    <div slot="section" class="vld-parent">
        <loading :active.sync="isLoading"
                 :is-full-page="true"
                 loader="dots"
        ></loading>
        <div class="tab-pane" id="">
            <form autocomplete="off">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div v-if="matrixShowFlag == false"
                                 style="max-width: 1000px; margin-left: auto; margin-right: auto;">
                                <h3><b>Set up your matrix: </b>
                                <span style="font-size: 20px;">select/create the characters you'd like to record in the matrix, then click on 'Go To Matrix'. You can add or remove characters after matrix is generated.</span>
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-12" v-if="collapsedFlag == false">
                            <div style="max-width: 1000px; margin-left: auto; margin-right: auto; margin-top:50px;">
                                <div>
                                    <b>I 'm measuring <input class="" v-model="taxonName" style="width: 330px;"
                                                             placeholder="Carex capitata"/>.</b>
                                </div>
                                <div class="margin-top-10">
                                    <b>I have <input v-model="columnCount" style="width: 180px;"
                                                     placeholder="3"> specimens / samples.</b>
                                </div>
                                <div class="margin-top-10 row">
                                    <div class="col-md-12" style="line-height: 38px;">
                                        <b class="some-container">I 'm measuring <a class="btn btn-primary"
                                                                                    v-on:click="showStandardCharacters()"
                                        >
                                            the recommended set of characters
                                        </a>
                                            <b v-tooltip="{ content:standardCharactersTooltip, classes: 'standard-tooltip'}">(what
                                                are they?) </b><br/> or</b>
                                    </div>
                                    <div class="col-md-12 margin-top-10">
                                        <b>Search/create other character:&nbsp;</b>
                                        <model-select :options="standardCharacters"
                                                      v-model="item"
                                                      placeholder="Search/create character here"
                                                      @searchchange="printSearchText"
                                                      @select="onSelect"
                                                      @resolve="onResolve"
                                        />

                                    </div>
                                </div>
                                <hr style="border-top: 2px solid; margin-top: 20px;">
                                <div class="margin-top-10 text-right">
                                    <a class="btn btn-primary" v-on:click="generateMatrix()" style="width: 200px;">Go To Matrix</a>
                                    <a class="btn btn-primary" v-on:click="loadMatrixDialog = true;"
                                       style="width: 200px; ">Load Matrix Version</a>
                                    <a class="btn btn-primary" v-on:click="confirmCollapse();collapsedFlag = true;"
                                       style="width: 40px;"><span class="glyphicon glyphicon-chevron-up"></span></a>
                                </div>
                                <div class="margin-top-10"
                                     v-if="userCharacters.find(ch => ch.standard == 0)">
                                    <h4><b><u>You've selected characters:</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a class="btn btn-add display-block" v-on:click="removeAllCharacters()"><span
                                                class="glyphicon glyphicon-remove"
                                                :set="previousUserCharacter=''"></span></a>
                                    </h4>
                                    <div v-for="eachCharacter in userCharacters"
                                         v-if="eachCharacter.standard == 0"
                                         v-tooltip="eachCharacter.tooltip"
                                         style="display: table; cursor: pointer;">
                                        <b v-if="eachCharacter.standard_tag != previousUserCharacter.standard_tag">
                                            {{ eachCharacter.standard_tag }} </b>
                                        <div style="margin-left: 50px;">
                                            <i v-bind:style="{color:userCharacters.filter(ch => ch.name == eachCharacter.name).length > 1 ? '#da7f38' : '#636b6f', 'font-weight': eachCharacter.deprecated >= 0 ? 'bold' : 'linear'}">{{ eachCharacter.name }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <a style="margin-left: 45px;" v-on:click="onResolveUserCharacter(eachCharacter)">
                                                    <span v-if="eachCharacter.deprecated >= 0" class="glyphicon glyphicon-wrench" ></span>
                                                </a>
                                                <a  v-on:click="removeStandardCharacter(eachCharacter.id)"
                                                    :set="previousUserCharacter=eachCharacter"
                                                    style="margin-left: 5px;"
                                                >
                                                    <span class="glyphicon glyphicon-remove">
                                                    </span>
                                                </a>
                                            </i>
                                        </div>
                                        <!--{{ eachCharacter.name }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a class="btn btn-add display-block"
                                           v-on:click="removeUserCharacter(eachCharacter.id)"><span
                                                class="glyphicon glyphicon-remove"></span></a>-->
                                    </div>
                                </div>
                                <div class="margin-top-10"
                                     v-if="userCharacters.find(ch => ch.standard == 1)">
                                    <h4><b><u>You've selected recommended characters:</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </b>
                                        <a class="btn btn-add display-block"
                                           v-on:click="removeAllStandardFlag = true;"><span
                                                class="glyphicon glyphicon-remove"></span></a></h4>
                                    <div v-for="eachTag in standardCharactersTags" v-if="userCharacters.find(ch => ch.standard_tag == eachTag && ch.standard == 1)" style="display: table; cursor: pointer;">
                                        <b>{{ eachTag }}</b>
                                        <div v-for="eachCharacter in userCharacters"
                                            v-if="eachCharacter.standard_tag == eachTag && (eachCharacter.standard == 1)"
                                            v-tooltip="eachCharacter.tooltip" style="margin-left: 50px;">
                                            <i v-bind:style="{color:userCharacters.filter(ch => ch.name == eachCharacter.name).length > 1 ? '#da7f38' : '#636b6f', 'font-weight': eachCharacter.deprecated >= 0 ? 'bold' : 'linear'}">{{ eachCharacter.name }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <a style="margin-left: 35px;" v-on:click="onResolveUserCharacter(eachCharacter)">
                                                    <span v-if="eachCharacter.deprecated >= 0" class="glyphicon glyphicon-wrench" ></span>
                                                </a>
                                                <a style="margin-left: 5px;" v-on:click="removeStandardCharacter(eachCharacter.id)">
                                                    <span class="glyphicon glyphicon-remove"></span>
                                                </a>
                                            </i>
                                        </div>
                                    </div>
                                    <!--<div v-for="eachCharacter in userCharacters"-->
                                         <!--v-if="eachCharacter.standard == 1 || !eachCharacter.username.includes(user.name)"-->
                                         <!--v-tooltip="eachCharacter.tooltip"-->
                                         <!--style="display: table; cursor: pointer;">-->
                                        <!--<b v-if="eachCharacter.standard_tag != previousCharacter.standard_tag">-->
                                            <!--{{ eachCharacter.standard_tag }} </b>-->

                                        <!--<div style="text-indent: 50px;"><i>{{ eachCharacter.name }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
                                            <!--<a class="btn btn-add display-block"-->
                                               <!--v-on:click="removeStandardCharacter(eachCharacter.id)"-->
                                               <!--:set="previousCharacter=eachCharacter"-->
                                            <!--&gt;<span-->
                                                    <!--class="glyphicon glyphicon-remove"></span></a></i></div>-->

                                    <!--</div>-->
                                </div>
                                <!-- repeat the buttoms here -->
                                <div v-if="userCharacters.length!=0" class="margin-top-10 text-right">
                                    <a class="btn btn-primary" v-on:click="generateMatrix()" style="width: 200px;">Go To Matrix</a>
                                    <a class="btn btn-primary" v-on:click="loadMatrixDialog = true;"
                                       style="width: 200px; ">Load Matrix Version</a>
                                    <a class="btn btn-primary" v-on:click="confirmCollapse();collapsedFlag = true;"
                                       style="width: 40px;"><span class="glyphicon glyphicon-chevron-up"></span></a>
                                </div>
                            </div>
                        </div>
                        <div v-if="collapsedFlag == true" class="row">
                            <div style="max-width: 1000px; margin-right: auto; margin-left: auto;">
                                <div class="col-md-2">
                                    <input v-model="taxonName" v-on:blur="changeTaxonName()"
                                           style="line-height: 38px; border: none;">
                                </div>
                                <div class="col-md-3">
                                    <input v-model="columnCount" v-on:keyup.enter="changeColumnCount()"
                                           v-on:blur="changeColumnCount()"
                                           style="width: 40px; margin-left: 30px; line-height: 38px; border:none;">
                                    Samples
                                </div>
                                <div class="col-md-5">
                                    <model-select :options="standardCharacters"
                                                  v-model="item"
                                                  placeholder="Search/create character here"
                                                  @searchchange="printSearchText"
                                                  @select="onSelect"
                                                  @resolve="onResolve"
                                    />
                                </div>
                                <div class="col-md-1">
                                    <a class="btn btn-primary" v-on:click="getIRI()" style="width: 40px;display:none;"><span
                                            class="glyphicon glyphicon-chevron-right"></span></a>
                                </div>
                            </div>
                            <div style="position: absolute; right: 150px;">
                                <a class="btn btn-primary" v-on:click="collapsedFlag = false;" style="width: 40px;"><span
                                        class="glyphicon glyphicon-chevron-down"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--<hr v-if="matrixShowFlag == true" style="margin-top: 40px; margin-bottom: 40px; border-top: 2px solid;">-->
                <div v-if="matrixShowFlag == true"
                     style="border-bottom: 2px solid; width: 100%; margin-top: 40px;text-align:center;">
                </div>
                <div v-if="matrixShowFlag == true" style="text-align: center;font-weight:bold;">
                    <div class="row">
                        <div class="col-md-3">
                            <a class="btn btn-primary" v-on:click="resetSystem()" style="height: 27px; font-size: 12px; margin-top: 3px;" v-if="user.email == 'hong@test.com'">Reset System</a>
                        </div>
                        <div class="col-md-6">
                            <h4 style="margin-top:3px;">matrix last loaded: {{ lastLoadMatrixName }}</h4>
                        </div>
                        <div class="col-md-3">
                            <a class="btn btn-primary" v-on:click="nameMatrixDialog = true;" style="height: 27px; font-size: 12px; margin-top: 3px;">Name this matrix version</a>
                        </div>
                    </div>
                </div>
                <div style="padding-left: 15px; padding-right: 15px; display: inline-flex; width: 100%;"
                     v-if="matrixShowFlag == true">
                     <!-- <a v-on:click="getIRIOfValues()"><span>Get IRI of values</span></a> -->
                    <div v-bind:class="{'width-95per': descriptionFlag == false}" style="min-width: 70%;">
                        <!--<ul class="nav nav-tabs">-->
                        <draggable
                                :list="userTags"
                                class="nav nav-tabs">
                            <li v-for="eachTag in userTags" v-bind:class="{ active: currentTab == eachTag.tag_name }"
                                :key="eachTag.name">
                                <a data-toggle="tab" v-on:click="showTableForTab(eachTag.tag_name)">
                                    {{ eachTag.tag_name}}
                                </a>
                                <div v-if="tagDeprecated[eachTag.tag_name] == 1" style="position: absolute;top: 0px;right: 2px;background: #3097D1;padding:4px;box-sizing: border-box;border-radius: 100%;"></div>
                            </li>
                            <!--<li v-for="eachTag in userTags" v-bind:class="{ active: currentTab == eachTag.tag_name }"><a-->
                            <!--data-toggle="tab" v-on:click="showTableForTab(eachTag.tag_name)">{{ eachTag.tag_name-->
                            <!--}}</a></li>-->
                        </draggable>
                        <!--</ul>-->
                        <div class="table-responsive">
                            <table class="table table-bordered cr-table">
                                <thead>
                                <tr>
                                    <th style="min-width: 300px; height: 43px; line-height: 43px; text-align: center;">
                                        Character
                                    </th>
                                    <th style="line-height: 43px; text-align: center;">
                                        Summary
                                    </th>
                                    <th v-if="header.id != 1" v-for="header in headers" style="min-width: 200px;">
                                        <input class="th-input" v-on:blur="saveHeader(header)" v-model="header.header"/>
                                        <a class="btn btn-add display-block"
                                           v-on:click="deleteHeader(header.id)"><span
                                                class="glyphicon glyphicon-remove"></span></a>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="row in values"
                                    v-if="userCharacters.find(ch => ch.id == row[0].character_id).show_flag == true">
                                    <td v-if="value.header_id == 1"
                                        v-for="value in row"
                                        style="cursor: pointer; display: flex; border-bottom:none">
                                        <div style="width: 30px;">
                                            <div style="height: 22px; line-height: 22px;">
                                                <span v-on:click="upUserValue(value.character_id)"
                                                      class="glyphicon glyphicon-chevron-up"></span>
                                            </div>
                                            <div style="height: 22px; line-height: 22px;">
                                                <span v-on:click="downUserValue(value.character_id)"
                                                      class="glyphicon glyphicon-chevron-down"></span>
                                            </div>
                                        </div>
                                        <div style="line-height: 30px;"
                                             v-tooltip="userCharacters.find(ch => ch.id == value.character_id).tooltip"
                                             v-bind:style="{'font-weight': userCharacters.find(ch => ch.id == value.character_id).deprecated >= 0 ? 'bold' : 'linear'}">
                                            {{ value.value }}
                                        </div>
                                        <div v-if="userCharacters.find(ch => ch.id == value.character_id).deprecated >= 0">
                                            <a  class="btn btn-add" 
                                                style="line-height: 30px; margin-left: 5px;"
                                                v-on:click="onResolveUserCharacter(userCharacters.find(ch => ch.id == value.character_id))">
                                                <span class="glyphicon glyphicon-wrench"></span>
                                            </a>
                                        </div>
                                        <div>
                                            <a class="btn btn-add"
                                               v-on:click="editCharacter(row[row.length - 1], true, false)"
                                               style="line-height: 30px;">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>
                                        </div>
                                        <div class="btn-group">
                                            <a v-if="checkHaveUnit(value.value) && !value.value.startsWith('Number of') && !value.value.startsWith('Ratio of')"
                                               class="btn btn-add dropdown-toggle"
                                               style="line-height: 30px; color: red;"
                                               data-toggle="dropdown">
                                                <span><b>{{ value.unit }}</b></span>
                                                <span class="glyphicon glyphicon-chevron-down"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </a>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a v-on:click="changeUnit(value.character_id, 'm')">m</a></li>
                                                <li><a v-on:click="changeUnit(value.character_id, 'dm')">dm</a></li>
                                                <li><a v-on:click="changeUnit(value.character_id, 'cm')">cm</a></li>
                                                <li><a v-on:click="changeUnit(value.character_id, 'mm')">mm</a></li>
                                            </ul>
                                        </div>
                                        <div v-if="checkHaveUnit(value.value)" class="btn-group">
                                            <a class="btn btn-add dropdown-toggle" style="line-height: 30px;"
                                               data-toggle="dropdown">
                                                <span><b>{{ value.summary }}</b></span>
                                                <span class="glyphicon glyphicon-chevron-down"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </a>
                                            <ul class="dropdown-menu" role="menu">
                                                <li>
                                                    <a v-on:click="changeSummary(value.character_id, 'range-percentile')">range-percentile</a>
                                                </li>
                                                <li><a v-on:click="changeSummary(value.character_id, 'mean')">mean</a>
                                                </li>
                                                <li>
                                                    <a v-on:click="changeSummary(value.character_id, 'median')">median</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div style="margin-left: 5px; line-height: 44px;">
                                            <a v-on:click="removeRowTable(value.character_id)" class="btn btn-add"><span
                                                    class="glyphicon glyphicon-remove"></span></a>
                                        </div>
                                    </td>
                                    <td style="line-height: 43px; text-align: center;">
                                        <div style="line-height: 21px;" v-html="calcSummary(row)"></div>
                                    </td>
                                    <template  v-for="value in row" v-if="value.header_id != 1">
                                        <td :key="value.id" v-on:click.self="focusedValue(value)" style="padding-left: 5px">
                                            <div v-if="checkHaveUnit(row.find(v => v.header_id == 1).value)" style="width: 80%; float:left">
                                                <input class="td-input" v-model="value.value" v-on:focus="focusedValue(value)"
                                                v-on:blur="saveItem($event, value)"/>
                                            </div>
                                            <div v-else style="width: 80%; float:left; text-align: center" v-on:click.self="focusedValue(value)">
                                                <div v-for="cv in allColorValues" v-if="cv.value_id == value.id" style="text-align: left" :key="cv.id">
                                                    <!-- {{colorValueText(cv)}} -->
                                                    <span style="color:#636b6f;" v-if="cv.negation && cv.negation != ''">{{cv.negation}} </span>
                                                    <span style="color:#636b6f;" v-if="cv.pre_constraint && cv.pre_constraint != ''">{{cv.pre_constraint}} </span>
                                                    <span style="color:#636b6f;" v-if="cv.negation && cv.negation != ''">{{cv.negation}} </span>
                                                    <span style="color:#636b6f;" v-if="cv.certainty_constraint && cv.certainty_constraint != ''">{{cv.certainty_constraint}} </span>
                                                    <span style="color:#636b6f;" v-if="cv.degree_constraint && cv.degree_constraint != ''">{{cv.degree_constraint}} </span>
                                                    <span 
                                                        v-bind:style="{'font-weight': deprecatedTerms.findIndex(value => value['deprecated IRI'] == cv.brightness_IRI) >= 0 ? 'bold' : 'linear'}"
                                                        style="color:#636b6f;" 
                                                        v-if="cv.brightness && cv.brightness != ''">
                                                        {{cv.brightness}} 
                                                        <a 
                                                            class="btn btn-add display-block" 
                                                            style="padding: 0px" 
                                                            v-on:click="onResolveColor(deprecatedTerms.findIndex(value => value['deprecated IRI'] == cv.brightness_IRI), 'colorBrightness')"
                                                            v-if="deprecatedTerms.findIndex(value => value['deprecated IRI'] == cv.brightness_IRI) >= 0">
                                                            <span class="glyphicon glyphicon-wrench">
                                                            </span>
                                                        </a>
                                                    </span>
                                                    
                                                    <span 
                                                        v-bind:style="{'font-weight': deprecatedTerms.findIndex(value => value['deprecated IRI'] == cv.reflectance_IRI) >= 0 ? 'bold' : 'linear'}"
                                                        style="color:#636b6f;" 
                                                        v-if="cv.reflectance && cv.reflectance != ''">
                                                        {{cv.reflectance}} 
                                                        <a 
                                                            class="btn btn-add display-block" 
                                                            style="padding: 0px" 
                                                            v-on:click="onResolveColor(deprecatedTerms.findIndex(value => value['deprecated IRI'] == cv.reflectance_IRI), 'colorReflectance')"
                                                            v-if="deprecatedTerms.findIndex(value => value['deprecated IRI'] == cv.reflectance_IRI) >= 0">
                                                            <span class="glyphicon glyphicon-wrench">
                                                            </span>
                                                        </a>
                                                    </span>
                                                    <span 
                                                        v-bind:style="{'font-weight': deprecatedTerms.findIndex(value => value['deprecated IRI'] == cv.saturation_IRI) >= 0 ? 'bold' : 'linear'}"
                                                        style="color:#636b6f;" 
                                                        v-if="cv.saturation && cv.saturation != ''">
                                                        {{cv.saturation}} 
                                                        <a 
                                                            class="btn btn-add display-block" 
                                                            style="padding: 0px" 
                                                            v-on:click="onResolveColor(deprecatedTerms.findIndex(value => value['deprecated IRI'] == cv.saturation_IRI), 'colorSaturation')"
                                                            v-if="deprecatedTerms.findIndex(value => value['deprecated IRI'] == cv.saturation_IRI) >= 0">
                                                            <span class="glyphicon glyphicon-wrench">
                                                            </span>
                                                        </a>
                                                    </span>
                                                    <span 
                                                        v-bind:style="{'font-weight': deprecatedTerms.findIndex(value => value['deprecated IRI'] == cv.colored_IRI) >= 0 ? 'bold' : 'linear'}"
                                                        style="color:#636b6f;" 
                                                        v-if="cv.colored && cv.colored != ''">
                                                        {{cv.colored}} 
                                                        <a 
                                                            class="btn btn-add display-block" 
                                                            style="padding: 0px" 
                                                            v-on:click="onResolveColor(deprecatedTerms.findIndex(value => value['deprecated IRI'] == cv.colored_IRI), 'colorColored')"
                                                            v-if="deprecatedTerms.findIndex(value => value['deprecated IRI'] == cv.colored_IRI) >= 0">
                                                            <span class="glyphicon glyphicon-wrench">
                                                            </span>
                                                        </a>
                                                    </span>
                                                    <span 
                                                        v-bind:style="{'font-weight': deprecatedTerms.findIndex(value => value['deprecated IRI'] == cv.multi_colored_IRI) >= 0 ? 'bold' : 'linear'}"
                                                        style="color:#636b6f;" 
                                                        v-if="cv.multi_colored && cv.multi_colored != ''">
                                                        {{cv.multi_colored}} 
                                                        <a 
                                                            class="btn btn-add display-block" 
                                                            style="padding: 0px" 
                                                            v-on:click="onResolveColor(deprecatedTerms.findIndex(value => value['deprecated IRI'] == cv.multi_colored_IRI), 'colorMultiColored')"
                                                            v-if="deprecatedTerms.findIndex(value => value['deprecated IRI'] == cv.multi_colored_IRI) >= 0">
                                                            <span class="glyphicon glyphicon-wrench">
                                                            </span>
                                                        </a>
                                                    </span>
                                                    <a class="btn btn-add display-block" style="padding: 0px" v-on:click="removeEachColor(cv)">
                                                        <span class="glyphicon glyphicon-remove">
                                                        </span>
                                                    </a>
                                                </div>
                                                <div v-for="ncv in allNonColorValues" v-if="ncv.value_id == value.id" style="text-align: left" :key="ncv.id" 
                                                    v-bind:style="{'font-weight': isNonColorDeprecated(ncv) >= 0 ? 'bold' : 'linear'}">
                                                    {{nonColorValueText(ncv)}}
                                                    <a class="btn btn-add display-block" style="padding: 0px" v-on:click="onResolveNonColorValue(ncv)" v-if="isNonColorDeprecated(ncv) >= 0">
                                                        <span class="glyphicon glyphicon-wrench">
                                                        </span>
                                                    </a>
                                                    <a class="btn btn-add display-block" style="padding: 0px" v-on:click="removeEachNonColor(ncv)">
                                                        <span class="glyphicon glyphicon-remove">
                                                        </span>
                                                    </a>
                                                </div>
                                                &nbsp;
                                            </div>
                                            <a style="width: 20%;" v-on:click="copyValuesToOther(value)">
                                                <img src="https://cdn0.iconfinder.com/data/icons/interaction-1-2-outlined/232/left-arrow-symbol-andright-arrow-symbol-forward-play-right-arrow-512.png"
                                                    style="width: 25px;"/>
                                            </a>
                                        </td>
                                    </template>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div style="border-left: 2px solid; margin-left: 5px;">
                        <div style="padding-top: 100px;">
                            <!--<div>-->
                            <!--<a class="btn btn-primary" v-on:click="expandTable()"><span class="glyphicon glyphicon-chevron-right"></span></a>-->
                            <!--</div>-->
                            <!--<div style="margin-top: 5px;">-->
                            <!--<a class="btn btn-primary" v-on:click="expandDescription()"><span class="glyphicon glyphicon-chevron-left"></span></a>-->
                            <!--</div>-->
                            <!--<a class="btn btn-default" style="border: none;" v-on:click="expandDescription()"><span
                                    class="glyphicon glyphicon-option-vertical" style="color: #1f648b;"></span></a>-->
                            <a class="btn btn-default" style="border: none;" v-tooltip="{ content:generateDescriptionTooltip, classes: 'standard-tooltip'}" v-on:click="expandDescription()">
                                <span
                                    style="color: #1f648b; font-weigth: bold; writing-mode: vertical-rl; text-orientation: upright; "
                                    >Generate Description</span>
                            </a>
                        </div>
                    </div>
                    <div v-if="descriptionFlag == true"
                         style="position:relative; min-width: 25%; max-width: 600px; overflow-y: scroll; word-wrap: break-word;"
                         class="panel">
                        <div class="panel-heading">
                            <div class="text-right" style="position: absolute; right: 10px; top: 0px;">
                                <a class="btn btn-primary" v-on:click="updateDescription()">Generate/Update</a>
                                <a class="btn btn-primary" v-on:click="exportDescription()">Export</a></div>
                        </div>
                        <div class="panel-body" style="min-height: 80px; position: absolute; right: 10px; top: 25px;"
                             v-html="descriptionText">
                        </div>

                    </div>

                </div>
                <div>
                    <div class="container">
                        <div v-if="newCharacterFlag" @close="newCharacterFlag = false">
                            <transition name="modal">
                                <div class="modal-mask character-modal">
                                    <div class="modal-wrapper">
                                        <div class="modal-container">
                                            <div class="modal-header">
                                                Input the character name in the input box and click OK.
                                            </div>
                                            <div class="modal-body">
                                                <b>Form character name:</b>
                                                <br>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input 
                                                            style="height: 26px; width: 100%;" 
                                                            type="text" 
                                                            v-on:focus="nounUndefined = false; lastCharacterDefinition = '';firstCharacterUndefined = false; firstCharacterDefinition = ''; wholeCharacterUndefined=false;wholeCharacterDefinition='';"
                                                            list="first_characters" 
                                                            placeholder="select or type"
                                                            v-model="firstCharacter"
                                                        />
                                                        <datalist id="first_characters">
                                                            <option value="Length">Length</option>
                                                            <option value="Width">Width</option>
                                                            <option value="Depth">Depth</option>
                                                            <option value="Diameter">Diameter</option>
                                                            <option value="Distance">Distance</option>
                                                            <option value="Color">Color</option>
                                                            <option value="Shape">Shape</option>
                                                            <option value="Texture">Texture</option>
                                                            <option value="Growth form">Growth form</option>
                                                        </datalist>
                                                        <!--<select v-model="firstCharacter" style="height: 26px;">-->
                                                        <!--<option>Length</option>-->
                                                        <!--<option>Width</option>-->
                                                        <!--<option>Depth</option>-->
                                                        <!--<option>Diameter</option>-->
                                                        <!--<option>Distance</option>-->
                                                        <!--</select>-->
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select v-model="middleCharacter" style="height: 26px;">
                                                            <option>of</option>
                                                            <option>between</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input v-model="lastCharacter" v-on:focus="nounUndefined = false; lastCharacterDefinition = '';firstCharacterUndefined = false; firstCharacterDefinition = ''; wholeCharacterUndefined=false;wholeCharacterDefinition='';" placeholder="enter a singular noun">
                                                        <br v-if="middleCharacter=='between'"/>
                                                        <div v-if="middleCharacter=='between'" style="width:100%; text-align: center">and</div>
                                                        <input v-if="middleCharacter=='between'" v-model="secondLastCharacter" v-on:focus="secondNounUndefined = false; lastCharacterDefinition = ''" placeholder="enter a singular noun">
                                                    </div>

                                                    <!--<input autofocus v-model="character.name" v-on:input="checkMsg"/>-->
                                                </div>
                                                <br>
                                                <div class ="row" v-if="firstCharacterUndefined">
                                                    <div class = "col-md-12">
                                                        What is {{firstCharacter}}? : <input v-model="firstCharacterDefinition" style="width:100%" :placeholder="'enter the definition of ' + firstCharacter">
                                                    </div>
                                                </div>
                                                <div class ="row" v-if="nounUndefined">
                                                    <div class = "col-md-12">
                                                        What is {{lastCharacter}}? : <input v-model="lastCharacterDefinition" style="width:100%" :placeholder="'enter the definition of ' + lastCharacter">
                                                    </div>
                                                </div>
                                                <div class ="row" v-if="secondNounUndefined && middleCharacter == 'between'">
                                                    <div class = "col-md-12">
                                                        What is {{secondLastCharacter}}? : <input v-model="secondLastCharacterDefinition" style="width:100%" :placeholder="'enter the definition of ' + secondLastCharacter">
                                                    </div>
                                                </div>
                                                <div class ="row" v-if="wholeCharacterUndefined">
                                                    <div class = "col-md-12">
                                                        What is {{firstCharacter + ' ' + middleCharacter + ' ' +  lastCharacter + (middleCharacter == 'between' ? (' and' + secondLastCharacter) : ' ')}}? : <input v-model="wholeCharacterDefinition" style="width:100%" :placeholder="'enter the definition of ' + firstCharacter + ' ' + middleCharacter + ' ' +  lastCharacter + (middleCharacter == 'between' ? (' and' + secondLastCharacter) : ' ')">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-primary ok-btn"
                                                   v-bind:class="{ disabled: !firstCharacter || !middleCharacter || !lastCharacter || nounUndefined && !lastCharacterDefinition || firstCharacterUndefined && !firstCharacterDefinition || wholeCharacterUndefined && !wholeCharacterDefinition || middleCharacter=='between' && (!secondLastCharacter || secondNounUndefined && !secondLastCharacterDefinition)}"
                                                   v-on:click="checkBracketConfirm()">
                                                    &nbsp; &nbsp; Next: Define Character &nbsp; &nbsp; </a>
                                                <a v-on:click="cancelNewCharacter()" class="btn btn-danger">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>
                        <div v-if="detailsFlag" @close="detailsFlag = false">
                            <transition name="modal">
                                <div class="modal-mask">
                                    <div class="modal-wrapper">
                                        <div class="modal-container">

                                            <div class="modal-header">
                                                <h3>Define character "{{ character.name }}", created by {{
                                                    character.username
                                                    }}</h3>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 radial-menu">
                                                        <ul style="margin-left: auto; margin-right: auto;">
                                                            <li><a v-on:click="showDetails('', metadataFlag)"></a></li>
                                                            <li class="method"
                                                                v-bind:class="{'back-grey': !checkHaveUnit(character.name)}">
                                                                <a
                                                                        :disabled="(!checkHaveUnit(character.name) || editFlag)"
                                                                        v-on:click="showDetails('method', metadataFlag)">1.
                                                                    Method<br><span
                                                                            class="glyphicon glyphicon-edit"></span></a>
                                                            </li>
                                                            <li class="unit"
                                                                v-bind:class="{'back-grey': !checkHaveUnit(character.name)}">
                                                                <a
                                                                        :disabled="!checkHaveUnit(character.name)"
                                                                        v-on:click="showDetails('unit', metadataFlag)">2.
                                                                    Unit<br><span
                                                                            class="glyphicon glyphicon-edit"></span></a>
                                                            </li>
                                                            <li class="tag"><a
                                                                    v-on:click="showDetails('tag', metadataFlag)">3.
                                                                Tag<br><span
                                                                        class="glyphicon glyphicon-edit"></span></a>
                                                            </li>
                                                            <li class="summary"
                                                                v-bind:class="{'back-grey': !checkHaveUnit(character.name)}">
                                                                <a
                                                                    :disabled="!checkHaveUnit(character.name)"
                                                                    v-on:click="showDetails('summary', metadataFlag)">4.
                                                                Summary<br>Function<br><span
                                                                        class="glyphicon glyphicon-edit"></span></a>
                                                            </li>
                                                            <li class="creator"><a
                                                                    v-on:click="showDetails('creator', metadataFlag)">Creator</a>
                                                            </li>
                                                            <li>
                                                                <a v-on:click="showDetails('usage', metadataFlag)">Usage</a>
                                                            </li>
                                                            <li>
                                                                <a v-on:click="showDetails('history', metadataFlag)">History</a>
                                                            </li>
                                                            <li><a v-on:click="showDetails('', metadataFlag)"></a></li>
                                                        </ul>
                                                        <div class="center">
                                                            <a>{{ character.name }}</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div id="metadataPlace">
                                                            <div :is="currentMetadata" :parentData="parentData"
                                                                 @interface="handleFcAfterDateBack">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 text-right" style="margin-top: 15px;">
                                                        <a v-if="viewFlag == false"
                                                           v-on:click="saveCharacter(metadataFlag)"
                                                           v-bind:class="{disabled: saveCharacterButtonFlag}"
                                                           class="btn btn-primary">Save</a>
                                                        <a v-if="viewFlag == true" v-on:click="use(item)"
                                                           class="btn btn-primary">Use this</a>
                                                        <!--<a v-if="viewFlag == true" v-on:click="enhance(item)"-->
                                                        <!--class="btn btn-primary">Clone and enhance</a>-->
                                                        <a v-if="viewFlag == true" v-on:click="enhance(item)"
                                                           class="btn btn-primary">Modify and Use &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b style="border: 1px solid #ffffff; background: #003366;"
                                                                title="Use this option when this character fits your needs but can be better defined. The original definition will be retained and the modified definition will be saved as a different character.">?</b></a>
                                                        <a v-on:click="cancelCharacter()"
                                                           class="btn btn-danger">Cancel</a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>
                        <div v-if="confirmMethod" @close="confirmMethod = false">
                            <transition name="modal">
                                <div class="modal-mask">
                                    <div class="modal-wrapper">
                                        <div class="modal-container">
                                            <div class="modal-header">
                                                <b>Review Your Decision</b>
                                            </div>
                                            <div class="modal-body">
                                                <div v-if="!character.method_as">
                                                    <div>
                                                        Please <b>review the method definition carefully</b>. Is this
                                                        what
                                                        you would
                                                        like
                                                        to save for <i>{{ character.name }}</i>?
                                                    </div>
                                                    <br>
                                                    <div v-if="character.method_from">
                                                        <b>From:</b> {{ character.method_from }}
                                                    </div>
                                                    <div v-if="character.method_to">
                                                        <b>To:</b> {{ character.method_to }}
                                                    </div>
                                                    <div v-if="character.method_include">
                                                        <b>Include:</b> {{ character.method_include }}
                                                    </div>
                                                    <div v-if="character.method_exclude">
                                                        <b>Exclude:</b> {{ character.method_exclude }}
                                                    </div>
                                                    <div v-if="character.method_where">
                                                        <b>Where:</b> {{ character.method_where }}
                                                    </div>
                                                </div>
                                                <div v-if="character.method_as">
                                                    <div>
                                                        Please <b>review the method definition carefully</b>. Is this
                                                        what
                                                        you
                                                        would like
                                                        to save for <i>{{ character.name }}</i>?
                                                    </div>
                                                    <div>
                                                        <img class="img-method"
                                                             style="width: 100%;"
                                                             v-bind:src="'https://drive.google.com/uc?id=' + character.method_as.split('id=')[1].substring(0, character.method_as.split('id=')[1].length)"/>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <a class="btn btn-primary ok-btn"
                                                       v-on:click="methodConfirm()">
                                                        &nbsp; &nbsp; Confirm &nbsp; &nbsp; </a>
                                                    <a v-on:click="cancelConfirmMethod()"
                                                       class="btn btn-danger">Cancel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>
                        <div v-if="confirmUnit" @close="confirmUnit = false">
                            <transition name="modal">
                                <div class="modal-mask">
                                    <div class="modal-wrapper">
                                        <div class="modal-container">
                                            <div class="modal-header">
                                                <b>Review Your Decision</b>
                                            </div>
                                            <div class="modal-body">
                                                <div>
                                                    You've selected <b>{{ character.unit }}</b> as the Unit for <i>{{
                                                    character.name }}</i>.
                                                </div>
                                                <div class="modal-footer">
                                                    <a class="btn btn-primary ok-btn"
                                                       v-on:click="unitConfirm()">
                                                        &nbsp; &nbsp; Confirm &nbsp; &nbsp; </a>
                                                    <a v-on:click="cancelConfirmUnit()"
                                                       class="btn btn-danger">Cancel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>
                    <div v-if="confirmTag" @close="confirmTag = false">
                        <transition name="modal">
                            <div class="modal-mask">
                                <div class="modal-wrapper">
                                    <div class="modal-container">
                                        <div class="modal-header">
                                            <b>Review Your Decision</b>
                                        </div>
                                        <div class="modal-body">
                                            <div>
                                                You've selected <b>{{ character.standard_tag }}</b> as the Tag for <i>{{
                                                character.name }}</i>.
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-primary ok-btn"
                                                   v-on:click="tagConfirm()">
                                                    &nbsp; &nbsp; Confirm &nbsp; &nbsp; </a>
                                                <a v-on:click="cancelConfirmTag()" class="btn btn-danger">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
                <div v-if="confirmSummary" @close="confirmSummary = false">
                    <transition name="modal">
                        <div class="modal-mask">
                            <div class="modal-wrapper">
                                <div class="modal-container">
                                    <div class="modal-header">
                                        <b>Review Your Decision</b>
                                    </div>
                                    <div class="modal-body">
                                        <div v-if="character.summary">
                                            <b>{{ character.summary }}</b> will be used as the Summary Function for <i>{{
                                            character.name }}</i>.
                                        </div>
                                        <div v-if="!character.summary">
                                            Confirm "{{ character.name }} is a categorical character. <br/>If it is a numerical character, click on Cancel, and select a summary function for it.
                                        </div>
                                        <div class="modal-footer">
                                            <a class="btn btn-primary ok-btn"
                                               v-on:click="confirmSave(metadataFlag)">
                                                &nbsp; &nbsp; Confirm &nbsp; &nbsp; </a>
                                            <a v-on:click="cancelConfirmSummary()" class="btn btn-danger">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
                <div v-if="removeAllStandardFlag" @close="removeAllStandardFlag = false">
                    <transition name="modal">
                        <div class="modal-mask">
                            <div class="modal-wrapper">
                                <div class="modal-container">
                                    <div class="modal-header">
                                        <b>Confirmation</b>
                                    </div>
                                    <div class="modal-body">
                                        <div>
                                            <b>Do you want to remove all recommended characters from your matrix?</b>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <a class="btn btn-primary ok-btn"
                                            v-on:click="removeAllStandardCharacters()">
                                            &nbsp; &nbsp; Confirm &nbsp; &nbsp; </a>
                                        <a v-on:click="removeAllStandardFlag = false;"
                                            class="btn btn-danger">Cancel</a>
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
                                    <div style="max-height:80vh; overflow-y:scroll;">
                                        <div class="modal-header">
                                            <b>Add a Value for <font style="color: #0070C0; font-style: italic">{{ currentCharacter.name }}:</font></b> {{ currentColorValue.value.slice(0, -2) }}
                                            <br/>
                                            <hr>
                                            <div v-if="existColorDetails.length > 0"
                                                style="border-radius: 5px; border: 1px solid; padding: 15px;">
                                                <div style="float: right;">
                                                    <a class="btn btn-primary" v-if="existColorDetailsFlag == false"
                                                    v-on:click="existColorDetailsFlag = true;">
                                                        <span class="glyphicon glyphicon-chevron-down"></span>
                                                    </a>
                                                    <a class="btn btn-primary" v-if="existColorDetailsFlag == true"
                                                    v-on:click="existColorDetailsFlag = false;">
                                                        <span class="glyphicon glyphicon-chevron-up"></span>
                                                    </a>
                                                </div>

                                                <div style="margin-top: 10px; min-height: auto;" class="table-responsive">
                                                    <div>
                                                        <b style="text-decoration: underline">Reuse a Value</b>
                                                    </div>
                                                    <div v-if="existColorDetailsFlag == true" style="margin-top: 10px;">
                                                        <div v-for="(eachDetails,index) in existColorDetails" :key="eachDetails.id" style="border-bottom: gray; padding 2px; font-size: 11pt">
                                                            <hr v-if="index" style="margin-top: 8px; margin-bottom: 8px; border-top-color: #ddd;">
                                                            <span style="margin-left: 20px; margin-right: 50px">
                                                                <a class="btn btn-primary"
                                                                v-on:click="selectExistDetails(eachDetails)" style="padding-top: 3px; padding-bottom: 3px;">Use this</a>
                                                            </span>
                                                            <b>
                                                                <span>
                                                                    {{ eachDetails.negation }}
                                                                </span>
                                                                <span>
                                                                    {{ eachDetails.pre_constraint }}
                                                                </span>
                                                                <span>
                                                                    {{ eachDetails.certainty_constraint }}
                                                                </span>
                                                                <span>
                                                                    {{ eachDetails.degree_constraint }}
                                                                </span>
                                                                <span>
                                                                    {{ eachDetails.brightness }}
                                                                </span>
                                                                <span>
                                                                    {{ eachDetails.reflectance }}
                                                                </span>
                                                                <span>
                                                                    {{ eachDetails.saturation }}
                                                                </span>
                                                                <span>
                                                                    {{ eachDetails.colored }}
                                                                </span>
                                                                <span>
                                                                    {{ eachDetails.multi_colored }}
                                                                </span>
                                                                <span>
                                                                    {{ eachDetails.post_constraint }}
                                                                </span>
                                                            </b>
                                                            <span>
                                                                , usages = {{ eachDetails.count }}
                                                            </span>
                                                            <span>
                                                                , creator = {{ eachDetails.username }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                        <div class="modal-body">

                                            <div style="border-radius: 5px; border: 1px solid; padding: 15px;">
                                                <div>
                                                    <b style="text-decoration: underline">Create/Edit Value</b>
                                                </div>
                                                <div>
                                                    <div style="display: inline-block;">
                                                        <!-- <input v-on:focus="changeColorSection(currentColorValue, 'negation', $event)"
                                                            style="width: 90px; border:none; border-bottom: 1px solid; text-align:center;"
                                                            v-model="currentColorValue.negation" placeholder=""> -->
                                                        <select style="width: 90px; height: 26px;"
                                                                v-model="currentColorValue.negation"
                                                                v-on:change="changeColorSection(currentNonColorValue, 'negation', $event)">
                                                            <option value=""></option>
                                                            <option value="not">not</option>
                                                        </select>
                                                        <h5>
                                                            negation
                                                        </h5>
                                                    </div>
                                                    <div style="display: inline-block;">
                                                        <input v-on:focus="changeColorSection(currentColorValue, 'pre_constraint', $event)"
                                                            style="width: 90px; height: 26px;"
                                                            v-model="currentColorValue.pre_constraint"
                                                            list="pre_list">
                                                        <datalist id="pre_list">
                                                            <option v-for="each in preList" :value="each">{{ each }}
                                                            </option>
                                                        </datalist>
                                                        <h5>
                                                            pre-constraint
                                                        </h5>
                                                    </div>
                                                    <div style="display: inline-block;">
                                                        <select style="width: 90px; height: 26px;"
                                                                v-model="currentColorValue.certainty_constraint"
                                                                v-on:change="changeColorSection(currentColorValue, 'certainty_constraint', $event)">
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
                                                                v-model="currentColorValue.degree_constraint"
                                                                v-on:change="changeColorSection(currentColorValue, 'degree_constraint', $event)">
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
                                                        <input v-on:focus="changeColorSection(currentColorValue, 'brightness', $event)"
                                                            v-on:keyup.enter="$event.target.blur();saveColorValue();"
                                                            style="width: 90px; border:none; border-bottom: 1px solid; text-align:left;"
                                                            v-model="currentColorValue.brightness"
                                                            class="color-input">
                                                        <h5>
                                                            brightness
                                                        </h5>
                                                    </div>
                                                    <div style="display: inline-block;">
                                                        <input v-on:focus="changeColorSection(currentColorValue, 'reflectance', $event)"
                                                            v-on:keyup.enter="$event.target.blur();saveColorValue();"
                                                            style="width: 90px; border:none; border-bottom: 1px solid; text-align:left;"
                                                            v-model="currentColorValue.reflectance"
                                                            class="color-input">
                                                        <h5>
                                                            reflectance
                                                        </h5>
                                                    </div>
                                                    <div style="display: inline-block;">
                                                        <input v-on:focus="changeColorSection(currentColorValue, 'saturation', $event)"
                                                            v-on:keyup.enter="$event.target.blur();saveColorValue();"
                                                            style="width: 90px; border:none; border-bottom: 1px solid; text-align:left;"
                                                            v-model="currentColorValue.saturation"
                                                            class="color-input">
                                                        <h5>
                                                            saturation
                                                        </h5>
                                                    </div>
                                                    <div style="display: inline-block;">
                                                        <input v-on:focus="changeColorSection(currentColorValue, 'colored', $event)"
                                                            v-on:keyup.enter="$event.target.blur();saveColorValue();"
                                                            style="width: 90px; border:none; border-bottom: 1px solid; text-align:left;"
                                                            v-model="currentColorValue.colored"
                                                            class="color-input">
                                                        <h5>
                                                            color
                                                        </h5>
                                                    </div>
                                                    <div style="display: inline-block;">
                                                        <input v-on:focus="changeColorSection(currentColorValue, 'multi_colored', $event)"
                                                            v-on:keyup.enter="$event.target.blur();saveColorValue();"
                                                            style="width: 90px; border:none; border-bottom: 1px solid; text-align:left;"
                                                            v-model="currentColorValue.multi_colored"
                                                            class="color-input">
                                                        <h5>
                                                            pattern
                                                        </h5>
                                                    </div>
                                                    <div style="display: inline-block;">
                                                        <input v-on:focus="changeColorSection(currentColorValue, 'post_constraint', $event)"
                                                            style="width: 90px;"
                                                            v-model="currentColorValue.post_constraint"
                                                            list="post_list">
                                                        <datalist id="post_list">
                                                            <option v-for="each in postList" :value="each">{{ each }}
                                                            </option>
                                                        </datalist>
                                                        <h5>
                                                            post-constraint
                                                        </h5>
                                                    </div>
                                                </div>
                                                <div v-if="currentColorValue.detailFlag == 'negation'"
                                                    style="margin-top: 10px;">
                                                    <input type="radio" id="not" v-model="currentColorValue.negation"
                                                        v-bind:value="'Not'"/> <label for="not">Not</label> <br/>
                                                    <input type="radio" id="unselect-not"
                                                        v-model="currentColorValue.negation"
                                                        v-bind:value="''"/> <label for="unselect-not">Unselect
                                                    Not</label>
                                                </div>
                                                <div v-if="(currentColorValue.detailFlag == 'brightness'
                                                || currentColorValue.detailFlag == 'reflectance'
                                                || currentColorValue.detailFlag == 'saturation'
                                                || currentColorValue.detailFlag == 'colored'
                                                || currentColorValue.detailFlag == 'multi_colored') && colorExistFlag"
                                                    style="margin-top: 10px;">
                                                    <!--<input style="width: 300px;" v-model="colorSearchText" placeholder="Enter a term to filter the term tree"/>-->
                                                    <tree
                                                            :data="treeData"
                                                            :options="colorTreeOption"
                                                            :filter="filterFlag?currentColorValue[currentColorValue.detailFlag]:null"
                                                            ref="colorTree"
                                                            @node:selected="onTreeNodeSelected"
                                                            style="max-height: 300px;">
                                                        <div slot-scope="{ node }" class="node-container">
                                                            <div class="node-text" v-tooltip="node.text">
                                                                {{ node.text }}
                                                                <span v-if="node.data.images && node.data.images.length != 0" class="glyphicon glyphicon-picture" @click="showViewer(node)"></span>
                                                            </div>
                                                        </div>
                                                    </tree>
                                                </div>
                                                <div v-if="(currentColorValue.detailFlag == 'brightness'
                                                || currentColorValue.detailFlag == 'reflectance'
                                                || currentColorValue.detailFlag == 'saturation'
                                                || currentColorValue.detailFlag == 'colored'
                                                || currentColorValue.detailFlag == 'multi_colored') && !colorExistFlag"
                                                    style="margin-top: 10px;">
                                                    <div v-for="flag in colorFlags" v-if="colorSynonyms[flag]" :key="flag">
                                                        <big>{{flag}} : {{originColorValue[flag]}}</big>
                                                        <br>
                                                        <b>Did you mean?</b>
                                                        <div style="margin-left:20px">
                                                            <div v-for="eachSynonym in colorSynonyms[flag]">
                                                                <input type="radio" v-bind:id="eachSynonym.term"
                                                                    v-bind:value="eachSynonym.term"
                                                                    v-on:change="selectedSynonymForColor(flag)"
                                                                    v-model="currentColorValue[flag]">
                                                                <label v-bind:for="eachSynonym.term"> {{ eachSynonym.term }} ({{
                                                                    eachSynonym.parentTerm }}): </label> {{
                                                                eachSynonym.definition ? eachSynonym.definition : 'No definition' }}
                                                                <!-- <a class="btn"
                                                                v-on:click="expandCommentSection(eachSynonym, currentColorValue.detailFlag)"><span
                                                                        class="glyphicon glyphicon-comment"></span></a>
                                                                <div v-if="eachSynonym.commentFlag == true">
                                                                    Do not like this term? improve or add definition for it:
                                                                    <input
                                                                            v-model="colorComment[currentColorValue.detailFlag]"
                                                                            style="width: 100%;">
                                                                </div> -->
                                                            </div>
                                                            <input type="radio" id="user-defined"
                                                                v-bind:value="defaultColorValue[flag]"
                                                                v-on:change="selectUserDefinedTerm(currentColorValue, flag, defaultColorValue[flag])"
                                                                v-model="currentColorValue[flag]">
                                                            <label for="user-defined">Use my term '{{ defaultColorValue[flag] }}'(as a
                                                                {{ changeFlagToLabel(flag) }}). Please
                                                                define the term, all input required:</label>
                                                            <div for="user-defined">
                                                                Definition: <input
                                                                    v-model="userColorDefinition[flag]"
                                                                    class="color-definition-input">
                                                                Used for Taxon:
                                                                <input v-model="colorTaxon[flag]"
                                                                    class="color-definition-input">
                                                                Sample Sentence:
                                                                <input
                                                                        v-model="colorSampleText[flag]"
                                                                        class="color-definition-input" placeholder=""><br/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                
                                                <div class="row">
                                                    <div style="float: right; margin-right: 20px">
                                                        <a class="btn btn-primary ok-btn"
                                                        :disabled="saveColorButtonFlag"
                                                        v-on:click="saveColorValue()">
                                                            Save </a>
                                                        &nbsp;&nbsp;
                                                        <!-- <a class="btn btn-primary ok-btn"
                                                        :disabled="saveColorButtonFlag"
                                                        v-on:click="saveColorValue(true)">
                                                            Save & New </a> -->
                                                        <a v-on:click="colorDetailsFlag = false;"
                                                        class="btn btn-danger">Cancel</a>
                                                        &nbsp;&nbsp;
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
                <div v-if="nonColorDetailsFlag" @close="nonColorDetailsFlag = false">
                    <transition name="modal">
                        <div class="modal-mask">
                            <div class="modal-wrapper">
                                <div class="modal-container">
                                    <div style="max-height:80vh; overflow-y:scroll;">
                                        <div class="modal-header">
                                            <b>Add a Value for <font style="color: #0070C0; font-style: italic">{{ currentCharacter.name }}:</font></b> {{ currentNonColorValue.value.slice(0, -2) }}
                                            <br/>
                                            <hr>
                                            <div v-if="existNonColorDetails.length > 0"
                                                style="border-radius: 5px; border: 1px solid; padding: 15px;">
                                                <div style="float: right;">
                                                    <a class="btn btn-primary" v-if="existNonColorDetailsFlag == false"
                                                    v-on:click="existNonColorDetailsFlag = true;">
                                                        <span class="glyphicon glyphicon-chevron-down"></span>
                                                    </a>
                                                    <a class="btn btn-primary" v-if="existNonColorDetailsFlag == true"
                                                    v-on:click="existNonColorDetailsFlag = false;">
                                                        <span class="glyphicon glyphicon-chevron-up"></span>
                                                    </a>
                                                </div>

                                                <div style="margin-top: 10px; min-height: auto;" class="table-responsive">
                                                    <div>
                                                        <b style="text-decoration: underline">Reuse a Value</b>
                                                    </div>
                                                    <div v-if="existNonColorDetailsFlag == true" style="margin-top: 10px;">
                                                        <div v-for="(eachDetails, index) in existNonColorDetails" :key="eachDetails.id" style="border-bottom: gray; padding 2px; font-size: 11pt">
                                                            <hr v-if="index" style="margin-top: 8px; margin-bottom: 8px; border-top-color: #ddd;">
                                                            <span style="margin-left: 20px; margin-right: 50px">
                                                                <a class="btn btn-primary" style="padding-top: 3px; padding-bottom: 3px;"
                                                                v-on:click="selectExistNonColorDetails(eachDetails)">Use this</a>
                                                            </span>
                                                            <b>
                                                                <span>
                                                                    {{ eachDetails.negation }}
                                                                </span>
                                                                <span>
                                                                    {{ eachDetails.pre_constraint }}
                                                                </span>
                                                                <span>
                                                                    {{ eachDetails.certainty_constraint }}
                                                                </span>
                                                                <span>
                                                                    {{ eachDetails.degree_constraint }}
                                                                </span>
                                                                <span>
                                                                    {{ eachDetails.main_value }}
                                                                </span>
                                                                <span>
                                                                    {{ eachDetails.post_constraint }}
                                                                </span>
                                                            </b>
                                                            <span>
                                                                , usages = {{ eachDetails.count }}
                                                            </span>
                                                            <span>
                                                                , creator = {{ eachDetails.username }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <!-- <div v-if="nonColorDetails.length > 0"
                                                style="border-radius: 5px; border: 1px solid; padding: 15px; margin-top: 10px;">
                                                <div style="float: right;">
                                                    <a class="btn btn-primary" v-if="currentNonColorValueExist == false"
                                                    v-on:click="currentNonColorValueExist = true;">
                                                        <span class="glyphicon glyphicon-chevron-down"></span>
                                                    </a>
                                                    <a class="btn btn-primary" v-if="currentNonColorValueExist == true"
                                                    v-on:click="currentNonColorValueExist = false;">
                                                        <span class="glyphicon glyphicon-chevron-up"></span>
                                                    </a>
                                                </div>
                                                <div>
                                                    <b>Current values</b>
                                                </div>
                                                <div v-for="(eachValue, index) in nonColorDetails"
                                                    v-if="currentNonColorValueExist == true" class="row"
                                                    style="margin-top: 5px;">
                                                    <div class="col-md-6">
                                                        <div style="display: inline-block;"
                                                            v-if="eachValue.negation != null">
                                                            {{ eachValue.negation }}
                                                        </div>
                                                        <div style="display: inline-block;"
                                                            v-if="eachValue.pre_constraint != null">
                                                            {{ eachValue.pre_constraint }}
                                                        </div>
                                                        <div style="display: inline-block;"
                                                            v-if="eachValue.certainty_constraint != null">
                                                            {{ eachValue.certainty_constraint }}
                                                        </div>
                                                        <div style="display: inline-block;"
                                                            v-if="eachValue.degree_constraint != null">
                                                            {{ eachValue.degree_constraint }}
                                                        </div>
                                                        <div style="display: inline-block;"
                                                            v-if="eachValue.main_value != null">
                                                            {{ eachValue.main_value }}
                                                        </div>
                                                        <div style="display: inline-block;"
                                                            v-if="eachValue.post_constraint != null">
                                                            {{ eachValue.post_constraint }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <a class="btn btn-primary" style="padding: 3px 6px;"
                                                        v-on:click="editEachNonColor(eachValue)">Edit</a>
                                                        <a class="btn btn-primary" style="padding: 3px 6px;"
                                                        v-on:click="removeEachNonColor(eachValue)">Remove</a>
                                                    </div>
                                                </div>
                                            </div> -->

                                        </div>
                                        <div class="modal-body">

                                            <div style="border-radius: 5px; border: 1px solid; padding: 15px;">
                                                <div>
                                                    <b style="text-decoration: underline">Create/Edit Value</b>
                                                </div>
                                                <div>
                                                    <div style="display: inline-block;">
                                                        <!-- <input v-on:focus="changeNonColorSection(currentNonColorValue, 'negation', $event)"
                                                            style="width: 90px; border:none; border-bottom: 1px solid; text-align:center;"
                                                            v-model="currentNonColorValue.negation" placeholder=""> -->
                                                        <select style="width: 90px; height: 26px;"
                                                                v-model="currentNonColorValue.negation"
                                                                v-on:change="changeNonColorSection(currentNonColorValue, 'negation', $event)">
                                                            <option value=""></option>
                                                            <option value="not">not</option>
                                                        </select>
                                                        <h5>
                                                            negation
                                                        </h5>
                                                    </div>
                                                    <div style="display: inline-block;">
                                                        <input v-on:focus="changeNonColorSection(currentNonColorValue, 'pre_constraint', $event)"
                                                            style="width: 90px; height: 26px;"
                                                            v-model="currentNonColorValue.pre_constraint"
                                                            list="pre_non_list">
                                                        <datalist id="pre_non_list">
                                                            <option v-for="each in preList" :value="each">{{ each }}
                                                            </option>
                                                        </datalist>
                                                        <h5>
                                                            pre-constraint
                                                        </h5>
                                                    </div>
                                                    <div style="display: inline-block;">
                                                        <select style="width: 90px; height: 26px;"
                                                                v-model="currentNonColorValue.certainty_constraint"
                                                                v-on:change="changeNonColorSection(currentNonColorValue, 'certainty_constraint', $event)">
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
                                                                v-model="currentNonColorValue.degree_constraint"
                                                                v-on:change="changeNonColorSection(currentNonColorValue, 'degree_constraint', $event)">
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
                                                        <input v-on:focus="changeNonColorSection(currentNonColorValue, 'main_value', $event)"
                                                            v-on:keyup.enter="$event.target.blur();saveNonColorValue();"
                                                            style="width: 90px; border:none; border-bottom: 1px solid; text-align:left;"
                                                            v-model="currentNonColorValue.main_value"
                                                            >
                                                        <h5>
                                                            {{ currentNonColorValue.placeholderName }}
                                                        </h5>
                                                    </div>
                                                    <div style="display: inline-block;">
                                                        <input v-on:focus="changeNonColorSection(currentNonColorValue, 'post_constraint', $event)"
                                                            style="width: 90px;"
                                                            v-model="currentNonColorValue.post_constraint"
                                                            list="post_non_list">
                                                        <datalist id="post_non_list" v-if="postList.length > 0">
                                                            <option v-for="each in postList" :value="each">{{ each }}
                                                            </option>
                                                        </datalist>
                                                        <h5>
                                                            post-constraint
                                                        </h5>
                                                    </div>
                                                </div>
                                                <!-- <div v-if="currentNonColorValue.detailFlag == 'negation'"
                                                    style="margin-top: 10px;">
                                                    <input type="radio" id="non-not" v-model="currentNonColorValue.negation"
                                                        v-bind:value="'Not'"/> <label for="non-not">Not</label> <br/>
                                                    <input type="radio" id="non-unselect-not"
                                                        v-model="currentNonColorValue.negation"
                                                        v-bind:value="''"/> <label for="non-unselect-not">Unselect
                                                    Not</label>
                                                </div> -->
                                                <div v-if="(currentNonColorValue.detailFlag == 'main_value') && nonColorExistFlag"
                                                    style="margin-top: 10px;">
                                                    <!--<input style="width: 300px;" v-model="nonColorSearchText" placeholder="Enter a term to filter the term tree"/>-->
                                                    <tree
                                                            :data="textureTreeData"
                                                            :options="colorTreeOption"
                                                            :filter="filterFlag?currentNonColorValue.main_value:null"
                                                            ref="nonColorTree"
                                                            @node:selected="onTextureTreeNodeSelected"
                                                            style="max-height: 300px;">
                                                        <div slot-scope="{ node }" class="node-container">
                                                            <div class="node-text" v-tooltip="node.text">
                                                                {{ node.text }}
                                                                <span v-if="node.data.images && node.data.images.length != 0" class="glyphicon glyphicon-picture" @click="showViewer(node)"></span>
                                                            </div>
                                                        </div>
                                                    </tree>
                                                </div>
                                                <div v-if="(currentNonColorValue.detailFlag == 'main_value') && !nonColorExistFlag"
                                                    style="margin-top: 10px;">
                                                    <b>Did you mean? </b>

                                                    <div v-if="searchNonColorFlag == 1">
                                                        <div v-for="eachSynonym in nonColorSynonyms">
                                                            <input type="radio" v-bind:id="eachSynonym.term"
                                                                v-bind:value="eachSynonym.term"
                                                                v-model="currentNonColorValue[currentNonColorValue.detailFlag]">
                                                            <label v-bind:for="eachSynonym.term">{{ eachSynonym.term }} ({{
                                                                eachSynonym.parentTerm }}): </label> {{
                                                            eachSynonym.definition ? eachSynonym.definition : 'No definition' }}
                                                            <!-- <a class="btn"
                                                            v-on:click="expandCommentSection(eachSynonym, currentNonColorValue.detailFlag)"><span
                                                                    class="glyphicon glyphicon-comment"></span></a>
                                                            <div v-if="eachSynonym.commentFlag == true">
                                                                Don't you like this term? improve or add definition for it:
                                                                <input
                                                                        v-model="nonColorComment[currentNonColorValue.detailFlag]"
                                                                        style="width: 100%;">
                                                            </div> -->
                                                        </div>
                                                    </div>
                                                    <div v-if="searchNonColorFlag !=2 ">
                                                        <input type="radio" id="non-user-defined"
                                                            v-bind:value="defaultNonColorValue"
                                                            v-on:change="selectUserDefinedTerm(currentNonColorValue, currentNonColorValue.detailFlag, defaultNonColorValue)"
                                                            v-model="currentNonColorValue[currentNonColorValue.detailFlag]">
                                                        <!--<input type="radio" id="non-user-defined"-->
                                                        <!--v-bind:value="defaultNonColorValue + '(user defined)'"-->
                                                        <!--v-on:change="selectUserDefinedTerm(currentNonColorValue, currentNonColorValue.detailFlag, defaultNonColorValue)"-->
                                                        <!--v-model="currentNonColorValue[currentNonColorValue.detailFlag]">-->
                                                        <label for="non-user-defined">Use my term '{{ defaultNonColorValue
                                                            }}'(please define the term, all input required):</label>
                                                        <div for="user-defined">
                                                            Definition: <input
                                                                v-model="userNonColorDefinition[currentNonColorValue.detailFlag]"
                                                                class="non-color-input-definition">
                                                            Taxon: <input
                                                                v-model="nonColorTaxon[currentNonColorValue.detailFlag]"
                                                                class="non-color-input-definition">
                                                            Sample Sentence: <input
                                                                v-model="nonColorSampleText[currentNonColorValue.detailFlag]"
                                                                class="non-color-input-definition">
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div style="float: right; margin-right: 20px">
                                                        <a class="btn btn-primary ok-btn"
                                                        :disabled="saveNonColorButtonFlag"
                                                        v-on:click="saveNonColorValue()">
                                                            Save </a>
                                                        <!-- <a class="btn btn-primary ok-btn"
                                                        :disabled="saveNonColorButtonFlag"
                                                        v-on:click="saveNonColorValue(true)">
                                                            Save & New </a> -->
                                                        <a v-on:click="nonColorDetailsFlag = false;currentNonColorValue.main_value='';currentNonColorValue.negation = null;currentNonColorValue.pre_constraint = null;currentNonColorValue.certainty_constraint = null;currentNonColorValue.degree_constraint = null;currentNonColorValue.post_constraint = null;currentNonColorValue.confirmedFlag['main_value'] = false;"
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
                <div v-if="confirmOverwriteFlag" @close="confirmOverwriteFlag = false">
                    <transition name="modal">
                        <div class="modal-mask">
                            <div class="modal-wrapper">
                                <div class="modal-container">
                                    <div class="modal-header">
                                        <b>Confirm to overwrite?</b>
                                    </div>
                                    <div class="modal-body">
                                        <div>
                                            <b>There are values in the cells to be filled. Overwrite or keep the old
                                            values?</b>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <a class="btn btn-primary ok-btn"
                                                   v-on:click="confirmOverwrite()">
                                                    Overwrite </a>
                                                <a v-on:click="keepExistingValue()"
                                                   class="btn btn-danger">Keep existing values</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
                <div v-if="toRemoveStandardConfirmFlag" @close="toRemoveStandardConfirmFlag = false">
                    <transition name="modal">
                        <div class="modal-mask">
                            <div class="modal-wrapper">
                                <div class="modal-container">
                                    <div class="modal-header">
                                        <b>Confirm to remove?</b>
                                    </div>
                                    <div class="modal-body">
                                        <div>
                                            <b>Are you sure you want to remove {{ userCharacters.find(each => each.id == toRemoveCharacterId).name }}?</b>
                                        </div>
                                        <br/>
                                        <br/>
                                        <i v-if="userCharacters.find(each => each.id == toRemoveCharacterId).standard == 1">
                                            This recommended character can be restored by selecting the character in "Search/create character' search box (see the image below).
                                        </i>
                                        <i v-if="userCharacters.find(each => each.id == toRemoveCharacterId).standard == 0">
                                            This user-defined character may not be restored after being deleted if it is not used by others. But you can always recreate this character by selecting 'Click HERE to create new character' as shown in the image below.
                                        </i>
                                        <img src="/chrecorder/public/images/remove_confirm_image.png">
                                    </div>
                                    <div class="modal-footer">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <a class="btn btn-primary ok-btn"
                                                   v-on:click="confirmRemoveCharacter()">
                                                    Remove </a>
                                                <a v-on:click="toRemoveStandardConfirmFlag = false"
                                                   class="btn btn-danger">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
                <div v-if="toRemoveHeaderConfirmFlag" @close="toRemoveHeaderConfirmFlag = false">
                    <transition name="modal">
                        <div class="modal-mask">
                            <div class="modal-wrapper">
                                <div class="modal-container">
                                    <div class="modal-header">
                                        <b>Confirm to remove?</b>
                                    </div>
                                    <div class="modal-body">
                                        <div>
                                            <b>Are you sure you want to remove {{ headers.find(each => each.id == toRemoveHeaderId).header }}?</b>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <a class="btn btn-primary ok-btn"
                                                   v-on:click="confirmRemoveHeader()">
                                                    Remove </a>
                                                <a v-on:click="toRemoveHeaderConfirmFlag = false"
                                                   class="btn btn-danger">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
                <div v-if="toCollapseConfirmFlag" @close="toCollapseConfirmFlag = false">
                    <transition name="modal">
                        <div class="modal-mask">
                            <div class="modal-wrapper">
                                <div class="modal-container">
                                    <div class="modal-header">
                                        <b>Confirmation</b>
                                    </div>
                                    <div class="modal-body">
                                        <div>
                                            <b>Do you want a matrix with your selected characters be displayed?</b>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <a class="btn btn-primary ok-btn"
                                                   v-on:click="toCollapseConfirmFlag=false;generateMatrix()">
                                                    Yes </a>
                                                <a v-on:click="toCollapseConfirmFlag = false"
                                                   class="btn btn-danger">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
                <div v-if="toRemoveBracketConfirmFlag" @close="toRemoveBracketConfirmFlag = false">
                    <transition name="modal">
                        <div class="modal-mask">
                            <div class="modal-wrapper">
                                <div class="modal-container">
                                    <div class="modal-header">
                                        <b>Confirmation</b>
                                    </div>
                                    <div class="modal-body">
                                        <div>
                                            <b>Please remove punctuation marks, such as (), from input terms: </b>
                                            {{ currentTermForBracket }}
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <a class="btn btn-primary ok-btn"
                                                   v-on:click="toRemoveBracketConfirmFlag=false">
                                                    OK </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
                <div v-if="resolveItemFlag" @close="resolveItemFlag = false">
                    <transition name="modal">
                        <div class="modal-mask">
                            <div class="modal-wrapper">
                                <div class="modal-container" style="width: 800px;">
                                    <div class="modal-header">
                                        <b>Resolve <font style="color: orange; font-style: italic">{{currentResolveItem['deprecate term']}}</font></b>
                                    </div>
                                    <div class="modal-body">
                                        <div style="border-radius: 5px; border: 1px solid; padding: 15px;">
                                            <div style="margin-top: 10px; min-height: auto;" class="table-responsive">
                                                <div style="margin-top: 0px;">
                                                    <div style="border-bottom: gray; padding 2px; font-size: 11pt">
                                                        <span style="width: 20%;">Deprecated Term:</span>
                                                        <b><span>{{currentResolveItem['deprecate term']}}</span></b>
                                                    </div>
                                                    <div style="border-bottom: gray; padding 2px; font-size: 11pt">
                                                        <hr style="margin-top: 8px; margin-bottom: 8px; border-top-color: #ddd;">
                                                        <span style="width: 20%;">Deprecated Reason:</span>
                                                        <b><span>{{currentResolveItem['deprecated reason']}}</span></b>
                                                    </div>
                                                    <div v-if="currentResolveItem['replacement term'] && currentResolveItem['replacement term'] != ''" style="border-bottom: gray; padding 2px; font-size: 11pt">
                                                        <hr style="margin-top: 8px; margin-bottom: 8px; border-top-color: #ddd;">
                                                        <span style="width: 20%;">Replacement Term:</span>
                                                        <b><span>{{currentResolveItem['replacement term']}}</span></b>
                                                    </div>
                                                    <div v-else>
                                                        <hr style="margin-top: 8px; margin-bottom: 8px; border-top-color: #ddd;">
                                                        No replacement term was provided.<br/>
                                                        Please use a different term, or <a v-on:click="handleDisputeTerm()">dispute the deprecation.</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <a class="btn btn-primary ok-btn"
                                                   v-bind:class="{disabled: currentResolveItem['replacement IRI'] == null || currentResolveItem['replacement IRI'] == ''}"
                                                   v-on:click="onAcceptResolveItem">
                                                    Accept Replacement Term</a>
                                                <a v-on:click="resolveItemFlag=false"
                                                   class="btn btn-danger">Close</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
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
                                                <div style="border-bottom: gray; padding 2px; font-size: 11pt">
                                                    <span style="width: 20%;">Deprecated Reason:</span>
                                                    <b><span>{{currentResolveItem['deprecated reason']}}</span></b>
                                                </div>
                                                <textarea placeholder="To dispute the deprecation decision on the term, please provide your reason(s) as detailed as possible" style="margin-top:10px; border-radius: 5px; border: 1px solid; padding: 15px; width: 100%;" rows="8" v-model="disputeMessage"></textarea>                                </div>
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
                <div v-if="nameMatrixDialog" @close="nameMatrixDialog = false">
                    <transition name="modal">
                        <div class="modal-mask">
                            <div class="modal-wrapper">
                                <div class="modal-container" style="width: 600px;">
                                    <div style="max-height:80vh; overflow-y: auto;">
                                        <div class="modal-header">
                                            <b style="text-align: left">Name this matrix version</b>
                                            <br/>
                                        </div>
                                        <div class="modal-body" style="min-height: 25vh;">
                                            <div style="margin-top: 0px;">
                                                <div style="border-bottom: gray; padding 2px; font-size: 11pt">
                                                    <!-- <v-autocomplete placeholder="name the version" :items="showNamesList" :min-len="1" @update-items="updateItems" @item-selected="itemSelected"></v-autocomplete> -->
                                                    <list-select
                                                        :list="namesList"
                                                        optionValue="matrix_name"
                                                        optionText="matrix_name"
                                                        :selectedItem="currentVersion"
                                                        @select="onSelectNameVersion"
                                                    >
                                                    </list-select>
                                                    <div style="margin-left: 12px;margin-top: 4px;font-style: italic;"> Using an existing name will overwrite the current matrix</div>
                                                </div>
                                                <!-- <div style="border-bottom: gray; padding 2px; font-size: 11pt; margin-top:16px;">
                                                    <div style="margin-left: 12px;margin-bottom: 4px;font-style: italic;"> Name as new version </div>
                                                    <input v-model="currentName" placeholder="name this version" v-on:keyup="onChangeCurrentName" style="width: 100%;height: 37px;padding: 16px;"/>
                                                </div> -->
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <a class="btn btn-primary ok-btn"
                                                        v-bind:class="{disabled: ((currentVersion == null || currentVersion.matrix_name == '') )}"
                                                        v-on:click="nameMatrix">
                                                        Save </a>
                                                    <a v-on:click="nameMatrixDialog=false"
                                                        class="btn btn-danger">Cancel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
                <div v-if="loadMatrixDialog" @close="loadMatrixDialog = false">
                    <transition name="modal">
                        <div class="modal-mask">
                            <div class="modal-wrapper">
                                <div class="modal-container" style="width: 600px;">
                                    <div style="max-height:80vh; overflow-y: auto;">
                                        <div class="modal-header">
                                            <b style="text-align: left">Load matrix version</b>
                                            <br/>
                                        </div>
                                        <div class="modal-body" style="min-height: 25vh;">
                                            <div style="margin-top: 0px;">
                                                <div style="border-bottom: gray; padding 2px; font-size: 11pt">
                                                    <list-select
                                                        :list="namesList"
                                                        optionValue="matrix_name"
                                                        optionText="matrix_name"
                                                        :selectedItem="loadVersion"
                                                        placeholder="select version name"
                                                        @select="onSelectLoadVersion"
                                                    >
                                                    </list-select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <a class="btn btn-primary ok-btn"
                                                        v-bind:class="{disabled: ( loadVersion == null || loadVersion.matrix_name == '')}"
                                                        v-on:click="loadMatrixConfirmDialog=true">
                                                        Load </a>
                                                    <a v-on:click="loadMatrixDialog=false"
                                                        class="btn btn-danger">Cancel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
                <div v-if="loadMatrixConfirmDialog" @close="loadMatrixConfirmDialog = false">
                    <transition name="modal">
                        <div class="modal-mask">
                            <div class="modal-wrapper">
                                <div class="modal-container" style="width: 500px;">
                                    <div style="max-height:80vh; overflow-y: auto;">
                                        <div class="modal-header">
                                            <b style="text-align: left">Confirmation</b>
                                            <br/>
                                        </div>
                                        <div class="modal-body">
                                            <b>Load this version will overwrite the current matrix. To access the current matrix later, make sure it's saved with a name. <a v-on:click="loadMatrixDialog=false;loadMatrixConfirmDialog=false;nameMatrixDialog=true;">Name this matrix version</a></b>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <a class="btn btn-primary ok-btn"
                                                        v-bind:class="{disabled: ( loadVersion == null || loadVersion.matrix_name == '')}"
                                                        v-on:click="importMatrix">
                                                        Load </a>
                                                    <a v-on:click="loadMatrixConfirmDialog=false"
                                                        class="btn btn-danger">Cancel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
            </form>
        </div>
        <viewer :images="images"           
                class="viewer" ref="viewer"
                @inited="inited"
        >
        <img v-for="src in images" :src="src" :key="src" style="display: none;">
        </viewer>
    </div>

</template>

<script>
    import Vue from 'vue';

    import method from '../Metadata/method.vue';
    import unit from '../Metadata/unit.vue';
    import tag from '../Metadata/tag.vue';
    import summary from '../Metadata/summary.vue';
    import creator from '../Metadata/creator.vue';
    import usage from '../Metadata/usage.vue';
    import history from '../Metadata/history.vue';
    import {mapState, mapGetters, mapMutations} from 'vuex';

    import {ModelSelect, ListSelect} from '../../libs/vue-search-select-lib'

    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    import LiquorTree from 'liquor-tree';

    import draggable from 'vuedraggable'

    import 'viewerjs/dist/viewer.css';
    import Viewer from 'v-viewer';
    import Autocomplete from 'v-autocomplete';
    import './autocomplete.css';

    // import {runTest, Character, ColorQuality} from '../../LeafColors';

    let Color = {
        WHITE : "white",
        BLACK : "black",
        GREEN : "green",
        YELLOW : "yellow",
        BROWN : "brown",
        GOLD : "gold",
        RED : "red",
        PURPLE : "purple",
    }
    
    
    function makeBaseAuth(user, pswd){ 
        var token = user + ':' + pswd;
        var hash = "";
        if (btoa) {
            hash = btoa(token);
        }
        return "Basic " + hash;
    }
    var inactivityTimeout = null;
    resetTimeout()
    function onUserInactivity() {
        window.location.href = "logout";
    }
    function resetTimeout() {
        if (inactivityTimeout)
            clearTimeout(inactivityTimeout);
        inactivityTimeout = setTimeout(onUserInactivity, 1000 * 3600);
    }
    window.onmousemove = resetTimeout;

    let Colorsets = {
        white: new Set([Color.WHITE, Color.BLACK, Color.GREEN, Color.YELLOW, Color.BROWN, Color.GOLD, Color.RED, Color.PURPLE]),
        yellow: new Set([Color.BLACK, Color.GREEN, Color.YELLOW, Color.BROWN]),
        green: new Set([Color.BLACK, Color.GREEN, Color.BROWN]), // Color.YELLOW
        gold: new Set([Color.BLACK, Color.BROWN, Color.GOLD, Color.RED]),
        brown: new Set([Color.BLACK, Color.BROWN]), // Color.GOLD, Color.PURPLE, Color.RED
        red: new Set([Color.BLACK, Color.GREEN, Color.RED, Color.PURPLE, Color.BROWN]),
        purple: new Set([Color.BLACK, Color.BROWN, Color.PURPLE]),
        black: new Set([Color.BLACK])
    }

    let Shapesets = {
        rounded: new Set(['obtuse']),
        acute: new Set(['awned', 'acuminate']),
        lanceolate: new Set(['elliptic-ovat']),
        oblanceolate: new Set(['obovat']),
        flat: new Set(['convex', 'concav']),
        elliptic: new Set(['ovate', 'obovat']),
        linear: new Set(['oblanceolate', 'lanceolat']),
    }

    let singularToPlural = {
        plant: 'plants',
        internode: 'internodes',
        rhizome: 'rhizomes',
        stem: 'stems',
        leaf: 'leaves',
        blade: 'blades',
        margin: 'margins',
        surface: 'surface',
        sheath: 'sheaths',
        ligule: 'ligules',
        apex: 'apex',
        scale: 'scales',
        vein: 'veins',
        stipe: 'stipe',
        beak: 'beak',
        tooth: 'teeth',
        perigynium: 'perigynia',
        stigma: 'stigmas',
        achene: 'achenes',
        anther: 'anthers',
        shoot: 'shoots',
        branch: 'branches',
        root: 'roots',
        culm: 'culms',
        midrib: 'midrib',
        band: 'bands',
        inflorescence: 'inflorescences',
        axis: 'axis',
        node: 'nodes',
        peduncle: 'peduncles',
        bract: 'bracts',
        rachis: 'rachis',
        spike: 'spikes',
        side: 'sides',
        style: 'styles',
        stamen: 'stamens',
        filament: 'filaments',
        cataphyll: 'cataphylls',
        flower: 'flowers',
        nerve: 'nerves',
        unit: 'unit',
        body: 'body',
        orifice: 'orifice',
    }

    Vue.use(LiquorTree);
    Vue.use({ModelSelect});
    Vue.use({ListSelect});
    Vue.use(Viewer, {
        defaultOptions: {
            zIndex: 9999
        }
    });
    Vue.use(Autocomplete);


    export default {
        props: [
            'user',
        ],
        computed: {
            ...mapState([
                'colorTreeData',
                'nonColorTreeData',
            ]),
            ...mapGetters([]),

        },
        data: function () {
            return {
                //previousCharacter:"",
                //previousUserCharacter:"",
                images: [
                    'https://drive.google.com/uc?id=15MsN-q-e9PPK7YXaYX1gQZiLNERCgl5r',
                    'https://drive.google.com/uc?id=1wwG6N8X6Mlq5KZHVjtDTQnVOKKZ6wVUw'
                ],
                character: {},
                userCharacters: [],
                defaultCharacters: [],
                defaultCharactersToolTip: [],
                standardCharacters: [],
                item: null,
                searchText: null,
                standardShowFlag: false,
                firstCharacter: null,
                firstCharacterDefinition: null,
                middleCharacter: null,
                lastCharacter: null,
                lastCharacterDefinition: null,
                secondLastCharacter: null,
                secondLastCharacterDefinition: null,
                newCharacterFlag: false,
                detailsFlag: false,
                metadataFlag: null,
                currentMetadata: null,
                parentData: null,
                viewFlag: false,
                standardCharactersTooltip: '',
                generateDescriptionTooltip: '',
                confirmMethod: false,
                confirmUnit: false,
                confirmTag: false,
                confirmSummary: false,
                columnCount: 0,
                taxonName: 'Carex capitata',
                matrixShowFlag: false,
                headers: [],
                values: [],
                collapsedFlag: false,
                value: {
                    value: ''
                },
                isLoading: false,
                userTags: [],
                nonColorType: '',
                oldUserTags: [],
                currentTab: '',
                descriptionText: '',
                descriptionFlag: false,
                editFlag: false,
                characterUsername: '',
                oldCharacter: {},
                enhanceFlag: false,
                removeAllStandardFlag: false,
                colorDetailsFlag: false,
                colorDetails: [],
                treeData: this.colorTreeData,
                colTreeData: [],
                colorSearchText: null,
                colorTreeOption: {
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
                colorExistFlag: false,
                searchColor: [],
                colorSynonyms: {},
                colorExactSynonyms: [],
                colorExactMatch: [],
                searchColorFlag: 0,
                exactColor: {},
                colorDetailId: null,
                defaultColorValue: [],
                originColorValue: [],
                colorComment: {},
                colorTaxon: {},
                colorSampleText: {},
                colorDefinition: {},
                userColorDefinition: {},
                preList: [],
                postList: [],
                nonColorDetails: [],
                nonColorDetailsFlag: false,
                textureTreeData: this.nonColorTreeData,
                nonColorSearchText: null,
                nonColorExistFlag: false,
                searchNonColor: [],
                nonColorSynonyms: [],
                exactNonColor: {},
                nonColorDetailId: null,
                defaultNonColorValue: null,
                nonColorComment: {},
                nonColorTaxon: {},
                nonColorSampleText: {},
                nonColorDefinition: {},
                userNonColorDefinition: {},
                searchNonColorFlag: 0,
                sharedFlag: true,
                allColorValues: [],
                allNonColorValues: [],
                currentColorValue: {
                    confirmedFlag: {
                        brightness: false,
                        reflectance: false,
                        saturation: false,
                        colored: false,
                        multi_colored: false,
                    }
                },
                colorFlags: [
                    "brightness",
                    "reflectance",
                    "saturation",
                    "colored",
                    "multi_colored",
                ],
                currentNonColorValue: {
                    confirmedFlag: {
                        main_value: false,
                    }
                },
                currentColorValueExist: false,
                currentNonColorValueExist: false,
                currentCharacter: {},
                methodFieldData: {
                    fromTerm: null,
                    fromId: null,
                    toTerm: null,
                    toId: null,
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
                    noneSynonymFlag: {
                        from: false,
                        to: false,
                        include: false,
                        exclude: false,
                        where: false,
                    }
                },
                checkMethodFlag: false,
                colorationData: {},
                nonColorationData: {},
                extraColors: [],
                existColorDetails: [],
                existColorDetailsFlag: false,
                existNonColorDetails: [],
                existNonColorDetailsFlag: false,
                copyValue: null,
                confirmOverwriteFlag: false,
                toRemoveCharacterId: null,
                toRemoveStandardConfirmFlag: false,
                toRemoveHeaderId: null,
                toRemoveHeaderConfirmFlag: false,
                toCollapseConfirmFlag: false,
                toRemoveBracketConfirmFlag: false,
                standardCharactersTags: [
                    'Habit',
                    'Stems',
                    'Leaves',
                    'Inflorescence',
                    'Staminate flowers',
                    'Inflorescence units',
                    'Perigynia',
                    'Pistillate scales',
                    'Achenes',
                    'Anthers',
                ],
                saveColorButtonFlag: false,
                saveNonColorButtonFlag: false,
                filterFlag: true,
                nounUndefined: false,
                secondNounUndefined: false,
                saveCharacterButtonFlag: false,
                firstCharacterUndefined: false,
                wholeCharacterUndefined: false,
                wholeCharacterDefinition: null,
                currentTermForBracket: null,
                deprecatedTerms: [],
                resolveItemFlag: false,
                currentResolveItem: {},
                currentResolveType: "",
                deprecatedTagName: "",
                messageDialogFlag: false,
                disputedTerm: '',
                disputeMessage: '',
                nameMatrixDialog: false,
                loadMatrixDialog: false,
                namesList: [],
                showNamesList: [],
                currentName: "",
                loadVersion: null,
                currentVersion: null,
                tagDeprecated: [],
                loadMatrixConfirmDialog: false,
                lastLoadMatrixName: '',
                treeResult: [],
                definitionData: []
            }
        },
        components: {
            ModelSelect,
            ListSelect,
            draggable,
            Loading,
        },
        methods: {
            showViewer (node) {
                var app = this;
                app.images = node.data.images;
                this.$viewer.show();
            },
            inited (viewer) {
                this.$viewer = viewer;
            },
            handleFcAfterDateBack (event) {
                console.log('hadleFcAfterDateBack function inited');
                var app = this;
                app.updatedFlag = true;
                $('.center').addClass('back-yellow');
                $('.' + app.metadataFlag).addClass('back-median-green');
                console.log("app.metadataFlag", app.metadataFlag);
                switch (app.metadataFlag) {
                    case 'method':
                        app.character.method_as = event[0];
                        app.term = event[1];
                        app.termValue = event[2];
                        app.character.method_from = event[4];
                        app.character.method_to = event[5];
                        app.character.method_include = event[6];
                        app.character.method_exclude = event[7];
                        app.character.method_where = event[8];
                        app.character.method_greenTick = event[10];
                        app.parentData = event;
                        app.methodUpdateFlag = true;
                        console.log("method return", event);
                        break;
                    case 'unit':
                        app.character.unit = event;
                        app.parentData = event;
                        app.unitUpdateFlag = true;
                        break;
                    case 'tag':
                        app.character.standard_tag = event;
                        app.parentData = event;
                        break;
                    case 'summary':
                        app.character.summary = event;
                        app.parentData = event;
                        break;
                    case 'creator':
                        app.character.creator = event;
                        app.parentData = event;
                        app.creatorUpdateFlag = true;
                        break;
                    default:
                        break;
                }
                console.log('app.character after handle: ', app.character); // get the data after child dealing
            },
            printSearchText (searchText) {
                var app = this;
                app.searchText = searchText;
            },
            async getTooltipImage(name, index) {
                var methodEntry = null;
                var src = '';
                var app = this;
                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + name.toLowerCase())
                    .then(function (resp) {
                        if (resp.data.entries.length > 0) {
                            methodEntry = resp.data.entries.filter(function(each) {
                                return each.resultAnnotations.some(e => e.property === "http://biosemantics.arizona.edu/ontologies/carex#elucidation") == true;
                            })[0];
                            if (methodEntry) {
                                for (var i = 0; i < methodEntry.resultAnnotations.length; i ++) {
                                    if (methodEntry.resultAnnotations[i].property == 'http://biosemantics.arizona.edu/ontologies/carex#elucidation') {
                                        console.log(methodEntry.resultAnnotations[i].value);
                                        if (src == '') {
                                            src = "<div style='display:flex; flex-direction: row;justify-content: center;'>";
                                        }
                                        if (methodEntry.resultAnnotations[i].value.indexOf('id=') < 0) {
                                            var id = methodEntry.resultAnnotations[i].value.slice(methodEntry.resultAnnotations[i].value.indexOf('file/d/') + 7, methodEntry.resultAnnotations[i].value.indexOf('/view?usp='));
                                            src = src + "<div><img alt='image' style='width: 128px; height: auto;margin-top:10px;margin-bottom:10px;margin-left:8px;margin-right:8px;' src='" + 'https://drive.google.com/uc?id=' + id + "'/></div>";
                                        } else {
                                            src = src + "<div><img alt='image' style='width: 128px; height: auto;margin-top:10px;margin-bottom:10px;margin-left:8px;margin-right:8px;' src='" + 'https://drive.google.com/uc?id=' + methodEntry.resultAnnotations[i].value.split('id=')[1].substring(0, methodEntry.resultAnnotations[i].value.split('id=')[1].length - 1) + "'/></div>";
                                        }
                                    }
                                }
                                if (src != '') {
                                    src += "</div>";
                                    app.standardCharacters[index].tooltip = src + app.standardCharacters[index].tooltip;
                                }
                            }
                        }
                    })
                    .catch(function (resp) {
                        console.log('exp search resp error', resp);
                    });
            },
            async getTooltipImageString(name) {
                var methodEntry = null;
                var src = '';
                var app = this;
                await axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + name.toLowerCase())
                    .then(function (resp) {
                        if (resp.data.entries.length > 0) {
                            methodEntry = resp.data.entries.filter(function(each) {
                                return each.resultAnnotations.some(e => e.property === "http://biosemantics.arizona.edu/ontologies/carex#elucidation") == true;
                            })[0];
                            if (methodEntry) {
                                for (var i = 0; i < methodEntry.resultAnnotations.length; i ++) {
                                    if (methodEntry.resultAnnotations[i].property == 'http://biosemantics.arizona.edu/ontologies/carex#elucidation') {
                                        console.log(methodEntry.resultAnnotations[i].value);
                                        if (src == '') {
                                            src = "<div style='display:flex; flex-direction: row;justify-content: center;'>";
                                        }
                                        if (methodEntry.resultAnnotations[i].value.indexOf('id=') < 0) {
                                            var id = methodEntry.resultAnnotations[i].value.slice(methodEntry.resultAnnotations[i].value.indexOf('file/d/') + 7, methodEntry.resultAnnotations[i].value.indexOf('/view?usp='));
                                            src = src + "<div><img alt='image' style='width: 128px; height: auto;margin-top:10px;margin-bottom:10px;margin-left:8px;margin-right:8px;' src='" + 'https://drive.google.com/uc?id=' + id + "'/></div>";
                                        } else {
                                            src = src + "<div><img alt='image' style='width: 128px; height: auto;margin-top:10px;margin-bottom:10px;margin-left:8px;margin-right:8px;' src='" + 'https://drive.google.com/uc?id=' + methodEntry.resultAnnotations[i].value.split('id=')[1].substring(0, methodEntry.resultAnnotations[i].value.split('id=')[1].length - 1) + "'/></div>";
                                        }
                                    }
                                }
                                if (src != '') {
                                    src += '</div>';
                                }
                            }
                        }
                    })
                    .catch(function (resp) {
                        console.log('exp search resp error', resp);
                    });
                return src;
            },
            async getTooltipImageUserChracter(name, index) {
                var methodEntry = null;
                var src = '';
                var app = this;
                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + name.toLowerCase())
                    .then(function (resp) {
                        if (resp.data.entries.length > 0) {
                            methodEntry = resp.data.entries.filter(function(each) {
                                return each.resultAnnotations.some(e => e.property === "http://biosemantics.arizona.edu/ontologies/carex#elucidation") == true;
                            })[0];
                            if (methodEntry) {
                                for (var i = 0; i < methodEntry.resultAnnotations.length; i ++) {
                                    if (methodEntry.resultAnnotations[i].property == 'http://biosemantics.arizona.edu/ontologies/carex#elucidation') {
                                        if (src == '') {
                                            src = "<div style='display:flex; flex-direction: row;justify-content: center;'>";
                                        }
                                        if (methodEntry.resultAnnotations[i].value.indexOf('id=') < 0) {
                                            var id = methodEntry.resultAnnotations[i].value.slice(methodEntry.resultAnnotations[i].value.indexOf('file/d/') + 7, methodEntry.resultAnnotations[i].value.indexOf('/view?usp='));
                                            src = src + "<div><img alt='image' style='width: 128px; height: auto;margin-top:10px;margin-bottom:10px;margin-left:8px;margin-right:8px;' src='" + 'https://drive.google.com/uc?id=' + id + "'/></div>";
                                        } else {
                                            src = src + "<div><img alt='image' style='width: 128px; height: auto;margin-top:10px;margin-bottom:10px;margin-left:8px;margin-right:8px;' src='" + 'https://drive.google.com/uc?id=' + methodEntry.resultAnnotations[i].value.split('id=')[1].substring(0, methodEntry.resultAnnotations[i].value.split('id=')[1].length - 1) + "'/></div>";
                                        }
                                    }
                                }
                                if (src != '') {
                                    src += '</div>';
                                    app.userCharacters[index].tooltip = src + app.userCharacters[index].tooltip;
                                }
                            }
                        }
                    })
                    .catch(function (resp) {
                        console.log('exp search resp error', resp);
                    });
            },
            onSelect(selectedItem) {
                var app = this;
                var selectedCharacter = app.defaultCharacters.find(ch => ch.id == selectedItem)

                // if (!selectedCharacter) {
                //     selectedCharacter = app.userCharacters.find(ch => ch.id == selectedItem);
                // } else if (app.userCharacters.find(ch => ch.name == selectedCharacter.name)) {
                //     selectedCharacter = app.userCharacters.find(ch => ch.name == selectedCharacter.name && ch.username == selectedCharacter.username);
                // }
                app.editFlag = false;
                sessionStorage.setItem('editFlag', false);
                if (!selectedCharacter) {
                    app.firstCharacter = '';
                    app.middleCharacter = '';
                    app.lastCharacter = '';
                    app.secondLastCharacter = '';
                    app.lastCharacterDefinition = '';
                    app.secondLastCharacterDefinition = '';
                    app.nounUndefined = false;
                    app.secondNounUndefined = false;
                    app.firstCharacterUndefined = false;
                    app.wholeCharacterUndefined = false;
                    app.firstCharacterDefinition = '';
                    app.wholeCharacterDefinition = '';

                    app.newCharacterFlag = true;
                    app.viewFlag = false;
                    sessionStorage.setItem('viewFlag', false);
                    sessionStorage.setItem('edit_created_other', false);
                } else {
                    app.character = selectedCharacter;
                    app.item = selectedItem;
                    console.log('selectedCharacter.username', app.character.username.substr(app.character.username.length - app.user.name.length));

                    if (app.character.username.includes(app.user.name)) {
                    //    app.viewFlag = false;
                    //    sessionStorage.setItem('viewFlag', false);
                    //    sessionStorage.setItem('edit_created_other', false);
                        app.editCharacter({character_id: app.character.id}, true);
                    } else {
                        app.viewFlag = true;
                        sessionStorage.setItem('viewFlag', true);
                        sessionStorage.setItem('edit_created_other', true);
                        app.editCharacter(app.character);
                    }
                }
                console.log('selectedCharacter', selectedCharacter);
            },
            onResolve(resolveItem) {
                var app = this;
                var selectedCharacter = app.defaultCharacters.find(ch => ch.id == resolveItem.value)
                console.log(selectedCharacter);
                console.log('resolveCharacter', resolveItem);
                app.resolveItemFlag = true;
                app.currentResolveItem = app.deprecatedTerms[resolveItem.deprecated];
                app.currentResolveType = "character";
            },
            onResolveUserCharacter(resolveCharacter) {
                var app = this;
                app.resolveItemFlag = true;
                app.currentResolveItem = app.deprecatedTerms[resolveCharacter.deprecated];
                app.currentResolveType = "character";
            },
            onResolveColor(index, resolveType) {
                var app = this;
                app.resolveItemFlag = true;
                app.currentResolveItem = app.deprecatedTerms[index];
                app.currentResolveType = resolveType;
            },
            onResolveNonColorValue(ncv) {
                var app = this;
                console.log(ncv);
                var index = app.deprecatedTerms.findIndex(value => value['deprecated IRI'] == ncv.main_value_IRI);
                app.currentResolveItem = app.deprecatedTerms[index];
                app.resolveItemFlag = true;
                app.currentResolveType = "nonColor";
            },
            onAcceptResolveItem() {
                var app = this;
                console.log(app.currentResolveType);
                console.log(app.currentResolveItem);
                var postValue = {};
                postValue['deprecatedIRI'] = app.currentResolveItem['deprecated IRI'];
                postValue['replacementTerm'] = app.currentResolveItem['replacement term'];
                postValue['replacementIRI'] = app.currentResolveItem['replacement IRI'];
                if (app.currentResolveType == "character") {
                    axios.post('/chrecorder/public/api/v1/resolveCharacter', postValue)
                        .then(function (resp) {
                            app.userCharacters = resp.data.characters;
                            app.headers = resp.data.headers;
                            app.values = resp.data.values;
                            app.taxonName = resp.data.taxon;
                            app.defaultCharacters = resp.data.defaultCharacters;
                            app.refreshDefaultCharacters();
                            app.refreshUserCharacters();
                            app.showTableForTab(app.currentTab);
                            app.resolveItemFlag = false;
                        })
                } else if (app.currentResolveType == "nonColor") {
                    axios.post('/chrecorder/public/api/v1/resolveNonColorValue', postValue)
                        .then(function (resp) {
                            app.values = resp.data.values;
                            app.nonColorDetails = resp.data.nonColorDetails;
                            app.allNonColorValues = resp.data.allNonColorValues;
                            app.getDeprecatedValue();
                            app.resolveItemFlag = false;
                        })
                } else if (app.currentResolveType == "colorBrightness") {
                    axios.post('/chrecorder/public/api/v1/resolveColorBrightness', postValue)
                        .then(function (resp) {
                            app.values = resp.data.values;
                            app.allColorValues = resp.data.allColorValues;
                            app.allNonColorValues = resp.data.allNonColorValues;
                            app.getDeprecatedValue();
                            app.resolveItemFlag = false;
                        })
                } else if (app.currentResolveType == "colorReflectance") {
                    axios.post('/chrecorder/public/api/v1/resolveColorReflectance', postValue)
                        .then(function (resp) {
                            app.values = resp.data.values;
                            app.allColorValues = resp.data.allColorValues;
                            app.allNonColorValues = resp.data.allNonColorValues;
                            app.getDeprecatedValue();
                            app.resolveItemFlag = false;
                        })
                } else if (app.currentResolveType == "colorSaturation") {
                    axios.post('/chrecorder/public/api/v1/resolveColorSaturation', postValue)
                        .then(function (resp) {
                            app.values = resp.data.values;
                            app.allColorValues = resp.data.allColorValues;
                            app.allNonColorValues = resp.data.allNonColorValues;
                            app.getDeprecatedValue();
                            app.resolveItemFlag = false;
                        })
                } else if (app.currentResolveType == "colorColored") {
                    axios.post('/chrecorder/public/api/v1/resolveColorColored', postValue)
                        .then(function (resp) {
                            app.values = resp.data.values;
                            app.allColorValues = resp.data.allColorValues;
                            app.allNonColorValues = resp.data.allNonColorValues;
                            app.getDeprecatedValue();
                            app.resolveItemFlag = false;
                        })
                } else if (app.currentResolveType == "colorMultiColored") {
                    axios.post('/chrecorder/public/api/v1/resolveColorMultiColored', postValue)
                        .then(function (resp) {
                            app.values = resp.data.values;
                            app.allColorValues = resp.data.allColorValues;
                            app.allNonColorValues = resp.data.allNonColorValues;
                            app.getDeprecatedValue();
                            app.resolveItemFlag = false;
                        })
                }
            },
            editCharacter(character, editFlag = false, standardFlag = false, enhanceFlag = false) {
                var app = this;
                app.editFlag = editFlag;
                console.log('app.editFlag', app.editFlag);
                if (editFlag) {
                    app.viewFlag = !editFlag;
                    sessionStorage.setItem('viewFlag', !editFlag);
                    sessionStorage.setItem('edit_created_other', !editFlag);
                    sessionStorage.setItem('editFlag', editFlag);
                    app.character = app.userCharacters.find(ch => ch.id == character.character_id);
                //    app.character.standard = 0;
                } else {
                    app.character = character;
                //    app.character.standard = 0;
                }

                console.log('app.character.username', app.character.username);
                console.log('app.user.name', app.user.name);
                if (standardFlag || (editFlag && !app.character.owner_name.includes(app.user.name))) {
                    app.editFlag = false;
                    app.viewFlag = true;
                    sessionStorage.setItem('viewFlag', true);
                    sessionStorage.setItem('editFlag', editFlag);
                    sessionStorage.setItem('edit_created_other', true);
                }
                app.item = app.character.id;
                sessionStorage.setItem("characterName", app.character.name);

                if (enhanceFlag) {
                    switch (app.metadataFlag) {
                        case 'method':
                            app.methodFieldData.fromTerm = null;
                            app.methodFieldData.fromId = null;
                            app.methodFieldData.toTerm = null;
                            app.methodFieldData.toId = null;
                            app.methodFieldData.includeTerm = null;
                            app.methodFieldData.includeId = null;
                            app.methodFieldData.excludeTerm = null;
                            app.methodFieldData.excludeId = null;
                            app.methodFieldData.whereTerm = null;
                            app.methodFieldData.whereId = null;
                            app.methodFieldData.fromNeedMore = false;
                            app.methodFieldData.toNeedMore = false;
                            app.methodFieldData.includeNeedMore = false;
                            app.methodFieldData.excludeNeedMore = false;
                            app.methodFieldData.whereNeedMore = false;
                            app.methodFieldData.fromSynonyms = [];
                            app.methodFieldData.toSynonyms = [];
                            app.methodFieldData.includeSynonyms = [];
                            app.methodFieldData.excludeSynonyms = [];
                            app.methodFieldData.whereSynonyms = [];
                            app.methodFieldData.noneSynonymFlag = {
                                from: false,
                                to: false,
                                include: false,
                                exclude: false,
                                where: false,
                            };
                            //
                            app.parentData = [];
                            app.parentData.push(app.character.method_as);
                            app.parentData[3] = app.user;
                            app.parentData[4] = app.character.method_from;
                            app.parentData[5] = app.character.method_to;
                            app.parentData[6] = app.character.method_include;
                            app.parentData[7] = app.character.method_exclude;
                            app.parentData[8] = app.character.method_where;
                            app.parentData[9] = app.methodFieldData;
                            app.currentMetadata = method;
                            break;
                        case 'unit':
                            app.parentData = app.character.unit;
                            app.currentMetadata = unit;
                            break;
                        case 'tag':
                            app.parentData = app.character.standard_tag;
                            app.currentMetadata = tag;
                            break;
                        case 'summary':
                            app.parentData = app.character.summary;
                            app.currentMetadata = summary;
                            break;
                        case 'creator':
                            app.parentData = app.character.username + ' via CR';//app.character.creator;
                            app.currentMetadata = creator;
                            break;
                        case 'usage':
                            axios.get('/chrecorder/public/api/v1/get-usage/' + app.character.id)
                                .then(function (resp) {
                                    app.parentData = [];
                                    app.parentData[0] = resp.data.usage_count;
                                    app.parentData[1] = app.user.name;
                                    app.parentData[2] = app.taxonName;
                                    app.currentMetadata = usage;
                                });

                            break;
                        case 'history':
                            app.parentData = app.character.history;
                            app.currentMetadata = history;
                            break;
                        default:
                            break;
                    }
                } else {
                    if (app.checkHaveUnit(app.character.name)) {
                        // Initializing the methodFieldData //
                        app.methodFieldData.fromTerm = null;
                        app.methodFieldData.fromId = null;
                        app.methodFieldData.toTerm = null;
                        app.methodFieldData.toId = null;
                        app.methodFieldData.includeTerm = null;
                        app.methodFieldData.includeId = null;
                        app.methodFieldData.excludeTerm = null;
                        app.methodFieldData.excludeId = null;
                        app.methodFieldData.whereTerm = null;
                        app.methodFieldData.whereId = null;
                        app.methodFieldData.fromNeedMore = false;
                        app.methodFieldData.toNeedMore = false;
                        app.methodFieldData.includeNeedMore = false;
                        app.methodFieldData.excludeNeedMore = false;
                        app.methodFieldData.whereNeedMore = false;
                        app.methodFieldData.fromSynonyms = [];
                        app.methodFieldData.toSynonyms = [];
                        app.methodFieldData.includeSynonyms = [];
                        app.methodFieldData.excludeSynonyms = [];
                        app.methodFieldData.whereSynonyms = [];
                        app.methodFieldData.noneSynonymFlag = {
                            from: false,
                            to: false,
                            include: false,
                            exclude: false,
                            where: false,
                        };
                        //
                        app.parentData = [];
                        app.parentData.push(app.character.method_as);
                        app.parentData[3] = app.user;
                        app.parentData[4] = app.character.method_from;
                        app.parentData[5] = app.character.method_to;
                        app.parentData[6] = app.character.method_include;
                        app.parentData[7] = app.character.method_exclude;
                        app.parentData[8] = app.character.method_where;
                        app.parentData[9] = app.methodFieldData;
                        app.metadataFlag = 'method';
                        app.currentMetadata = method;
                    }
                    else {
                        app.parentData = app.character.standard_tag;
                        app.metadataFlag = 'tag';
                        app.currentMetadata = tag;
                    }
                }
                app.detailsFlag = true;
            },
            trimInputString(inputString) {
                var index = 0;
                while (inputString[index] == ' ') { 
                    index ++;
                }
                inputString = inputString.slice(index, inputString.length);
                index = inputString.length - 1;
                while (inputString[index] == ' ') {
                    index --;
                }
                inputString = inputString.slice(0, index + 1);
                inputString = inputString.replaceAll('      ', ' ').replaceAll('     ', ' ').replaceAll('    ', ' ').replaceAll('   ', ' ').replaceAll('  ', ' ');
                return inputString;
            },
            checkBracketConfirm() {
                var app = this;
                console.log("check bracket");
                app.currentTermForBracket = '';
                if (app.firstCharacter.indexOf('(') > -1 || app.firstCharacter.indexOf(')') > -1 || app.lastCharacter.indexOf('(') > -1 || app.lastCharacter.indexOf(')') > -1 || app.secondLastCharacter.indexOf('(') > -1 || app.secondLastCharacter.indexOf(')') > -1 ) {
                    if (app.firstCharacter.indexOf('(') > -1 || app.firstCharacter.indexOf(')') > -1) {
                        app.currentTermForBracket += app.firstCharacter;
                    }
                    if (app.lastCharacter.indexOf('(') > -1 || app.lastCharacter.indexOf(')') > -1) {
                        if (app.currentTermForBracket != '') {
                            app.currentTermForBracket += ', ';
                        }
                        app.currentTermForBracket += app.lastCharacter;
                    }
                    if (app.secondLastCharacter.indexOf('(') > -1 || app.secondLastCharacter.indexOf(')') > -1) {
                        if (app.currentTermForBracket != '') {
                            app.currentTermForBracket += ', ';
                        }
                        app.currentTermForBracket += app.secondLastCharacter;
                    }
                    app.toRemoveBracketConfirmFlag = true;
                }
                else {
                    app.firstCharacter = app.trimInputString(app.firstCharacter);
                    app.lastCharacter = app.trimInputString(app.lastCharacter);
                    app.secondLastCharacter = app.trimInputString(app.secondLastCharacter);
                    app.checkStoreCharacter();
                }
            },
            async checkStoreCharacter() {
                var app = this;
                var requestBody = {};
                
                if (app.firstCharacterUndefined){
                    var date = new Date();
                    requestBody = {
                        "user": app.sharedFlag ? '' : app.user.name,
                        "ontology": "carex",
                        "term": app.firstCharacter.toLowerCase().replace(' ', '_').replace('-', '_'),
                        "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#toreview",
                        "definition": app.firstCharacterDefinition,
                        "elucidation": "",
                        "createdBy": app.user.name,
                        "creationDate": ("0" + date.getMonth()).slice(-2) + '-' + ("0" + date.getDate()).slice(-2) + '-' + date.getFullYear(),
                        "definitionSrc": app.user.name,
                    };
                    console.log(requestBody);
                    await axios.post('http://shark.sbs.arizona.edu:8080/class', requestBody)
                        .then(function (resp) {
                            console.log('shark api class resp', resp);
                            axios.post('http://shark.sbs.arizona.edu:8080/save', {
                                user: app.sharedFlag ? '' : app.user.name,
                                ontology: 'carex'
                            })
                                .then(function (resp) {
                                    console.log('save api resp', resp);
                                    app.firstCharacterUndefined = false;
                                });
                        });
                }

                if (app.nounUndefined){
                    var date = new Date();
                    requestBody = {
                        "user": app.sharedFlag ? '' : app.user.name,
                        "ontology": "carex",
                        "term": app.lastCharacter.toLowerCase().replaceAll(' ', '_').replace('-', '_'),
                        "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#toreview",
                        "definition": app.lastCharacterDefinition,
                        "elucidation": "",
                        "createdBy": app.user.name,
                        "creationDate": ("0" + date.getMonth()).slice(-2) + '-' + ("0" + date.getDate()).slice(-2) + '-' + date.getFullYear(),
                        "definitionSrc": app.user.name,
                    };
                    console.log(requestBody);
                    await axios.post('http://shark.sbs.arizona.edu:8080/class', requestBody)
                        .then(function (resp) {
                            console.log('shark api class resp', resp);
                            axios.post('http://shark.sbs.arizona.edu:8080/save', {
                                user: app.sharedFlag ? '' : app.user.name,
                                ontology: 'carex'
                            })
                                .then(function (resp) {
                                    console.log('save api resp', resp);
                                    app.nounUndefined = false;
                                });
                        });
                }

                if (app.middleCharacter == 'between' && app.secondNounUndefined){
                    var date = new Date();
                    requestBody = {
                        "user": app.sharedFlag ? '' : app.user.name,
                        "ontology": "carex",
                        "term": app.secondLastCharacter.toLowerCase().replaceAll(' ', '_').replace('-', '_'),
                        "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#toreview",
                        "definition": app.secondLastCharacterDefinition,
                        "elucidation": "",
                        "createdBy": app.user.name,
                        "creationDate": ("0" + date.getMonth()).slice(-2) + '-' + ("0" + date.getDate()).slice(-2) + '-' + date.getFullYear(),
                        "definitionSrc": app.user.name,
                    };
                    console.log(requestBody);
                    await axios.post('http://shark.sbs.arizona.edu:8080/class', requestBody)
                        .then(function (resp) {
                            console.log('shark api class resp', resp);
                            axios.post('http://shark.sbs.arizona.edu:8080/save', {
                                user: app.sharedFlag ? '' : app.user.name,
                                ontology: 'carex'
                            })
                                .then(function (resp) {
                                    console.log('save api resp', resp);
                                    app.secondNounUndefined = false;
                                });
                        });
                }

                let wholeCharacter = app.firstCharacter + ' ' + app.middleCharacter + ' ' + app.lastCharacter;
                
                if (app.middleCharacter == 'between'){
                    wholeCharacter += ' and ' + app.secondLastCharacter;
                }
                if (app.wholeCharacterUndefined){
                    var date = new Date();
                    requestBody = {
                        "user": app.sharedFlag ? '' : app.user.name,
                        "ontology": "carex",
                        "term": wholeCharacter.toLowerCase(),
                        "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#toreview",
                        "definition": app.wholeCharacterDefinition,
                        "elucidation": "",
                        "createdBy": app.user.name,
                        "creationDate": ("0" + date.getMonth()).slice(-2) + '-' + ("0" + date.getDate()).slice(-2) + '-' + date.getFullYear(),
                        "definitionSrc": app.user.name,
                    };
                    console.log(requestBody);
                    await axios.post('http://shark.sbs.arizona.edu:8080/class', requestBody)
                        .then(function (resp) {
                            console.log('shark api class resp', resp);
                            axios.post('http://shark.sbs.arizona.edu:8080/save', {
                                user: app.sharedFlag ? '' : app.user.name,
                                ontology: 'carex'
                            })
                                .then(function (resp) {
                                    console.log('save api resp', resp);
                                    app.wholeCharacterUndefined = false;
                                });
                        });
                }

                // if (app.nounUndefined){
                //     var date = new Date();
                //     requestBody = {
                //         "user": app.sharedFlag ? '' : app.user.name,
                //         "ontology": "carex",
                //         "term": app.lastCharacter,
                //         "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#toreview",
                //         "definition": app.lastCharacterDefinition,
                //         "elucidation": "",
                //         "createdBy": app.user.name,
                //         "creationDate": ("0" + date.getMonth()).slice(-2) + '-' + ("0" + date.getDate()).slice(-2) + '-' + date.getFullYear(),
                //         "definitionSrc": app.user.name,
                //     };
                //     console.log(requestBody);
                //     axios.post('http://shark.sbs.arizona.edu:8080/class', requestBody)
                //         .then(function (resp) {
                //             console.log('shark api class resp', resp);
                //             axios.post('http://shark.sbs.arizona.edu:8080/save', {
                //                 user: app.sharedFlag ? '' : app.user.name,
                //                 ontology: 'carex'
                //             })
                //                 .then(function (resp) {
                //                     console.log('save api resp', resp);
                //                 });
                //         });
                //     if (app.middleCharacter == 'between' && app.secondNounUndefined){
                //         var date = new Date();
                //         requestBody = {
                //             "user": app.sharedFlag ? '' : app.user.name,
                //             "ontology": "carex",
                //             "term": app.secondLastCharacter,
                //             "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#toreview",
                //             "definition": app.secondLastCharacterDefinition,
                //             "elucidation": "",
                //             "createdBy": app.user.name,
                //             "creationDate": ("0" + date.getMonth()).slice(-2) + '-' + ("0" + date.getDate()).slice(-2) + '-' + date.getFullYear(),
                //             "definitionSrc": app.user.name,
                //         };
                //         console.log(requestBody);
                //         axios.post('http://shark.sbs.arizona.edu:8080/class', requestBody)
                //             .then(function (resp) {
                //                 console.log('shark api class resp', resp);
                //                 axios.post('http://shark.sbs.arizona.edu:8080/save', {
                //                     user: app.sharedFlag ? '' : app.user.name,
                //                     ontology: 'carex'
                //                 })
                //                     .then(function (resp) {
                //                         console.log('save api resp', resp);
                //                     });
                //             });
                //     }
                //     app.storeCharacter();
                //     return;
                // }
                // else if (app.middleCharacter == 'between' && app.secondNounUndefined) {
                //     axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.lastCharacter.toLowerCase())
                //         .then(function(resp){
                //             console.log('term?'+app.lastCharacter, resp.data);
                //             if (!resp.data.entries.length){
                //                 app.nounUndefined = true;
                //                 return
                //             }
                //             else {
                //                 var date = new Date();
                //                 requestBody = {
                //                     "user": app.sharedFlag ? '' : app.user.name,
                //                     "ontology": "carex",
                //                     "term": app.secondLastCharacter,
                //                     "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#toreview",
                //                     "definition": app.secondLastCharacterDefinition,
                //                     "elucidation": "",
                //                     "createdBy": app.user.name,
                //                     "creationDate": ("0" + date.getMonth()).slice(-2) + '-' + ("0" + date.getDate()).slice(-2) + '-' + date.getFullYear(),
                //                     "definitionSrc": app.user.name,
                //                 };
                //                 console.log(requestBody);
                //                 axios.post('http://shark.sbs.arizona.edu:8080/class', requestBody)
                //                     .then(function (resp) {
                //                         console.log('shark api class resp', resp);
                //                         axios.post('http://shark.sbs.arizona.edu:8080/save', {
                //                             user: app.sharedFlag ? '' : app.user.name,
                //                             ontology: 'carex'
                //                         })
                //                             .then(function (resp) {
                //                                 console.log('save api resp', resp);
                //                             });
                //                     });
                //                 app.storeCharacter();
                //             }
                //         });
                        
                // }

                await axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.firstCharacter.toLowerCase().replaceAll(' ', '_').replace('-', '_'))
                    .then(function(resp){
                        console.log('term?'+app.firstCharacter, resp.data);
                        if (!resp.data.entries.length){
                            app.firstCharacterUndefined = true;
                        }
                    });

                await axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.lastCharacter.toLowerCase().replaceAll(' ', '_').replace('-', '_'))
                    .then(function(resp){
                        console.log('term?'+app.lastCharacter, resp.data);
                        if (!resp.data.entries.length){
                            app.nounUndefined = true;
                        }
                    });

                if (app.middleCharacter == 'between') {
                    await axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.secondLastCharacter.toLowerCase().replaceAll(' ', '_').replace('-', '_'))
                    .then(function(resp){
                        console.log('term?'+app.secondLastCharacter, resp.data);
                        if (!resp.data.entries.length){
                            app.secondNounUndefined = true;
                        }
                    });
                } 

                await axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + wholeCharacter.toLowerCase().replaceAll(' ', '_'))
                    .then(function(resp){
                        console.log('term?'+wholeCharacter, resp.data);
                        if (!resp.data.entries.length){
                            app.wholeCharacterUndefined = true;
                        }
                    });
                
                console.log(app.firstCharacterUndefined);
                console.log(app.nounUndefined);
                console.log(app.wholeCharacterUndefined);
                
                if (!app.firstCharacterUndefined && !app.nounUndefined && (app.middleCharacter != 'between' || !app.secondNounUndefined) && !app.wholeCharacterUndefined) {
                    app.storeCharacter();
                }

                // var secondChecked = false;
                // axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.lastCharacter.toLowerCase())
                //     .then(function(resp){
                //         console.log('term?'+app.lastCharacter, resp.data);
                //         if (!resp.data.entries.length){
                //             app.nounUndefined = true;
                //         }
                //         else {
                //             if (app.middleCharacter != 'between' || secondChecked){
                //                 app.storeCharacter();
                //             }
                //             else{
                //                 secondChecked = true;
                //             }
                //         }
                //     });
                    
                // if (app.middleCharacter == 'between'){
                //     axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.secondLastCharacter.toLowerCase())
                //         .then(function(resp){
                //             console.log('term?'+app.secondLastCharacter, resp.data);
                //             if (!resp.data.entries.length){
                //                 app.secondNounUndefined = true;
                //             }
                //             else {
                //                 if (secondChecked){
                //                     app.storeCharacter();
                //                 }
                //                 else{
                //                     secondChecked = true;
                //                 }
                //             }
                //         });
                // }

            },
            colorValueCell(colorDetails){
                var app = this;
                return colorDetails.map(cv=>app.colorValueText(cv)).join('&nbsp;&nbsp;') + '&nbsp;';
            },
            colorValueText(cv){
                var txt = '';
                if (cv.negation && cv.negation != ''){
                    txt += cv.negation + ' ';
                }
                if (cv.pre_constraint && cv.pre_constraint != ''){
                    txt += cv.pre_constraint + ' ';
                }
                if (cv.certainty_constraint && cv.certainty_constraint != ''){
                    txt += cv.certainty_constraint + ' ';
                }
                if (cv.degree_constraint && cv.degree_constraint != ''){
                    txt += cv.degree_constraint + ' ';
                }
                if (cv.brightness && cv.brightness != ''){
                    txt += cv.brightness + ' ';
                }
                if (cv.reflectance && cv.reflectance != ''){
                    txt += cv.reflectance + ' ';
                }
                if (cv.saturation && cv.saturation != ''){
                    txt += cv.saturation + ' ';
                }
                if (cv.colored && cv.colored != ''){
                    txt += cv.colored + ' ';
                }
                if (cv.multi_colored && cv.multi_colored != ''){
                    txt += cv.multi_colored + ' ';
                }
                if (cv.post_constraint && cv.post_constraint != ''){
                    txt += cv.post_constraint + ' ';
                }
                if (txt != ''){
                    txt = txt.slice(0,-1);
                }
                return txt + '; ';
            },
            nonColorValueCell(nonColorDetails){
                var app = this;
                return nonColorDetails.map(ncv=>app.nonColorValueText(ncv)).join('&nbsp;&nbsp;') + '&nbsp;';
            },
            nonColorValueText(ncv){
                var txt = '';
                if (ncv.negation && ncv.negation != ''){
                    txt += ncv.negation + ' ';
                }
                if (ncv.pre_constraint && ncv.pre_constraint != ''){
                    txt += ncv.pre_constraint + ' ';
                }
                if (ncv.certainty_constraint && ncv.certainty_constraint != ''){
                    txt += ncv.certainty_constraint + ' ';
                }
                if (ncv.degree_constraint && ncv.degree_constraint != ''){
                    txt += ncv.degree_constraint + ' ';
                }
                if (ncv.main_value && ncv.main_value != ''){
                    txt += ncv.main_value + ' ';
                }
                if (ncv.post_constraint && ncv.post_constraint != ''){
                    txt += ncv.post_constraint + ' ';
                }
                if (txt != ''){
                    txt = txt.slice(0,-1);
                }
                return txt + '; ';
            },
            isColorDepreacted(ncv) {

            },
            isNonColorDeprecated(ncv) {
                var app = this;
                var index = app.deprecatedTerms.findIndex(value => value['deprecated IRI'] == ncv.main_value_IRI);
                return index;
            },
            async storeCharacter() {
                var app = this;
                app.character = {};
                app.character.name = app.firstCharacter + ' ' + app.middleCharacter + ' ' + app.lastCharacter;
                
                if (app.middleCharacter == 'between'){
                    app.character.name += ' and ' + app.secondLastCharacter;
                }

                app.character.name = app.character.name.toLowerCase();
                app.character.name = app.character.name.charAt(0).toUpperCase() + app.character.name.slice(1);

                console.log("StoreCharacter!!!", app.character);
                await axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term='+app.character.name.toLowerCase()).then(resp=>{
                    if (resp.data.entries.length > 0) {
                        let methodEntry = resp.data.entries.filter(function(each) {
                            return each.resultAnnotations.some(e => e.property === "http://www.geneontology.org/formats/oboInOwl#id") == true;
                        })[0];
                        if (methodEntry) {
                            console.log(methodEntry);
                            for (var j = 0; j < methodEntry.resultAnnotations.length; j ++) {
                                if (methodEntry.resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                    app.character.IRI = methodEntry.resultAnnotations[j].value;
                                    break;
                                }
                            }
                        }
                    }
                });
                app.character.username = app.user.name;
                app.characterUsername = app.user.name;
                app.character.standard = 0;
                app.character.creator = app.user.name + ' via CR';
                app.editFlag = false;

                if (app.checkHaveUnit(app.character.name)) {
                    // Initializing the methodFieldData //
                    app.methodFieldData.fromTerm = null;
                    app.methodFieldData.fromId = null;
                    app.methodFieldData.toTerm = null;
                    app.methodFieldData.toId = null;
                    app.methodFieldData.includeTerm = null;
                    app.methodFieldData.includeId = null;
                    app.methodFieldData.excludeTerm = null;
                    app.methodFieldData.excludeId = null;
                    app.methodFieldData.whereTerm = null;
                    app.methodFieldData.whereId = null;
                    app.methodFieldData.fromNeedMore = false;
                    app.methodFieldData.toNeedMore = false;
                    app.methodFieldData.includeNeedMore = false;
                    app.methodFieldData.excludeNeedMore = false;
                    app.methodFieldData.whereNeedMore = false;
                    app.methodFieldData.fromSynonyms = [];
                    app.methodFieldData.toSynonyms = [];
                    app.methodFieldData.includeSynonyms = [];
                    app.methodFieldData.excludeSynonyms = [];
                    app.methodFieldData.whereSynonyms = [];
                    app.methodFieldData.noneSynonymFlag = {
                        from: false,
                        to: false,
                        include: false,
                        exclude: false,
                        where: false,
                    };
                    //
                    app.parentData = [];
                    app.parentData[3] = app.user;
                    app.parentData[9] = app.methodFieldData;
                    app.metadataFlag = 'method';
                    app.currentMetadata = method;
                } else {
                    app.parentData = '';
                    app.metadataFlag = 'tag';
                    app.currentMetadata = tag;
                }

                sessionStorage.setItem("characterName", app.character.name);
                app.newCharacterFlag = false;
                app.detailsFlag = true;
            },
            cancelNewCharacter() {
                var app = this;
                app.newCharacterFlag = false;
            },
            cancelCharacter() {
                var app = this;
                app.detailsFlag = false;
            },
            showDetails(metadata, previousMetadata = null) {
                var app = this;

                console.log('checkHaveUnit', app.checkHaveUnit(app.character.name));

                console.log('metadata', metadata);
                console.log("app.character=", app.character);
                if (( app.checkHaveUnit(app.character.name)) || ( metadata != 'method' && metadata != 'unit' && metadata != 'summary' )) {
                    app.metadataFlag = metadata;
                    switch (metadata) {
                        case 'method':
                            // Initializing the methodFieldData //
                            app.methodFieldData.fromTerm = null;
                            app.methodFieldData.fromId = null;
                            app.methodFieldData.toTerm = null;
                            app.methodFieldData.toId = null;
                            app.methodFieldData.includeTerm = null;
                            app.methodFieldData.includeId = null;
                            app.methodFieldData.excludeTerm = null;
                            app.methodFieldData.excludeId = null;
                            app.methodFieldData.whereTerm = null;
                            app.methodFieldData.whereId = null;
                            app.methodFieldData.fromNeedMore = false;
                            app.methodFieldData.toNeedMore = false;
                            app.methodFieldData.includeNeedMore = false;
                            app.methodFieldData.excludeNeedMore = false;
                            app.methodFieldData.whereNeedMore = false;
                            app.methodFieldData.fromSynonyms = [];
                            app.methodFieldData.toSynonyms = [];
                            app.methodFieldData.includeSynonyms = [];
                            app.methodFieldData.excludeSynonyms = [];
                            app.methodFieldData.whereSynonyms = [];
                            app.methodFieldData.noneSynonymFlag = {
                                from: false,
                                to: false,
                                include: false,
                                exclude: false,
                                where: false,
                            };
                            //
                            app.parentData = [];
                            app.parentData[0] = app.character.method_as;
                            app.parentData[3] = app.user;
                            app.parentData[4] = app.character.method_from;
                            app.parentData[5] = app.character.method_to;
                            app.parentData[6] = app.character.method_include;
                            app.parentData[7] = app.character.method_exclude;
                            app.parentData[8] = app.character.method_where;
                            app.parentData[9] = app.methodFieldData;
                            app.currentMetadata = method;
                            break;
                        case 'unit':
                            app.parentData = app.character.unit;
                            app.currentMetadata = unit;
                            break;
                        case 'tag':
                            app.parentData = app.character.standard_tag;
                            app.currentMetadata = tag;
                            break;
                        case 'summary':
                            app.parentData = app.character.summary;
                            app.currentMetadata = summary;
                            break;
                        case 'creator':
                            app.parentData = app.character.username + ' via CR';//app.character.creator;
                            app.currentMetadata = creator;
                            break;
                        case 'usage':
                            axios.get('/chrecorder/public/api/v1/get-usage/' + app.character.id)
                                .then(function (resp) {
                                    app.parentData = [];
                                    app.parentData[0] = resp.data.usage_count;
                                    app.parentData[1] = app.user.name;
                                    app.parentData[2] = app.taxonName;
                                    app.currentMetadata = usage;
                                });

                            break;
                        case 'history':
                            app.parentData = app.character.history;
                            app.currentMetadata = history;
                            break;
                        default:
                            break;
                    }
                }

            },
            checkNumericalCharacter(characterName) {
                var result = false;
                if (characterName.startsWith('Length')
                    || characterName.startsWith('Width')
                    || characterName.startsWith('Depth')
                    || characterName.startsWith('Diameter')
                    || characterName.startsWith('Count')
                    || characterName.startsWith('Distance')
                    || characterName.startsWith('Number')
                    || characterName.startsWith('Ratio')) {
                    result = true;
                }
                return result;
            },
            showStandardCharacters() {
                var app = this;
                // app.isLoading = true;
                app.standardShowFlag = !app.standardShowFlag;
                console.log('userCharacters', app.userCharacters);
                console.log('defaultCharacters', app.defaultCharacters);
                var postCharacters = [];
                for (var i = 0; i < app.defaultCharacters.length; i++) {
                    var character = app.defaultCharacters[i];
                    if (!app.userCharacters.find(ch => ch.name == character.name) && character.standard == 1) {
                    //    character.username = app.user.name;
                        character.show_flag = false;
                        if (character.name.startsWith('Length of')
                            || character.name.startsWith('Width of')
                            || character.name.startsWith('Depth of')
                            || character.name.startsWith('Diameter of')
                            || character.name.startsWith('Count of')
                            || character.name.startsWith('Distance between')) {
                            character.unit = 'cm';
                            character.summary = 'range-percentile';
                        } else if (character.name.startsWith('Number of')
                            || character.name.startsWith('Ratio of')) {
                            character.unit = '';
                            character.summary = 'range-percentile';
                        } else {
                            character.unit = '';
                            character.summary = '';
                        }
                        postCharacters.push(character);
                    }
                }

                console.log('postCharacters', postCharacters);

                axios.post('/chrecorder/public/api/v1/character/add-standard', postCharacters)
                    .then(function (resp) {
                        console.log('addStandard resp', resp.data);
                        app.userCharacters = resp.data;
                        app.isLoading = false;
                        app.refreshUserCharacters();
                        app.showTableForTab(app.currentTab);
                    });
            },
            removeStandardCharacter(characterId) {
                var app = this;
                console.log('characterId', characterId);
                app.toRemoveCharacterId = characterId;
                app.toRemoveStandardConfirmFlag = true;
            },
            confirmRemoveCharacter() {
                var app = this;

                axios.post("/chrecorder/public/api/v1/character/delete/" + app.user.id + "/" + app.toRemoveCharacterId)
                    .then(function (resp) {
                        app.toRemoveCharacterId = null;
                        app.toRemoveStandardConfirmFlag = false;
                        app.userCharacters = resp.data.characters;
                        app.headers = resp.data.headers;
                        app.values = resp.data.values;
                        app.defaultCharacters = resp.data.defaultCharacters;
                        if (app.userCharacters.length == 0) {
                            app.matrixShowFlag = false;
                        }
                        app.refreshUserCharacters();
                        app.refreshDefaultCharacters();
                        if (app.userTags.length!= resp.data.userTags.length && !resp.data.userTags.find(ch => ch == app.currentTab)){
                            app.userTags = resp.data.userTags;
                            if (app.userTags[0])app.showTableForTab(app.userTags[0].tag_name);
                        }
                        else app.showTableForTab(app.currentTab);
                        
                    });

            },
            removeUserCharacter(characterId) {
                var app = this;
                var oldUserTag = app.userCharacters.find(ch => ch.id == characterId).standard_tag;
                axios.post("/chrecorder/public/api/v1/character/delete/" + app.user.id + "/" + characterId)
                    .then(function (resp) {
                        app.defaultCharacters = resp.data.defaultCharacters;
                        app.refreshDefaultCharacters();
                        app.userCharacters = resp.data.characters;
                        app.headers = resp.data.headers;
                        app.values = resp.data.values;
                        if (app.userCharacters.length == 0) {
                            app.matrixShowFlag = false;
                        }
                        if (!app.userCharacters.find(ch => ch.standard_tag == oldUserTag)) {
                            var jsonUserTag = {
                                user_id: app.user.id,
                                user_tag: oldUserTag
                            };
                            console.log('remove jsonUserTag', jsonUserTag);
                            axios.post("/chrecorder/public/api/v1/user-tag/remove", jsonUserTag)
                                .then(function (resp) {
                                    console.log("remove UserTag", resp.data);
                                    app.showTableForTab(app.currentTab);
                                });
                        }
                        app.refreshUserCharacters();
                        app.showTableForTab(app.currentTab);
                    });
            },
            removeAllCharacters() {
                var app = this;
                // app.isLoading = true;
                axios.post('/chrecorder/public/api/v1/character/remove-all')
                    .then(function (resp) {
                        app.isLoading = false;
                        app.userCharacters = resp.data.characters;
                        app.headers = resp.data.headers;
                        app.values = resp.data.values;
                        if (app.userCharacters.length == 0) {
                            app.matrixShowFlag = false;
                        }
                        app.refreshUserCharacters();
                        app.showTableForTab(app.currentTab);
                    });
            },

            checkDctionary: async() => {

            },
            saveCharacter(metadataFlag) {
                var app = this;
                console.log('app.character = ', app.character);
                console.log('metadataFlag', metadataFlag);
                app.saveCharacterButtonFlag = true;
                app.methodFieldData.fromTerm = null;
                app.methodFieldData.fromId = null;
                app.methodFieldData.toTerm = null;
                app.methodFieldData.toId = null;
                app.methodFieldData.includeTerm = null;
                app.methodFieldData.includeId = null;
                app.methodFieldData.excludeTerm = null;
                app.methodFieldData.excludeId = null;
                app.methodFieldData.whereTerm = null;
                app.methodFieldData.whereId = null;
                app.methodFieldData.fromNeedMore = false;
                app.methodFieldData.toNeedMore = false;
                app.methodFieldData.includeNeedMore = false;
                app.methodFieldData.excludeNeedMore = false;
                app.methodFieldData.whereNeedMore = false;
                app.methodFieldData.fromSynonyms = [];
                app.methodFieldData.toSynonyms = [];
                app.methodFieldData.includeSynonyms = [];
                app.methodFieldData.excludeSynonyms = [];
                app.methodFieldData.whereSynonyms = [];
                app.methodFieldData.noneSynonymFlag = {
                    from: false,
                    to: false,
                    include: false,
                    exclude: false,
                    where: false,
                };

                var checkMethod = true;

                var tempViewFlag = (sessionStorage.getItem('viewFlag') == 'true');

                var awaitCount = 0;

                if (!app.editFlag&&(app.checkHaveUnit(app.character.name) == true) && (tempViewFlag == false)) {
                    var tempFlag = false;
                    awaitCount ++;
                    console.log('awaitCount',awaitCount);
                    axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.character.name.toLowerCase())
                        .then(function (resp) {
                            awaitCount --;
                            console.log('awaitCount',awaitCount);
                            console.log('search term resp', resp.data);
                            for (var i = 0; i < resp.data.entries.length; i++) {
                                if (resp.data.entries[i].term == app.character.name) {
                                    tempFlag = true;
                                    break;
                                }
                            }
                        });
                    if (app.character['method_from'] != null && app.character['method_from'] != '') {
                        awaitCount ++;
                        console.log('awaitCount',awaitCount);
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.character['method_from'].toLowerCase())
                            .then(function (resp) {
                                awaitCount --;
                                console.log('awaitCount',awaitCount);
                                console.log('method_from search resp', resp.data);
                                for (var i = 0; i < resp.data.entries.length; i++) {
                                    if (resp.data.entries[i].score == 1) {
                                        app.methodFieldData.fromTerm = resp.data.entries[i].term;
                                        app.methodFieldData.fromId = resp.data.entries[i].resultAnnotations.filter(function (e) {
                                            return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                        })[0].value;
                                        console.log('fromId', app.methodFieldData.fromId);
                                        break;
                                    }

                                }
                                if (app.methodFieldData.fromId == null && (!app.character.method_greenTick || !app.character.method_greenTick.from)) {
                                    checkMethod = false;
                                    app.methodFieldData.fromNeedMore = true;
                                    app.methodFieldData.fromSynonyms = resp.data.entries;
                                    if (app.methodFieldData.fromSynonyms.length == 0) {
                                        app.methodFieldData.noneSynonymFlag.from = true;
                                    }
                                    for (var i = 0; i < app.methodFieldData.fromSynonyms.length; i++) {
                                        app.methodFieldData.fromSynonyms[i].tooltip = '';
                                        var temp = app.methodFieldData.fromSynonyms[i].resultAnnotations.filter(function (e) {
                                            return e.property == 'http://purl.oblibrary.org/obo/IAO_0000115';
                                        });
                                        if (temp.length > 0) {
                                            app.methodFieldData.fromSynonyms[i].tooltip = temp[0].value;
                                        }
                                    }
                                }
                            });
                    }

                    if (app.character['method_to'] != null && app.character['method_to'] != '') {
                        awaitCount ++;
                        console.log('awaitCount',awaitCount);
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.character['method_to'].toLowerCase())
                            .then(function (resp) {
                                awaitCount --;
                                console.log('awaitCount',awaitCount);
                                console.log('method_to search resp', resp.data);
                                for (var i = 0; i < resp.data.entries.length; i++) {
                                    if (resp.data.entries[i].score == 1) {
                                        app.methodFieldData.toTerm = resp.data.entries[i].term;
                                        app.methodFieldData.toId = resp.data.entries[i].resultAnnotations.filter(function (e) {
                                            return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                        })[0].value;
                                        console.log('toId', app.methodFieldData.toId);
                                        break;
                                    }

                                }
                                if (app.methodFieldData.toId == null && (!app.character.method_greenTick || !app.character.method_greenTick.to)) {
                                    checkMethod = false;
                                    app.methodFieldData.toNeedMore = true;
                                    app.methodFieldData.toSynonyms = resp.data.entries;
                                    if (app.methodFieldData.toSynonyms.length == 0) {
                                        app.methodFieldData.noneSynonymFlag.to = true;
                                    }
                                    for (var i = 0; i < app.methodFieldData.toSynonyms.length; i++) {
                                        app.methodFieldData.toSynonyms[i].tooltip = '';
                                        var temp = app.methodFieldData.toSynonyms[i].resultAnnotations.filter(function (e) {
                                            return e.property == 'http://purl.oblibrary.org/obo/IAO_0000115';
                                        });
                                        if (temp.length > 0) {
                                            app.methodFieldData.toSynonyms[i].tooltip = temp[0].value;
                                        }
                                    }
                                }
                            });
                    }
                    if (app.character['method_include'] != null && app.character['method_include'] != '') {
                        awaitCount ++;
                        console.log('awaitCount',awaitCount);
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.character['method_include'].toLowerCase())
                            .then(function (resp) {
                                awaitCount --;
                                console.log('awaitCount',awaitCount);
                                for (var i = 0; i < resp.data.entries.length; i++) {
                                    if (resp.data.entries[i].score == 1) {
                                        app.methodFieldData.includeTerm = resp.data.entries[i].term;
                                        app.methodFieldData.includeId = resp.data.entries[i].resultAnnotations.filter(function (e) {
                                            return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                        })[0].value;
                                        console.log('includeId', app.methodFieldData.includeId);
                                        break;
                                    }

                                }
                                if (app.methodFieldData.includeId == null && (!app.character.method_greenTick || !app.character.method_greenTick.include)) {
                                    checkMethod = false;
                                    app.methodFieldData.includeNeedMore = true;
                                    app.methodFieldData.includeSynonyms = resp.data.entries;
                                    if (app.methodFieldData.includeSynonyms.length == 0) {
                                        app.methodFieldData.noneSynonymFlag.include = true;
                                    }
                                    for (var i = 0; i < app.methodFieldData.includeSynonyms.length; i++) {
                                        app.methodFieldData.includeSynonyms[i].tooltip = '';
                                        var temp = app.methodFieldData.includeSynonyms[i].resultAnnotations.filter(function (e) {
                                            return e.property == 'http://purl.oblibrary.org/obo/IAO_0000115';
                                        });
                                        if (temp.length > 0) {
                                            app.methodFieldData.includeSynonyms[i].tooltip = temp[0].value;
                                        }
                                    }
                                }
                            });
                    }

                    if (app.character['method_exclude'] != null && app.character['method_exclude'] != '') {
                        awaitCount ++;
                        console.log('awaitCount',awaitCount);
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.character['method_exclude'].toLowerCase())
                            .then(function (resp) {
                                awaitCount --;
                                console.log('awaitCount',awaitCount);
                                for (var i = 0; i < resp.data.entries.length; i++) {
                                    if (resp.data.entries[i].score == 1) {
                                        app.methodFieldData.excludeTerm = resp.data.entries[i].term;
                                        app.methodFieldData.excludeId = resp.data.entries[i].resultAnnotations.filter(function (e) {
                                            return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                        })[0].value;
                                        console.log('includeId', app.methodFieldData.excludeId);
                                        break;
                                    }

                                }
                                if (app.methodFieldData.excludeId == null && (!app.character.method_greenTick || !app.character.method_greenTick.exclude)) {
                                    checkMethod = false;
                                    app.methodFieldData.excludeNeedMore = true;
                                    app.methodFieldData.excludeSynonyms = resp.data.entries;
                                    if (app.methodFieldData.excludeSynonyms.length == 0) {
                                        app.methodFieldData.noneSynonymFlag.exclude = true;
                                    }
                                    for (var i = 0; i < app.methodFieldData.excludeSynonyms.length; i++) {
                                        app.methodFieldData.excludeSynonyms[i].tooltip = '';
                                        var temp = app.methodFieldData.excludeSynonyms[i].resultAnnotations.filter(function (e) {
                                            return e.property == 'http://purl.oblibrary.org/obo/IAO_0000115';
                                        });
                                        if (temp.length > 0) {
                                            app.methodFieldData.excludeSynonyms[i].tooltip = temp[0].value;
                                        }
                                    }
                                }
                            });
                    }

                    if (app.character['method_where'] != null && app.character['method_where'] != '') {
                        awaitCount ++;
                        console.log('awaitCount',awaitCount);
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.character['method_where'].toLowerCase())
                            .then(function (resp) {
                                awaitCount --;
                                console.log('awaitCount',awaitCount);
                                for (var i = 0; i < resp.data.entries.length; i++) {
                                    if (resp.data.entries[i].score == 1) {
                                        app.methodFieldData.whereTerm = resp.data.entries[i].term;
                                        app.methodFieldData.whereId = resp.data.entries[i].resultAnnotations.filter(function (e) {
                                            return e.property == 'http://www.geneontology.org/formats/oboInOwl#id';
                                        })[0].value;
                                        console.log('includeId', app.methodFieldData.whereId);
                                        break;
                                    }

                                }
                                if (app.methodFieldData.whereId == null && (!app.character.method_greenTick || !app.character.method_greenTick.where)) {
                                    checkMethod = false;
                                    app.methodFieldData.whereNeedMore = true;
                                    app.methodFieldData.whereSynonyms = resp.data.entries;
                                    if (app.methodFieldData.whereSynonyms.length == 0) {
                                        app.methodFieldData.noneSynonymFlag.where = true;
                                    }
                                    for (var i = 0; i < app.methodFieldData.whereSynonyms.length; i++) {
                                        app.methodFieldData.whereSynonyms[i].tooltip = '';
                                        var temp = app.methodFieldData.whereSynonyms[i].resultAnnotations.filter(function (e) {
                                            return e.property == 'http://purl.oblibrary.org/obo/IAO_0000115';
                                        });
                                        if (temp.length > 0) {
                                            app.methodFieldData.whereSynonyms[i].tooltip = temp[0].value;
                                        }
                                    }
                                }
                            });
                    }


                    if (tempFlag == false) {

                        var jsonClass = {
                            "user": app.sharedFlag ? '' : app.user.name,
                            "ontology": 'carex',
                            "term": app.character.name,
                            "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#toreview",
                            "definition": '',
                            "createdBy": app.user.name,
                            "creationDate": new Date(),
                            "definitionSrc": "tba",
                            "examples": "tba",
                            "logicDefinition": "measured_from some [" + app.character['method_from'] + "] and measured_to some [" + app.character['method_to'] + "]"
                        };
                        if (app.character['method_from'] != null) {
                            jsonClass.definition = jsonClass.definition + 'from [' + app.character['method_from'] + ']';
                        }
                        if (app.character['method_to'] != null) {
                            jsonClass.definition = jsonClass.definition + ' to [' + app.character['method_to'] + ']';
                        }
                        if (app.character['method_include'] != null) {
                            jsonClass.definition = jsonClass.definition + ' include [' + app.character['method_include'] + ']';
                        }
                        if (app.character['method_exclude'] != null) {
                            jsonClass.definition = jsonClass.definition + ' exclude [' + app.character['method_exclude'] + ']';
                        }
                        if (app.character['method_where'] != null) {
                            jsonClass.definition = jsonClass.definition + ' where [' + app.character['method_where'] + ']';
                        }
                        if (app.character.name.toLowerCase().split(' ')[0] == 'distance') {
                            jsonClass.superclassIRI = "http://biosemantics.arizona.edu/ontologies/carex#perceived-distance"
                        } else if (app.character.name.toLowerCase().split(' ')[0] == 'length') {
                            jsonClass.superclassIRI = "http://biosemantics.arizona.edu/ontologies/carex#perceived-length"

                        } else if (app.character.name.toLowerCase().split(' ')[0] == 'width') {
                            jsonClass.superclassIRI = "http://biosemantics.arizona.edu/ontologies/carex#perceived-width"
                        }
                        axios.post('http://shark.sbs.arizona.edu:8080/class', jsonClass)
                            .then(function (resp) {
                                console.log('class resp', resp);
                                axios.post('http://shark.sbs.arizona.edu:8080/save', {
                                    "user": app.sharedFlag ? '' : app.user.name,
                                    "ontology": 'carex'
                                })
                                    .then(function (resp) {
                                        console.log('save resp', resp);
                                    });
                            })
                            .catch(function (resp) {
                                console.log('class error resp', resp);
                            });
                    } else {
                        var jsonClass = {
                            "user": app.sharedFlag ? '' : app.user.name,
                            "ontology": 'carex',
                            "term": app.character.name + '(' + app.user.name + ')',
                            "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#toreview",
                            "definition": '',
                            "createdBy": app.user.name,
                            "creationDate": new Date(),
                            "definitionSrc": "tba",
                            "examples": "tba",
                            "logicDefinition": "measured_from some [" + app.character['method_from'] + "] and measured_to some [" + app.character['method_to'] + "]"
                        };

                        if (app.character['method_from'] != null) {
                            jsonClass.definition = jsonClass.definition + 'from [' + app.character['method_from'] + ']';
                        }
                        if (app.character['method_to'] != null) {
                            jsonClass.definition = jsonClass.definition + ' to [' + app.character['method_to'] + ']';
                        }
                        if (app.character['method_include'] != null) {
                            jsonClass.definition = jsonClass.definition + ' include [' + app.character['method_include'] + ']';
                        }
                        if (app.character['method_exclude'] != null) {
                            jsonClass.definition = jsonClass.definition + ' exclude [' + app.character['method_exclude'] + ']';
                        }
                        if (app.character['method_where'] != null) {
                            jsonClass.definition = jsonClass.definition + ' where [' + app.character['method_where'] + ']';
                        }
                        if (app.character.name.toLowerCase().split(' ')[0] == 'distance') {
                            jsonClass.superclassIRI = "http://biosemantics.arizona.edu/ontologies/carex#perceived-distance"
                        } else if (app.character.name.toLowerCase().split(' ')[0] == 'length') {
                            jsonClass.superclassIRI = "http://biosemantics.arizona.edu/ontologies/carex#perceived-length"

                        } else if (app.character.name.toLowerCase().split(' ')[0] == 'width') {
                            jsonClass.superclassIRI = "http://biosemantics.arizona.edu/ontologies/carex#perceived-width"
                        }

                        axios.post('http://shark.sbs.arizona.edu:8080/class', jsonClass)
                            .then(function (resp) {
                                console.log('class resp', resp);
                                axios.post('http://shark.sbs.arizona.edu:8080/save', {
                                    "user": app.sharedFlag ? '' : app.user.name,
                                    "ontology": 'carex'
                                })
                                    .then(function (resp) {
                                        console.log('save resp', resp);
                                    });
                            })
                            .catch(function (resp) {
                                console.log('class error resp', resp);
                            });
                    }
                    //    });
                }

                const awaitTimerID = setInterval(function(){
                    if (awaitCount)return;
                    clearInterval(awaitTimerID);
                    if (checkMethod == false) {
                        console.log('checkMethod', checkMethod);
                        if (app.metadataFlag != 'method') {
                            console.log('not method field');
                            app.parentData = [];
                            app.parentData[0] = app.character.method_as;
                            app.parentData[3] = app.user;
                            app.parentData[4] = app.character.method_from;
                            app.parentData[5] = app.character.method_to;
                            app.parentData[6] = app.character.method_include;
                            app.parentData[7] = app.character.method_exclude;
                            app.parentData[8] = app.character.method_where;
                            app.parentData[9] = app.methodFieldData;
                            app.metadataFlag = 'method';
                            app.currentMetadata = method;
                        } else {
                            console.log('method field');
                            app.currentMetadata = null;

                            setTimeout(function () {
                                app.parentData = [];
                                app.parentData[0] = app.character.method_as;
                                app.parentData[3] = app.user;
                                app.parentData[4] = app.character.method_from;
                                app.parentData[5] = app.character.method_to;
                                app.parentData[6] = app.character.method_include;
                                app.parentData[7] = app.character.method_exclude;
                                app.parentData[8] = app.character.method_where;
                                app.parentData[9] = app.methodFieldData;
                                app.metadataFlag = 'method';
                                app.currentMetadata = method;
                            }, 10);


                        }

                    } else {
                        var checkFields = true;

                        if (app.character.summary == ''
                            || app.character.summary == null
                            || app.character.summary == undefined) {
                            if (app.checkHaveUnit(app.character.name)) {
                                app.character.summary = 'range-percentile';
                            } else {
                                app.character.summary = '';
                            }
                        }

                        if ((app.character['method_from'] == null || app.character['method_from'] == '') &&
                            (app.character['method_to'] == null || app.character['method_to'] == '') &&
                            (app.character['method_include'] == null || app.character['method_include'] == '') &&
                            (app.character['method_exclude'] == null || app.character['method_exclude'] == '') &&
                            (app.character['method_where'] == null || app.character['method_where'] == '')) {
                            checkFields = false;
                        }

                        if (!app.character['unit']) {
                            checkFields = false;
                        }

                        if (!app.checkHaveUnit(app.character.name)) {
                            checkFields = true;
                        }


                        if (checkFields) {
                            if ((app.character.standard_tag == null
                                || app.character.standard_tag == ''
                                || app.character.standard_tag == undefined)) {
                                app.showDetails('tag', app.metadataFlag);

                            } else {
                                if (app.checkHaveUnit(app.character.name)) {
                                    app.confirmMethod = true;
                                } else {
                                    app.confirmTag = true;
                                }
                            }

                        } else {
                            app.showDetails('unit', app.metadataFlag);
                        }
                    }
                    app.saveCharacterButtonFlag = false;
                }, 100)
            },
            use(characterId) {
                var app = this;
                console.log('use', characterId);
                app.character = app.userCharacters.find(ch => ch.id == characterId);
                if (!app.character) {
                    app.character = app.defaultCharacters.find(ch => ch.id == characterId);
                    app.character.name = app.character.name.toLowerCase();
                    app.character.name = app.character.name.charAt(0).toUpperCase() + app.character.name.slice(1);
                    if (!app.userCharacters.find(ch => ch.name.toLowerCase() == app.character.name.toLowerCase()
                      && ch.method_from == app.character.method_from
                      && ch.method_to == app.character.method_to
                      && ch.method_include == app.character.method_include
                      && ch.method_exclude == app.character.method_exclude
                      && ch.method_where == app.character.method_where
                     )) {
                        console.log('app.character', app.character);
            //    app.character.show_flag = false;
            //             app.character.standard = 1;
                        app.characterUsername = app.character.username;

                        var checkFields = true;
                        if (((this.character['method_from'] == null || this.character['method_from'] == '') &&
                            (this.character['method_to'] == null || this.character['method_to'] == '') &&
                            (this.character['method_include'] == null || this.character['method_include'] == '') &&
                            (this.character['method_exclude'] == null || this.character['method_exclude'] == '') &&
                            (this.character['method_where'] == null || this.character['method_where'] == '')) &&
                            app.checkHaveUnit(app.character.name)) {
                            checkFields = false;
                        }

                        if (!app.character['unit'] && app.checkHaveUnit(app.character.name)) {
                            app.character.unit = 'cm';
                            if (!app.character['summary']) {
                                app.character.summary = 'range-percentile';
                            }
                        }

                        if (checkFields) {
                            if (app.checkHaveUnit(app.character.name)) {
                                app.confirmMethod = true;
                            } else {
                                app.confirmTag = true;
                            }

                        } else {
                            app.showDetails('unit', app.metadataFlag);
                        }
                    } else {
                        alert("The character already exists for this user!!");
                        app.detailsFlag = false;
                    }
                } else {
                    alert("The character already exists for this user!!");
                    app.detailsFlag = false;
                }

            },
            enhance(characterId) {
                var app = this;
                app.item = characterId;
                console.log('characterId', characterId);
                var selectedCharacter = app.defaultCharacters.find(ch => ch.id == characterId);
                if (!selectedCharacter) {
                    selectedCharacter = app.userCharacters.find(ch => ch.id == characterId);
                }
                console.log('selectedCharacter.username', selectedCharacter.username);
            //    selectedCharacter.username = selectedCharacter.username + ', ' + app.user.name;
            //    app.characterUsername = selectedCharacter.username + ', ' + app.user.name;
                app.oldCharacter.method_from = selectedCharacter.method_from;
                app.oldCharacter.method_to = selectedCharacter.method_to;
                app.oldCharacter.method_include = selectedCharacter.method_include;
                app.oldCharacter.method_exclude = selectedCharacter.method_exclude;
                app.oldCharacter.method_where = selectedCharacter.method_where;
            //    app.editFlag = true;
                selectedCharacter.creator = app.user.name + ' via CR';
                selectedCharacter.standard = 0;
                app.detailsFlag = false;
                sessionStorage.setItem('viewFlag', false);
                sessionStorage.setItem('edit_created_other', false);
                sessionStorage.setItem('editFlag', false);
                app.viewFlag = false;
                app.enhanceFlag = true;
                setTimeout(function () {
                    app.editCharacter(selectedCharacter, false, false, true);
                }, 1);
            },
            methodConfirm() {
                var app = this;
                app.confirmMethod = false;
                app.confirmUnit = true;
            },
            cancelConfirmMethod() {
                var app = this;
                app.confirmMethod = false;
            },
            unitConfirm() {
                var app = this;
                app.confirmUnit = false;
                app.confirmTag = true;
            },
            cancelConfirmUnit() {
                var app = this;
                app.confirmUnit = false;
            },
            tagConfirm() {
                var app = this;
                app.confirmTag = false;
                app.confirmSummary = true;
            },
            cancelConfirmTag() {
                var app = this;
                app.confirmTag = false;
            },
            cancelConfirmSummary() {
                var app = this;
                app.confirmSummary = false;
            },
            checkUserTag(userTag) {
                var app = this;

            },
            confirmSave(metadataFlag) {
                var app = this;
                var userId = sessionStorage.getItem("userId");
                app.confirmSummary = false;
                axios.get("/chrecorder/public/api/v1/character/" + userId)
                    .then(function (resp) {
                        console.log('getCharacter', resp);
                        var currentCharacters = resp.data.characters;
                    //    app.character.standard = 0;
                    //    app.character.username = app.characterUsername;
                        app.character.name = app.character.name.toLowerCase();
                        app.character.name = app.character.name.charAt(0).toUpperCase() + app.character.name.slice(1);
                        if (currentCharacters.find(ch => ch.name.toLowerCase() == app.character.name.toLowerCase()
                            && ch.method_from == app.character.method_from
                            && ch.method_to == app.character.method_to
                            && ch.method_include == app.character.method_include
                            && ch.method_exclude == app.character.method_exclude
                            && ch.method_where == app.character.method_where)) {
                            if (app.editFlag || app.enhanceFlag) {
                                if (app.character.standard_tag == app.currentTab) {
                                    app.character.show_flag = true;
                                } else {
                                    app.character.show_flag = false;
                                }
                                console.log('oldCharacter', app.oldCharacter);
                                console.log('currentCharacter', app.character);
                                if ((app.character.method_from != app.oldCharacter.method_from)
                                    || (app.character.method_to != app.oldCharacter.method_to)
                                    || (app.character.method_include != app.oldCharacter.method_include)
                                    || (app.character.method_exclude != app.oldCharacter.method_exclude)
                                    || (app.character.method_where != app.oldCharacter.method_where)) {
                                    console.log('******1');
                                    console.log('app.character.username', app.character.username);
                                    console.log('app.character.owner_name', app.character.owner_name);
                                    if (!app.character.username.includes(app.character.owner_name)) {
                                        console.log('******2');
                                        app.character.standard = 0;
                                        app.character.username += ', ' + app.character.owner_name;
                                    }
                                }
        
                                axios.post('/chrecorder/public/api/v1/character/update-character', app.character)
                                    .then(function (resp) {
                                        app.userTags = resp.data.userTags;
                                        app.userCharacters = resp.data.characters;
                                        app.headers = resp.data.headers;
                                        app.values = resp.data.values;
                                        app.taxonName = resp.data.taxon;
                                        app.defaultCharacters = resp.data.defaultCharacters;
                                        app.refreshDefaultCharacters();
                                        app.refreshUserCharacters();
                                        app.showTableForTab(app.character.standard_tag);

                                        app.enhanceFlag = false;
                                        app.detailsFlag = false
                                    });
                            } else {
                                alert("The character already exists for this user!!");
                            }
                        } else {
                            if (app.character.standard_tag == app.currentTab) {
                                app.character.show_flag = true;
                            } else {
                                app.character.show_flag = false;
                            }
                            if (app.enhanceFlag) {
                                if ((app.character.method_from != app.oldCharacter.method_from)
                                    || (app.character.method_to != app.oldCharacter.method_to)
                                    || (app.character.method_include != app.oldCharacter.method_include)
                                    || (app.character.method_exclude != app.oldCharacter.method_exclude)
                                    || (app.character.method_where != app.oldCharacter.method_where)) {
                                    console.log('******1');
                                    console.log('app.character.username', app.character.username);
                                    console.log('app.character.owner_name', app.character.owner_name);
                                    if (!app.character.username.includes(app.character.owner_name)) {
                                        console.log('******2');
                                        app.character.standard = 0;
                                        app.character.username += ', ' + app.user.name;
                                    }
                                }
                            }

                            if (app.matrixShowFlag) {
                                axios.post('/chrecorder/public/api/v1/character/add-character', app.character)
                                    .then(function (resp) {
                                        if (!app.userCharacters.find(ch => ch.standard_tag == app.character.standard_tag)) {
                                            var jsonUserTag = {
                                                user_id: app.user.id,
                                                user_tag: app.character.standard_tag
                                            };
                                            console.log('jsonUserTag', jsonUserTag);
                                            axios.post("/chrecorder/public/api/v1/user-tag/create", jsonUserTag)
                                                .then(function (resp) {
                                                    axios.get('/chrecorder/public/api/v1/user-tag/' + app.user.id)
                                                        .then(function (resp) {
                                                            app.userTags = resp.data;
                                                        });
                                                    console.log("create UserTag", resp.data);
                                                });
                                        } else {
                                            axios.get('/chrecorder/public/api/v1/user-tag/' + app.user.id)
                                                .then(function (resp) {
                                                    app.userTags = resp.data;
                                                });
                                        }
                                        app.userCharacters = resp.data.characters;
                                        app.headers = resp.data.headers;
                                        app.values = resp.data.values;
                                        app.taxonName = resp.data.taxon;
                                        app.defaultCharacters = resp.data.defaultCharacters;
                                        console.log('defaultCharacters', app.defaultCharacters);
                                        app.refreshDefaultCharacters();
                                        app.refreshUserCharacters();

                                        app.enhanceFlag = false;
                                        app.detailsFlag = false;
                                        app.showTableForTab(app.currentTab);
                                    });
                            } else {
                                axios.post("/chrecorder/public/api/v1/character/create", app.character)
                                    .then(function (resp) {
                                        if (!app.userCharacters.find(ch => ch.standard_tag == app.character.standard_tag)) {
                                            var jsonUserTag = {
                                                user_id: app.user.id,
                                                user_tag: app.character.standard_tag
                                            };
                                            console.log('jsonUserTag', jsonUserTag);
                                            axios.post("/chrecorder/public/api/v1/user-tag/create", jsonUserTag)
                                                .then(function (resp) {
                                                    console.log("create UserTag", resp.data);
                                                });
                                        }
                                        app.userCharacters = resp.data.characters;
                                        app.refreshUserCharacters();
                                        app.defaultCharacters = resp.data.defaultCharacters;
                                        app.refreshDefaultCharacters();


                                        app.detailsFlag = false;
                                        app.showTableForTab(app.currentTab);
                                    });
                            }
                        }

                    });
                console.log("app.character", app.character);
            },
            confirmCollapse() {
                var app = this;
                //alert('');
                console.log('userTags', app.userTags);
                console.log('userCharacters', app.userCharacters);
                console.log('standardCharactersTags', app.standardCharactersTags);
                console.log(app.userCharacters.find(ch => ch.standard == 1 || !ch.username.includes(app.user.name)));
                console.log(app.userCharacters.find(ch => ch.standard == 0 && ch.username.includes(app.user.name)));
                console.log(app.matrixShowFlag);
                if ((app.userCharacters.find(ch => ch.standard == 1 || !ch.username.includes(app.user.name)) || app.userCharacters.find(ch => ch.standard == 0 && ch.username.includes(app.user.name)))&&!app.matrixShowFlag){
                    app.toCollapseConfirmFlag = true;
                }
            },
            generateMatrix() {
                var app = this;
                // app.isLoading = true;
                if ((isNaN(app.columnCount) == false) && app.columnCount > 0 && app.taxonName != "") {
                    var jsonMatrix = {
                        'user_id': app.user.id,
                        'column_count': app.columnCount,
                        'taxon': app.taxonName
                    };
                    app.oldUserTags = [];
                    axios.post('/chrecorder/public/api/v1/matrix-store', jsonMatrix)
                        .then(function (resp) {
                            app.isLoading = false;
                            console.log('resp storeMatrix', resp.data);
                            app.matrixShowFlag = true;
                            app.collapsedFlag = true;
                            app.userCharacters = resp.data.characters;
                            app.headers = resp.data.headers;
                            app.values = resp.data.values;
                            console.log('app.userTags', app.userTags);
                            axios.get('/chrecorder/public/api/v1/user-tag/' + app.user.id)
                                .then(function (resp) {
                                    app.userTags = resp.data;
                                    app.showTableForTab(app.currentTab);
                                });
                            app.refreshUserCharacters(true);

                            console.log('userCharacters', app.userCharacters);
                        });

                } else {
                    app.isLoading = false;
                    alert("You need to fill the taxon name and specimen count in the input box!")
                }

            },
            deleteHeader(headerId) {
                var app = this;
                app.toRemoveHeaderId = headerId;
                app.toRemoveHeaderConfirmFlag = true;
            },
            confirmRemoveHeader() {
                var app = this;
                // app.isLoading = true;
                axios.post('/chrecorder/public/api/v1/delete-header/' + app.toRemoveHeaderId)
                    .then(function (resp) {
                        console.log('delete header', resp.data);
                        app.headers = resp.data.headers;
                        app.values = resp.data.values;
                        app.userCharacters = resp.data.characters;
                        app.columnCount = resp.data.headers.length - 1;
                        app.isLoading = false;
                        app.toRemoveHeaderConfirmFlag = false;
                        app.refreshUserCharacters();
                        app.showTableForTab(app.currentTab);

                    });
            },
            changeTaxonName() {
                var app = this;
                axios.post('/chrecorder/public/api/v1/change-taxon/' + app.taxonName)
                    .then(function (resp) {
                        app.taxonName = resp.data.taxon;
                    });
            },
            changeColumnCount() {
                var app = this;
                if (app.columnCount < app.headers.length - 1) {
                    app.columnCount = app.headers.length - 1;
                    app.isLoading = false;
                    alert("To reduce the size of the matrix, use the remove button (x) in the matrix.");
                } else if (app.columnCount == app.headers.length -1) {
                }
                else {
                    app.generateMatrix();
                }
            },
            saveItem(event, value) {
                var app = this;
                var currentCharacter = app.userCharacters.find(ch => ch.id == value.character_id);
                if (app.checkHaveUnit(currentCharacter.name)) {
                    axios.post('/chrecorder/public/api/v1/character/update', value)
                        .then(function (resp) {
                            console.log('saveItem', resp.data);
                            if (resp.data.error_input == 1) {
                                event.target.style.color = 'red';
                            } else {
                                event.target.style.color = 'black';
                                app.userCharacters = resp.data.characters;
                                app.headers = resp.data.headers;
                                for (var i=0;i<app.values.length;i++){
                                    for (var j=0;j<app.values[i].length;j++){
                                        if (app.values[i][j].id==value.id){
                                            app.values[i][j]=resp.data.values[i][j];
                                        }
                                    }
                                }
                                //app.values = resp.data.values;
                                app.defaultCharacters = resp.data.defaultCharacters;
                                app.refreshUserCharacters();
                                app.refreshDefaultCharacters();
                                app.showTableForTab(app.currentTab);
                            }
                        });
                }


            },
            removeAllStandardCharacters() {
                var app = this;
                // app.isLoading = true;
                axios.get('/chrecorder/public/api/v1/character/remove-all-standard')
                    .then(function (resp) {
                        app.removeAllStandardFlag = false;
                        app.isLoading = false;
                        app.userCharacters = resp.data.characters;
                        app.headers = resp.data.headers;
                        app.values = resp.data.values;
                        app.userTags = resp.data.tags;
                        if (app.userCharacters.length == 0) {
                            app.matrixShowFlag = false;
                        }
                        console.log('delTags',resp.data.delTags);
                        app.refreshUserCharacters();
                        app.showTableForTab(app.currentTab);
                    });
            },
            getDeprecatedValue () {
                var app = this;
                for (var i = 0; i < app.userTags.length; i ++) {
                    console.log(app.userTags[i].tag_name);
                    app.tagDeprecated[app.userTags[i].tag_name] = app.isDeprecatedExistOnTab(app.userTags[i].tag_name);
                }
            },
            async refreshUserCharacters (showTabFlag = false) {
                var app = this;
                for (var i = 0; i < app.userCharacters.length; i++) {
                    app.userCharacters[i].tooltip = '';
                    if (app.userCharacters[i].method_from != null && app.userCharacters[i].method_from != '') {
                        app.userCharacters[i].tooltip = app.userCharacters[i].tooltip + 'From: ' + app.userCharacters[i].method_from + ', ';
                    }
                    if (app.userCharacters[i].method_to != null && app.userCharacters[i].method_to != '') {
                        app.userCharacters[i].tooltip += 'To: ' + app.userCharacters[i].method_to + ', ';
                    }
                    if (app.userCharacters[i].method_include != null && app.userCharacters[i].method_include != '') {
                        app.userCharacters[i].tooltip += 'Include: ' + app.userCharacters[i].method_include + ', ';
                    }
                    if (app.userCharacters[i].method_exclude != null && app.userCharacters[i].method_exclude != '') {
                        app.userCharacters[i].tooltip += 'Exclude: ' + app.userCharacters[i].method_exclude + ', ';
                    }
                    if (app.userCharacters[i].method_where != null && app.userCharacters[i].method_where != '') {
                        app.userCharacters[i].tooltip += 'Where: ' + app.userCharacters[i].method_where;
                    }
                    var definitionKey = app.userCharacters[i].name.slice(0, app.userCharacters[i].name.indexOf(' '));
                    var definitionVar = app.definitionData.find(eachDefinition => eachDefinition.IRI == ('http://biosemantics.arizona.edu/ontologies/carex#' + (definitionKey == 'Color' ? 'coloration' : definitionKey.toLowerCase())));
                    if (definitionVar) {
                        if (app.userCharacters[i].tooltip != '') {
                            app.userCharacters[i].tooltip += '<br/>';
                        }
                        app.userCharacters[i].tooltip += definitionKey + ' is defined as ' + definitionVar.definition;
                    }
                    app.userCharacters[i].deprecated = app.deprecatedTerms.findIndex(value => value['deprecated IRI'] == app.userCharacters[i].IRI);
                    app.getTooltipImageUserChracter(app.userCharacters[i].name, i);
                }
                for (var i = 0; i < app.userTags.length; i ++) {
                    console.log(app.userTags[i].tag_name);
                    app.tagDeprecated[app.userTags[i].tag_name] = app.isDeprecatedExistOnTab(app.userTags[i].tag_name);
                }
                app.characterUsername = app.user.name;
            },
            tagOrder(tag){
                var app = this;
                for (var i=0;i<app.standardCharactersTags.length;i++){
                    if (app.standardCharactersTags[i] == tag.tag_name){
                        return i;
                    }
                }
                return 10000;
            },
            showTableForTab(tagName) {
                var app = this;
                // app.isLoading = true;
                if (!app.userTags.find(tag => tag.tag_name == tagName)){
                    tagName = app.userTags[0].tag_name;
                }
                app.currentTab = tagName;
                if (app.oldUserTags.length!=app.userTags.length){
                    app.userTags.sort((a,b) => app.tagOrder(a)-app.tagOrder(b));
                    app.oldUserTags = app.userTags;
                }


                for (var i=0;i<app.userCharacters.length;i++){
                    app.userCharacters[i].show_flag = app.userCharacters[i].standard_tag == app.currentTab;
                }
                app.isLoading = false;

            },
            isDeprecatedExistOnTab(tagName) {
                var app = this;
                var isDeprecated = 0;
                console.log("isDeprecatedExist");
                for (var i = 0; i < app.userCharacters.length; i ++) {
                    if (app.userCharacters[i].standard_tag == tagName) {
                        if (app.deprecatedTerms.findIndex(value => value['deprecated IRI'] == app.userCharacters[i].IRI) >= 0) {
                            return 1;
                        }
                        var rows = app.values.find(value => value[0].character_id == app.userCharacters[i].id);
                        if (rows && !app.checkHaveUnit(rows.find(v => v.header_id == 1).value)) {
                            for (var j = 0; j < rows.length; j ++) {
                                if (rows[j].header_id != 1) {
                                    for (var ind = 0; ind < app.allColorValues.length; ind ++) {
                                        if (app.allColorValues[ind].value_id == rows[j].id) {
                                            var colorValue = app.allColorValues[ind];
                                            if (app.deprecatedTerms.findIndex(value => value['deprecated IRI'] == colorValue.brightness_IRI) >= 0) {
                                                return 1;
                                            }
                                            if (app.deprecatedTerms.findIndex(value => value['deprecated IRI'] == colorValue.reflectance_IRI) >= 0) {
                                                return 1;
                                            }
                                            if (app.deprecatedTerms.findIndex(value => value['deprecated IRI'] == colorValue.saturation_IRI) >= 0) {
                                                return 1;
                                            }
                                            if (app.deprecatedTerms.findIndex(value => value['deprecated IRI'] == colorValue.colored_IRI) >= 0) {
                                                return 1;
                                            }
                                            if (app.deprecatedTerms.findIndex(value => value['deprecated IRI'] == colorValue.multi_colored_IRI) >= 0) {
                                                return 1;
                                            }
                                        }
                                    }
                                    for (var ind = 0; ind < app.allNonColorValues.length; ind ++) {
                                        if (app.allNonColorValues[ind].value_id == rows[j].id) {                              
                                            var nonColorValue = app.allNonColorValues[ind];
                                            if (app.deprecatedTerms.findIndex(value => value['deprecated IRI'] == nonColorValue.main_value_IRI) >= 0) {
                                                return 1;
                                            }
                                        }
                                    }

                                }
                            }
                        }                        
                    }
                }
                return 0;
            },
            hideAllCharacter() {
                var app = this;
                for (var i = 0; i < app.userCharacters.length; i++) {
                    app.userCharacters[i].show_flag = false;
                }
            },
            removeRowTable(characterId) {
                var app = this;

                app.toRemoveCharacterId = characterId;
                app.toRemoveStandardConfirmFlag = true;

            },
            changeUnit(characterId, unit) {
                var app = this;

                var jsonPost = {
                    character_id: characterId,
                    unit: unit
                };
                axios.post('/chrecorder/public/api/v1/character/update-unit', jsonPost)
                    .then(function (resp) {
                        app.userCharacters = resp.data.characters;
                        app.headers = resp.data.headers;
                        app.values = resp.data.values;
                        app.refreshUserCharacters();
                        app.showTableForTab(app.currentTab);
                    });
            },
            changeSummary(characterId, summary) {
                var app = this;

                var jsonPost = {
                    character_id: characterId,
                    summary: summary
                };
                axios.post('/chrecorder/public/api/v1/character/update-summary', jsonPost)
                    .then(function (resp) {
                        app.userCharacters = resp.data.characters;
                        app.headers = resp.data.headers;
                        app.values = resp.data.values;
                        app.refreshUserCharacters();
                        app.showTableForTab(app.currentTab);
                    });
            },
            upUserValue(valueId) {
                console.log(valueId);
                var app = this;
                var showedCharacters = app.userCharacters.filter(ch => ch.show_flag == true);
                var index = showedCharacters.indexOf(showedCharacters.find(ch => ch.id == valueId));
                app.swap(index, false, showedCharacters);
            },
            downUserValue(valueId) {
                var app = this;
                var showedCharacters = app.userCharacters.filter(ch => ch.show_flag == true);
                var index = showedCharacters.indexOf(showedCharacters.find(ch => ch.id == valueId));
                app.swap(index, true, showedCharacters);

            },
            swap(valueIndex, directionFlag = true, showedCharacters) {
                var app = this;
                const maxLength = showedCharacters.length;
                var secondIndex;
                if ((directionFlag == true) && (valueIndex < maxLength - 1)) {
                    secondIndex = valueIndex + 1;
                }
                else if ((directionFlag == false) && (valueIndex > 0)) {
                    secondIndex = valueIndex - 1;
                }
                axios.post('/chrecorder/public/api/v1/character/change-order', {
                    firstId: showedCharacters[valueIndex].id,
                    secondId: showedCharacters[secondIndex].id,
                })
                    .then(function (resp) {
                        console.log('resp', resp);
                        app.isLoading = false;
                        app.values = resp.data.values;
                        app.userCharacters = resp.data.characters;
                        console.log('app.userCharacters', app.userCharacters);
                        app.refreshUserCharacters();
                        app.showTableForTab(app.currentTab);
                    });
            },
            async refreshDefaultCharacters() {
                var app = this;
                app.standardCharacters = [];
                console.log("temp.text");
                for (var i = 0; i < app.defaultCharacters.length; i++) {
                    var temp = {};
                    temp.name = app.defaultCharacters[i].name;
                    temp.text = app.defaultCharacters[i].name + ' by ' + app.defaultCharacters[i].username + ' (' + app.defaultCharacters[i].usage_count + ')';
                    temp.value = app.defaultCharacters[i].id;
                    temp.tooltip = '';

                    if (app.defaultCharacters[i].method_from != null && app.defaultCharacters[i].method_from != '') {
                        temp.tooltip = temp.tooltip + 'From: ' + app.defaultCharacters[i].method_from + ', ';
                    }
                    if (app.defaultCharacters[i].method_to != null && app.defaultCharacters[i].method_to != '') {
                        temp.tooltip = temp.tooltip + 'To: ' + app.defaultCharacters[i].method_to + ', ';
                    }
                    if (app.defaultCharacters[i].method_include != null && app.defaultCharacters[i].method_include != '') {
                        temp.tooltip = temp.tooltip + 'Include: ' + app.defaultCharacters[i].method_include + ', ';
                    }
                    if (app.defaultCharacters[i].method_exclude != null && app.defaultCharacters[i].method_exclude != '') {
                        temp.tooltip = temp.tooltip + 'Exclude: ' + app.defaultCharacters[i].method_exclude + ', ';
                    }
                    if (app.defaultCharacters[i].method_where != null && app.defaultCharacters[i].method_where != '') {
                        temp.tooltip = temp.tooltip + 'Where: ' + app.defaultCharacters[i].method_where;
                    }
                    temp.deprecated = app.deprecatedTerms.findIndex(value => value['deprecated IRI'] == app.defaultCharacters[i].IRI);
                    // temp.tooltip = temp.tooltip + '<br/>Image Here</div>';
                    var imageSrc = await app.getTooltipImageString(temp.name);
                    temp.tooltip = imageSrc + temp.tooltip;
                    app.standardCharacters.push(temp);
                }

                // for (var index = 0; index < app.standardCharacters.length; index ++) {
                //     app.getTooltipImage(app.standardCharacters[index].name, index);
                // }
            },
            // expandTable() {
            //     var app = this;
            //     if (app.descriptionFlag != -1) {
            //         app.descriptionFlag--;
            //     }
            // },
            expandDescription() {
                var app = this;
                app.descriptionFlag = !app.descriptionFlag;
                if (app.descriptionFlag == true) {
                    app.updateDescription();
                }
            },
            checkHaveUnit(string) {
                var app = this;

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
            convertPluralWord(word){
                if (singularToPlural[word]){
                    return singularToPlural[word];
                }
                if (singularToPlural[word.toLowerCase()]) {
                    return singularToPlural[word.toLowerCase()].charAt(0).toUpperCase() + singularToPlural[word.toLowerCase()].slice(1);
                }
                return word;
            },
            updateDescription() {
                var app = this;

                app.descriptionText = '';

                for (var i = 0; i < app.userTags.length; i++) {
                    var char_names = [];
                    app.descriptionText += '<b>' + app.userTags[i].tag_name + ': ' + '</b>';
                    var filteredCharacters = app.userCharacters.filter(function (eachCharacter) {
                        return eachCharacter.standard_tag == app.userTags[i].tag_name;
                    });
                    for (var j = 0; j < filteredCharacters.length; j++) {
                        var filteredValues = app.values.filter(function (eachValue) {
                            return eachValue[0].character_id == filteredCharacters[j].id;
                        })[0];
                        var tempValueArray = [];
                        for (var k = 0; k < filteredValues.length; k++) {
                            if (filteredValues[k].header_id != 1) {
                                tempValueArray.push(filteredValues[k].value);
                            }
                        }
                        if (app.checkValueArray(tempValueArray)) {
                            
                            var currentCharacter = app.userCharacters.find(ch => ch.id == filteredValues[0].character_id);
                            if (!currentCharacter.name.split(' of ')[1]){
                                currentCharacter.name = currentCharacter.name.replace(' between ', ' of ');
                            }
                            var char_name = currentCharacter.name.split(' of ')[1].toLowerCase().split(' in ')[0];
                            const temp = char_name;
                            char_name = char_name.charAt(0).toUpperCase() + char_name.slice(1);
                            if (char_name.toLowerCase() != currentCharacter.standard_tag.toLowerCase() && !char_names.find(ch => ch == char_name.toLowerCase())) {
                                char_names.filter(ch => char_name.toLowerCase().includes(ch)).map(ch => {
                                    char_name = char_name.split(' ').filter(sp => !ch.includes(sp.toLowerCase())).join(' ');
                                });
                                char_names.push(temp);
                                var words = char_name.split(' ');
                                words[words.length - 1] = app.convertPluralWord(words[words.length - 1]);
                                char_name = words.join(' ');
                                app.descriptionText += char_name + ' ';
                            }
                            
                            if (app.checkHaveUnit(filteredCharacters[j].name)) {
                                switch (filteredCharacters[j].summary) {
                                    case "range-percentile":
                                        if (filteredCharacters[j].name.startsWith('Distance')) {
                                            app.descriptionText += filteredCharacters[j].name + ' ';
                                        }
                                        var tempRpArray = [];
                                        for (var l = 0; l < tempValueArray.length; l++) {
                                            if (tempValueArray[l] != null && tempValueArray[l] != '' && tempValueArray[l] != undefined) {
                                                tempRpArray.push(tempValueArray[l]);
                                            }
                                        }
                                        
                                        tempRpArray.sort((a, b) => a - b);
                                        var minValue = tempRpArray[0];
                                        var maxValue = tempRpArray[tempRpArray.length - 1];
                                        var range;
                                        if (tempRpArray.length >= 5){
                                            range = '(' + minValue + '-)' + tempRpArray[Math.floor((tempRpArray.length - 1) / 4)] + '-' + tempRpArray[Math.ceil((tempRpArray.length - 1) * 3 / 4)] + '(-' + maxValue + ')';
                                        }
                                        else if (tempRpArray.length == 1 || minValue == maxValue){
                                            range = minValue;
                                        }
                                        else {
                                            range = minValue + '-' + maxValue;
                                        }
                                        // if (tempRpArray.length % 2 == 0) {
                                        //     minPercentileValue = tempRpArray[tempRpArray.length / 2 - 1];
                                        //     maxPercentileValue = tempRpArray[tempRpArray.length / 2];
                                        // } else {
                                        //     if (tempRpArray.length == 1) {
                                        //         minPercentileValue = tempRpArray[0];
                                        //         maxPercentileValue = tempRpArray[0];
                                        //     } else {
                                        //         minPercentileValue = tempRpArray[tempRpArray.length / 2 - 1.5];
                                        //         maxPercentileValue = tempRpArray[tempRpArray.length / 2 + 0.5];
                                        //     }
                                        // }
                                        // if (minValue == maxValue) {
                                        //     app.descriptionText += minValue;
                                        // } else {
                                        //     app.descriptionText += '(' + minValue + '-)' + minPercentileValue + '-' + maxPercentileValue + '(-' + maxValue + ') ';
                                        //     // app.descriptionText += minPercentileValue + '-' + maxPercentileValue;
                                        // }

                                        app.descriptionText += range;

                                        if (filteredCharacters[j].unit) {
                                            app.descriptionText += ' ' + filteredCharacters[j].unit
                                        }

                                        break;
                                    case "median":
                                        var tempMedianArray = [];
                                        for (var l = 0; l < tempValueArray.length; l++) {
                                            if (tempValueArray[l] != null && tempValueArray[l] != '' && tempValueArray[l] != undefined) {
                                                tempMedianArray.push(tempValueArray[l]);
                                            }
                                        }
                                        tempMedianArray.sort((a, b) => a - b);
                                        if (tempMedianArray.length % 2 == 0) {
                                            app.descriptionText += (parseFloat(tempMedianArray[tempMedianArray.length / 2 - 1]) + parseFloat(tempMedianArray[tempMedianArray.length / 2])) / 2;
                                        } else {
                                            app.descriptionText += tempMedianArray[tempMedianArray.length / 2 - 0.5];
                                        }
                                        if (filteredCharacters[j].unit && !filteredCharacters[j].name.startsWith('Number of') && !filteredCharacters[j].name.startsWith('Ratio of')) {
                                            app.descriptionText += filteredCharacters[j].unit
                                        }
                                        break;
                                    case "mean":
                                        var sum = 0;
                                        var arrayLength = 0;
                                        for (var l = 0; l < tempValueArray.length; l++) {
                                            if (tempValueArray[l] != null && tempValueArray[l] != '' && tempValueArray[l] != undefined) {
                                                sum += parseInt(tempValueArray[l], 10); //don't forget to add the base
                                                arrayLength++;
                                            }
                                        }

                                        var avg = parseFloat(sum / arrayLength).toFixed(1);
                                        app.descriptionText += avg;
                                        if (filteredCharacters[j].unit) {
                                            app.descriptionText += ' ' + filteredCharacters[j].unit
                                        }
                                        break;
                                    default:
                                        break;
                                }
                                if (filteredCharacters[j].name.startsWith('Length')) {
                                    app.descriptionText += ' long; ';
                                }
                                else if (filteredCharacters[j].name.startsWith('Width')) {
                                    app.descriptionText += ' wide; ';
                                }
                                else if (filteredCharacters[j].name.startsWith('Height')) {
                                    app.descriptionText += ' tall; ';
                                }
                                else if (filteredCharacters[j].name.startsWith('Diameter')) {
                                    app.descriptionText += ' in diameter; ';
                                }
                                else if (filteredCharacters[j].name.startsWith('Depth')) {
                                    app.descriptionText += ' in depth; ';
                                }
                                else {
                                    app.descriptionText += ' ; ';
                                }
                            } else {
                                if (currentCharacter.name.split(' of ')[0] == 'Color') {
                                    var checkValueIdArray = [];
                                    var isInvariant = true;
                                    var cTmp = '';
                                    
                                    for (var k = 0; k < filteredValues.length; k++) {
                                        if (filteredValues[k].header_id != 1 && filteredValues[k].value != '') {
                                            cTmp = filteredValues[k].value;
                                        }
                                    }
                                    for (var k = 0; k < filteredValues.length; k++) {
                                        if (filteredValues[k].header_id != 1) {
                                            checkValueIdArray.push(filteredValues[k].id);
                                            if (filteredValues[k].value != '' && filteredValues[k].value != cTmp){
                                                isInvariant = false;
                                            }
                                        }
                                    }

                                    var colorValues = app.allColorValues.filter(eachValue => checkValueIdArray.includes(eachValue.value_id));
                                    var objColorValues = {
                                        'empty': []
                                    };
                                    var arraySortedColor = [];
                                    var cloneSortedColor = [];
                                    for (var l = 0; l < colorValues.length; l++) {
                                        if (colorValues[l].post_constraint != null && colorValues[l].post_constraint != '') {
                                            if (!(colorValues[l].post_constraint in objColorValues)) {
                                                objColorValues[colorValues[l].post_constraint] = [];
                                            }
                                            var jsonColorValue = {
                                                colored: colorValues[l].colored,
                                                brightness: colorValues[l].brightness,
                                                saturation: colorValues[l].saturation,
                                                count: 0,
                                                value: '',
                                                multi_colored: colorValues[l].multi_colored
                                            };
                                            if (colorValues[l].negation != null && colorValues[l].negation != '') {
                                                jsonColorValue.value = colorValues[l].negation + ' ';
                                            }
                                            if (colorValues[l].pre_constraint != null && colorValues[l].pre_constraint != '') {
                                                jsonColorValue.value += colorValues[l].pre_constraint + ' ';
                                            }
                                            if (colorValues[l].brightness != null && colorValues[l].brightness != '') {
                                                if (colorValues[l].brightness.endsWith(')')){
                                                    colorValues[l].brightness = colorValues[l].brightness.slice(0, colorValues[l].brightness.indexOf('('));
                                                }
                                                jsonColorValue.value += colorValues[l].brightness + ' ';
                                            }
                                            if (colorValues[l].reflectance != null && colorValues[l].reflectance != '') {
                                                if (colorValues[l].reflectance.endsWith(')')){
                                                    colorValues[l].reflectance = colorValues[l].reflectance.slice(0, colorValues[l].reflectance.indexOf('('));
                                                }
                                                jsonColorValue.value += colorValues[l].reflectance + ' ';
                                            }
                                            if (colorValues[l].saturation != null && colorValues[l].saturation != '') {
                                                if (colorValues[l].saturation.endsWith(')')){
                                                    colorValues[l].saturation = colorValues[l].saturation.slice(0, colorValues[l].saturation.indexOf('('));
                                                }
                                                jsonColorValue.value += colorValues[l].saturation + ' ';
                                            }
                                            if (colorValues[l].colored != null && colorValues[l].colored != '') {
                                                if (colorValues[l].colored.endsWith(')')){
                                                    colorValues[l].colored = colorValues[l].colored.slice(0, colorValues[l].colored.indexOf('('));
                                                }
                                                jsonColorValue.value += colorValues[l].colored;
                                            }
                                            if (colorValues[l].multi_colored != null && colorValues[l].multi_colored != '') {
                                                if (colorValues[l].multi_colored.endsWith(')')){
                                                    colorValues[l].multi_colored = colorValues[l].multi_colored.slice(0, colorValues[l].multi_colored.indexOf('('));
                                                }
                                                jsonColorValue.value += ' ' + colorValues[l].multi_colored;
                                            }

                                            objColorValues[colorValues[l].post_constraint].push(jsonColorValue);
                                        } else {
                                            var jsonColorValue = {
                                                colored: colorValues[l].colored,
                                                brightness: colorValues[l].brightness,
                                                saturation: colorValues[l].saturation,
                                                count: 0,
                                                value: '',
                                                multi_colored: colorValues[l].multi_colored
                                            };
                                            if (colorValues[l].negation != null && colorValues[l].negation != '') {
                                                jsonColorValue.value = colorValues[l].negation + ' ';
                                            }
                                            if (colorValues[l].pre_constraint != null && colorValues[l].pre_constraint != '') {
                                                jsonColorValue.value += colorValues[l].pre_constraint + ' ';
                                            }
                                            if (colorValues[l].brightness != null && colorValues[l].brightness != '') {
                                                if (colorValues[l].brightness.endsWith(')')){
                                                    colorValues[l].brightness = colorValues[l].brightness.slice(0, colorValues[l].brightness.indexOf('('));
                                                }
                                                jsonColorValue.value += colorValues[l].brightness + ' ';
                                            }
                                            if (colorValues[l].reflectance != null && colorValues[l].reflectance != '') {
                                                if (colorValues[l].reflectance.endsWith(')')){
                                                    colorValues[l].reflectance = colorValues[l].reflectance.slice(0, colorValues[l].reflectance.indexOf('('));
                                                }
                                                jsonColorValue.value += colorValues[l].reflectance + ' ';
                                            }
                                            if (colorValues[l].saturation != null && colorValues[l].saturation != '') {
                                                if (colorValues[l].saturation.endsWith(')')){
                                                    colorValues[l].saturation = colorValues[l].saturation.slice(0, colorValues[l].saturation.indexOf('('));
                                                }
                                                jsonColorValue.value += colorValues[l].saturation + ' ';
                                            }
                                            if (colorValues[l].colored != null && colorValues[l].colored != '') {
                                                if (colorValues[l].colored.endsWith(')')){
                                                    colorValues[l].colored = colorValues[l].colored.slice(0, colorValues[l].colored.indexOf('('));
                                                }
                                                jsonColorValue.value += colorValues[l].colored;
                                            }
                                            if (colorValues[l].multi_colored != null && colorValues[l].multi_colored != '') {
                                                if (colorValues[l].multi_colored.endsWith(')')){
                                                    colorValues[l].multi_colored = colorValues[l].multi_colored.slice(0, colorValues[l].multi_colored.indexOf('('));
                                                }
                                                jsonColorValue.value += ' ' + colorValues[l].multi_colored;
                                            }

                                            objColorValues['empty'].push(jsonColorValue);
                                        }
                                    }

                                    for (var objKey in objColorValues) {
                                        for (var l = 0; l < objColorValues[objKey].length; l++) {
                                            objColorValues[objKey][l].count = objColorValues[objKey].filter(function (each) {
                                                if (objColorValues[objKey][l].multi_colored != null && objColorValues[objKey][l].multi_colored != '') {
                                                    return each.value.endsWith(objColorValues[objKey][l].value);
                                                } else {
                                                    if (each.multi_colored != null && each.multi_colored != '') {
                                                        return each.value.substring(0, each.value.length - (each.multi_colored.length + 1)).endsWith(objColorValues[objKey][l].value);
                                                    } else {
                                                        return each.value.endsWith(objColorValues[objKey][l].value);
                                                    }
                                                }
                                            }).length;
                                        }
                                        objColorValues[objKey] = app.sortColorValue(objColorValues[objKey]);

                                        while (objColorValues[objKey].length > 0) {
                                            arraySortedColor.push([]);
                                            objColorValues[objKey][0].objKey = objKey;
                                            arraySortedColor[arraySortedColor.length - 1].push(objColorValues[objKey][0]);
                                            var matchColor = objColorValues[objKey][0];
                                            objColorValues[objKey].shift();
                                            var index = 0;
                                            for (var m = 0; m < (objColorValues[objKey].length + index); m++) {
                                                if (app.checkAllowRange(matchColor, objColorValues[objKey][m - index])) {
                                                    objColorValues[objKey][m - index].objKey = objKey;
                                                    arraySortedColor[arraySortedColor.length - 1].push(objColorValues[objKey][m - index]);
                                                    objColorValues[objKey].splice(m - index, 1);
                                                    index++;
                                                }
                                            }
                                        }

                                        console.log('arraySortedColor', arraySortedColor);
                                        cloneSortedColor = [];
                                        for (var l = 0; l < arraySortedColor.length; l++) {
                                            cloneSortedColor[l] = arraySortedColor[l];
                                            for (var m = 0; m < arraySortedColor[l].length; m++) {
                                                var tempArray = arraySortedColor[l].filter(function (each) {
                                                    if (arraySortedColor[l][m].multi_colored != null && arraySortedColor[l][m].multi_colored != '') {
                                                        return (each.value.endsWith(arraySortedColor[l][m].value)) && (each.value != arraySortedColor[l][m].value);
                                                    } else {
                                                        if (each.multi_colored != null && each.multi_colored != '') {
                                                            return each.value.substring(0, each.value.length - (each.multi_colored.length + 1)).endsWith(arraySortedColor[l][m].value);
                                                        } else {
                                                            return (each.value.endsWith(arraySortedColor[l][m].value)) && (each.value != arraySortedColor[l][m].value);
                                                        }
                                                    }
                                                });
                                                cloneSortedColor[l] = cloneSortedColor[l].filter(function (el) {
                                                    return !tempArray.includes(el);
                                                });
                                            }
                                        }
                                        console.log('cloneSortedColor', cloneSortedColor);


                                    }
                                    var tempIndex = 0;
                                    console.log('arraySortedColor', arraySortedColor);
                                    console.log('cloneSortedColor', cloneSortedColor);
                                    for (var objKey in objColorValues) {
                                        var tempArraySorted = arraySortedColor.filter(each => each[0].objKey == objKey);
                                        var countArray = cloneSortedColor.filter(each => each[0].objKey == objKey);
                                        for (var l = 0; l < countArray.length; l++) {
                                            var eachCount = 0;
                                            for (var m = 0; m < countArray[l].length; m++) {
                                                eachCount += countArray[l][m].count;
                                            }
                                            for (var m = 0; m < tempArraySorted.length; m++) {
                                                if (tempArraySorted[m].includes(countArray[l][0])) {
                                                    tempArraySorted[m].eachCount = eachCount;
                                                }
                                            }
                                        }
                                        console.log('countArray', countArray);
                                        console.log('tempArraySorted', tempArraySorted);
                                        tempArraySorted.sort((a, b) => a.eachCount > b.eachCount ? -1 : 1);
                                        var objByPercentage = {};

                                        for (var l = 0; l < tempArraySorted.length; l++) {

                                            var tempProperty = app.getPercentageForDescription(colorValues.length, tempArraySorted[l].eachCount);
                                            console.log('tempProperty', tempProperty);

                                            if (tempArraySorted[l].length > 1) {

                                                if (!objByPercentage[tempProperty]) {
                                                    objByPercentage[tempProperty] = [];
                                                }
                                                objByPercentage[tempProperty].push(tempArraySorted[l][0].value + ' to ' + tempArraySorted[l][1].value);
                                                if (tempArraySorted[l].length > 2) {
                                                    for (var m = 2; m < tempArraySorted[l].length; m++) {
                                                        objByPercentage[tempProperty][objByPercentage[tempProperty].length - 1] += ' to ' + tempArraySorted[l][m].value;
                                                    }
                                                }
                                            }
                                            else if (tempArraySorted[l].length == 1) {
                                                if (!objByPercentage[tempProperty]) {
                                                    objByPercentage[tempProperty] = [];
                                                }
                                                objByPercentage[tempProperty].push(tempArraySorted[l][0].value);
                                            }
                                        }
                                        console.log('objByPercentage', objByPercentage);

                                        for (var [objIndex, [key, value]] of Object.entries(Object.entries(objByPercentage))) {
                                            if (objIndex > 0) {
                                                app.descriptionText += ', ';
                                            }
                                            if (!isInvariant)
                                                app.descriptionText += key;
                                            for (var percentageIndex = 0; percentageIndex < objByPercentage[key].length; percentageIndex++) {
                                                if (percentageIndex > 0) {
                                                    app.descriptionText += ' or';
                                                }
                                                app.descriptionText += ' ' + objByPercentage[key][percentageIndex];
                                            }
                                        }

                                        if (objKey != 'empty') {
                                            app.descriptionText += ' ' + objKey;
                                        }
                                        tempIndex++;

                                    }

                                    app.descriptionText += '; ';
                                }
                                else {
                                    app.nonColorType = currentCharacter.name.split(' of ')[0].toLowerCase();
                                    var checkValueIdArray = [];
                                    var isInvariant = true;
                                    var cTmp = '';
                                    
                                    for (var k = 0; k < filteredValues.length; k++) {
                                        if (filteredValues[k].header_id != 1 && filteredValues[k].value != '') {
                                            cTmp = filteredValues[k].value;
                                        }
                                    }
                                    for (var k = 0; k < filteredValues.length; k++) {
                                        if (filteredValues[k].header_id != 1) {
                                            checkValueIdArray.push(filteredValues[k].id);
                                            if (filteredValues[k].value != '' && filteredValues[k].value != cTmp){
                                                isInvariant = false;
                                            }
                                        }
                                    }

                                    var nonColorValues = app.allNonColorValues.filter(eachValue => checkValueIdArray.includes(eachValue.value_id));
                                    var objNonColorValues = {
                                        'empty': []
                                    };
                                    var arraySortedNonColor = [];
                                    var cloneSortedNonColor = [];
                                    for (var l = 0; l < nonColorValues.length; l++) {
                                        if (nonColorValues[l].post_constraint != null && nonColorValues[l].post_constraint != '') {
                                            if (!(nonColorValues[l].post_constraint in objNonColorValues)) {
                                                objNonColorValues[nonColorValues[l].post_constraint] = [];
                                            }
                                            var jsonNonColorValue = {
                                                main_value: nonColorValues[l].main_value,
                                                count: 0,
                                                value: '',
                                            };
                                            if (nonColorValues[l].negation != null && nonColorValues[l].negation != '') {
                                                jsonNonColorValue.value = nonColorValues[l].negation + ' ';
                                            }
                                            if (nonColorValues[l].pre_constraint != null && nonColorValues[l].pre_constraint != '') {
                                                jsonNonColorValue.value += nonColorValues[l].pre_constraint + ' ';
                                            }
                                            if (nonColorValues[l].degree_constraint != null && nonColorValues[l].degree_constraint != '') {
                                                jsonNonColorValue.value += nonColorValues[l].degree_constraint + ' ';
                                            }

                                            if (nonColorValues[l].main_value != null && nonColorValues[l].main_value != '') {
                                                if (nonColorValues[l].main_value.endsWith(')')){
                                                    nonColorValues[l].main_value = nonColorValues[l].main_value.slice(0, nonColorValues[l].main_value.indexOf('('));
                                                }
                                                jsonNonColorValue.value += nonColorValues[l].main_value + ' ';
                                            }

                                            objNonColorValues[nonColorValues[l].post_constraint].push(jsonNonColorValue);
                                        } else {
                                            var jsonNonColorValue = {
                                                main_value: nonColorValues[l].main_value,
                                                count: 0,
                                                value: '',
                                            };
                                            if (nonColorValues[l].negation != null && nonColorValues[l].negation != '') {
                                                jsonNonColorValue.value = nonColorValues[l].negation + ' ';
                                            }
                                            if (nonColorValues[l].pre_constraint != null && nonColorValues[l].pre_constraint != '') {
                                                jsonNonColorValue.value += nonColorValues[l].pre_constraint + ' ';
                                            }
                                            if (nonColorValues[l].degree_constraint != null && nonColorValues[l].degree_constraint != '') {
                                                jsonNonColorValue.value += nonColorValues[l].degree_constraint + ' ';
                                            }

                                            if (nonColorValues[l].main_value != null && nonColorValues[l].main_value != '') {
                                                if (nonColorValues[l].main_value.endsWith(')')){
                                                    nonColorValues[l].main_value = nonColorValues[l].main_value.slice(0, nonColorValues[l].main_value.indexOf('('));
                                                }
                                                jsonNonColorValue.value += nonColorValues[l].main_value + ' ';
                                            }

                                            objNonColorValues['empty'].push(jsonNonColorValue);
                                        }
                                    }
                                    for (var objKey in objNonColorValues) {
                                        for (var l = 0; l < objNonColorValues[objKey].length; l++) {
                                            objNonColorValues[objKey][l].count = objNonColorValues[objKey].filter(function (each) {
                                                return each.value.endsWith(objNonColorValues[objKey][l].value);
                                            }).length;
                                        }
                                        console.log('objNonColorValues ',objNonColorValues[objKey]);
                                        for (var l = 0; l < objNonColorValues[objKey].length; l++) {
                                            if (objNonColorValues[objKey][l].count > 1) {
                                                var tempArray = objNonColorValues[objKey].filter(function (each) {
                                                    return each.value.endsWith(objNonColorValues[objKey][l].value) && each.value != objNonColorValues[objKey][l].value;
                                                });
                                                objNonColorValues[objKey] = objNonColorValues[objKey].filter(function (el) {
                                                    return !tempArray.includes(el);
                                                });
                                            }
                                        }
                                        objNonColorValues[objKey] = app.sortNonColorValue(objNonColorValues[objKey]);
                                        while (objNonColorValues[objKey].length > 0) {
                                            arraySortedNonColor.push([]);
                                            objNonColorValues[objKey][0].objKey = objKey;
                                            arraySortedNonColor[arraySortedNonColor.length - 1].push(objNonColorValues[objKey][0]);
                                            var matchValue = objNonColorValues[objKey][0];
                                            objNonColorValues[objKey].shift();
                                            var index = 0;
                                            for (var m = 0; m < (objNonColorValues[objKey].length + index); m++) {
                                                if (app.checkNonColorAllowRange(matchValue, objNonColorValues[objKey][m - index])) {
                                                    objNonColorValues[objKey][m - index].objKey = objKey;
                                                    arraySortedNonColor[arraySortedNonColor.length - 1].push(objNonColorValues[objKey][m - index]);
                                                    objNonColorValues[objKey].splice(m - index, 1);
                                                    index++;
                                                }
                                            }
                                        }

                                        for (var l = 0; l < arraySortedNonColor.length; l++) {
                                            cloneSortedNonColor[l] = arraySortedNonColor[l];
                                            for (var m = 0; m < arraySortedNonColor[l].length; m++) {
                                                var tempArray = arraySortedNonColor[l].filter(function (each) {
                                                    return each.value.endsWith(arraySortedNonColor[l][m].value) && each.value != arraySortedNonColor[l][m].value;
                                                });
                                                cloneSortedNonColor[l] = cloneSortedNonColor[l].filter(function (el) {
                                                    return !tempArray.includes(el);
                                                });
                                            }
                                        }

                                    }
                                    var tempIndex = 0;
                                    for (var objKey in objNonColorValues) {
                                        var tempArraySorted = arraySortedNonColor.filter(each => each[0].objKey == objKey);
                                        var countArray = cloneSortedNonColor.filter(each => each[0].objKey == objKey);
                                        for (var l = 0; l < countArray.length; l++) {
                                            var eachCount = 0;
                                            for (var m = 0; m < countArray[l].length; m++) {
                                                eachCount += countArray[l][m].count;
                                            }
                                            for (var m = 0; m < tempArraySorted.length; m++) {
                                                if (tempArraySorted[m].includes(countArray[l][0])) {
                                                    tempArraySorted[m].eachCount = eachCount;
                                                }
                                            }
                                        }
                                        console.log('countArray', countArray);
                                        console.log('tempArraySorted', tempArraySorted);
                                        tempArraySorted.sort((a, b) => a.eachCount > b.eachCount ? -1 : 1);

                                        var objByPercentage = {};

                                        for (var l = 0; l < tempArraySorted.length; l++) {


                                            var tempProperty = app.getPercentageForDescription(nonColorValues.length, tempArraySorted[l].eachCount);
                                            if (!objByPercentage[tempProperty]) {
                                                objByPercentage[tempProperty] = [];
                                            }


                                            if (tempArraySorted[l].length > 1) {
                                                objByPercentage[tempProperty].push(tempArraySorted[l][0].value + ' to ' + tempArraySorted[l][1].value);
                                                if (tempArraySorted[l].length > 2) {
                                                    for (var m = 2; m < tempArraySorted[l].length; m++) {
                                                        objByPercentage[tempProperty][objByPercentage[tempProperty].length - 1] += ' to ' + tempArraySorted[l][m].value;
                                                    }
                                                }


                                            } else {
                                                objByPercentage[tempProperty].push(tempArraySorted[l][0].value);
                                            }
                                        }

                                        for (var [objIndex, [key, value]] of Object.entries(Object.entries(objByPercentage))) {
                                            if (objIndex > 0) {
                                                app.descriptionText += ', ';
                                            }

                                            if (!isInvariant)
                                                app.descriptionText += key;
                                            for (var percentageIndex = 0; percentageIndex < objByPercentage[key].length; percentageIndex++) {
                                                if (percentageIndex > 0) {
                                                    app.descriptionText += ' or';
                                                }
                                                app.descriptionText += ' ' + objByPercentage[key][percentageIndex];
                                            }
                                        }

                                        if (objKey != 'empty') {
                                            app.descriptionText += ' ' + objKey;
                                        }
                                        tempIndex++;

                                    }

                                    app.descriptionText += '; ';
                                }
                            }
                        }
                    }
                    if (app.descriptionText.slice(-2) == '; ') {
                        app.descriptionText = app.descriptionText.substring(0, app.descriptionText.length - 2);
                    }
                    app.descriptionText += '. ';
                    app.descriptionText += '<br/>';

                }

            },
            //get any percentile from an array
            getPercentile(data, percentile) {
                var index = (percentile/100) * data.length;
                var result;
                if (Math.floor(index) == index) {
                    result = (data[(index-1)] + data[index])/2;
                }
                else {
                    result = data[Math.floor(index)];
                }
                return result;
            },
            sortColorValue(arrayColorValues) {
                var app = this;

                arrayColorValues.sort((a, b) => (!app.checkAllowRange(a, b) && a.colored > b.colored) ? 1 : -1);
                
                arrayColorValues.sort(function (x, y) {
                    return x.colored == 'white' ? -1 : y.colored == 'white' ? 1 : 0;
                });
                arrayColorValues.sort(function (x, y) {
                    return x.colored == 'black' ? 1 : y.colored == 'black' ? -1 : 0;
                });

                var obj = {};

                for (var i = 0; i < arrayColorValues.length; i++)
                    obj[arrayColorValues[i]['value']] = arrayColorValues[i];

                var returnArray = [];
                for (var key in obj)
                    returnArray.push(obj[key]);
                return returnArray;
            },
            sortNonColorValue(arrayNonColorValue) {
                var app = this;

                arrayNonColorValue.sort((a, b) => (!app.checkNonColorAllowRange(a, b) && a.value > b.value) ? 1 : -1)
                var obj = {};

                for (var i = 0; i < arrayNonColorValue.length; i++)
                    obj[arrayNonColorValue[i]['value']] = arrayNonColorValue[i];

                var returnArray = [];
                for (var key in obj)
                    returnArray.push(obj[key]);

                return returnArray;
            },
            getPercentageForDescription(totalCount, eachCount) {
                if (totalCount == 1) {
                    return '';
                } else {
                    var percentage = eachCount / totalCount * 100;
                    if (percentage < 25) {
                        return 'rarely';
                    } else if (percentage >= 25 && percentage < 50) {
                        return 'sometimes';
                    } else if (percentage >= 50 && percentage < 75) {
                        return 'usually';
                    } else if (percentage >= 75 && percentage < 100) {
                        return 'frequently';
                    }
                    else {
                        return '';
                    }
                }
            },
            checkNonColorAllowRange(firstValue, secondValue) {
                var app = this;
                var returnFlag = false;

                var firstMatchValues = app.getNonPrimaryColor(firstValue.main_value, app.nonColorType);
                var secondMatchValues = app.getNonPrimaryColor(secondValue.main_value, app.nonColorType);

                for (let i = 0 ; i < firstMatchValues.length ; i ++){
                    for (let j = 0 ; j < secondMatchValues.length ; j ++){
                        if (app.nonColorType == 'shape'){
                            if (Shapesets[firstMatchValues[i]] && Shapesets[firstMatchValues[i]].has(secondMatchValues[j])){
                                return true;
                            }
                        }
                        else if (firstMatchValues[i] == secondMatchValues[j]){
                            return true;
                        }
                    }
                }

                return false;

            },
            checkAllowRange(firstColor, secondColor) {
                var app = this;
                var returnFlag = false;

                var firstMatchColors = app.getPrimaryColor(firstColor.colored);
                var secondMatchColors = app.getPrimaryColor(secondColor.colored);

                for (let i = 0 ; i < firstMatchColors.length ; i ++){
                    for (let j = 0 ; j < secondMatchColors.length ; j ++){
                        if (Colorsets[firstMatchColors[i]] && Colorsets[firstMatchColors[i]].has(secondMatchColors[j])){
                            return true;
                        }
                    }
                }

                return false;

            },
            async exportDescription() {
                var app = this;
                await axios.post('/chrecorder/public/api/v1/export-description', {template: app.descriptionText, taxon: app.taxonName})
                    .then(function (resp) {
                        console.log('resp', resp.data);
                        if (resp.data.is_success == 1) {
                            window.location.href = resp.data.doc_url;
                        } else {
                            alert('Error occurred while exporting data!');
                        }
                    });
                axios.post('/chrecorder/public/api/v1/export-description-csv',
                    {
                        userCharacters: app.userCharacters,
                        values: app.values,
                        userTags: app.userTags,
                        headers: app.headers,
                        taxon: app.taxonName
                    })
                    .then(function(resp) {
                        if (resp.data.is_success == 1) {
                            window.location.href = resp.data.doc_url;
                        } else {
                            alert('Error occurred while exporting csv file!');
                        }
                });
                axios.post('/chrecorder/public/api/v1/export-description-trig')
                .then(function(resp) {
                    console.log(resp.data);
                    if (resp.data.is_success == 1) {
                        let a = document.createElement('a');
                        a.href = resp.data.doc_url;
                        a.download = resp.data.doc_url.split('/')[resp.data.doc_url.split('/').length - 1];
                        a.click();
                    } else {
                        alert('Error occurred while exporting trig file!');
                    }
                });
            },
            checkValueArray(tempArray) {
                var app = this;
                var returnFlag = false;
                for (var i = 0; i < tempArray.length; i++) {
                    if (tempArray[i] != '' && tempArray[i] != null && tempArray[i] != ' ' && tempArray[i] != undefined) {
                        returnFlag = true;
                    }
                }
                return returnFlag;
            },
            changeFlagToLabel(flag) {
                var returnText = flag;
                switch (flag) {
                    case 'colored':
                        returnText = 'color';
                        break;
                    case 'multi_colored':
                        returnText = 'pattern';
                        break;
                    default:
                        break;
                }
                return returnText;
            },
            saveHeader(header) {
                var app = this;
                console.log('header', header.header);
                axios.post('/chrecorder/public/api/v1/update-header', header)
                    .then(function (resp) {
                        app.headers = resp.data.headers;
                        app.values = resp.data.values;
                        app.getDeprecatedValue();
                        showTableForTab(app.currentTab);
                    });
            },
            async saveColorValue(newFlag = false) {
                var app = this;
                if (app.saveColorButtonFlag){
                    return;
                }

                app.currentTermForBracket = '';
                if (app.currentColorValue['brightness'] != undefined && (app.currentColorValue['brightness'].indexOf('(') > -1 || app.currentColorValue['brightness'].indexOf(')') > -1)) {
                    app.currentTermForBracket = app.currentColorValue['brightness'];
                }
                if (app.currentColorValue['saturation'] != undefined && (app.currentColorValue['saturation'].indexOf('(') > -1 || app.currentColorValue['saturation'].indexOf(')') > -1)) {
                    if (app.currentTermForBracket != '') {
                        app.currentTermForBracket +=', ';
                    }
                    app.currentTermForBracket += app.currentColorValue['saturation'];
                }
                if (app.currentColorValue['reflectance'] != undefined && (app.currentColorValue['reflectance'].indexOf('(') > -1 || app.currentColorValue['reflectance'].indexOf(')') > -1)) {
                    if (app.currentTermForBracket != '') {
                        app.currentTermForBracket +=', ';
                    }
                    app.currentTermForBracket += app.currentColorValue['reflectance'];
                }
                if (app.currentColorValue['colored'] != undefined && (app.currentColorValue['colored'].indexOf('(') > -1 || app.currentColorValue['colored'].indexOf(')') > -1)) {
                    if (app.currentTermForBracket != '') {
                        app.currentTermForBracket +=', ';
                    }
                    app.currentTermForBracket += app.currentColorValue['colored'];
                }
                if (app.currentColorValue['multi_colored'] != undefined && (app.currentColorValue['multi_colored'].indexOf('(') > -1 || app.currentColorValue['multi_colored'].indexOf(')') > -1)) {
                    if (app.currentTermForBracket != '') {
                        app.currentTermForBracket +=', ';
                    }
                    app.currentTermForBracket += app.currentColorValue['multi_colored'];
                }
                console.log(app.currentTermForBracket);
                if (app.currentTermForBracket != '') {
                    app.toRemoveBracketConfirmFlag = true;
                    return;
                }

                var postFlag = true;
                var comparedFlag = true;
                app.saveColorButtonFlag = true;
                console.log('currentColorValue', app.currentColorValue);
                console.log('app.currentColorValue', app.currentColorValue);
                
                app.originColorValue = app.currentColorValue;

                if (app.currentColorValue['brightness'] && app.currentColorValue.confirmedFlag['brightness'] == false && !app.colorSynonyms['brightness'] && !app.searchTreeData(app.colTreeData['brightness'], app.currentColorValue['brightness'])) {
                    comparedFlag = false;
                    app.saveColorButtonFlag = false;
                    app.searchColorSelection(app.currentColorValue, 'brightness');
                }
                if (app.currentColorValue['saturation'] && app.currentColorValue.confirmedFlag['saturation'] == false && !app.colorSynonyms['saturation'] && !app.searchTreeData(app.colTreeData['saturation'], app.currentColorValue['saturation'])) {
                    comparedFlag = false;
                    app.saveColorButtonFlag = false;
                    app.searchColorSelection(app.currentColorValue, 'saturation');
                }
                if (app.currentColorValue['reflectance'] && app.currentColorValue.confirmedFlag['reflectance'] == false && !app.colorSynonyms['reflectance'] && !app.searchTreeData(app.colTreeData['reflectance'], app.currentColorValue['reflectance'])) {
                    comparedFlag = false;
                    app.saveColorButtonFlag = false;
                    app.searchColorSelection(app.currentColorValue, 'reflectance');
                }
                if (app.currentColorValue['colored'] && app.currentColorValue.confirmedFlag['colored'] == false && !app.colorSynonyms['colored'] && !app.searchTreeData(app.colTreeData['colored'], app.currentColorValue['colored'])) {
                    comparedFlag = false;
                    app.saveColorButtonFlag = false;
                    app.searchColorSelection(app.currentColorValue, 'colored');
                }
                if (app.currentColorValue['multi_colored'] && app.currentColorValue.confirmedFlag['multi_colored'] == false && !app.colorSynonyms['multi_colored'] && !app.searchTreeData(app.colTreeData['multi_colored'], app.currentColorValue['multi_colored'])) {
                    comparedFlag = false;
                    app.saveColorButtonFlag = false;
                    app.searchColorSelection(app.currentColorValue, 'multi_colored');
                }

                if (!comparedFlag) {
                    app.colorExistFlag = false;
                }
                else {
                    if ((app.currentColorValue.colored == undefined || app.currentColorValue.colored == 'undefined' || app.currentColorValue.colored == null || app.currentColorValue.colored == '')
                        && (app.currentColorValue.multi_colored == undefined || app.currentColorValue.multi_colored == 'undefined' || app.currentColorValue.multi_colored == null || app.currentColorValue.multi_colored == '')
                        && (app.currentColorValue.negation == undefined || app.currentColorValue.negation == 'undefined' || app.currentColorValue.negation == null || app.currentColorValue.negation == '')
                        && (app.currentColorValue.post_constraint == undefined || app.currentColorValue.post_constraint == 'undefined' || app.currentColorValue.post_constraint == null || app.currentColorValue.post_constraint == '')
                        && (app.currentColorValue.pre_constraint == undefined || app.currentColorValue.pre_constraint == 'undefined' || app.currentColorValue.pre_constraint == null || app.currentColorValue.pre_constraint == '')
                        && (app.currentColorValue.reflectance == undefined || app.currentColorValue.reflectance == 'undefined' || app.currentColorValue.reflectance == null || app.currentColorValue.reflectance == '')
                        && (app.currentColorValue.certainty_constraint == undefined || app.currentColorValue.certainty_constraint == 'undefined' || app.currentColorValue.certainty_constraint == null || app.currentColorValue.certainty_constraint == '')
                        && (app.currentColorValue.brightness == undefined || app.currentColorValue.brightness == 'undefined' || app.currentColorValue.brightness == null || app.currentColorValue.brightness == '')
                        && (app.currentColorValue.degree_constraint == undefined || app.currentColorValue.degree_constraint == 'undefined' || app.currentColorValue.degree_constraint == null || app.currentColorValue.degree_constraint == '')
                        && (app.currentColorValue.saturation == undefined || app.currentColorValue.saturation == 'undefined' || app.currentColorValue.saturation == null || app.currentColorValue.saturation == '')) {
                        axios.get('/chrecorder/public/api/v1/get-color-details/' + app.currentColorValue.value_id)
                            .then(function (resp) {
                                app.colorDetails = resp.data.colorDetails;
                                app.values = resp.data.values;
                                app.getDeprecatedValue();
                                app.colorDetailsFlag = false;
                                app.saveColorButtonFlag = false;

                            });
                    } else {
                        var postValue = {};
                        var requestBody = {};
                        postValue['value_id'] = app.currentColorValue['value_id'];
                        if (app.currentColorValue.id) {
                            postValue['id'] = app.currentColorValue.id;
                        }
                        for (var key in app.currentColorValue) {
                            if (app.checkColorProperty(key)) {
                                postValue[key] = app.currentColorValue[key];
                            }
                        }
                        for (var i=0;i<app.colorFlags.length;i++){
                            const flag=app.colorFlags[i];
                            if ( !app.colTreeData[flag] || !app.searchTreeData(app.colTreeData[flag],app.currentColorValue[flag]) ){
                                if (app.currentColorValue[flag] == app.defaultColorValue[flag] && app.currentColorValue[flag] != '' && app.currentColorValue[flag] != undefined && app.currentColorValue[flag] != null){
                                    if (app.colorSynonyms[flag]){
                                        if (!app.userColorDefinition[flag] || app.userColorDefinition[flag]==''){
                                            alert('pease enter definition of '+flag);
                                            app.saveColorButtonFlag = false;
                                            return;
                                        }
                                        if ( app.colorSampleText[flag] =='' || !app.colorSampleText[flag] ){
                                            alert('pease enter sample sentence of '+flag);
                                            app.saveColorButtonFlag = false;
                                            return;
                                        }
                                    }
                                    var date = new Date();
                                    console.log('flag',flag);
                                    requestBody = {
                                        "user": app.sharedFlag ? '' : app.user.name,
                                        "ontology": "carex",
                                        "term": postValue[flag],
                                        "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#toreview",
                                        "definition": app.userColorDefinition[flag],
                                        "elucidation": "",
                                        "createdBy": app.user.name,
                                        "creationDate": ("0" + date.getMonth()).slice(-2) + '-' + ("0" + date.getDate()).slice(-2) + '-' + date.getFullYear(),
                                        "definitionSrc": app.user.name,
                                        "examples": app.colorSampleText[flag] + ", used in taxon " + app.colorTaxon[flag],
                                        "logicDefinition": "",
                                    };
                                    console.log(requestBody);
                                    axios.post('http://shark.sbs.arizona.edu:8080/class', requestBody)
                                        .then(function (resp) {
                                            console.log('shark api class resp', resp);
                                            axios.post('http://shark.sbs.arizona.edu:8080/save', {
                                                user: app.sharedFlag ? '' : app.user.name,
                                                ontology: 'carex'
                                            })
                                                .then(function (resp) {
                                                    console.log('save api resp', resp);
                                                });
                                        });
                                }
                                else if (app.colorSynonyms[flag]){
                                    var synonym=app.colorSynonyms[flag].find( eachSynonym => eachSynonym.term == app.currentColorValue[flag]);
                                    var date = new Date();
                                    requestBody = {
                                        "user": app.sharedFlag ? '' : app.user.name,
                                        "ontology": "carex",
                                        "term": postValue['main_value'],
                                        "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#toreview",
                                        "definition": synonym.definition,
                                        "elucidation": "",
                                        "createdBy": app.user.name,
                                        "creationDate": ("0" + date.getMonth()).slice(-2) + '-' + ("0" + date.getDate()).slice(-2) + '-' + date.getFullYear(),
                                        "definitionSrc": app.user.name,
                                        "examples": app.colorSampleText['main_value'] + ", used in taxon " + app.colorTaxon['main_value'],
                                        "logicDefinition": "",
                                    };
                                    console.log(requestBody);
                                    axios.post('http://shark.sbs.arizona.edu:8080/class', requestBody)
                                        .then(function (resp) {
                                            console.log('shark api class resp', resp);
                                            axios.post('http://shark.sbs.arizona.edu:8080/save', {
                                                user: app.sharedFlag ? '' : app.user.name,
                                                ontology: 'carex'
                                            })
                                                .then(function (resp) {
                                                    console.log('save api resp', resp);
                                                });
                                        });
                                }
                            }
                        }
                        console.log('postFlag', postFlag);
            //    }
                        if (postFlag == true) {
                            axios.post('/chrecorder/public/api/v1/save-color-value', postValue)
                                .then(async function (resp) {
                                    if (postValue['brightness'] && postValue['brightness'] != '') {
                                        await axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term='+postValue['brightness'].toLowerCase().replace('-', ' ')).then(resultsp=>{
                                            if (resultsp.data.entries.length > 0) {
                                                let methodEntry = resultsp.data.entries.filter(function(each) {
                                                    return each.resultAnnotations.some(e => e.property === "http://www.geneontology.org/formats/oboInOwl#id") == true;
                                                })[0];
                                                if (methodEntry) {
                                                    for (var j = 0; j < methodEntry.resultAnnotations.length; j ++) {
                                                        if (methodEntry.resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                                            axios.post("/chrecorder/public/api/v1/setColorBrightnessIRI", {id: resp.data.id, IRI: methodEntry.resultAnnotations[j].value});
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    }
                                    if (postValue['reflectance'] && postValue['reflectance'] != '') {
                                        await axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term='+postValue['reflectance'].toLowerCase().replace('-', ' ')).then(resultsp=>{
                                            if (resultsp.data.entries.length > 0) {
                                                let methodEntry = resultsp.data.entries.filter(function(each) {
                                                    return each.resultAnnotations.some(e => e.property === "http://www.geneontology.org/formats/oboInOwl#id") == true;
                                                })[0];
                                                if (methodEntry) {
                                                    for (var j = 0; j < methodEntry.resultAnnotations.length; j ++) {
                                                        if (methodEntry.resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                                            axios.post("/chrecorder/public/api/v1/setColorReflectanceIRI", {id: resp.data.id, IRI: methodEntry.resultAnnotations[j].value});
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    }
                                    if (postValue['saturation'] && postValue['saturation'] != '') {
                                        await axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term='+postValue['saturation'].toLowerCase().replace('-', ' ')).then(resultsp=>{
                                            if (resultsp.data.entries.length > 0) {
                                                let methodEntry = resultsp.data.entries.filter(function(each) {
                                                    return each.resultAnnotations.some(e => e.property === "http://www.geneontology.org/formats/oboInOwl#id") == true;
                                                })[0];
                                                if (methodEntry) {
                                                    for (var j = 0; j < methodEntry.resultAnnotations.length; j ++) {
                                                        if (methodEntry.resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                                            axios.post("/chrecorder/public/api/v1/setColorSaturationIRI", {id: resp.data.id, IRI: methodEntry.resultAnnotations[j].value});
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    }
                                    if (postValue['colored'] && postValue['colored'] != '') {
                                        await axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term='+postValue['colored'].toLowerCase().replace('-', ' ')).then(resultsp=>{
                                            if (resultsp.data.entries.length > 0) {
                                                let methodEntry = resultsp.data.entries.filter(function(each) {
                                                    return each.resultAnnotations.some(e => e.property === "http://www.geneontology.org/formats/oboInOwl#id") == true;
                                                })[0];
                                                if (methodEntry) {
                                                    for (var j = 0; j < methodEntry.resultAnnotations.length; j ++) {
                                                        if (methodEntry.resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                                            axios.post("/chrecorder/public/api/v1/setColorColoredIRI", {id: resp.data.id, IRI: methodEntry.resultAnnotations[j].value});
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    }
                                    if (postValue['multi_colored'] && postValue['multi_colored'] != '') {
                                        await axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term='+postValue['multi_colored'].toLowerCase().replace('-', ' ')).then(resultsp=>{
                                            if (resultsp.data.entries.length > 0) {
                                                let methodEntry = resultsp.data.entries.filter(function(each) {
                                                    return each.resultAnnotations.some(e => e.property === "http://www.geneontology.org/formats/oboInOwl#id") == true;
                                                })[0];
                                                if (methodEntry) {
                                                    for (var j = 0; j < methodEntry.resultAnnotations.length; j ++) {
                                                        if (methodEntry.resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                                            axios.post("/chrecorder/public/api/v1/setColorMultiColoredIRI", {id: resp.data.id, IRI: methodEntry.resultAnnotations[j].value});
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    }
                                    app.saveColorButtonFlag = false;
                                    app.values = resp.data.values;
                                    app.preList = resp.data.preList;
                                    app.postList = resp.data.postList;
                                    if (newFlag == false) {
                                        app.colorDetailsFlag = false;
                                    } else {
                                        app.colorDetailsFlag = true;
                                        app.colTreeData = [];
                                        app.currentColorValueExist = true;
                                        app.colorComment = {};
                                        app.colorTaxon = {
                                            'brightness': app.taxonName,
                                            'reflectance': app.taxonName,
                                            'saturation': app.taxonName,
                                            'colored': app.taxonName,
                                            'multi_colored': app.taxonName,
                                        };
                                        app.colorSampleText = {};
                                        app.colorDefinition = {};
                                        app.userColorDefinition = {};
                                        app.currentColorValue.taxon = app.taxonName;
                                        app.colorSynonyms = {};
                                    }
                                    app.colorDetails = resp.data.colorDetails;
                                    app.allColorValues = resp.data.allColorValues;
                                    app.allNonColorValues = resp.data.allNonColorValues;
                                    app.currentColorValue['value_id'] = app.currentColorValue.value_id;
                                    app.getDeprecatedValue();
                                }).then(() => {
                                    axios.post('/chrecorder/public/api/v1/getAllDetails')
                                        .then(function (respColor) {
                                            app.allColorValues = respColor.data.colorValues;
                                            app.getDeprecatedValue();
                                        })
                                });
                        } else {
                            app.saveColorButtonFlag = false;
                            $('.color-definition-input').css('border', '1px solid red');
                        }
                    }
                }


            //    for (var i = 0; i < app.colorDetails.length; i++) {
            //      }

            },
            removeColorValue() {
                var app = this;

                console.log('app.colorDetails[0].value_id', app.colorDetails[0].value_id);
                axios.post('/chrecorder/public/api/v1/remove-color-value', {value_id: app.colorDetails[0].value_id})
                    .then(function (resp) {
                        app.values = resp.data.values;
                        app.preList = resp.data.preList;
                        app.postList = resp.data.postList;
                        app.allColorValues = resp.data.allColorValues;
                        app.allNonColorValues = resp.data.allNonColorValues;
                        app.getDeprecatedValue();
                        app.colorDetailsFlag = false;
                    });
            },
            saveNonColorValue(newFlag = false) {
                var app = this;
                if (app.saveNonColorButtonFlag){
                    return;
                }

                app.currentTermForBracket = '';
                if (app.currentNonColorValue['main_value'].indexOf('(') > -1 || app.currentNonColorValue['main_value'].indexOf(')') > -1) {
                    app.currentTermForBracket = app.currentNonColorValue['main_value'];
                }
                if (app.currentTermForBracket != '') {
                    app.toRemoveBracketConfirmFlag = true;
                    return;
                }
                
                var characterId = app.values.find(eachValue => eachValue.find(eachItem => eachItem.id == app.currentNonColorValue.value_id) != null)[0].character_id;
                var characterName = app.userCharacters.find(ch => ch.id == characterId).name;
                console.log('characterName', characterName);

                var searchText = characterName.split(' of ')[0].toLowerCase();
                if (searchText[searchText - 1] == ' ') {
                    searchText = searchText.substring(0, searchText.length - 1);
                    console.log('searchText1', searchText);
                }
                searchText = searchText.replace(' ', '-');

                searchText = searchText.replace(' ', '-');
                var postFlag = true;
                app.saveNonColorButtonFlag = true;
                console.log('app.currentNonColorValue',app.currentNonColorValue);
                console.log('app.currentNonColorValue.confirmedFlag',app.currentNonColorValue.confirmedFlag);
                console.log("app.currentNonColorValue['main_value']",app.currentNonColorValue['main_value']);
                console.log("app.currentNonColorValue.confirmedFlag['main_value']",app.currentNonColorValue.confirmedFlag['main_value']);
                if (app.currentNonColorValue['main_value'] && app.currentNonColorValue.confirmedFlag['main_value'] == false && !app.searchTreeData(app.textureTreeData, app.currentNonColorValue.main_value)) {
                    app.saveNonColorButtonFlag = false;
                    app.searchNonColorSelection(app.currentNonColorValue, 'main_value');
                } else {
                    if ((app.currentNonColorValue.negation == 'undefined' || app.currentNonColorValue.negation == '' || app.currentNonColorValue.negation == null)
                        && (app.currentNonColorValue.pre_constraint == 'undefined' || app.currentNonColorValue.pre_constraint == '' || app.currentNonColorValue.pre_constraint == null)
                        && (app.currentNonColorValue.main_value == 'undefined' || app.currentNonColorValue.main_value == '' || app.currentNonColorValue.main_value == null)
                        && (app.currentNonColorValue.post_constraint == 'undefined' || app.currentNonColorValue.post_constraint == '' || app.currentNonColorValue.post_constraint == null)) {
                        axios.get('/chrecorder/public/api/v1/get-non-color-details/' + app.currentNonColorValue.value_id)
                            .then(function (resp) {
                                app.nonColorDetails = resp.data.nonColorDetails;
                                app.values = resp.data.values;
                                app.getDeprecatedValue();
                                app.nonColorDetailsFlag = false;

                            });
                    } else {
                        console.log('postFlag', postFlag);
                //    for (var i = 0; i < app.nonColorDetails.length; i++) {
                        var postValue = {};
                        postValue['value_id'] = app.currentNonColorValue['value_id'];
                        if (app.currentNonColorValue.id) {
                            postValue['id'] = app.currentNonColorValue.id;
                        }
                        postValue['negation'] = app.currentNonColorValue.negation;
                        postValue['pre_constraint'] = app.currentNonColorValue.pre_constraint;
                        postValue['certainty_constraint'] = app.currentNonColorValue.certainty_constraint;
                        postValue['degree_constraint'] = app.currentNonColorValue.degree_constraint;
                        postValue['post_constraint'] = app.currentNonColorValue.post_constraint;
                        postValue['main_value'] = app.currentNonColorValue.main_value;
                        var requestBody = {};
                        if (app.currentNonColorValue['main_value'] != null && app.currentNonColorValue['main_value'] != '') {
                            console.log('a1');
                            if ((app.searchNonColorFlag == 0 || app.searchNonColorFlag ==1 && app.currentNonColorValue['main_value'] == app.defaultNonColorValue) && postFlag == true) {

                                console.log('a2');
                                
                                if ( !app.nonColorExistFlag ){
                                    if ( app.userNonColorDefinition['main_value']=='' || app.userNonColorDefinition['main_value'] == null || app.userNonColorDefinition['main_value'] == undefined){
                                        alert('please enter definition');
                                        app.saveNonColorButtonFlag = false;
                                        return;
                                    }
                                    if ( app.nonColorSampleText['main_value']=='' || app.nonColorSampleText['main_value'] == null || app.nonColorSampleText['main_value'] == undefined){
                                        alert('please enter sample sentence');
                                        app.saveNonColorButtonFlag = false;
                                        return;
                                    }
                                }
                                
                                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.currentNonColorValue['main_value'])
                                    .then(function (resp) {
                                        if (resp.data.entries.length == 0) {
                                            postValue['main_value'] = app.currentNonColorValue['main_value'];
                                            var date = new Date();
                                            requestBody = {
                                                "user": app.sharedFlag ? '' : app.user.name,
                                                "ontology": "carex",
                                                "term": postValue['main_value'],
                                                "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#toreview",
                                                "definition": app.userNonColorDefinition['main_value'],
                                                "elucidation": "",
                                                "createdBy": app.user.name,
                                                "creationDate": ("0" + date.getMonth()).slice(-2) + '-' + ("0" + date.getDate()).slice(-2) + '-' + date.getFullYear(),
                                                "definitionSrc": app.user.name,
                                                "examples": app.nonColorSampleText['main_value'] + ", used in taxon " + app.nonColorTaxon['main_value'],
                                                "logicDefinition": "",
                                            };
                                            axios.post('http://shark.sbs.arizona.edu:8080/class', requestBody)
                                                .then(function (resp) {
                                                    console.log('shark api class resp', resp);
                                                    axios.post('http://shark.sbs.arizona.edu:8080/save', {
                                                        user: app.sharedFlag ? '' : app.user.name,
                                                        ontology: 'carex'
                                                    })
                                                        .then(function (resp) {
                                                            console.log('save api resp', resp);
                                                        });
                                                });
                                        } 
                                    });

                                //    }

                            } else if (app.searchNonColorFlag == 1 && postFlag == true) {
                                console.log('a3');
                                    console.log('a5');
                                    var synonym=app.nonColorSynonyms.find( eachSynonym => eachSynonym.term == app.currentNonColorValue['main_value']);
                                    var date = new Date();
                                    requestBody = {
                                        "user": app.sharedFlag ? '' : app.user.name,
                                        "ontology": "carex",
                                        "term": postValue['main_value'],
                                        "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#toreview",
                                        "definition": synonym.definition,
                                        "elucidation": "",
                                        "createdBy": app.user.name,
                                        "creationDate": ("0" + date.getMonth()).slice(-2) + '-' + ("0" + date.getDate()).slice(-2) + '-' + date.getFullYear(),
                                        "definitionSrc": app.user.name,
                                        "examples": app.nonColorSampleText['main_value'] + ", used in taxon " + app.nonColorTaxon['main_value'],
                                        "logicDefinition": "",
                                    };
                                    axios.post('http://shark.sbs.arizona.edu:8080/class', requestBody)
                                        .then(function (resp) {
                                            console.log('shark api class resp', resp);
                                            axios.post('http://shark.sbs.arizona.edu:8080/save', {
                                                user: app.sharedFlag ? '' : app.user.name,
                                                ontology: 'carex'
                                            })
                                                .then(function (resp) {
                                                    console.log('save api resp', resp);
                                                });
                                        });
                                // requestBody = {
                                //     "user": app.sharedFlag ? '' : app.user.name,
                                //     "ontology": "carex",
                                //     "definition": app.nonColorDefinition['main_value'],
                                //     "providedBy": app.user.name,
                                //     "exampleSentence": "",
                                //     "classIRI": "http://biosemantics.arizona.edu/ontologies/carex#" + postValue['main_value']
                                // };
                                // axios.post('http://shark.sbs.arizona.edu:8080/definition', requestBody)
                                //     .then(function (resp) {
                                //         console.log('shark api definition resp', resp);
                                //         axios.post('http://shark.sbs.arizona.edu:8080/save', {
                                //             user: app.sharedFlag ? '' : app.user.name,
                                //             ontology: 'carex'
                                //         })
                                //             .then(function (resp) {
                                //                 console.log('save api resp', resp);
                                //             });
                                //     });
                            }
                        }

                        if (postFlag == true) {
                            console.log('a6');
                            if (postValue['main_value'] != '') {
                                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + app.currentNonColorValue['main_value'])
                                    .then(function (resp) {
                                        if (resp.data.entries.length > 0) {
                                            let methodEntry = resp.data.entries.filter(function(each) {
                                                return each.resultAnnotations.some(e => e.property === "http://www.geneontology.org/formats/oboInOwl#id") == true;
                                            })[0];
                                            if (methodEntry) {
                                                for (var j = 0; j < methodEntry.resultAnnotations.length; j ++) {
                                                    if (methodEntry.resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                                        postValue['main_value_IRI'] = methodEntry.resultAnnotations[j].value;
                                                        break;
                                                    }
                                                }
                                            }
                                            axios.post('/chrecorder/public/api/v1/save-non-color-value', postValue)
                                                .then(function (resp) {
                                                    app.saveNonColorButtonFlag = false;
                                                    app.values = resp.data.values;
                                                    app.preList = resp.data.preList;
                                                    app.postList = resp.data.postList;
                                                    app.nonColorDetails = resp.data.nonColorDetails;
                                                    app.allNonColorValues = resp.data.allNonColorValues;
                                                    app.currentNonColorValue.detailsFlag = null;
                                                    app.getDeprecatedValue();
                                                    if (newFlag == false) {
                                                        app.nonColorDetailsFlag = false;
                                                    } else {
                                                        app.nonColorDetailsFlag = true;
                                                        app.currentNonColorValueExist = true;
                                                        app.nonColorComment = {};
                                                        app.nonColorTaxon = {
                                                            'main_value': app.taxonName,
                                                        };
                                                        app.nonColorSampleText = {};
                                                        app.nonColorDefinition = {};
                                                        app.userNonColorDefinition = {};
                                                        app.currentNonColorValue.taxon = app.taxonName;
                                                    }
                                                });
                                        }
                                    });
                            } else {
                                axios.post('/chrecorder/public/api/v1/save-non-color-value', postValue)
                                    .then(function (resp) {
                                        app.saveNonColorButtonFlag = false;
                                        app.values = resp.data.values;
                                        app.preList = resp.data.preList;
                                        app.postList = resp.data.postList;
                                        app.nonColorDetails = resp.data.nonColorDetails;
                                        app.allNonColorValues = resp.data.allNonColorValues;
                                        app.currentNonColorValue.detailsFlag = null;
                                        app.getDeprecatedValue();
                                        if (newFlag == false) {
                                            app.nonColorDetailsFlag = false;
                                        } else {
                                            app.nonColorDetailsFlag = true;
                                            app.currentNonColorValueExist = true;
                                            app.nonColorComment = {};
                                            app.nonColorTaxon = {
                                                'main_value': app.taxonName,
                                            };
                                            app.nonColorSampleText = {};
                                            app.nonColorDefinition = {};
                                            app.userNonColorDefinition = {};
                                            app.currentNonColorValue.taxon = app.taxonName;
                                        }
                                    });
                            }
                        } else {
                            app.saveNonColorButtonFlag = false;
                            $('.non-color-input-definition').css('border', '1px solid red');
                        }
                    }
                }
            },
            removeNonColorValue() {
                var app = this;

                axios.post('/chrecorder/public/api/v1/remove-non-color-value', {value_id: app.nonColorDetails[0].value_id})
                    .then(function (resp) {
                        app.values = resp.data.values;
                        app.preList = resp.data.preList;
                        app.postList = resp.data.postList;
                        app.getDeprecatedValue();
                        app.nonColorDetailsFlag = false;
                    });
            },
            checkColorProperty(property) {
                if (property == 'negation'
                    || property == 'pre_constraint'
                    || property == 'certainty_constraint'
                    || property == 'degree_constraint'
                    || property == 'brightness'
                    || property == 'reflectance'
                    || property == 'saturation'
                    || property == 'colored'
                    || property == 'multi_colored'
                    || property == 'post_constraint') {
                    return true;
                } else {
                    return false;
                }
            },
            saveNewColorValue() {

            },
            removeNonColorDuplicates(array) {
                var result = [];

                for (var i = 0; i < array.length; i++) {
                    var temp = array[i];
                    var resultFlag = true;
                    for (var j = 0; j < result.length; j++) {
                        if (array[i].negation == result[j].negation
                            && array[i].pre_constraint == result[j].pre_constraint
                            && array[i].certainty_constraint == result[j].certainty_constraint
                            && array[i].degree_constraint == result[j].degree_constraint
                            && array[i].main_value == result[j].main_value
                            && array[i].post_constraint == result[j].post_constraint) {
                            result[j].count = result[j].count + 1;
                            resultFlag = false;
                        }
                    }
                    if (resultFlag) {
                        array[i].count = 1;
                        result.push(array[i]);
                    }
                }

                return result;

            },
            removeArrayDuplicates(array) {
                var result = [];


                for (var i = 0; i < array.length; i++) {
                    var temp = array[i];
                    var resultFlag = true;
                    for (var j = 0; j < result.length; j++) {
                        if (array[i].negation == result[j].negation
                            && array[i].pre_constraint == result[j].pre_constraint
                            && array[i].certainty_constraint == result[j].certainty_constraint
                            && array[i].degree_constraint == result[j].degree_constraint
                            && array[i].brightness == result[j].brightness
                            && array[i].reflectance == result[j].reflectance
                            && array[i].saturation == result[j].saturation
                            && array[i].colored == result[j].colored
                            && array[i].multi_colored == result[j].multi_colored
                            && array[i].post_constraint == result[j].post_constraint) {
                            result[j].count = result[j].count + 1;
                            resultFlag = false;
                        }
                    }
                    if (resultFlag) {
                        array[i].count = 1;
                        result.push(array[i]);
                    }
                }

                return result;
            },
            removeDuplicates(arr) {

                const result = [];
                const duplicatesIndices = [];

                // Loop through each item in the original array
                arr.forEach((current, index) => {

                    if (duplicatesIndices.includes(index)) return;

                    result.push(current);

                    // Loop through each other item on array after the current one
                    for (let comparisonIndex = index + 1; comparisonIndex < arr.length; comparisonIndex++) {

                        const comparison = arr[comparisonIndex];
                        const currentKeys = Object.keys(current);
                        const comparisonKeys = Object.keys(comparison);

                        // Check number of keys in objects
                        if (currentKeys.length !== comparisonKeys.length) continue;

                        // Check key names
                        const currentKeysString = currentKeys.sort().join("").toLowerCase();
                        const comparisonKeysString = comparisonKeys.sort().join("").toLowerCase();
                        if (currentKeysString !== comparisonKeysString) continue;

                        // Check values
                        let valuesEqual = true;
                        for (let i = 0; i < currentKeys.length; i++) {
                            const key = currentKeys[i];
                            if (current[key] !== comparison[key]) {
                                valuesEqual = false;
                                break;
                            }
                        }
                        if (valuesEqual) duplicatesIndices.push(comparisonIndex);

                    } // end for loop

                }); // end arr.forEach()

                return result;
            },
            focusedValue(value) {
                var app = this;

                app.currentColorValue = {
                    confirmedFlag: {
                        brightness: false,
                        reflectance: false,
                        saturation: false,
                        colored: false,
                        multi_colored: false,
                    }
                };
                app.currentNonColorValue = {
                    confirmedFlag: {
                        main_value: false,
                    }
                };
                app.currentNonColorValue.detailFlag =null;
                app.currentColorValue.detailsFlag = null;
                app.currentColorValue.value_id = value.id;
                app.currentNonColorValue.confirmedFlag = {
                    main_value: false,
                };
                app.currentColorValue.value = value.value;
                app.currentNonColorValue.value = value.value;
                app.existColorDetails = [];
                app.existNonColorDetails = [];
                app.colorDetails = [];
                app.nonColorDetails = [];
                app.extraColors = [];
                app.currentNonColorValue.detailsFlag = null;
                // app.currentNonColorValue.main_value = '';
                // app.currentNonColorValue.negation = '';
                // app.currentNonColorValue.pre_constraint = '';
                // app.currentNonColorValue.post_constraint = '';
                // app.currentNonColorValue.degree_constraint = '';
                // app.currentNonColorValue.certainty_constraint = '';
                app.currentNonColorValue.value_id = value.id;
                app.existColorDetailsFlag = true;
                app.existNonColorDetailsFlag = true;

                app.saveNonColorButtonFlag = false;
                app.saveColorButtonFlag = false;
                console.log('test', value);
                var currentCharacter = app.userCharacters.find(ch => ch.id == value.character_id);
                app.currentCharacter = currentCharacter;
                console.log('currentCharacter', currentCharacter.name)

                if (!app.checkHaveUnit(currentCharacter.name)) {
                    console.log('!checkHaveUnit');
                    axios.get('/chrecorder/public/api/v1/get-constraint/' + currentCharacter.name)
                        .then(function (resp) {
                            app.preList = resp.data.preList;
                            app.postList = resp.data.postList;
                        });
                    if (currentCharacter.name.startsWith('Color')) {
                        app.colTreeData = [];
                        
                        app.colorDetailsFlag = true;
                        app.currentColorValueExist = true;
                        app.colorComment = {};
                        app.colorTaxon = {
                            'brightness': app.taxonName,
                            'reflectance': app.taxonName,
                            'saturation': app.taxonName,
                            'colored': app.taxonName,
                            'multi_colored': app.taxonName,
                        };
                        app.colorSampleText = {};
                        app.colorDefinition = {};
                        app.userColorDefinition = {};
                        app.currentColorValue.taxon = app.taxonName;

                        app.colorSynonyms=[];
                        axios.get('/chrecorder/public/api/v1/get-color-details/' + value.id)
                            .then(function (resp) {
                                console.log('get-color resp', resp.data);
                                app.colorDetails = resp.data.colorDetails;
                                app.existColorDetails = resp.data.existColorDetails;
                                for (var i = 0; i < app.existColorDetails.length; i++) {
                                    delete app.existColorDetails[i].created_at;
                                    delete app.existColorDetails[i].updated_at;
                                    delete app.existColorDetails[i].id;
                                    delete app.existColorDetails[i].value_id;
                                }

                                console.log('resp.data.existColorDetails', resp.data.existColorDetails);
                                app.existColorDetails = app.removeArrayDuplicates(app.existColorDetails);

                                if (app.colorDetails.length == 0) {
                                    app.currentColorValueExist = false;
                                } else {
                                    console.log('currentColorValue', app.currentColorValue);
                                    app.currentColorValueExist = true;
                                    app.currentColorValue.value_id = value.id;
                                    $('.color-input').attr('placeholder', '');
                                    app.colorComment = {};
                                    app.colorTaxon = {
                                        'brightness': app.taxonName,
                                        'reflectance': app.taxonName,
                                        'saturation': app.taxonName,
                                        'colored': app.taxonName,
                                        'multi_colored': app.taxonName,
                                    };
                                    app.colorSampleText = {};
                                    app.colorDefinition = {};
                                    app.userColorDefinition = {};
                                    app.currentColorValue.taxon = app.taxonName;
                                    for (var i = 0; i < app.colorDetails.length; i++) {
                                        app.colorDetails[i].taxon = app.taxonName;
                                        app.colorDetails[i].detailFlag = null;
                                    }
                                }
                            });
                    } else {
                        app.nonColorDetailsFlag = true;
                        app.textureTreeData = null;
                        
                        app.nonColorDetailsFlag = true;
                        app.currentNonColorValueExist = true;
                        app.nonColorComment = {};
                        app.nonColorTaxon = {
                            'main_value': app.taxonName,
                        };
                        app.nonColorSampleText = {};
                        app.nonColorDefinition = {};
                        app.userNonColorDefinition = {};
                        app.currentNonColorValue.taxon = app.taxonName;
 
                        app.currentNonColorValue.placeholderName = currentCharacter.name.split('of')[0].toLowerCase();
                        if (app.currentNonColorValue.placeholderName[app.currentNonColorValue.placeholderName.length - 1] == ' ') {
                            app.currentNonColorValue.placeholderName = app.currentNonColorValue.placeholderName.substring(0, app.currentNonColorValue.placeholderName.length - 1);
                        }
                        app.currentNonColorValue.placeholderName = app.currentNonColorValue.placeholderName.replace(' ', '-');

                        axios.get('/chrecorder/public/api/v1/get-non-color-details/' + value.id)
                            .then(function (resp) {
                                app.nonColorDetails = resp.data.nonColorDetails;
                                app.existNonColorDetails = resp.data.existNonColorDetails;
                                for (var i = 0; i < app.existNonColorDetails.length; i++) {
                                    delete app.existNonColorDetails[i].created_at;
                                    delete app.existNonColorDetails[i].updated_at;
                                    delete app.existNonColorDetails[i].id;
                                    delete app.existNonColorDetails[i].value_id;
                                }

                                console.log('resp.data.existNonColorDetails', resp.data.existNonColorDetails);
                                app.existNonColorDetails = app.removeNonColorDuplicates(app.existNonColorDetails);
                                if (app.nonColorDetails.length == 0) {
                                    app.currentNonColorValueExist = false;
                                    app.nonColorTaxon = {
                                        'main_value': app.taxonName,
                                    };
                                    app.nonColorSampleText = {};
                                    app.nonColorDefinition = {};
                                    app.userNonColorDefinition = {};
                                    app.currentNonColorValue.placeholderName = currentCharacter.name.split('of')[0].toLowerCase();
                                    if (app.currentNonColorValue.placeholderName[app.currentNonColorValue.placeholderName.length - 1] == ' ') {
                                        app.currentNonColorValue.placeholderName = app.currentNonColorValue.placeholderName.substring(0, app.currentNonColorValue.placeholderName.length - 1);
                                    }
                                    app.currentNonColorValue.placeholderName = app.currentNonColorValue.placeholderName.replace(' ', '-');
                                    app.currentNonColorValue.taxon = app.taxonName;
                                } else {
                                    app.currentNonColorValueExist = true;
                                    app.nonColorComment = {};
                                    app.nonColorTaxon = {
                                        'main_value': app.taxonName,
                                    };
                                    app.nonColorSampleText = {};
                                    app.nonColorDefinition = {};
                                    app.userNonColorDefinition = {};
                                    app.currentNonColorValue.placeholderName = currentCharacter.name.split('of')[0].toLowerCase();
                                    if (app.currentNonColorValue.placeholderName[app.currentNonColorValue.placeholderName.length - 1] == ' ') {
                                        app.currentNonColorValue.placeholderName = app.currentNonColorValue.placeholderName.substring(0, app.currentNonColorValue.placeholderName.length - 1);
                                    }
                                    app.currentNonColorValue.placeholderName = app.currentNonColorValue.placeholderName.replace(' ', '-');
                                    app.currentNonColorValue.taxon = app.taxonName;
                                    for (var i = 0; i < app.nonColorDetails.length; i++) {
                                        app.nonColorDetails[i].taxon = app.taxonName;
                                        app.nonColorDetails[i].detailFlag = null;
                                        app.nonColorDetails[i].placeholderName = currentCharacter.name.split(' ')[0].toLowerCase();
                                    }
                                }
                            });
                    }

                }
            },
            changeColorSection(color, flag, event) {
                var app = this;

                app.colorSearchText = '';
                app.nonColorSearchText = '';

                if (!color.detailFlag){
                    app.$store.state.colorTreeData = {};
                }
                color.detailFlag = flag;
                app.currentColorValue.confirmedFlag[flag] = false;
                app.colorSynonyms[flag] = undefined;

                if (app.checkHaveColorValueSet(flag)) {
                    color.detailFlag = ' ';
                    if (!app.colTreeData[flag]){
                        app.colorExistFlag = false;
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/getSubclasses?baseIri=http://biosemantics.arizona.edu/ontologies/carex&term=' + app.changeToSubClassName(flag))
                            .then(async function (resp) {
                                app.$store.state.colorTreeData = resp.data;
                                app.colTreeData[flag] = resp.data;
                                app.getImageFromColorTreeData(app.$store.state.colorTreeData);

                                color.detailFlag = flag;
                                app.colorExistFlag = true;
                                
                                app.filterFlag = false;
                                const timerID=setTimeout(() => {
                                    app.filterFlag = true;
                                }, 50)
                                if (app.colorDetailsFlag){
                                    app.colorDetailsFlag = false;
                                    app.colorDetailsFlag = true;
                                }
                                console.log('color', color);
                                if (color.id) {
                                    app.colorDetailId = color.id;
                                //    color.detailFlag = flag;
                                    app.colorDetails.find(eachColor => eachColor.id == app.colorDetailId).detailFlag = flag;
                                    for (var i = 0; i < app.colorDetails.length; i++) {
                                        if (app.colorDetails[i].id == color.id) {
                                            app.colorDetails[i].detailFlag = flag;
                                            app.colorDetails[i][flag] = app.colorDetails[i][flag] + ';';
                                            app.colorDetails[i][flag] = app.colorDetails[i][flag].substring(0, app.colorDetails[i][flag].length - 1);
                                            if (app.colorDetails[i][flag] == 'null' || app.colorDetails[i][flag] == null) {
                                                app.colorDetails[i][flag] = '';
                                            }
                                        }
                                    }

                                }
                            });
                    }
                    else {
                        console.log('flag',flag);
                        color.detailFlag = flag;
                        if (app.colTreeData[flag].text != app.$store.state.colorTreeData.text){
                            app.colorExistFlag = false;
                            const timerID = setTimeout(() => {
                                app.$store.state.colorTreeData = app.colTreeData[flag];
                                app.colorExistFlag =true;
                                app.filterFlag = false;
                                const timerID=setTimeout(() => {
                                    app.filterFlag = true;
                                }, 50)
                                
                                if (app.colorDetailsFlag){
                                    app.colorDetailsFlag = false;
                                    app.colorDetailsFlag = true;
                                }
                            }, 50)
                        }
                        else if (!app.colorExistFlag){
                            app.colorExistFlag = true;
                            app.filterFlag = false;
                            const timerID=setTimeout(() => {
                                app.filterFlag = true;
                            }, 50)
                            if (app.colorDetailsFlag){
                                app.colorDetailsFlag = false;
                                app.colorDetailsFlag = true;
                            }
                        }
                        // setTimeout(() => {
                        //     console.log("v-if", (app.currentColorValue.detailFlag == 'brightness'
                        //     || app.currentColorValue.detailFlag == 'reflectance'
                        //     || app.currentColorValue.detailFlag == 'saturation'
                        //     || app.currentColorValue.detailFlag == 'colored'
                        //     || app.currentColorValue.detailFlag == 'multi_colored') && app.colorExistFlag);
                        //     console.log("app.colorExistFlag",app.colorExistFlag);
                        //     console.log("app.currentColorValue.detailFlag",app.currentColorValue.detailFlag);
                        // },300)
                    }
                } else {
                    color.detailFlag = flag;
                    if (color.id) {
                        app.colorDetailId = color.id;
                        for (var i = 0; i < app.colorDetails.length; i++) {
                            if (app.colorDetails[i].id == color.id) {
                                app.colorDetails[i].detailFlag = flag;
                                app.colorDetails[i][flag] = app.colorDetails[i][flag] + ';';
                                app.colorDetails[i][flag] = app.colorDetails[i][flag].substring(0, app.colorDetails[i][flag].length - 1);
                                if (app.colorDetails[i][flag] == 'null' || app.colorDetails[i][flag] == null) {
                                    app.colorDetails[i][flag] = '';
                                }
                            }
                        }
                        console.log('app.colorDetails', app.colorDetails);
                    } else {
                        if (app.colorDetails.length > 0) {
                            app.colorDetails[app.colorDetails.length - 1].detailFlag = flag;

                        }

                    }
                }
                console.log('flag', flag);
            },
            getImageFromColorTreeData(tData) {
                var app = this;
                var methodEntry = null;
                if (!tData) return ;
                tData.data["images"] = [];
                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + tData.text.replace(' ', '_').replace('-', '_').toLowerCase())
                    .then(function (resp) {
                        if (resp.data.entries.length > 0) {
                            methodEntry = resp.data.entries.filter(function(each) {
                                return each.resultAnnotations.some(e => e.property === "http://biosemantics.arizona.edu/ontologies/carex#elucidation") == true;
                            })[0];
                            if (methodEntry) {
                                for (var i = 0; i < methodEntry.resultAnnotations.length; i ++) {
                                    if (methodEntry.resultAnnotations[i].property == 'http://biosemantics.arizona.edu/ontologies/carex#elucidation') {
                                        var id = '';
                                        if (methodEntry.resultAnnotations[i].value.indexOf('id=') < 0) {
                                            id = methodEntry.resultAnnotations[i].value.slice(methodEntry.resultAnnotations[i].value.indexOf('file/d/') + 7, methodEntry.resultAnnotations[i].value.indexOf('/view?usp='));
                                        } else {
                                            id = methodEntry.resultAnnotations[i].value.split('id=')[1].substring(0, methodEntry.resultAnnotations[i].value.split('id=')[1].length - 1);
                                        }
                                        console.log(tData.text);
                                        console.log(('https://drive.google.com/uc?id=' + id));
                                        tData.data["images"].push('https://drive.google.com/uc?id=' + id);
                                    }
                                }
                            }
                        }
                    })
                    .catch(function (resp) {
                        console.log('exp search resp error', resp);
                    });
                for (let i = 0; tData.children && i < tData.children.length; i ++){
                    app.getImageFromColorTreeData(tData.children[i]);
                }
                return ;
            },
            changeNonColorSection(nonColor, flag, event) {
                var app = this;
                app.nonColorSearchText = '';

                let treeFlag = app.currentNonColorValue.detailsFlag == null && app.nonColorExistFlag && !(!app.textureTreeData);
                app.currentNonColorValue.detailsFlag = flag;

                var characterId = app.values.find(eachValue => eachValue.find(eachItem => eachItem.id == nonColor.value_id) != null)[0].character_id;
                var characterName = app.userCharacters.find(ch => ch.id == characterId).name;
                console.log('characterName', characterName);

                var searchText = characterName.split(' of ')[0].toLowerCase();
                if (searchText[searchText - 1] == ' ') {
                    searchText = searchText.substring(0, searchText.length - 1);
                    console.log('searchText1', searchText);
                }
                searchText = searchText.replace(' ', '-');

                // if (searchText == 'relationship'){
                //     searchText = 'relational quality';
                // }
                console.log('searchText', searchText);
                if (flag == 'negation') {
                    event.target.placeholder = '';
                }
            //    } else if (flag == 'main_value') {
            //        event.target.placeholder = searchText[0];
            //    }

                if (flag == 'main_value') {
                    
                    if (app.userNonColorDefinition['main_value']==' ')
                        app.userNonColorDefinition['main_value']='';
                    if (app.nonColorSampleText['main_value']==' ')
                        app.nonColorSampleText['main_value']='';
                        
                    app.currentNonColorValue.confirmedFlag['main_value'] = false;
                    if (!app.textureTreeData){
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/getSubclasses?baseIri=http://biosemantics.arizona.edu/ontologies/carex&term=' + searchText.replace('-', '_'))
                            .then(function (resp) {
                                app.textureTreeData = resp.data;
                                app.getImageFromColorTreeData(app.textureTreeData);

                                app.currentNonColorValue.detailFlag = flag;
                                if (app.nonColorDetailsFlag){
                                    app.nonColorDetailsFlag = false;
                                    app.nonColorDetailsFlag = true;
                                }

                                app.filterFlag = false;
                                app.nonColorExistFlag = true;
                                const timerID=setTimeout(() => {
                                    app.filterFlag = true;
                                }, 50)

                                if (nonColor.id) {
                                    app.nonColorDetailId = nonColor.id;
                                    for (var i = 0; i < app.nonColorDetails.length; i++) {
                                        if (app.nonColorDetails[i].id == nonColor.id) {
                                            app.nonColorDetails[i].detailFlag = flag;
                                            app.nonColorDetails[i][flag] = app.nonColorDetails[i][flag] + ';';
                                            app.nonColorDetails[i][flag] = app.nonColorDetails[i][flag].substring(0, app.nonColorDetails[i][flag].length - 1);
                                            if (app.nonColorDetails[i][flag] == 'null' || app.nonColorDetails[i][flag] == null) {
                                                app.nonColorDetails[i][flag] = '';
                                            }
                                        }
                                    }
                                }
                            });
                    }
                    else if (treeFlag){
                        app.currentNonColorValue.detailFlag = flag;
                        if (app.nonColorDetailsFlag){
                            app.nonColorDetailsFlag = false;
                            app.nonColorDetailsFlag = true;
                        }

                        app.filterFlag = false;
                        app.nonColorExistFlag = true;
                        const timerID=setTimeout(() => {
                            app.filterFlag = true;
                        }, 50)
                    }
                    else {
                        app.currentNonColorValue.detailFlag = flag;
                        if (app.nonColorDetailsFlag){
                            app.nonColorDetailsFlag = false;
                            app.nonColorDetailsFlag = true;
                        }

                        app.nonColorExistFlag = true;
                    }
                } else {
                    if (nonColor.id) {
                        app.nonColorDetailId = nonColor.id;
                        for (var i = 0; i < app.nonColorDetails.length; i++) {
                            app.nonColorDetails[i].detailFlag = flag;
                            app.nonColorDetails[i][flag] = app.nonColorDetails[i][flag] + ';';
                            app.nonColorDetails[i][flag] = app.nonColorDetails[i][flag].substring(0, app.nonColorDetails[i][flag].length - 1);
                            if (app.nonColorDetails[i][flag] == 'null' || app.nonColorDetails[i][flag] == null) {
                                app.nonColorDetails[i][flag] = '';
                            }
                        }
                    } else {
                        if (app.nonColorDetails.length > 0) {
                            app.nonColorDetails[app.nonColorDetails.length - 1].detailFlag = flag;
                        }
                    }
                }
            },
            changeToSubClassName(flag) {
                var searchFlag = flag;
                switch (flag) {
                    case 'brightness':
                        searchFlag = 'color_brightness';
                        break;
                    case 'reflectance':
                        searchFlag = 'reflectance';
                        break;
                    case 'saturation':
                        searchFlag = 'color_saturation';
                        break;
                    case 'colored':
                        searchFlag = 'colored';
                        break;
                    case 'multi_colored':
                        searchFlag = 'multi_colored';
                        break;
                    default:
                        break;
                }

                return searchFlag;
            },
            searchTreeData(tData, txt) {
                var app = this;
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
            async searchColorSelection(color, flag) {
                var app = this;
                app.defaultColorValue[flag] = color[flag];
                app.extraColors = [];
                var arrayFlag = [
                    'brightness',
                    'reflectance',
                    'saturation',
                    'colored',
                    'multi_colored'
                ];

                if (app.searchTreeData(app.colTreeData[flag], color[flag])){
                    app.currentColorValue.confirmedFlag[flag] = true;
                    return;
                }

                app.colorSynonyms[flag]=[];

            //    axios.get('http://shark.sbs.arizona.edu:8080/carex/search?user=' + app.user.name + '&term=' + color[flag])
                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + color[flag])
                    .then(async function (resp) {
                        console.log(color[flag]);
                        console.log('search carex resp', resp.data);
                        app.searchColor = resp.data.entries;
                        if (app.searchColor.length == 0) {
                            app.searchColorFlag = 0;
                            // for (var i = 0; i < arrayFlag.length; i++) {
                            //     if (arrayFlag[i] != flag) {
                            //         await axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + color[flag] + '&ancestorIRI=http://biosemantics.arizona.edu/ontologies/carex%23' + app.changeToSubClassName(arrayFlag[i]))
                            //             .then(function (resp) {
                            //                 if (resp.data.entries.length != 0) {
                            //                     var tempJson = {
                            //                         flag: arrayFlag[i],
                            //                         value: color[flag]
                            //                     };
                            //                     app.extraColors.push(tempJson);
                            //                 }
                            //             });

                            //     }
                            // }
                            app.currentColorValue.confirmedFlag[flag] = true;
                        } else if (app.searchColor.find(eachColor => eachColor.resultAnnotations.find(eachProperty => (eachProperty.property.endsWith('ynonym') && eachProperty.value == color[flag]) || eachColor.term == color[flag]))) {
                            app.searchColorFlag = 1;
                            app.colorSynonyms[flag] = app.searchColor.filter(function (eachColor) {
                                return eachColor.resultAnnotations.find(eachProperty => (eachProperty.property.endsWith('hasBroadSynonym') && eachProperty.value == color[flag])
                                    || (eachProperty.property.endsWith('has_not_recommended_synonym') && eachProperty.value == color[flag])
                                    || (eachProperty.property.endsWith('hasExactSynonym') && eachProperty.value == color[flag])) != null || eachColor.term == color[flag];

                            });
                            console.log(flag);
                            console.log('app.colorSynonyms',app.colorSynonyms);
                            for (var i = 0; i < app.colorSynonyms[flag].length; i++) {
                                if (app.colorSynonyms[flag][i].term == color[flag]){
                                    app.defaultColorValue[flag] = app.defaultColorValue[flag] + ' (' + flag + ')';
                                }
                                if (app.colorSynonyms[flag][i].resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115'))) {
                                    app.colorSynonyms[flag][i].definition = app.colorSynonyms[flag][i].resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                                    //var index = app.colorDetails.indexOf(color);
                                    //app.colorDefinition[index][flag] = app.colorSynonyms[i].resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                                } else {
                                    var index = app.colorDetails.indexOf(color);
                                    app.colorDefinition[flag] = null;
                                }
                            }
                            app.colorExistFlag = true;
                            app.colorExistFlag = false;
                        }
                        else {
                            app.searchColorFlag = 0;
                        }
                        console.log('app.searchColorFlag', app.searchColorFlag);
                    });
            },
            searchNonColorSelection(nonColor, flag) {
                var app = this;
                app.nonColorExistFlag = false;
                app.defaultNonColorValue = nonColor[flag];
                app.nonColorMainValue = nonColor[flag];
                app.nonColorSynonyms = [];
                app.colorExistFlag = false;

            //    axios.get('http://shark.sbs.arizona.edu:8080/carex/search?user=' + app.user.name + '&term=' + nonColor[flag])
                console.log(nonColor[flag]);
                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + nonColor[flag])
                    .then(function (resp) {
                        console.log('search carex resp', resp.data);
                        app.searchNonColor = resp.data.entries;
                        app.searchNonColorFlag = 0;
                        app.currentNonColorValue.confirmedFlag['main_value'] = true;
                        if (app.searchNonColor.find(eachValue => eachValue.resultAnnotations.find(eachProperty => (eachProperty.property.endsWith('ynonym') && eachProperty.value == nonColor[flag])) || eachValue.term == nonColor[flag])) {
                            app.searchNonColorFlag = 1;
                            app.nonColorSynonyms = app.searchNonColor.filter(function (eachValue) {
                                return eachValue.resultAnnotations.find(eachProperty => (eachProperty.property.endsWith('hasBroadSynonym') && eachProperty.value == nonColor[flag])
                                    || (eachProperty.property.endsWith('has_not_recommended_synonym') && eachProperty.value == nonColor[flag])
                                    || (eachProperty.property.endsWith('hasExactSynonym') && eachProperty.value == nonColor[flag])) != null || eachValue.term == nonColor[flag];

                            });
                            for (var i = 0; i < app.nonColorSynonyms.length; i++) {
                                if (app.nonColorSynonyms[i].term == nonColor[flag]){
                                    var characterId = app.values.find(eachValue => eachValue.find(eachItem => eachItem.id == app.currentNonColorValue.value_id) != null)[0].character_id;
                                    var characterName = app.userCharacters.find(ch => ch.id == characterId).name;
                                    console.log('characterName', characterName);

                                    var searchText = characterName.split(' of ')[0].toLowerCase();
                                    if (searchText[searchText - 1] == ' ') {
                                        searchText = searchText.substring(0, searchText.length - 1);
                                        console.log('searchText1', searchText);
                                    }

                                    app.defaultNonColorValue = app.defaultNonColorValue + ' (' + searchText + ')';
                                }
                                if (app.nonColorSynonyms[i].resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115'))) {
                                    app.nonColorSynonyms[i].definition = app.nonColorSynonyms[i].resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                                    var index = app.nonColorDetails.indexOf(nonColor);
                                    app.nonColorDefinition[flag] = app.nonColorSynonyms[i].resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                                } else {
                                    var index = app.nonColorDetails.indexOf(nonColor);
                                    app.nonColorDefinition[flag] = null;
                                }
                            }
                        } 
                        
                        // else if (app.searchNonColor.find(eachValue => eachValue.term == nonColor[flag])) {
                        //     app.searchNonColorFlag = 2;
                        //     app.exactNonColor = app.searchNonColor.find(eachValue => eachValue.term == nonColor[flag]);
                        //     if (app.exactNonColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115'))) {
                        //         app.exactNonColor.definition = app.exactNonColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                        //         var index = app.nonColorDetails.indexOf(nonColor);
                        //         app.nonColorDefinition[flag] = app.exactNonColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                        //         app.currentNonColorValue.confirmedFlag['main_value'] = true;
                        //     } else {
                        //         var index = app.nonColorDetails.indexOf(nonColor);
                        //         app.nonColorDefinition[flag] = null;
                        //     }
                        // }
                        console.log('app.searchNonColorFlag', app.searchNonColorFlag);
                    });
            },
            onTreeNodeSelected(node) {
                var app = this;
                app.colorDetailsFlag = false;
                console.log('treeNode', node);
                app.colorDetailsFlag = true;
                app.currentColorValue.confirmedFlag[app.currentColorValue.detailFlag] = true;
                app.currentColorValue[app.currentColorValue.detailFlag] = app.currentColorValue[app.currentColorValue.detailFlag] + ';';
                app.currentColorValue[app.currentColorValue.detailFlag] = app.currentColorValue[app.currentColorValue.detailFlag].substring(0, app.currentColorValue[app.currentColorValue.detailFlag].length - 1);
                app.currentColorValue[app.currentColorValue.detailFlag] = node.data.text;

            },
            onTextureTreeNodeSelected(node) {
                var app = this;
                app.nonColorDetailsFlag = false;
                app.nonColorDetailsFlag = true;
                app.currentNonColorValue.confirmedFlag['main_value'] = true;
                app.currentNonColorValue['main_value'] = app.currentNonColorValue['main_value'] + ';';
                app.currentNonColorValue['main_value'] = app.currentNonColorValue['main_value'].substring(0, app.currentNonColorValue['main_value'].length - 1);
                app.currentNonColorValue['main_value'] = node.data.text;
                app.defaultNonColorValue = node.data.text;
                app.searchNonColorFlag = 0;
            },
            checkHaveColorValueSet(text) {
                if (text == 'brightness'
                    || text == 'reflectance'
                    || text == 'saturation'
                    || text == 'colored'
                    || text == 'multi_colored') {
                    return true;
                } else {
                    return false;
                }
            },
            expandCommentSection(synonym, flag) {
                var app = this;

                if (flag == 'main_value') {
                    for (var i = 0; i < app.nonColorSynonyms.length; i++) {
                        if (app.nonColorSynonyms[i].term == synonym.term
                            && app.nonColorSynonyms[i].parentTerm == synonym.parentTerm) {
                            var tempTermName = app.nonColorSynonyms[i].term;
                            if (!app.nonColorSynonyms[i].commentFlag) {
                                app.nonColorSynonyms[i].term = 'test1';
                                app.nonColorSynonyms[i].commentFlag = true;
                                app.nonColorSynonyms[i].term = tempTermName;
                            } else {
                                app.nonColorSynonyms[i].term = 'test2';
                                app.nonColorSynonyms[i].commentFlag = false;
                                app.nonColorSynonyms[i].term = tempTermName;
                            }
                        }
                    }
                } else {
                    for (var i = 0; i < app.colorSynonyms.length; i++) {
                        if (app.colorSynonyms[i].term == synonym.term
                            && app.colorSynonyms[i].parentTerm == synonym.parentTerm) {
                            console.log('123');
                            console.log('app.colorSynonyms[i].commentFlag', app.colorSynonyms[i].commentFlag);
                            var tempTermName = app.colorSynonyms[i].term;
                            if (!app.colorSynonyms[i].commentFlag) {
                                app.colorSynonyms[i].term = 'test1';
                                app.colorSynonyms[i].commentFlag = true;
                                app.colorSynonyms[i].term = tempTermName;
                            } else {
                                app.colorSynonyms[i].term = 'test2';
                                app.colorSynonyms[i].commentFlag = false;
                                app.colorSynonyms[i].term = tempTermName;
                            }
                        }
                    }
                }

            },
            selectUserDefinedTerm(color, flag, value) {
                var app = this;

                if (flag == 'main_value') {
                    app.currentNonColorValue.confirmedFlag[flag] = true;
                } else {
                    app.currentColorValue.confirmedFlag[flag] = true;
                }

                if (app.colorDetails.length == 1) {

                }
            },
            onSelectNameVersion(item) {
                var app = this;
                app.currentVersion = item;
                console.log(item);
                app.currentName = "";
            },
            onChangeCurrentName(text) {
                var app = this;
                console.log("onChange current name", text);
                app.currentVersion = null;
            },
            onSelectLoadVersion(item) {
                var app = this;
                app.loadVersion = item;
            },
            importMatrix() {
                var app = this;
                app.isLoading = true;
                console.log(app.loadVersion);
                var postValue = {};
                postValue['matrixname'] = app.loadVersion.matrix_name;
                app.loadMatrixDialog = false;
                app.loadMatrixConfirmDialog = false;
                axios.post('/chrecorder/public/api/v1/importMatrix', postValue)
                    .then((res) => {
                        console.log(res);
                        app.userCharacters = res.data.characters;
                        app.headers = res.data.headers;
                        app.values = res.data.values;
                        app.userTags = res.data.tags;
                        app.allColorValues = res.data.allColorValues;
                        app.allNonColorValues = res.data.allNonColorValues;
                        app.refreshUserCharacters();
                        app.showTableForTab(app.currentTab);
                        app.lastLoadMatrixName = app.loadVersion.matrix_name;
                        sessionStorage.setItem('lastMatrixName', app.loadVersion.matrix_name);
                        app.isLoading = false;
                        app.collapsedFlag = true;
                        app.loadVersion = null;
                    })
                
            },
            nameMatrix() {
                var app = this;
                app.isLoading = true;
                var selectedMatrixName = app.currentVersion ? app.currentVersion.matrix_name : app.currentName;
                console.log("name", selectedMatrixName);
                console.log("user", app.user);
                var postValue = {};
                postValue['matrixname'] = selectedMatrixName;
                app.nameMatrixDialog = false;
                var overwriteFlg = false;
                app.namesList.forEach((eachName) => {
                    if (eachName.matrix_name == selectedMatrixName) {
                        overwriteFlg = true;
                    }
                });
                if (overwriteFlg == false) {
                    axios.post('/chrecorder/public/api/v1/nameMatrix', postValue)
                        .then((res) => {
                            console.log(res);
                            app.isLoading = false;
                            app.currentVersion = null;
                            app.currentName = '';
                            app.showNamesList = [];
                            app.namesList = res.data.matrixNames;
                        })
                } else {
                    console.log('overwrite to ', selectedMatrixName);
                    axios.post('/chrecorder/public/api/v1/overwriteMatrix', postValue)
                        .then((res) => {
                            console.log(res);
                            app.isLoading = false;
                            app.currentVersion = null;
                            app.currentName = '';
                            app.showNamesList = [];
                        })
                }
            },
            updateItems(text) {
                var app = this;
                if (text == '') {
                    app.showNamesList = [];
                } else {
                    app.showNamesList = [];
                    app.namesList.forEach((eachName) => {
                        if (eachName.matrix_name.indexOf(text) >= 0) {
                            app.showNamesList.push(eachName.matrix_name);
                        }
                    });
                    app.currentName = text;
                }
            },
            itemSelected(text) {
                var app = this;
                app.currentName = text;
            },
            editEachColor(color) {
                console.log('color', color);
                var app = this;
                app.currentColorValue = color;
                app.currentColorValue.confirmedFlag = {
                    brightness: false,
                    reflectance: false,
                    saturation: false,
                    colored: false,
                    multi_colored: false
                };
                app.extraColors = [];
                app.currentColorValue.detailsFlag = null;
                
                // setTimeout(() => {
                //     console.log("v-if", (app.currentColorValue.detailFlag == 'brightness'
                //     || app.currentColorValue.detailFlag == 'reflectance'
                //     || app.currentColorValue.detailFlag == 'saturation'
                //     || app.currentColorValue.detailFlag == 'colored'
                //     || app.currentColorValue.detailFlag == 'multi_colored') && app.colorExistFlag);
                //     console.log("app.colorExistFlag",app.colorExistFlag);
                //     console.log("app.currentColorValue.detailFlag",app.currentColorValue.detailFlag);
                // },300)
            },
            removeEachColor(color) {
                console.log('color', color);
                var app = this;
                axios.post('/chrecorder/public/api/v1/remove-each-color-details', color)
                    .then(function (resp) {
                        app.colorDetails = resp.data.colorDetails;
                        app.values = resp.data.values;
                        app.preList = resp.data.preList;
                        app.postList = resp.data.postList;
                        app.allColorValues = resp.data.allColorValues;
                        app.allNonColorValues = resp.data.allNonColorValues;
                        app.getDeprecatedValue();
                    });
            },
            editEachNonColor(value) {
                var app = this;
                var tempPlaceholderName = app.currentNonColorValue.placeholderName;
                app.currentNonColorValue = value;
                app.currentNonColorValue.confirmedFlag = {
                    'main_value': false
                };
                // app.currentNonColorValue.confirmedFlag['main_value'] = false;
                app.currentNonColorValue.placeholderName = tempPlaceholderName;
                app.currentNonColorValue.detailsFlag = null;
                // setTimeout(()=>{
                //     console.log('v-if',(app.currentNonColorValue.detailFlag == 'main_value') && app.nonColorExistFlag);
                //     console.log('app.currentNonColorValue.detailFlag',app.currentNonColorValue.detailFlag);
                //     console.log('app.nonColorExistFlag',app.nonColorExistFlag);
                // },300)
            },
            removeEachNonColor(value) {
                var app = this;
                console.log("non_color", value);
                axios.post('/chrecorder/public/api/v1/remove-each-non-color-details', value)
                    .then(function (resp) {
                        app.nonColorDetails = resp.data.nonColorDetails;
                        app.values = resp.data.values;
                        app.preList = resp.data.preList;
                        app.postList = resp.data.postList;
                        app.allColorValues = resp.data.allColorValues;
                        app.allNonColorValues = resp.data.allNonColorValues;
                        app.getDeprecatedValue();
                    });
            },
            getUserTag: async() => {
                var app = this;
                return axios.get("/chrecorder/public/api/v1/user-tag/" + app.user.id);
            },
            getPrimaryColor(detailColor) {
                var app = this;
                var primaryColors = [];
                for (var key in app.colorationData) {
                    if (app.colorationData[key].includes(detailColor)) {
                        primaryColors.push(key);
                    }
                }
                return primaryColors;
            },
            getNonPrimaryColor(detailNonColor, nonColorType) {
                var app = this;
                var primaryNonColors = [];
                for (var key in app.nonColorationData[nonColorType]) {
                    if (app.nonColorationData[nonColorType][key].includes(detailNonColor)) {
                        primaryNonColors.push(key);
                    }
                }
                return primaryNonColors;
            },
            selectedSynonymForColor(detailFlag) {
                var app = this;

                //app.currentColorValue.confirmedFlag[detailFlag] = true;
            },
            selectExtraOption(flag, value, currentFlag) {
                var app = this;

                app.currentColorValue[flag] = value;
            //    app.currentColorValue[currentFlag] = value;
            },
            selectExistDetails(colorDetails) {
                var app = this;

                console.log('colorDetails', colorDetails);
                console.log('app.currentColorValue', app.currentColorValue);
                app.colorDetailsFlag = false;
                app.currentColorValue.negation = colorDetails.negation;
                app.currentColorValue.pre_constraint = colorDetails.pre_constraint;
                app.currentColorValue.certainty_constraint = colorDetails.certainty_constraint;
                app.currentColorValue.degree_constraint = colorDetails.degree_constraint;
                app.currentColorValue.brightness = colorDetails.brightness;
                app.currentColorValue.saturation = colorDetails.saturation;
                app.currentColorValue.reflectance = colorDetails.reflectance;
                app.currentColorValue.colored = colorDetails.colored;
                app.currentColorValue.multi_colored = colorDetails.multi_colored;
                app.currentColorValue.post_constraint = colorDetails.post_constraint;
                app.colorDetailsFlag = true;
            },
            selectExistNonColorDetails(nonColorDetails) {
                var app = this;
                app.nonColorDetailsFlag = false;
                app.currentNonColorValue.negation = nonColorDetails.negation;
                app.currentNonColorValue.pre_constraint = nonColorDetails.pre_constraint;
                app.currentNonColorValue.certainty_constraint = nonColorDetails.certainty_constraint;
                app.currentNonColorValue.degree_constraint = nonColorDetails.degree_constraint;
                app.currentNonColorValue.main_value = nonColorDetails.main_value;
                app.currentNonColorValue.post_constraint = nonColorDetails.post_constraint;
                app.currentNonColorValue.confirmedFlag['main_value'] = true;
                app.nonColorSampleText['main_value'] = ' ';
                app.userNonColorDefinition['main_value'] = ' ';
                app.nonColorDetailsFlag = true;
            },
            copyValuesToOther(value) {
                var app = this;
                console.log('app.userCharacters', app.userCharacters);
                console.log('value', value);
                app.copyValue = value;
                var i;
                for (var i=0;i<app.values.length;i++){
                    for (var j=0;j<app.values[i].length;j++){
                        if (parseInt(app.values[i][j].header_id)!=1
                            && app.values[i][j].character_id==value.character_id
                            && app.values[i][j].header_id!=value.header_id
                            && app.values[i][j].value!=''
                            && app.values[i][j].value!=null){
                                app.confirmOverwriteFlag = true;
                                return;
                        }
                    }
                }
                for (var i=0;i<app.values.length;i++){
                    for (var j=0;j<app.values[i].length;j++){
                        if (parseInt(app.values[i][j].header_id)!=1
                            && app.values[i][j].character_id==value.character_id
                            && app.values[i][j].header_id!=value.header_id){
                                app.values[i][j].value=app.copyValue.value;
                        }
                    }
                }
                this.confirmOverwrite();
            },
            confirmOverwrite() {
                var app = this;

                axios.post('/chrecorder/public/api/v1/overwrite-value', app.copyValue)
                    .then(function (resp) {
                        app.confirmOverwriteFlag = false;
                        app.values = resp.data.values;
                        app.preList = resp.data.preList;
                        app.postList = resp.data.postList;
                        app.allColorValues = resp.data.allColorValues;
                        app.allNonColorValues = resp.data.allNonColorValues;
                        app.getDeprecatedValue();
                    });

            },
            keepExistingValue() {
                var app = this;

                axios.post('/chrecorder/public/api/v1/keep-exist-value', app.copyValue)
                    .then(function (resp) {
                        app.confirmOverwriteFlag = false;
                        app.values = resp.data.values;
                        app.preList = resp.data.preList;
                        app.postList = resp.data.postList;
                        app.allColorValues = resp.data.allColorValues;
                        app.allNonColorValues = resp.data.allNonColorValues;
                        app.getDeprecatedValue();
                    });
            },
            calcSummary(row) {
                var app = this;
                //console.log('row',row);

                var characterName = row.find(each => each.header_id == 1).value;
                if (app.checkNumericalCharacter(characterName)) {
                    if (row.find(each => (each.header_id != 1 && each.value != null && each.value != ''))) {
                        var sum = 0;
                        var tempRpArray = [];
                        for (var i = 0; i < row.length; i++) {
                            if (row[i].header_id != 1 && row[i].value != null && row[i].value != '' && row[i].value != undefined) {
                                sum += parseFloat(row[i].value, 10); //don't forget to add the base
                                tempRpArray.push(row[i].value);
                            }
                        }

                        var mean = parseFloat(sum / tempRpArray.length).toFixed(1);

                        tempRpArray.sort((a, b) => a - b);
                        var minValue = tempRpArray[0];
                        var maxValue = tempRpArray[tempRpArray.length - 1];

                        var range;
                        if (tempRpArray.length >= 5){
                            range = '(' + minValue + '-)' + app.getPercentile(tempRpArray, 25) + '-' + app.getPercentile(tempRpArray, 75) + '(-' + maxValue + ')';
                        }
                        else if (tempRpArray.length == 1 || minValue==maxValue){
                            range = minValue;
                        }
                        else {
                            range = minValue + '-' + maxValue;
                        }
                        return "mean=" + mean + "<br/>" + "range=" + range;
                    }

                }
                return ''
            },
            getIRI() {
                var app = this;
                for (let i = 0 ; i < app.defaultCharacters.length; i ++) {
                    axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term='+app.defaultCharacters[i].name.toLowerCase().replace(' ', '_').replace('-', '_')).then(resp=>{
                        if (resp.data.entries.length > 0) {
                            let methodEntry = resp.data.entries.filter(function(each) {
                                return each.resultAnnotations.some(e => e.property === "http://www.geneontology.org/formats/oboInOwl#id") == true;
                            })[0];
                            if (methodEntry) {
                                for (var j = 0; j < methodEntry.resultAnnotations.length; j ++) {
                                    if (methodEntry.resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                         $.get("/chrecorder/public/api/v1/character/setIRI", {IRI: methodEntry.resultAnnotations[j].value, id: app.defaultCharacters[i].id});
                                         break;
                                    }
                                }
                            }
                        }
                    });
                }
                axios.get('/chrecorder/public/api/v1/character/getCharacterNames').then(result=>{
                    
                    for (let i = 0 ; i < result.data.length; i ++) {
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term='+result.data[i].name.toLowerCase().replace(' ', '_').replace('-', '_')).then(resp=>{
                            if (resp.data.entries.length > 0) {
                                let methodEntry = resp.data.entries.filter(function(each) {
                                    return each.resultAnnotations.some(e => e.property === "http://www.geneontology.org/formats/oboInOwl#id") == true;
                                })[0];
                                if (methodEntry) {
                                    for (var j = 0; j < methodEntry.resultAnnotations.length; j ++) {
                                        if (methodEntry.resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                            $.get("/chrecorder/public/api/v1/character/setCharacterIRI", {IRI: methodEntry.resultAnnotations[j].value, name: result.data[i].name});
                                            break;
                                        }
                                    }
                                }
                            }
                        });
                    }
                })
            },
            getIRIOfValues() {
                var app = this;
                console.log("get IRI of Values");
                for (let i = 0 ; i < app.allNonColorValues.length; i ++) {
                    if (app.allNonColorValues[i].main_value && app.allNonColorValues[i].main_value != "") {
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term='+app.allNonColorValues[i].main_value.toLowerCase().replace('-', ' ')).then(resp=>{
                            if (resp.data.entries.length > 0) {
                                let methodEntry = resp.data.entries.filter(function(each) {
                                    return each.resultAnnotations.some(e => e.property === "http://www.geneontology.org/formats/oboInOwl#id") == true;
                                })[0];
                                if (methodEntry) {
                                    for (var j = 0; j < methodEntry.resultAnnotations.length; j ++) {
                                        if (methodEntry.resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                            console.log(app.allNonColorValues[i].id, app.allNonColorValues[i].main_value, methodEntry.resultAnnotations[j].value);
                                            axios.post("/chrecorder/public/api/v1/setNonColorValueIRI", {id: app.allNonColorValues[i].id, main_value_IRI: methodEntry.resultAnnotations[j].value});
                                            break;
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
                for (let i = 0 ; i < app.allColorValues.length; i ++) {
                    if (app.allColorValues[i].brightness && app.allColorValues[i].brightness != "") {
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term='+app.allColorValues[i].brightness.toLowerCase().replace('-', ' ')).then(resp=>{
                            if (resp.data.entries.length > 0) {
                                let methodEntry = resp.data.entries.filter(function(each) {
                                    return each.resultAnnotations.some(e => e.property === "http://www.geneontology.org/formats/oboInOwl#id") == true;
                                })[0];
                                if (methodEntry) {
                                    for (var j = 0; j < methodEntry.resultAnnotations.length; j ++) {
                                        if (methodEntry.resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                            console.log(app.allColorValues[i].id, app.allColorValues[i].brightness, methodEntry.resultAnnotations[j].value);
                                            axios.post("/chrecorder/public/api/v1/setColorBrightnessIRI", {id: app.allColorValues[i].id, IRI: methodEntry.resultAnnotations[j].value});
                                            break;
                                        }
                                    }
                                }
                            }
                        });
                    }
                    if (app.allColorValues[i].reflectance && app.allColorValues[i].reflectance != "") {
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term='+app.allColorValues[i].reflectance.toLowerCase().replace('-', ' ')).then(resp=>{
                            if (resp.data.entries.length > 0) {
                                let methodEntry = resp.data.entries.filter(function(each) {
                                    return each.resultAnnotations.some(e => e.property === "http://www.geneontology.org/formats/oboInOwl#id") == true;
                                })[0];
                                if (methodEntry) {
                                    for (var j = 0; j < methodEntry.resultAnnotations.length; j ++) {
                                        if (methodEntry.resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                            console.log(app.allColorValues[i].id, app.allColorValues[i].reflectance, methodEntry.resultAnnotations[j].value);
                                            axios.post("/chrecorder/public/api/v1/setColorReflectanceIRI", {id: app.allColorValues[i].id, IRI: methodEntry.resultAnnotations[j].value});
                                            break;
                                        }
                                    }
                                }
                            }
                        });
                    }
                    if (app.allColorValues[i].saturation && app.allColorValues[i].saturation != "") {
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term='+app.allColorValues[i].saturation.toLowerCase().replace('-', ' ')).then(resp=>{
                            if (resp.data.entries.length > 0) {
                                let methodEntry = resp.data.entries.filter(function(each) {
                                    return each.resultAnnotations.some(e => e.property === "http://www.geneontology.org/formats/oboInOwl#id") == true;
                                })[0];
                                if (methodEntry) {
                                    for (var j = 0; j < methodEntry.resultAnnotations.length; j ++) {
                                        if (methodEntry.resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                            console.log(app.allColorValues[i].id, app.allColorValues[i].saturation, methodEntry.resultAnnotations[j].value);
                                            axios.post("/chrecorder/public/api/v1/setColorSaturationIRI", {id: app.allColorValues[i].id, IRI: methodEntry.resultAnnotations[j].value});
                                            break;
                                        }
                                    }
                                }
                            }
                        });
                    }
                    if (app.allColorValues[i].colored && app.allColorValues[i].colored != "") {
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term='+app.allColorValues[i].colored.toLowerCase().replace('-', ' ')).then(resp=>{
                            if (resp.data.entries.length > 0) {
                                let methodEntry = resp.data.entries.filter(function(each) {
                                    return each.resultAnnotations.some(e => e.property === "http://www.geneontology.org/formats/oboInOwl#id") == true;
                                })[0];
                                if (methodEntry) {
                                    for (var j = 0; j < methodEntry.resultAnnotations.length; j ++) {
                                        if (methodEntry.resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                            console.log(app.allColorValues[i].id, app.allColorValues[i].colored, methodEntry.resultAnnotations[j].value);
                                            axios.post("/chrecorder/public/api/v1/setColorColoredIRI", {id: app.allColorValues[i].id, IRI: methodEntry.resultAnnotations[j].value});
                                            break;
                                        }
                                    }
                                }
                            }
                        });
                    }
                    if (app.allColorValues[i].multi_colored && app.allColorValues[i].multi_colored != "") {
                        axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term='+app.allColorValues[i].multi_colored.toLowerCase().replace('-', ' ')).then(resp=>{
                            if (resp.data.entries.length > 0) {
                                let methodEntry = resp.data.entries.filter(function(each) {
                                    return each.resultAnnotations.some(e => e.property === "http://www.geneontology.org/formats/oboInOwl#id") == true;
                                })[0];
                                if (methodEntry) {
                                    for (var j = 0; j < methodEntry.resultAnnotations.length; j ++) {
                                        if (methodEntry.resultAnnotations[j].property == 'http://www.geneontology.org/formats/oboInOwl#id') {
                                            console.log(app.allColorValues[i].id, app.allColorValues[i].multi_colored, methodEntry.resultAnnotations[j].value);
                                            axios.post("/chrecorder/public/api/v1/setColorMultiColoredIRI", {id: app.allColorValues[i].id, IRI: methodEntry.resultAnnotations[j].value});
                                            break;
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            },
            handleDisputeTerm() {
                var app = this;
                app.messageDialogFlag = true;
                app.disputedTerm = app.currentResolveItem['deprecate term']
                app.disputeMessage = "";
                app.resolveItemFlag = false;
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
                app.disputeMessage = "";
            },
            getDefinition(data) {
                var app = this;
                if (data.data.details) {
                    for (var i = 0; i < data.data.details.length; i ++) {
                        if (data.data.details[i].IRI && data.data.details[i].definition) {
                            app.definitionData.push({
                                'IRI': data.data.details[i].IRI,
                                'definition': data.data.details[i].definition
                            });
                        }
                    }
                }
                if (data.children) {
                    for (var i = 0; i < data.children.length; i ++) {
                        app.getDefinition(data.children[i]);
                    }
                }
            },
            resetSystem() {
                // window.location.reload();
                var app = this;
                axios.get("/chrecorder/public/api/v1/resetSystem")
                .then((resp) => {
                    // console.log(resp);
                    alert("Click OK to reset the system");
                    window.location.reload()
                });
            }
        },
        watch: {
            colorTreeData(newData) {
                this.treeData = newData;
                // do data transformations etc
                // trigger UI refresh
            },
            nonColorTreeData(newData) {
                this.textureTreeData = newData;
            },

        },
        async created() {
            var app = this;
            app.generateDescriptionTooltip = '<div>Click this button to generate a textual description of the taxon based on the matrix. Use up/down arrow in the matrix to adjust the order of the characters shown in the text</div>';
            // app.isLoading = true;
            await axios.get("http://shark.sbs.arizona.edu:8080/carex/getTree")
            .then(function (resp) {
                app.treeResult = resp.data;
                console.log("treeResult", app.treeResult);
                app.getDefinition(app.treeResult);
            });
            console.log("definitnionData", app.definitionData);
            await axios.get("http://shark.sbs.arizona.edu:8080/carex/getDeprecatedClasses")
                .then(function (resp) {
                    app.deprecatedTerms = resp.data['deprecated classes'];
                    console.log('app.deprecatedTerms', app.deprecatedTerms);
                });
            console.log('standard_characters');
            await axios.get('/chrecorder/public/api/v1/standard_characters')
                .then(async function (resp) {
                    console.log('standardCharacters', resp);
                    app.defaultCharacters = resp.data;
                    var toolTopIndex = 0;
                    for (var i = 0; i < resp.data.length; i++) {
                        var temp = {};
                        temp.name = resp.data[i].name;
                        if (resp.data[i].standard == 1) {
                            if (toolTopIndex % 4 == 0) {
                                app.standardCharactersTooltip += '<div class="row">';
                            }
                            app.standardCharactersTooltip += '<div class="col-md-3">';
                            app.standardCharactersTooltip = app.standardCharactersTooltip + '<h6>' + resp.data[i].name + '</h6>';
                            app.standardCharactersTooltip += '</div>';
                            if (toolTopIndex % 4 == 3) {
                                app.standardCharactersTooltip += '</div>';
                            }
                            toolTopIndex ++;
                        }
                        temp.text = resp.data[i].name + ' by ' + resp.data[i].username + ' (' + resp.data[i].usage_count + ')';
                        temp.value = resp.data[i].id;
                        temp.tooltip = '';

                        if (resp.data[i].method_from != null && resp.data[i].method_from != '') {
                            temp.tooltip = temp.tooltip + 'From: ' + resp.data[i].method_from + ', ';
                        }
                        if (resp.data[i].method_to != null && resp.data[i].method_to != '') {
                            temp.tooltip = temp.tooltip + 'To: ' + resp.data[i].method_to + ', ';
                        }
                        if (resp.data[i].method_include != null && resp.data[i].method_include != '') {
                            temp.tooltip = temp.tooltip + 'Include: ' + resp.data[i].method_include + ', ';
                        }
                        if (resp.data[i].method_exclude != null && resp.data[i].method_exclude != '') {
                            temp.tooltip = temp.tooltip + 'Exclude: ' + resp.data[i].method_exclude + ', ';
                        }
                        if (resp.data[i].method_where != null && resp.data[i].method_where != '') {
                            temp.tooltip = temp.tooltip + 'Where: ' + resp.data[i].method_where;
                        }
                        // var definitionKey = temp.name.slice(0, temp.name.indexOf(' '));
                        // var definitionVar = app.definitionData.find(eachDefinition => eachDefinition.IRI == ('http://biosemantics.arizona.edu/ontologies/carex#' + (definitionKey == 'Color' ? 'coloration' : definitionKey.toLowerCase)));
                        // if (definitionVar) {
                        //     temp.tooltip += definitionKey + ' is defined as ' + definitionVar.definition;
                        // }
                        temp.deprecated = app.deprecatedTerms.findIndex(value => value['deprecated IRI'] == resp.data[i].IRI);
                        app.standardCharacters.push(temp);
                    }
                    console.log('v1/character');
                    axios.get("/chrecorder/public/api/v1/character/" + app.user.id)
                        .then(function (resp) {
                            console.log('resp character', resp.data);
                            app.userCharacters = resp.data.characters;
                            app.headers = resp.data.headers;
                            app.values = resp.data.values;
                            console.log('headers', app.headers);
                            console.log('values', app.values);
                            app.allColorValues = resp.data.allColorValues;
                            app.allNonColorValues = resp.data.allNonColorValues;
                            if (resp.data.taxon != null) {
                                app.taxonName = resp.data.taxon;
                            }
                            app.columnCount = resp.data.headers.length - 1;
                            if (app.columnCount == 0) {
                                app.columnCount = 3;
                            }
                            if (app.values.find(value => value.header_id != 1)) {
                                if (app.values.find(value => value.header_id != 1).length != 0) {
                                    app.matrixShowFlag = true;
                                    app.collapsedFlag = true;

                                }
                            }

                            app.refreshUserCharacters();
                            if (app.deprecatedTagName && app.deprecatedTagName != '') {
                                app.showTableForTab(app.deprecatedTagName);
                            } else {
                                if (app.userTags[0]) {
                                    app.showTableForTab(app.userTags[0].tag_name);
                                }                            
                            }
                            // if (app.deprecatedTagName && app.deprecatedTagName != '') {
                            //     app.showTableForTab(app.deprecatedTagName);
                            // } else {
                            //     if (app.currentTab!='') {
                            //         app.showTableForTab(app.currentTab);
                            //     }
                            // }
                        });
                }).then(function () {
                    for (var i = 0; i < app.standardCharacters.length; i ++) {
                        app.getTooltipImage(app.standardCharacters[i].name, i);
                    }
                    app.isLoading = false;
                    // console.log(app.userCharacters.length);
                    // for (var i = 0; i < app.userCharacters.length; i ++) {
                    //     app.getTooltipImageUserChracter(app.userCharacters[i].name, i);
                    // }
                });

            axios.get('http://shark.sbs.arizona.edu:8080/carex/getSubclasses?baseIri=http://biosemantics.arizona.edu/ontologies/carex&term=quality')
                .then(function (resp) {
                    console.log('colorationData', resp.data);
                    var colorData = resp.data.children.find(ch=>ch.text=="coloration").children[0].children;
                    for (var i = 0; i < colorData.length; i++) {
                        app.colorationData[colorData[i]['text']] = [];
                        app.colorationData[colorData[i]['text']].push(colorData[i]['text']);
                        if ('children' in colorData[i]) {
                            for (var j = 0; j < colorData[i].children.length; j++) {
                                app.colorationData[colorData[i]['text']].push(colorData[i].children[j].text);
                            }
                        }
                    }
                    
                    var qualityData = resp.data.children;
                    for (var i = 0; i < qualityData.length; i++) {
                        if (qualityData[i].text=='coloration') {
                            continue;
                        }
                        app.nonColorationData[qualityData[i]['text']] = {};
                        if ('children' in qualityData[i]) {
                            for (var j = 0; j < qualityData[i].children.length; j++) {
                                app.nonColorationData[qualityData[i]['text']][qualityData[i].children[j].text]=[];
                                app.nonColorationData[qualityData[i]['text']][qualityData[i].children[j].text].push(qualityData[i].children[j].text);
                                
                                if ('children' in qualityData[i].children[j]) {
                                    for (var k = 0; k < qualityData[i].children[j].children.length; k++) {
                                        app.nonColorationData[qualityData[i]['text']][qualityData[i].children[j].text].push(qualityData[i].children[j].children[k].text);

                                        if ('children' in qualityData[i].children[j].children[k]) {
                                            for (var l = 0; l < qualityData[i].children[j].children[k].children.length; l++) {
                                                app.nonColorationData[qualityData[i]['text']][qualityData[i].children[j].text].push(qualityData[i].children[j].children[k].children[l].text);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    console.log('app.nonColorationData', app.nonColorationData);
                    //app.updateDescription();
                });
            axios.get("/chrecorder/public/api/v1/user-tag/" + app.user.id)
                .then(function (resp) {
                    resp.data.sort((a,b) => app.tagOrder(a)-app.tagOrder(b));
                    app.userTags = resp.data;
                    
                    for (var i = 0; i < app.userTags.length; i ++) {
                        console.log(app.userTags[i].tag_name);
                        app.tagDeprecated[app.userTags[i].tag_name] = app.isDeprecatedExistOnTab(app.userTags[i].tag_name);
                    }

                    if (app.deprecatedTagName && app.deprecatedTagName != '') {
                        app.showTableForTab(app.deprecatedTagName);
                    } else {
                        if (app.userTags[0]) {
                            app.showTableForTab(app.userTags[0].tag_name);
                        }                            
                    }
                    console.log('userTags', app.userTags);
                });
            axios.get("/chrecorder/public/api/v1/getMatrixNames")
                .then((resp) => {
                    console.log('matrixNames', resp);
                    app.namesList = resp.data;
                })
        },
        mounted() {
            var app = this;
            app.user.name = app.user.email.split('@')[0];
            app.characterUsername = app.user.name;
            sessionStorage.setItem('userId', app.user.id);
            app.deprecatedTagName = sessionStorage.getItem('depreCatedTagName');
            app.lastLoadMatrixName = sessionStorage.getItem('lastMatrixName');
            console.log("Mounted");
            console.log("app.deprecatedTagName", app.deprecatedTagName);
            sessionStorage.removeItem('depreCatedTagName');
        }
    }

</script>