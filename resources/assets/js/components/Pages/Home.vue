<template>
    <div slot="section" class="vld-parent">
        <loading :active.sync="isLoading"
                 :is-full-page="true"
                 :width="255"
                 :height="255"
                 ></loading>
        <div class="tab-pane" id="">
            <form autocomplete="off">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div v-if="matrixShowFlag == false"
                                 style="max-width: 1000px; margin-left: auto; margin-right: auto;">
                                <h3><b>Set up your matrix</b></h3>
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
                                                     placeholder="3"> specimens.</b>
                                </div>
                                <div class="margin-top-10 row">
                                    <div class="col-md-12" style="line-height: 38px;">
                                        <b class="some-container">I 'm measuring <a class="btn btn-primary"
                                                                                    v-on:click="showStandardCharacters()"
                                                                                    >
                                                the recommended set of characters
                                        </a> <b v-tooltip="{ content:standardCharactersTooltip, classes: 'standard-tooltip'}">(what are they?) </b><br/> or</b>
                                    </div>
                                    <div class="col-md-12 margin-top-10">
                                        <b>Search/create other character:&nbsp;</b>
                                        <model-select :options="standardCharacters"
                                                      v-model="item"
                                                      placeholder="Search/create character here"
                                                      @searchchange="printSearchText"
                                                      @select="onSelect"
                                        />

                                    </div>
                                </div>
                                <hr style="border-top: 2px solid; margin-top: 20px;">
                                <div class="margin-top-10 text-right">
                                    <a class="btn btn-primary" v-on:click="generateMatrix()" style="width: 200px;">Generate
                                        Matrix</a>
                                    <a class="btn btn-primary" v-on:click="importMatrix()"
                                       style="width: 200px; background-color: grey; border-color: grey;">Import (CR)
                                        Matrix</a>
                                    <a class="btn btn-primary" v-on:click="collapsedFlag = true;"
                                       style="width: 40px;"><span class="glyphicon glyphicon-chevron-up"></span></a>
                                </div>
                                <div class="margin-top-10"
                                     v-if="userCharacters.find(ch => ch.standard == 0 && ch.username.includes(user.name))">
                                    <h4><b><u>You've selected characters:</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a class="btn btn-add display-block" v-on:click="removeAllCharacters()"><span
                                                class="glyphicon glyphicon-remove"
                                                :set="previousUserCharacter=''"></span></a>
                                    </h4>
                                    <div v-for="eachCharacter in userCharacters"
                                         v-if="eachCharacter.standard == 0 && eachCharacter.username.includes(user.name)"
                                         v-tooltip="eachCharacter.tooltip"
                                         style="display: table; font-weight: bold; cursor: pointer;">
                                          <b v-if="eachCharacter.standard_tag != previousUserCharacter.standard_tag"> 
                                             {{ eachCharacter.standard_tag }} </b>
                                         
                                            <div style="text-indent: 50px;"><i>{{ eachCharacter.name }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a class="btn btn-add display-block"
                                               v-on:click="removeStandardCharacter(eachCharacter.id)"
                                               :set="previousUserCharacter=eachCharacter"
                                               ><span
                                                    class="glyphicon glyphicon-remove"></span></a></i></div> 
                                        <!--{{ eachCharacter.name }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a class="btn btn-add display-block"
                                           v-on:click="removeUserCharacter(eachCharacter.id)"><span
                                                class="glyphicon glyphicon-remove"></span></a>-->
                                    </div>
                                </div>
                                <div class="margin-top-10"
                                     v-if="userCharacters.find(ch => ch.standard == 1 || !ch.username.includes(user.name))">
                                    <h4><b><u>You've selected recommended characters:</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>
                                        <a class="btn btn-add display-block"
                                           v-on:click="removeAllStandardFlag = true;"><span
                                                class="glyphicon glyphicon-remove"
                                                :set="previousCharacter=''"></span></a></h4>

                                    <div v-for="eachCharacter in userCharacters"
                                         v-if="eachCharacter.standard == 1 || !eachCharacter.username.includes(user.name)"
                                         v-tooltip="eachCharacter.tooltip"
                                         style="display: table; cursor: pointer;">
                                         <b v-if="eachCharacter.standard_tag != previousCharacter.standard_tag"> 
                                             {{ eachCharacter.standard_tag }} </b>
                                         
                                            <div style="text-indent: 50px;"><i>{{ eachCharacter.name }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a class="btn btn-add display-block"
                                               v-on:click="removeStandardCharacter(eachCharacter.id)"
                                               :set="previousCharacter=eachCharacter"
                                               ><span
                                                    class="glyphicon glyphicon-remove"></span></a></i></div> 
                                         
                                        <!--<b>{{ eachCharacter.name }} {{ eachCharacter.standard_tag }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a class="btn btn-add display-block"
                                               v-on:click="removeStandardCharacter(eachCharacter.id)"><span
                                                    class="glyphicon glyphicon-remove"></span></a></b> -->
                                        <!--<a-->
                                        <!--v-on:click="removeStandardCharacter(eachCharacter.id)" style="cursor: pointer;">-->
                                        <!--X </a>-->
                                    </div>
                                </div>
                                <!-- repeat the buttoms here -->
                                <div v-if="userCharacters.length!=0" class="margin-top-10 text-right">
                                    <a class="btn btn-primary" v-on:click="generateMatrix()" style="width: 200px;">Generate
                                        Matrix</a>
                                    <a class="btn btn-primary" v-on:click="importMatrix()"
                                       style="width: 200px; background-color: grey; border-color: grey;">Import (CR)
                                        Matrix</a>
                                    <a class="btn btn-primary" v-on:click="collapsedFlag = true;"
                                       style="width: 40px;"><span class="glyphicon glyphicon-chevron-up"></span></a>
                                </div>
                            </div>
                        </div>
                        <div v-if="collapsedFlag == true">
                            <div style="max-width: 1000px; margin-right: auto; margin-left: auto;">
                                <div class="col-md-1">
                                    <a class="btn btn-primary" v-on:click="collapsedFlag = false;" style="width: 40px;"><span
                                            class="glyphicon glyphicon-chevron-down"></span></a>
                                </div>
                                <div class="col-md-2">
                                    <input v-model="taxonName" v-on:blur="changeTaxonName()"
                                           style="line-height: 38px; border: none;">
                                </div>
                                <div class="col-md-3">
                                    <input v-model="columnCount" v-on:keyup.enter="changeColumnCount()"
                                           v-on:blur="changeColumnCount()"
                                           style="width: 40px; margin-left: 30px; line-height: 38px; border:none;">
                                    Specimens
                                </div>
                                <div class="col-md-5">
                                    <model-select :options="standardCharacters"
                                                  v-model="item"
                                                  placeholder="Search/create character here"
                                                  @searchchange="printSearchText"
                                                  @select="onSelect"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--<hr v-if="matrixShowFlag == true" style="margin-top: 40px; margin-bottom: 40px; border-top: 2px solid;">-->
                <div v-if="matrixShowFlag == true"
                     style="border-bottom: 2px solid; width: 100%; margin-top: 40px;"></div>

                <div style="padding-left: 15px; padding-right: 15px; display: inline-flex; width: 100%;"
                     v-if="matrixShowFlag == true">
                    <div v-bind:class="{'width-95per': descriptionFlag == false}" style="min-width: 70%;">
                        <!--<ul class="nav nav-tabs">-->
                            <draggable
                                    :list="userTags"
                                    class="nav nav-tabs">
                                    <li v-for="eachTag in userTags" v-bind:class="{ active: currentTab == eachTag.tag_name }" :key="eachTag.name">
                                        <a data-toggle="tab" v-on:click="showTableForTab(eachTag.tag_name)">
                                            {{ eachTag.tag_name}}
                                        </a>
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
                                        style="cursor: pointer; display: flex; ">
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
                                        <div style="line-height: 44px;" v-tooltip="userCharacters.find(ch => ch.id == value.character_id).tooltip">
                                            {{ value.value }}
                                        </div>
                                        <div>
                                            <a class="btn btn-add"
                                               v-on:click="editCharacter(row[row.length - 1], true, row[row.length - 1].standard)"
                                               style="line-height: 30px;">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>
                                        </div>
                                        <div class="btn-group">
                                            <a v-if="checkHaveUnit(value.value) && !value.value.startsWith('Number of') && !value.value.startsWith('Ratio of')"
                                               class="btn btn-add dropdown-toggle" style="line-height: 30px;"
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
                                    <td v-if="value.header_id != 1" v-for="value in row">
                                        <input class="td-input" v-model="value.value" v-on:focus="focusedValue(value)"
                                               v-on:blur="saveItem($event, value)"/>
                                    </td>
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
                            <a class="btn btn-default" style="border: none;" v-on:click="expandDescription()"><span
                                    style="color: #1f648b; font-weigth: bold; writing-mode: vertical-rl; text-orientation: upright; ">Generate Description</span></a>        
                        </div>
                    </div>
                    <div v-if="descriptionFlag == true"
                         style="position:relative; min-width: 25%; max-width: 600px; overflow-y: scroll; word-wrap: break-word;"
                         class="panel">
                        <div class="panel-heading"><b>Generated Description</b></div>
                        <div class="panel-body" style="min-height: 100px;" v-html="descriptionText">
                        </div>
                        <div class="text-right" style="position: absolute; right: 10px; bottom: 10px;">
                            <a class="btn btn-primary" v-on:click="updateDescription()">Update</a>
                            <a class="btn btn-primary" v-on:click="exportDescription()">Export</a>
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
                                                        <input style="height: 26px; width: 100%;" type="text"
                                                               list="first_characters" placeholder="select or type" v-model="firstCharacter"/>
                                                        <datalist id="first_characters">
                                                            <option value="Length">Length</option>
                                                            <option value="Width">Width</option>
                                                            <option value="Depth">Depth</option>
                                                            <option value="Diameter">Diameter</option>
                                                            <option value="Distance">Distance</option>
                                                            <option value="Color">Color</option>
                                                            <option value="Shape">Shape</option>
                                                            <option value="Texture">Texture</option>
                                                            <option value="Distance"></option>
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
                                                        <input v-model="lastCharacter">
                                                    </div>

                                                    <!--<input autofocus v-model="character.name" v-on:input="checkMsg"/>-->
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-primary ok-btn"
                                                   v-bind:class="{ disabled: !firstCharacter || !middleCharacter || !lastCharacter }"
                                                   v-on:click="storeCharacter()">
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
                                                <h3>Define character "{{ character.name }}", created by {{ character.username
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
                                                                        :disabled="!checkHaveUnit(character.name)"
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
                                                            <li class="summary"><a
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
                                                           class="btn btn-primary">Save</a>
                                                        <a v-if="viewFlag == true" v-on:click="use(item)"
                                                           class="btn btn-primary">Use this</a>
                                                        <a v-if="viewFlag == true" v-on:click="enhance(item)"
                                                           class="btn btn-primary">Clone and enhance</a>
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
                                <div class="modal-mask character-modal">
                                    <div class="modal-wrapper">
                                        <div class="modal-container">
                                            <div class="modal-header">
                                                Confirm Method
                                            </div>
                                            <div class="modal-body">
                                                <div v-if="!character.method_as">
                                                    <div>
                                                        Please <b>review the method definition carefully</b>. Is this what
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
                                                        Please <b>review the method definition carefully</b>. Is this what
                                                            you
                                                            would like
                                                            to save for <i>{{ character.name }}</i>?
                                                    </div>
                                                    <div>
                                                        <img class="img-method"
                                                             style="width: 100%;"
                                                             v-bind:src="'https://drive.google.com/uc?id=' + character.method_as.split('id=')[1].substring(0, character.method_where.split('id=')[1].length)"/>
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
                                <div class="modal-mask character-modal">
                                    <div class="modal-wrapper">
                                        <div class="modal-container">
                                            <div class="modal-header">
                                                Confirm Unit
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
                            <div class="modal-mask character-modal">
                                <div class="modal-wrapper">
                                    <div class="modal-container">
                                        <div class="modal-header">
                                            Confirm Tag
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
                        <div class="modal-mask character-modal">
                            <div class="modal-wrapper">
                                <div class="modal-container">
                                    <div class="modal-header">
                                        Confirm Summary Function
                                    </div>
                                    <div class="modal-body">
                                        <div v-if="character.summary">
                                            <b>{{ character.summary }}</b> will be used as the Summary Function for <i>{{
                                            character.name }}</i>.
                                        </div>
                                        <div v-if="!character.summary">
                                            Summary function not selected. Categorical characters do not need a summary function.
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
                        <div class="modal-mask character-modal">
                            <div class="modal-wrapper">
                                <div class="modal-container">
                                    <div class="modal-header">
                                        Confirm to Remove Recommended Characters
                                    </div>
                                    <div class="modal-body">
                                        <div>
                                            Do you want to remove all recommended characters from your matrix?
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
                        </div>
                    </transition>
                </div>
                <div v-if="colorDetailsFlag" @close="colorDetailsFlag = false">
                    <transition name="modal">
                        <div class="modal-mask">
                            <div class="modal-wrapper">
                                <div class="modal-container">
                                    <div class="modal-header">
                                        Formulate a value: type in a blank to select existing phrases or use your own terms(need
                                        definition)
                                    </div>
                                    <div class="modal-body">
                                        <div v-for="(eachColor, index) in colorDetails">
                                            <div>
                                                <div style="display: inline-block;">
                                                    <input v-on:focus="changeColorSection(eachColor, 'negation', $event)"
                                                           style="width: 90px; border:none; border-bottom: 1px solid; text-align:center;"
                                                           v-model="eachColor.negation" placeholder="">
                                                    <h5>
                                                        negation
                                                    </h5>
                                                </div>
                                                <div style="display: inline-block;">
                                                    <input v-on:focus="changeColorSection(eachColor, 'pre_constraint', $event)"
                                                           style="width: 90px;"
                                                           v-model="eachColor.pre_constraint"
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
                                                    <input v-on:focus="changeColorSection(eachColor, 'brightness', $event)"
                                                           v-on:keyup.enter="searchColorSelection(eachColor, 'brightness')"
                                                           style="width: 90px; border:none; border-bottom: 1px solid; text-align:center;"
                                                           v-model="eachColor.brightness" 
                                                           class="color-input">
                                                    <h5>
                                                        brightness
                                                    </h5>
                                                </div>
                                                <div style="display: inline-block;">
                                                    <input v-on:focus="changeColorSection(eachColor, 'reflectance', $event)"
                                                           v-on:keyup.enter="searchColorSelection(eachColor, 'reflectance')"
                                                           style="width: 90px; border:none; border-bottom: 1px solid; text-align:center;"
                                                           v-model="eachColor.reflectance" 
                                                           class="color-input">
                                                    <h5>
                                                        reflectance
                                                    </h5>
                                                </div>
                                                <div style="display: inline-block;">
                                                    <input v-on:focus="changeColorSection(eachColor, 'saturation', $event)"
                                                           v-on:keyup.enter="searchColorSelection(eachColor, 'saturation')"
                                                           style="width: 90px; border:none; border-bottom: 1px solid; text-align:center;"
                                                           v-model="eachColor.saturation" 
                                                           class="color-input">
                                                    <h5>
                                                        saturation
                                                    </h5>
                                                </div>
                                                <div style="display: inline-block;">
                                                    <input v-on:focus="changeColorSection(eachColor, 'colored', $event)"
                                                           v-on:keyup.enter="searchColorSelection(eachColor, 'colored')"
                                                           style="width: 90px; border:none; border-bottom: 1px solid; text-align:center;"
                                                           v-model="eachColor.colored" 
                                                           class="color-input">
                                                    <h5>
                                                        color
                                                    </h5>
                                                </div>
                                                <div style="display: inline-block;">
                                                    <input v-on:focus="changeColorSection(eachColor, 'multi_colored', $event)"
                                                           v-on:keyup.enter="searchColorSelection(eachColor, 'multi_colored')"
                                                           style="width: 90px; border:none; border-bottom: 1px solid; text-align:center;"
                                                           v-model="eachColor.multi_colored" 
                                                           class="color-input">
                                                    <h5>
                                                        pattern
                                                    </h5>
                                                </div>
                                                <div style="display: inline-block;">
                                                    <input v-on:focus="changeColorSection(eachColor, 'post_constraint', $event)"
                                                           style="width: 90px;"
                                                           v-model="eachColor.post_constraint"
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
                                            <div v-if="eachColor.detailFlag == 'negation'" style="margin-top: 10px;">
                                                <input type="radio" id="not" v-model="eachColor.negation"
                                                       v-bind:value="'Not'"/> <label for="not">Not</label> <br/>
                                                <input type="radio" id="unselect-not" v-model="eachColor.negation"
                                                       v-bind:value="''"/> <label for="unselect-not">Unselect
                                                Not</label>
                                            </div>
                                            <div v-if="(eachColor.detailFlag == 'brightness'
                                            || eachColor.detailFlag == 'reflectance'
                                            || eachColor.detailFlag == 'saturation'
                                            || eachColor.detailFlag == 'colored'
                                            || eachColor.detailFlag == 'multi_colored') && colorExistFlag"
                                                 style="margin-top: 10px;">
                                                <!--<input style="width: 300px;" v-model="colorSearchText" placeholder="Enter a term to filter the term tree"/>-->
                                                <tree
                                                        :data="treeData"
                                                        :options="colorTreeOption"
                                                        :filter="eachColor[eachColor.detailFlag]"
                                                        ref="tree"
                                                        @node:selected="onTreeNodeSelected"
                                                        style="max-height: 300px;">
                                                    <div slot-scope="{ node }" class="node-container">
                                                        <div class="node-text" v-tooltip="node.text">{{ node.text }}
                                                        </div>
                                                    </div>
                                                </tree>
                                            </div>
                                            <div v-if="(eachColor.detailFlag == 'brightness'
                                            || eachColor.detailFlag == 'reflectance'
                                            || eachColor.detailFlag == 'saturation'
                                            || eachColor.detailFlag == 'colored'
                                            || eachColor.detailFlag == 'multi_colored') && !colorExistFlag"
                                                 style="margin-top: 10px;">

                                                <div v-if="searchColorFlag == 1">
                                                    <div v-for="eachSynonym in colorSynonyms">
                                                        <b>Did you mean?</b>
                                                        <input type="radio" v-bind:id="eachSynonym.term"
                                                               v-bind:value="eachSynonym.term"
                                                               v-model="eachColor[eachColor.detailFlag]">
                                                        <label v-bind:for="eachSynonym.term"> {{ eachSynonym.term }} ({{
                                                            eachSynonym.parentTerm }}): </label> {{
                                                        eachSynonym.definition ? eachSynonym.definition : 'No definition' }}
                                                        <a class="btn"
                                                           v-on:click="expandCommentSection(eachSynonym, eachColor.detailFlag)"><span
                                                                class="glyphicon glyphicon-comment"></span></a>
                                                        <div v-if="eachSynonym.commentFlag == true">
                                                            Do not like this term? improve or add definition for it:
                                                            <input
                                                                    v-model="colorComment[index][eachColor.detailFlag]"
                                                                    style="width: 100%;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div v-if="searchColorFlag == 2">
                                                    Did you mean <b>{{ exactColor.term }}</b>?<br/>
                                                    Current Definition: <input
                                                        v-model="colorDefinition[index][eachColor.detailFlag]"
                                                        style="width: 70%;">
                                                    <!--Hong modified this: Did you mean <b>{{ exactColor.term }}</b>?<br/>
                                                    Current Definition: <input
                                                        v-model="app.exactColor.definition"
                                                        style="width: 70%;">   --> 
                                                    <br/>If current defintion is not displayed, feel free to add a definition above.

                                                </div>
                                                <div v-if="searchColorFlag !=2 ">
                                                    <input type="radio" id="user-defined"
                                                           v-bind:value="defaultColorValue + '(user defined)'"
                                                           v-on:change="selectUserDefinedTerm(eachColor, eachColor.detailFlag, defaultColorValue)"
                                                           v-model="eachColor[eachColor.detailFlag]">
                                                    <label for="user-defined">Use my term (please define the term, all input required):</label>
                                                    <div for="user-defined">
                                                        Definition: <input
                                                            v-model="userColorDefinition[index][eachColor.detailFlag]"
                                                            class="color-definition-input">
                                                        Used for Taxon:
                                                        <input v-model="colorTaxon[index][eachColor.detailFlag]"
                                                               class="color-definition-input">
                                                        Sample Sentence:
                                                        <input
                                                            v-model="colorSampleText[index][eachColor.detailFlag]"
                                                            class="color-definition-input" placeholder=""><br/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="row">
                                            <div class="col-md-6" style="text-align: left;">
                                                <a class="btn btn-primary ok-btn"
                                                   v-on:click="removeColorValue()">
                                                    Remove Value </a>
                                            </div>
                                            <div class="col-md-6">
                                                <a class="btn btn-primary ok-btn"
                                                   v-on:click="saveColorValue()">
                                                    Save </a>
                                                <!--<a class="btn btn-primary ok-btn"-->
                                                   <!--v-on:click="saveNewColorValue()">-->
                                                    <!--Save & New </a>-->
                                                <a v-on:click="colorDetailsFlag = false;" class="btn btn-danger">Cancel</a>
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
                                    <div class="modal-header">
                                        Formulate a value: select existing phrases or use your own terms(need
                                        definition)
                                    </div>
                                    <div class="modal-body">
                                        <div v-for="(eachValue, index) in nonColorDetails">
                                            <div>
                                                <div style="display: inline-block;">
                                                    <input v-on:focus="changeNonColorSection(eachValue, 'negation', $event)"
                                                           style="width: 90px; border:none; border-bottom: 1px solid; text-align:center;"
                                                           v-model="eachValue.negation" placeholder="">
                                                    <h5>
                                                        negation
                                                    </h5>
                                                </div>
                                                <div style="display: inline-block;">
                                                    <input v-on:focus="changeNonColorSection(eachValue, 'pre_constraint', $event)"
                                                           style="width: 90px;"
                                                           v-model="eachValue.pre_constraint"
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
                                                    <input v-on:focus="changeNonColorSection(eachValue, 'main_value', $event)"
                                                           v-on:keyup.enter="searchNonColorSelection(eachValue, 'main_value')"
                                                           style="width: 90px; border:none; border-bottom: 1px solid; text-align:center;"
                                                           v-model="eachValue.main_value"
                                                           :placeholder="eachValue.placeholderName">
                                                    <h5>
                                                        {{ eachValue.placeholderName }}
                                                    </h5>
                                                </div>
                                                <div style="display: inline-block;">
                                                    <input v-on:focus="changeNonColorSection(eachValue, 'post_constraint', $event)"
                                                           style="width: 90px;"
                                                           v-model="eachValue.post_constraint"
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
                                            <div v-if="eachValue.detailFlag == 'negation'" style="margin-top: 10px;">
                                                <input type="radio" id="non-not" v-model="eachValue.negation"
                                                       v-bind:value="'Not'"/> <label for="non-not">Not</label> <br/>
                                                <input type="radio" id="non-unselect-not" v-model="eachValue.negation"
                                                       v-bind:value="''"/> <label for="non-unselect-not">Unselect
                                                Not</label>
                                            </div>
                                            <div v-if="(eachValue.detailFlag == 'main_value') && nonColorExistFlag"
                                                 style="margin-top: 10px;">
                                                <!--<input style="width: 300px;" v-model="nonColorSearchText" placeholder="Enter a term to filter the term tree"/>-->
                                                <tree
                                                        :data="textureTreeData"
                                                        :options="colorTreeOption"
                                                        :filter="eachValue.main_value"
                                                        ref="tree"
                                                        @node:selected="onTextureTreeNodeSelected"
                                                        style="max-height: 300px;">
                                                    <div slot-scope="{ node }" class="node-container">
                                                        <div class="node-text" v-tooltip="node.text">{{ node.text }}
                                                        </div>
                                                    </div>
                                                </tree>
                                            </div>
                                            <div v-if="(eachValue.detailFlag == 'main_value') && !nonColorExistFlag"
                                                 style="margin-top: 10px;">

                                                <div v-if="searchNonColorFlag == 1">
                                                    <div v-for="eachSynonym in nonColorSynonyms">
                                                        <b>Did you mean? </b>
                                                        <input type="radio" v-bind:id="eachSynonym.term"
                                                               v-bind:value="eachSynonym.term"
                                                               v-model="eachValue[eachValue.detailFlag]">
                                                        <label v-bind:for="eachSynonym.term">{{ eachSynonym.term }} ({{
                                                            eachSynonym.parentTerm }}): </label> {{
                                                        eachSynonym.definition ? eachSynonym.definition : 'No definition' }}
                                                        <a class="btn"
                                                           v-on:click="expandCommentSection(eachSynonym, eachValue.detailFlag)"><span
                                                                class="glyphicon glyphicon-comment"></span></a>
                                                        <div v-if="eachSynonym.commentFlag == true">
                                                            Don't you like this term? improve or add definition for it:
                                                            <input
                                                                    v-model="nonColorComment[index][eachValue.detailFlag]"
                                                                    style="width: 100%;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div v-if="searchNonColorFlag == 2">
                                                    Here is what we known about <b>{{ exactNonColor.term }}</b>
                                                    Definition: <input
                                                        v-model="nonColorDefinition[index][eachValue.detailFlag]"
                                                        style="width: 70%;">

                                                </div>
                                                <div v-if="searchNonColorFlag !=2 ">
                                                    <input type="radio" id="non-user-defined"
                                                           v-bind:value="defaultNonColorValue + '(user defined)'"
                                                           v-on:change="selectUserDefinedTerm(eachValue, eachValue.detailFlag, defaultNonColorValue)"
                                                           v-model="eachValue[eachValue.detailFlag]">
                                                    <label for="non-user-defined">Use my term (please define the term, all input required):</label>
                                                    <div for="user-defined">
                                                        Definition: <input
                                                            v-model="userNonColorDefinition[index][eachValue.detailFlag]"
                                                            class="non-color-input-definition">
                                                        Taxon: <input
                                                            v-model="nonColorTaxon[index][eachValue.detailFlag]"
                                                            class="non-color-input-definition">
                                                        Sample Sentence: <input
                                                            v-model="nonColorSampleText[index][eachValue.detailFlag]"
                                                            class="non-color-input-definition">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="row">
                                            <div class="col-md-6" style="text-align: left;">
                                                <a class="btn btn-primary ok-btn"
                                                   v-on:click="removeNonColorValue()">
                                                    Remove Value </a>
                                            </div>
                                            <div class="col-md-6">
                                                <a class="btn btn-primary ok-btn"
                                                   v-on:click="saveNonColorValue()">
                                                    Save </a>
                                                <!--<a class="btn btn-primary ok-btn"-->
                                                   <!--v-on:click="saveNewNonColorValue()">-->
                                                    <!--Save & New </a>-->
                                                <a v-on:click="nonColorDetailsFlag = false;"
                                                   class="btn btn-danger">Cancel</a>
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

    import {ModelSelect} from '../../libs/vue-search-select-lib'

    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    import LiquorTree from 'liquor-tree';

    import draggable from 'vuedraggable'

    Vue.use(LiquorTree);
    Vue.use({ModelSelect});
  


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
                character: {},
                userCharacters: [],
                defaultCharacters: [],
                standardCharacters: [],
                item: null,
                searchText: null,
                standardShowFlag: false,
                firstCharacter: null,
                middleCharacter: null,
                lastCharacter: null,
                newCharacterFlag: false,
                detailsFlag: false,
                metadataFlag: null,
                currentMetadata: null,
                parentData: null,
                viewFlag: false,
                standardCharactersTooltip: '',
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
                colorSearchText: null,
                colorTreeOption: {
                    multiple: true,
                    autoCheckChildren: false,
                    parentSelect: false,
                },
                colorExistFlag: false,
                searchColor: [],
                colorSynonyms: [],
                colorExactSynonyms: [],
                colorExactMatch: [],
                searchColorFlag: 0,
                exactColor: {},
                colorDetailId: null,
                defaultColorValue: '',
                colorComment: [],
                colorTaxon: [],
                colorSampleText: [],
                colorDefinition: [],
                userColorDefinition: [],
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
                nonColorComment: [],
                nonColorTaxon: [],
                nonColorSampleText: [],
                nonColorDefinition: [],
                userNonColorDefinition: [],
                searchNonColorFlag: 0,
                sharedFlag: true,
            }
        },
        components: {
            ModelSelect,
            draggable,
            Loading
        },

        methods: {
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
                app.searchText = searchText;
            },
            onSelect(selectedItem) {
                var app = this;
                var selectedCharacter = app.defaultCharacters.find(ch => ch.id == selectedItem)

                if (!selectedCharacter) {
                    selectedCharacter = app.userCharacters.find(ch => ch.id == selectedItem);
                } else if (app.userCharacters.find(ch => ch.name == selectedCharacter.name)) {
                    selectedCharacter = app.userCharacters.find(ch => ch.name == selectedCharacter.name);
                }
                if (!selectedCharacter) {
                    app.firstCharacter = '';
                    app.middleCharacter = '';
                    app.lastCharacter = '';

                    app.newCharacterFlag = true;
                    app.viewFlag = false;
                    sessionStorage.setItem('viewFlag', false);
                    sessionStorage.setItem('edit_created_other', false);
                } else {
                    app.character = selectedCharacter;
                    app.item = selectedItem;
                    console.log('selectedCharacter.username', app.character.username.substr(app.character.username.length - app.user.name.length));

                    if (app.character.username.includes(app.user.name)) {
//                        app.viewFlag = false;
//                        sessionStorage.setItem('viewFlag', false);
//                        sessionStorage.setItem('edit_created_other', false);
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
            editCharacter(character, editFlag = false, standardFlag = false) {
                var app = this;
                app.editFlag = editFlag;
                console.log('app.editFlag', app.editFlag);
                if (editFlag) {
                    app.viewFlag = !editFlag;
                    sessionStorage.setItem('viewFlag', !editFlag);
                    sessionStorage.setItem('edit_created_other', !editFlag);
                    app.character = app.userCharacters.find(ch => ch.id == character.character_id);
//                    app.character.standard = 0;
                } else {
                    app.character = character;
//                    app.character.standard = 0;
                }

                if (standardFlag || (editFlag && !app.character.username.includes(app.user.name))) {
                    app.editFlag = false;
                    app.viewFlag = true;
                    sessionStorage.setItem('viewFlag', true);
                    sessionStorage.setItem('edit_created_other', true);
                }
                app.item = app.character.id;
                sessionStorage.setItem("characterName", app.character.name);

                if (app.checkHaveUnit(app.character.name)) {
                    app.parentData = [];
                    app.parentData.push(app.character.method_as);
                    app.parentData[3] = app.user;
                    app.parentData[4] = app.character.method_from;
                    app.parentData[5] = app.character.method_to;
                    app.parentData[6] = app.character.method_include;
                    app.parentData[7] = app.character.method_exclude;
                    app.parentData[8] = app.character.method_where;
                    app.metadataFlag = 'method';
                    app.currentMetadata = method;
                } else {
                    app.parentData = app.character.standard_tag;
                    app.metadataFlag = 'tag';
                    app.currentMetadata = tag;
                }

                app.detailsFlag = true;
            },
            storeCharacter() {
                var app = this;
                app.character = {};
                app.character.name = app.firstCharacter + ' ' + app.middleCharacter + ' ' + app.lastCharacter;
                app.character.username = app.user.name;
                app.characterUsername = app.user.name;
                app.character.standard = 0;
                app.character.creator = app.user.name + ' via CR';


                if (app.checkHaveUnit(app.character.name)) {
                    app.parentData = [];
                    app.parentData[3] = app.user;
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

                console.log("metadata", metadata);
                console.log("app.character=", app.character);
                if (app.checkHaveUnit(app.character.name) || (metadata != 'method' && metadata != 'unit')) {
                    app.metadataFlag = metadata;
                    switch (metadata) {
                        case 'method':
                            app.parentData = [];
                            app.parentData[0] = app.character.method_as;
                            app.parentData[3] = app.user;
                            app.parentData[4] = app.character.method_from;
                            app.parentData[5] = app.character.method_to;
                            app.parentData[6] = app.character.method_include;
                            app.parentData[7] = app.character.method_exclude;
                            app.parentData[8] = app.character.method_where;
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
            showStandardCharacters() {
                var app = this;
                app.isLoading = true;
                app.standardShowFlag = !app.standardShowFlag;
                console.log('test', app.userCharacters);
                var postCharacters = [];
                for (var i = 0; i < app.defaultCharacters.length; i++) {
                    var character = app.defaultCharacters[i];
                    if (!app.userCharacters.find(ch => ch.name == character.name) && character.standard == 1) {
//                        character.username = app.user.name;
                        character.show_flag = false;
                        if (character.name.startsWith('Length of')
                            || character.name.startsWith('Width of')
                            || character.name.startsWith('Depth of')
                            || character.name.startsWith('Diameter of')
                            || character.name.startsWith('Count of')
                            || character.name.startsWith('Distance between')) {
                            character.unit = 'cm';
                            character.summary = 'mean';
                        } else if (character.name.startsWith('Number of')
                            || character.name.startsWith('Ratio of')) {
                            character.unit = '';
                            character.summary = 'mean';
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
                    });
            },
            removeStandardCharacter(characterId) {
                var app = this;

                axios.post("/chrecorder/public/api/v1/character/delete/" + app.user.id + "/" + characterId)
                    .then(function (resp) {
                        app.userCharacters = resp.data.characters;
                        app.headers = resp.data.headers;
                        app.values = resp.data.values;
                        if (app.userCharacters.length == 0) {
                            app.matrixShowFlag = false;
                        }
                        app.refreshUserCharacters();

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
                                });
                        }
                        app.refreshUserCharacters();

                    });
            },
            removeAllCharacters() {
                var app = this;
                app.isLoading = true;
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
                    });
            },
            saveCharacter(metadataFlag) {
                var app = this;
                console.log('app.character = ', app.character);
                console.log('saveCharacter', metadataFlag);

                if (app.character.summary == ''
                    || app.character.summary == null
                    || app.character.summary == undefined) {
                    if (app.checkHaveUnit(app.character.name)) {
                        app.character.summary = 'mean';
                    } else {
                        app.character.summary = '';
                    }
                }

//                if (app.character['id'] && !app.editFlag) {
//                    delete app.character['id'];
//                }

                var checkFields = true;
                if ((this.character['method_from'] == null || this.character['method_from'] == '') &&
                    (this.character['method_to'] == null || this.character['method_to'] == '') &&
                    (this.character['method_include'] == null || this.character['method_include'] == '') &&
                    (this.character['method_exclude'] == null || this.character['method_exclude'] == '') &&
                    (this.character['method_where'] == null || this.character['method_where'] == '')) {
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
            },
            use(characterId) {
                var app = this;
                console.log('use', characterId);
                app.character = app.userCharacters.find(ch => ch.id == characterId);
                if (!app.character) {
                    app.character = app.defaultCharacters.find(ch => ch.id == characterId);
                    if (!app.userCharacters.find(ch => ch.name == app.character.name)) {
                        console.log('app.character', app.character);
//                app.character.show_flag = false;
                        app.character.standard = 1;
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
                                app.character.summary = 'mean';
                            }
                        }

                        if (checkFields) {
//                    if (app.character['id']) {
//                        delete app.character['id'];
//                    }
                            if (app.checkHaveUnit(app.character.name)) {
                                app.confirmMethod = true;
                            } else {
                                app.confirmTag = true;
                            }

                        } else {
                            app.showDetails('unit', app.metadataFlag);
                        }
                    } else {
                        app.detailsFlag = false;
                    }
                } else {
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
//                selectedCharacter.username = selectedCharacter.username + ', ' + app.user.name;
//                app.characterUsername = selectedCharacter.username + ', ' + app.user.name;
                app.oldCharacter.method_from = selectedCharacter.method_from;
                app.oldCharacter.method_to = selectedCharacter.method_to;
                app.oldCharacter.method_include = selectedCharacter.method_include;
                app.oldCharacter.method_exclude = selectedCharacter.method_exclude;
                app.oldCharacter.method_where = selectedCharacter.method_where;
//                app.editFlag = true;
                selectedCharacter.creator = app.user.name + ' via CR';
                app.detailsFlag = false;
                sessionStorage.setItem('viewFlag', false);
                sessionStorage.setItem('edit_created_other', false);
                app.viewFlag = false;
                app.enhanceFlag = true;
                setTimeout(function () {
                    app.editCharacter(selectedCharacter);
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
//                        app.character.standard = 0;
//                        app.character.username = app.characterUsername;
                        if (currentCharacters.find(ch => ch.name == app.character.name)) {
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
                                        app.detailsFlag = false;
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
                                    });
                            }
                        }

                    });
                console.log("app.character", app.character);
            },
            generateMatrix() {
                var app = this;
                app.isLoading = true;
                if ((isNaN(app.columnCount) == false) && app.columnCount > 0 && app.taxonName != "") {
                    var jsonMatrix = {
                        'user_id': app.user.id,
                        'column_count': app.columnCount,
                        'taxon': app.taxonName
                    };
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
                                    app.showTableForTab(app.userTags[0].tag_name);
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
                app.isLoading = true;
                axios.post('/chrecorder/public/api/v1/delete-header/' + headerId)
                    .then(function (resp) {
                        console.log('delete header', resp.data);
                        app.headers = resp.data.headers;
                        app.values = resp.data.values;
                        app.userCharacters = resp.data.characters;
                        app.columnCount = resp.data.headers.length - 1;
                        app.isLoading = false;
                        app.refreshUserCharacters();

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
                app.isLoading = true;
                if (app.columnCount < app.headers.length - 1) {
                    app.columnCount = app.headers.length - 1;
                    app.isLoading = false;
                    alert("To reduce the size of the matrix, use the remove button (x) in the matrix.");
                } else {
                    axios.post('/chrecorder/public/api/v1/add-more-column/' + app.columnCount)
                        .then(function (resp) {
                            console.log('addMoreColumn resp', resp.data);
                            app.userCharacters = resp.data.characters;
                            app.headers = resp.data.headers;
                            app.values = resp.data.values;
                            app.taxonName = resp.data.taxon;
                            app.isLoading = false;
                            app.refreshUserCharacters();

                        });
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
                                app.values = resp.data.values;
                                app.defaultCharacters = resp.data.defaultCharacters;
                                app.refreshUserCharacters();
                                app.refreshDefaultCharacters();
                            }
                        });
                }


            },
            removeAllStandardCharacters() {
                var app = this;
                app.isLoading = true;
                axios.post('/chrecorder/public/api/v1/character/remove-all-standard')
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
                        app.refreshUserCharacters();
                    });
            },
            refreshUserCharacters (showTabFlag = false) {
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
                }
                app.characterUsername = app.user.name;
            },
            showTableForTab(tagName) {
                var app = this;
                app.isLoading = true;
                app.currentTab = tagName;
                axios.post('/chrecorder/public/api/v1/show-tab-character/' + tagName)
                    .then(function (resp) {
                        app.isLoading = false;
                        app.userCharacters = resp.data.characters;
                        app.headers = resp.data.headers;
                        app.values = resp.data.values;
//                        var height = $('.cr-table').height();
//                        $('.table-responsive').css('height', height + 150 + 'px');
                        app.refreshUserCharacters();
                    });
            },
            hideAllCharacter() {
                var app = this;
                for (var i = 0; i < app.userCharacters.length; i++) {
                    app.userCharacters[i].show_flag = false;
                }
            },
            removeRowTable(characterId) {
                var app = this;
                axios.post('/chrecorder/public/api/v1/character/delete/' + app.user.id + '/' + characterId)
                    .then(function (resp) {
                        app.userCharacters = resp.data.characters;
                        app.headers = resp.data.headers;
                        app.values = resp.data.values;
                        app.userTags = resp.data.userTags;
                        if (app.userCharacters.length == 0) {
                            app.matrixShowFlag = false;
                        }
                        app.refreshUserCharacters();
                    });
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
                    });
            },
            upUserValue(valueId) {
                var app = this;
                var showedCharacters = app.userCharacters.filter(ch => ch.show_flag == true);
                var index = showedCharacters.indexOf(showedCharacters.find(ch => ch.id == valueId));
                app.swap(showedCharacters[index].id, false, showedCharacters.length);
            },
            downUserValue(valueId) {
                var app = this;
                var showedCharacters = app.userCharacters.filter(ch => ch.show_flag == true);
                var index = showedCharacters.indexOf(showedCharacters.find(ch => ch.id == valueId));
                app.swap(showedCharacters[index].id, true, showedCharacters.length);

            },
            swap(valueId, directionFlag = true, maxLength) {
                var app = this;
                var showedValues = app.values.filter(function (eachValue) {
                    return app.userCharacters.find(ch => ch.id == eachValue[0].character_id).show_flag == true;
                });
                var valueIndex = showedValues.indexOf(app.values.find(value => value[0].character_id == valueId));
                if ((directionFlag == true) && (valueIndex < maxLength - 1)) {
                    var tmp = showedValues[valueIndex];
                    app.isLoading = true;
                    console.log('tmp', tmp);
//                    app.values.splice(valueIndex, 1);
//                    app.values.splice(valueIndex + 1, 0, tmp);
                    axios.post('/chrecorder/public/api/v1/character/change-order', {
                        order: 'down',
                        characterId: tmp[0].character_id,
                    })
                        .then(function (resp) {
                            console.log('resp', resp);
                            app.isLoading = false;
                            app.values = resp.data.values;
                            app.userCharacters = resp.data.characters;
                            console.log('app.userCharacters', app.userCharacters);
                            app.refreshUserCharacters();
                        });
                } else if ((directionFlag == false) && (valueIndex > 0)) {
                    var tmp = showedValues[valueIndex];
                    app.isLoading = true;
//                    app.values.splice(valueIndex, 1);
//                    app.values.splice(valueIndex - 1, 0, tmp);
                    axios.post('/chrecorder/public/api/v1/character/change-order', {
                        order: 'up',
                        characterId: tmp[0].character_id,
                    })
                        .then(function (resp) {
                            app.isLoading = false;
                            app.values = resp.data.values;
                            app.userCharacters = resp.data.characters;
                            app.refreshUserCharacters();
                        });
                }

            },
            refreshDefaultCharacters() {
                var app = this;
                app.standardCharacters = [];
                console.log("temp.text");
                for (var i = 0; i < app.defaultCharacters.length; i++) {
                    var temp = {};
                    temp.name = app.defaultCharacters[i].name;
                    temp.text = app.defaultCharacters[i].name + ' by ' + app.defaultCharacters[i].username + ' (' + app.defaultCharacters[i].usage_count + ')';
                    if (app.defaultCharacters[i].name == 'Growth form of plant') {
                        console.log('temp.text', temp.text);
                    }
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
                    app.standardCharacters.push(temp);
                }
            },
//            expandTable() {
//                var app = this;
//                if (app.descriptionFlag != -1) {
//                    app.descriptionFlag--;
//                }
//            },
            expandDescription() {
                var app = this;
                app.descriptionFlag = !app.descriptionFlag;
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
            updateDescription() {
                var app = this;

                app.descriptionText = '';

                for (var i = 0; i < app.userTags.length; i++) {
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
                                        var minPercentileValue = 0;
                                        var maxPercentileValue = 0;
                                        if (tempRpArray.length % 2 == 0) {
                                            minPercentileValue = tempRpArray[tempRpArray.length / 2 - 1];
                                            maxPercentileValue = tempRpArray[tempRpArray.length / 2];
                                        } else {
                                            if (tempRpArray.length == 1) {
                                                minPercentileValue = tempRpArray[0];
                                                maxPercentileValue = tempRpArray[0];
                                            } else {
                                                minPercentileValue = tempRpArray[tempRpArray.length / 2 - 1.5];
                                                maxPercentileValue = tempRpArray[tempRpArray.length / 2 + 0.5];
                                            }
                                        }
                                        app.descriptionText += '(' + minValue + '-)' + minPercentileValue + '-' + maxPercentileValue + '(-' + maxValue + ') ';

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
                                            app.descriptionText += filteredCharacters[j].unit
                                        }
                                        break;
                                    default:
                                        break;
                                }
                                if (filteredCharacters[j].name.startsWith('Length')) {
                                    app.descriptionText += ' long; ';
                                } else if (filteredCharacters[j].name.startsWith('Width')) {
                                    app.descriptionText += ' wide; ';

                                } else if (filteredCharacters[j].name.startsWith('Height')) {
                                    app.descriptionText += ' tall; ';

                                } else if (filteredCharacters[j].name.startsWith('Diameter')) {
                                    app.descriptionText += ' diameter; ';
                                } else {
                                    app.descriptionText += ' ; ';
                                }
                            } else {
                                if (tempValueArray.find(v => v == 'green')) {
                                    if (tempValueArray.find(v => v == 'grey')) {
                                        app.descriptionText += 'frequently grey, occasionally green; '
                                    } else {
                                        app.descriptionText += 'usually light grey, occasionally dark grey or green; '
                                    }
                                } else {
                                    app.descriptionText += 'sometimes hairy at base or middle part, sometimes ciliate, or occasionally smooth throughout; '
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
            exportDescription() {
                var app = this;
                axios.post('/chrecorder/public/api/v1/export-description', {template: app.descriptionText, taxon: app.taxonName})
                    .then(function (resp) {
                        console.log('resp', resp.data);
                        if (resp.data.is_scucess == 1) {
                            window.location.href = resp.data.doc_url;
                        } else {
                            alert('Error occurred while exporting data!');
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
            saveHeader(header) {
                var app = this;
                console.log('header', header.header);
                axios.post('/chrecorder/public/api/v1/update-header', header)
                    .then(function (resp) {
                        app.headers = resp.data.headers;
                    });
            },
            saveColorValue() {
                var app = this;

                var postValues = [];

                var postFlag = true;

                for (var i = 0; i < app.colorDetails.length; i++) {
                    var tempValue = {};
                    tempValue['value_id'] = app.colorDetails[i]['value_id'];
                    if (app.colorDetails[i].id) {
                        tempValue['id'] = app.colorDetails[i].id;
                    }
                    for (var key in app.colorDetails[i]) {
                        if (app.checkColorProperty(key)) {
                            tempValue[key] = app.colorDetails[i][key];
                            var requestBody = {};
                            console.log('app.colorDetails[i][key]', app.colorDetails[i][key]);
                            if (app.colorDetails[i][key] != null && app.colorDetails[i][key] != '') {
                                if (app.colorDetails[i][key].endsWith('(user defined)') && postFlag == true) {
                                    if (app.userColorDefinition[i][key] == ''
                                        || app.userColorDefinition[i][key] == null
                                        || app.userColorDefinition[i][key] == undefined
                                        || app.colorSampleText[i][key] == ''
                                        || app.colorSampleText[i][key] == null
                                        || app.colorSampleText[i][key] == undefined
                                        || app.colorTaxon[i][key] == ''
                                        || app.colorTaxon[i][key] == null
                                        || app.colorTaxon[i][key] == undefined) {
                                         postFlag = false;
                                    } else if (postFlag == true){
                                        tempValue[key] = app.colorDetails[i][key].substr(0, app.colorDetails[i][key].length - 14);
                                        console.log('colorSampleText', app.colorSampleText[i][key]);
                                        var date = new Date();
                                        requestBody = {
                                            "user": app.sharedFlag ? '' : app.user.name,
                                            "ontologylo": "carex",
                                            "term": tempValue[key],
                                            "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#" + app.changeToSubClassName(key),
                                            "definition": app.userColorDefinition[i][key],
                                            "elucidation": "",
                                            "createdBy": app.user.name,
                                            "creationDate": ("0" + date.getMonth()).slice(-2) + '-' + ("0" + date.getDate()).slice(-2) + '-' + date.getFullYear(),
                                            "definitionSrc": app.user.name,
                                            "examples": app.colorSampleText[i][key]+ ", used in taxon " + app.colorTaxon[i][key],
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


                                } else if (app.colorDefinition[i][key] && postFlag == true) {
                                    requestBody = {
                                        "user": app.sharedFlag ? '' : app.user.name,
                                        "ontology": "carex",
                                        "definition": app.colorDefinition[i][key],
                                        "providedBy": app.user.name,
                                        "exampleSentence": "",
                                        "classIRI": "http://biosemantics.arizona.edu/ontologies/carex#" + tempValue[key]
                                    };
                                    axios.post('http://shark.sbs.arizona.edu:8080/definition', requestBody)
                                        .then(function (resp) {
                                            console.log('shark api definition resp', resp);
                                            axios.post('http://shark.sbs.arizona.edu:8080/save', {
                                                user: app.sharedFlag ? '' : app.user.name,
                                                ontology: 'carex'
                                            })
                                                .then(function (resp) {
                                                    console.log('save api resp', resp);
                                                });
                                        });
                                    console.log('user defined', app.colorDefinition[i][key]);
                                } else if (app.colorComment[i][key] && postFlag == true) {
                                    requestBody = {
                                        "user": app.sharedFlag ? '' : app.user.name,
                                        "ontology": "carex",
                                        "comment": app.colorComment[i][key],
                                        "providedBy": app.user.name,
                                        "exampleSentence": "",
                                        "classIRI": "http://biosemantics.arizona.edu/ontologies/carex#" + tempValue[key]
                                    };
                                    axios.post('http://shark.sbs.arizona.edu:8080/comment', requestBody)
                                        .then(function (resp) {
                                            console.log('shark api comment resp', resp);
                                            axios.post('http://shark.sbs.arizona.edu:8080/save', {
                                                user: app.sharedFlag ? '' : app.user.name,
                                                ontology: 'carex'
                                            })
                                                .then(function (resp) {
                                                    console.log('save api resp', resp);
                                                });
                                        });
                                    console.log('colorComment', app.colorComment[i][key]);

                                }
                            }

                        }
                    }
                    postValues.push(tempValue);
                }
                if (postFlag == true) {
                    axios.post('/chrecorder/public/api/v1/save-color-value', postValues)
                        .then(function (resp) {
                            app.values = resp.data.values;
                            app.preList = resp.data.preList;
                            app.postList = resp.data.postList;
                            app.colorDetailsFlag = false;
                            console.log('save color value resp', postValues);
                        });
                } else {
                    $('.color-definition-input').css('border', '1px solid red');
                }

                console.log('postValues', postValues);

            },
            removeColorValue() {
                var app = this;

                console.log('app.colorDetails[0].value_id', app.colorDetails[0].value_id);
                axios.post('/chrecorder/public/api/v1/remove-color-value', {value_id: app.colorDetails[0].value_id})
                    .then(function (resp) {
                        app.values = resp.data.values;
                        app.preList = resp.data.preList;
                        app.postList = resp.data.postList;
                        app.colorDetailsFlag = false;
                    });
            },
            saveNonColorValue() {
                var app = this;

                var postValues = [];
                var postFlag = true;
                for (var i = 0; i < app.nonColorDetails.length; i++) {
                    var tempValue = {};
                    tempValue['value_id'] = app.nonColorDetails[i]['value_id'];
                    if (app.nonColorDetails[i].id) {
                        tempValue['id'] = app.nonColorDetails[i].id;
                    }
                    tempValue['negation'] = app.nonColorDetails[i].negation;
                    tempValue['pre_constraint'] = app.nonColorDetails[i].pre_constraint;
                    tempValue['post_constraint'] = app.nonColorDetails[i].post_constraint;
                    tempValue['main_value'] = app.nonColorDetails[i].main_value;
                    var requestBody = {};
                    if (app.nonColorDetails[i]['main_value'] != null && app.nonColorDetails[i]['main_value'] != '') {
                        if (app.nonColorDetails[i]['main_value'].endsWith('(user defined)') && postFlag == true) {
                            if (app.userNonColorDefinition[i]['main_value'] == ''
                                || app.userNonColorDefinition[i]['main_value'] == null
                                || app.userNonColorDefinition[i]['main_value'] == undefined
                                || app.nonColorSampleText[i]['main_value'] == ''
                                || app.nonColorSampleText[i]['main_value'] == null
                                || app.nonColorSampleText[i]['main_value'] == undefined
                                || app.nonColorTaxon[i]['main_value'] == ''
                                || app.nonColorTaxon[i]['main_value'] == null
                                || app.nonColorTaxon[i]['main_value'] == undefined) {
                                postFlag = false;
                            } else if (postFlag == true) {
                                tempValue['main_value'] = app.nonColorDetails[i]['main_value'].substr(0, app.nonColorDetails[i]['main_value'].length - 14);
                                var date = new Date();
                                requestBody = {
                                    "user": app.sharedFlag ? '' : app.user.name,
                                    "ontology": "carex",
                                    "term": tempValue['main_value'],
                                    "superclassIRI": "http://biosemantics.arizona.edu/ontologies/carex#texture",
                                    "definition": app.userNonColorDefinition[i]['main_value'],
                                    "elucidation": "",
                                    "createdBy": app.user.name,
                                    "creationDate": ("0" + date.getMonth()).slice(-2) + '-' + ("0" + date.getDate()).slice(-2) + '-' + date.getFullYear(),
                                    "definitionSrc": app.user.name,
                                    "examples": app.nonColorSampleText[i]['main_value'] + ", used in taxon " + app.nonColorTaxon[i]['main_value'],
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

                        } else if (app.nonColorDefinition[i]['main_value'] && postFlag == true) {
                            requestBody = {
                                "user": app.sharedFlag ? '' : app.user.name,
                                "ontology": "carex",
                                "definition": app.nonColorDefinition[i]['main_value'],
                                "providedBy": app.user.name,
                                "exampleSentence": "",
                                "classIRI": "http://biosemantics.arizona.edu/ontologies/carex#" + tempValue['main_value']
                            };
                            axios.post('http://shark.sbs.arizona.edu:8080/definition', requestBody)
                                .then(function (resp) {
                                    console.log('shark api definition resp', resp);
                                    axios.post('http://shark.sbs.arizona.edu:8080/save', {
                                        user: app.sharedFlag ? '' : app.user.name,
                                        ontology: 'carex'
                                    })
                                        .then(function (resp) {
                                            console.log('save api resp', resp);
                                        });
                                });
                        } else if (app.nonColorComment[i]['main_value'] && postFlag == true) {
                            requestBody = {
                                "user": app.sharedFlag ? '' : app.user.name,
                                "ontology": "carex",
                                "comment": app.nonColorComment[i]['main_value'],
                                "providedBy": app.user.name,
                                "exampleSentence": "",
                                "classIRI": "http://biosemantics.arizona.edu/ontologies/carex#" + tempValue['main_value']
                            };
                            axios.post('http://shark.sbs.arizona.edu:8080/comment', requestBody)
                                .then(function (resp) {
                                    console.log('shark api comment resp', resp);
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

                    postValues.push(tempValue);
                }

                if (postFlag == true) {
                    axios.post('/chrecorder/public/api/v1/save-non-color-value', postValues)
                        .then(function (resp) {
                            app.values = resp.data.values;
                            app.preList = resp.data.preList;
                            app.postList = resp.data.postList;
                            app.nonColorDetailsFlag = false;
                        });
                } else {
                    $('.non-color-input-definition').css('border', '1px solid red');
                }

            },
            removeNonColorValue() {
                var app = this;

                axios.post('/chrecorder/public/api/v1/remove-non-color-value', {value_id: app.nonColorDetails[0].value_id})
                    .then(function (resp) {
                        app.values = resp.data.values;
                        app.preList = resp.data.preList;
                        app.postList = resp.data.postList;
                        app.nonColorDetailsFlag = false;
                    });
            },
            checkColorProperty(property) {
                if (property == 'negation'
                    || property == 'pre_constraint'
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
            focusedValue(value) {
                var app = this;

                var currentCharacter = app.userCharacters.find(ch => ch.id == value.character_id);
                if (!app.checkHaveUnit(currentCharacter.name)) {
                    axios.get('/chrecorder/public/api/v1/get-constraint/' + currentCharacter.name)
                        .then(function (resp) {
                            app.preList = resp.data.preList;
                            app.postList = resp.data.postList;
                        });
                    if (currentCharacter.name.startsWith('Color')) {
                        app.colorDetailsFlag = true;
                        axios.get('/chrecorder/public/api/v1/get-color-details/' + value.id)
                            .then(function (resp) {
                                console.log('get-color resp', resp.data);
                                app.colorDetails = resp.data.colorDetails;
                                if (app.colorDetails.length == 0) {
                                    app.colorComment = [];
                                    app.colorTaxon = [];
                                    app.colorSampleText = [];
                                    app.colorDefinition = [];
                                    app.userColorDefinition = [];
                                    app.colorDetails.push({value_id: value.id, detailFlag: null});
                                    app.colorComment.push({});
                                    app.colorTaxon.push({
                                        'brightness': app.taxonName,
                                        'reflectance': app.taxonName,
                                        'saturation': app.taxonName,
                                        'colored': app.taxonName,
                                        'multi_colored': app.taxonName,
                                    });
                                    app.colorSampleText.push({});
                                    app.colorDefinition.push({});
                                    app.userColorDefinition.push({});
                                } else {
                                    $('.color-input').attr('placeholder', '');
                                    console.log('place holder test');
                                    app.colorComment = [];
                                    app.colorTaxon = [];
                                    app.colorSampleText = [];
                                    app.colorDefinition = [];
                                    app.userColorDefinition = [];
                                    for (var i = 0; i < app.colorDetails.length; i++) {
                                        app.colorDetails[i].taxon = app.taxonName;
                                        app.colorDetails[i].detailFlag = null;
                                        app.colorComment.push({});
                                        app.colorTaxon.push({
                                            'brightness': app.taxonName,
                                            'reflectance': app.taxonName,
                                            'saturation': app.taxonName,
                                            'colored': app.taxonName,
                                            'multi_colored': app.taxonName,
                                        });
                                        app.colorSampleText.push({});
                                        app.colorDefinition.push({});
                                        app.userColorDefinition.push({});
                                    }
                                }
                            });
                    } else {
                        app.nonColorDetailsFlag = true;


                        axios.get('/chrecorder/public/api/v1/get-non-color-details/' + value.id)
                            .then(function (resp) {
                                app.nonColorDetails = resp.data.nonColorDetails;
                                if (app.nonColorDetails.length == 0) {
                                    app.nonColorComment = [];
                                    app.nonColorTaxon = [];
                                    app.nonColorSampleText = [];
                                    app.nonColorDefinition = [];
                                    app.userNonColorDefinition = [];
                                    app.nonColorDetails.push({
                                        value_id: value.id,
                                        detailFlag: null,
                                        placeholderName: currentCharacter.name.split(' ')[0].toLowerCase()
                                    });
                                    app.nonColorComment.push({});
                                    app.nonColorTaxon.push({
                                        'main_value': app.taxonName,
                                    });
                                    app.nonColorSampleText.push({});
                                    app.nonColorDefinition.push({});
                                    app.userNonColorDefinition.push({});
                                } else {
                                    app.nonColorComment = [];
                                    app.nonColorTaxon = [];
                                    app.nonColorSampleText = [];
                                    app.nonColorDefinition = [];
                                    app.userNonColorDefinition = [];
                                    for (var i = 0; i < app.nonColorDetails.length; i++) {
                                        app.nonColorDetails[i].taxon = app.taxonName;
                                        app.nonColorDetails[i].detailFlag = null;
                                        app.nonColorDetails[i].placeholderName = currentCharacter.name.split(' ')[0].toLowerCase();
                                        app.nonColorComment.push({});
                                        app.nonColorTaxon.push({
                                            'main_value': app.taxonName,
                                        });
                                        app.nonColorSampleText.push({});
                                        app.nonColorDefinition.push({});
                                        app.userNonColorDefinition.push({});
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

                console.log('event.target', event.target);
                if (flag == 'negation') {
                    event.target.placeholder = '';
                } else if (app.checkHaveColorValueSet(flag)) {
                    $(":input").css('background', '#ffffff');
                    event.target.style.background = '#82c8fa';
                }
                color.detailFlag = null;
                app.colorExistFlag = false;
//                if (!color.id) {
//                    app.colorDetails[app.colorDetails.length - 1][flag] = '';
//                } else {
//                    for (var i = 0; i < app.colorDetails.length; i++) {
//                        if (app.colorDetails[i].id == color.id) {
//                            app.colorDetails[i][flag] = '';
//                        }
//                    }
//                }

                if (app.checkHaveColorValueSet(flag)) {
                    axios.get('http://shark.sbs.arizona.edu:8080/carex/getSubclasses?baseIri=http://biosemantics.arizona.edu/ontologies/carex&term=' + app.changeToSubClassName(flag))
                        .then(function (resp) {
                            app.$store.state.colorTreeData = resp.data;
                            color.detailFlag = flag;
                            app.colorExistFlag = true;
                            console.log('color', color);
                            if (color.id) {
                                app.colorDetailId = color.id;
//                                color.detailFlag = flag;
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
                        app.colorDetails[app.colorDetails.length - 1].detailFlag = flag;
                    }
                }
                console.log('flag', flag);
            },
            changeNonColorSection(nonColor, flag, event) {
                var app = this;
                app.nonColorSearchText = '';

                var characterId = app.values.find(eachValue => eachValue.find(eachItem => eachItem.id == nonColor.value_id) != null)[0].character_id;
                var characterName = app.userCharacters.find(ch => ch.id == characterId).name;
                console.log('characterName', characterName);

                var searchText = characterName.split(' ');

                if (flag == 'negation') {
                    event.target.placeholder = '';
                }
//                } else if (flag == 'main_value') {
//                    event.target.placeholder = searchText[0];
//                }
                app.nonColorExistFlag = false;

                if (flag == 'main_value') {
                    axios.get('http://shark.sbs.arizona.edu:8080/carex/getSubclasses?baseIri=http://biosemantics.arizona.edu/ontologies/carex&term=' + searchText[0].toLowerCase())
                        .then(function (resp) {
                            app.textureTreeData = resp.data;
                            nonColor.detailFlag = flag;
                            app.nonColorExistFlag = true;
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
                        app.nonColorDetails[app.nonColorDetails.length - 1].detailFlag = flag;
                    }
                }


            },
            changeToSubClassName(flag) {
                var searchFlag = null;
                switch (flag) {
                    case 'brightness':
                        searchFlag = 'color brightness';
                        break;
                    case 'reflectance':
                        searchFlag = 'reflectance';
                        break;
                    case 'saturation':
                        searchFlag = 'color saturation';
                        break;
                    case 'colored':
                        searchFlag = 'colored';
                        break;
                    case 'multi_colored':
                        searchFlag = 'multi-colored';
                        break;
                    default:
                        break;
                }

                return searchFlag;
            },
            searchColorSelection(color, flag) {
                var app = this;
                app.colorExistFlag = false;
                app.defaultColorValue = color[flag];
//                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?user=' + app.user.name + '&term=' + color[flag])
                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + color[flag] + '&ancestorIRI=http://biosemantics.arizona.edu/ontologies/carex%23' + app.changeToSubClassName(flag))
                    .then(function (resp) {
                        console.log('search carex resp', resp.data);
                        app.searchColor = resp.data.entries;
                        if (app.searchColor.length == 0) {
                            app.searchColorFlag = 0;
                            if (color.id && !color[flag].endsWith('(user defined)')) {
                                for (var i = 0; i < app.colorDetails.length; i++) {
                                    if (app.colorDetails[i].id == color.id) {
                                        app.colorDetails[i][flag] = color[flag] + '(user defined)';
                                    }
                                }
                            } else if (!color[flag].endsWith('(user defined)')) {
                                app.colorDetails[0][flag] = color[flag] + '(user defined)';
                            }
                        } else if (app.searchColor.find(eachColor => eachColor.resultAnnotations.find(eachProperty => (eachProperty.property.endsWith('hasBroadSynonym') && eachProperty.value == color[flag])
                            || (eachProperty.property.endsWith('has_not_recommended_synonym') && eachProperty.value == color[flag])))) {
                            app.searchColorFlag = 1;
                            app.colorSynonyms = app.searchColor.filter(function (eachColor) {
                                return eachColor.resultAnnotations.find(eachProperty => (eachProperty.property.endsWith('hasBroadSynonym') && eachProperty.value == color[flag])
                                    || (eachProperty.property.endsWith('has_not_recommended_synonym') && eachProperty.value == color[flag])
                                    || (eachProperty.property.endsWith('hasExactSynonym') && eachProperty.value == color[flag])) != null || eachColor.term == color[flag];

                            });
                            for (var i = 0; i < app.colorSynonyms.length; i++) {
                                if (app.colorSynonyms[i].resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115'))) {
                                    app.colorSynonyms[i].definition = app.colorSynonyms[i].resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                                    //var index = app.colorDetails.indexOf(color);
                                    //app.colorDefinition[index][flag] = app.colorSynonyms[i].resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                                } else {
                                    var index = app.colorDetails.indexOf(color);
                                    app.colorDefinition[index][flag] = null;
                                }
                            }
                        } else if (app.searchColor.find(eachColor => eachColor.term == color[flag])) {
                            app.searchColorFlag = 2;
                            app.exactColor = app.searchColor.find(eachColor => eachColor.term == color[flag]);
                            if (app.exactColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115'))) {
                                app.exactColor.definition = app.exactColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                                //var index = app.colorDetails.indexOf(color);
                                //app.colorDefinition[index][flag] = app.exactColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                            } else {
                                var index = app.colorDetails.indexOf(color);
                                app.colorDefinition[index][flag] = null;
                            }
                        } else if (app.searchColor.find(eachColor => eachColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('hasExactSynonym') && eachProperty.value == color[flag]))) {
                            app.searchColorFlag = 2;
                            app.exactColor = app.searchColor.find(eachColor => eachColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('hasExactSynonym') && eachProperty.value == color[flag]));
                            if (app.exactColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115'))) {
                                app.exactColor.definition = app.exactColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                                var index = app.colorDetails.indexOf(color);
                                app.colorDefinition[index][flag] = app.exactColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                            } else {
                                var index = app.colorDetails.indexOf(color);
                                app.colorDefinition[index][flag] = null;
                            }
                        }
                        console.log('app.searchColorFlag', app.searchColorFlag);
                    });
            },
            searchNonColorSelection(nonColor, flag) {
                var app = this;
                app.nonColorExistFlag = false;
                app.defaultNonColorValue = nonColor[flag];
//                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?user=' + app.user.name + '&term=' + nonColor[flag])
                axios.get('http://shark.sbs.arizona.edu:8080/carex/search?term=' + nonColor[flag] + '&ancestorIRI=http://biosemantics.arizona.edu/ontologies/carex%23' + app.changeToSubClassName(flag))
                    .then(function (resp) {
                        console.log('search carex resp', resp.data);
                        app.searchNonColor = resp.data.entries;
                        if (app.searchNonColor.length == 0) {
                            app.searchNonColorFlag = 0;
                            if (nonColor.id && !nonColor[flag].endsWith('(user defined)')) {
                                for (var i = 0; i < app.nonColorDetails.length; i++) {
                                    if (app.nonColorDetails[i].id == nonColor.id) {
                                        app.nonColorDetails[i][flag] = nonColor[flag] + '(user defined)';
                                    }
                                }
                            } else if (!nonColor[flag].endsWith('(user defined)')) {
                                app.nonColorDetails[0][flag] = nonColor[flag] + '(user defined)';
                            }
                        } else if (app.searchNonColor.find(eachValue => eachValue.resultAnnotations.find(eachProperty => (eachProperty.property.endsWith('hasBroadSynonym') && eachProperty.value == nonColor[flag])
                            || (eachProperty.property.endsWith('has_not_recommended_synonym') && eachProperty.value == nonColor[flag])))) {
                            app.searchNonColorFlag = 1;
                            app.nonColorSynonyms = app.searchNonColor.filter(function (eachValue) {
                                return eachValue.resultAnnotations.find(eachProperty => (eachProperty.property.endsWith('hasBroadSynonym') && eachProperty.value == nonColor[flag])
                                    || (eachProperty.property.endsWith('has_not_recommended_synonym') && eachProperty.value == nonColor[flag])
                                    || (eachProperty.property.endsWith('hasExactSynonym') && eachProperty.value == nonColor[flag])) != null || eachValue.term == nonColor[flag];

                            });
                            for (var i = 0; i < app.nonColorSynonyms.length; i++) {
                                if (app.nonColorSynonyms[i].resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115'))) {
                                    app.nonColorSynonyms[i].definition = app.nonColorSynonyms[i].resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                                    var index = app.nonColorDetails.indexOf(nonColor);
                                    app.nonColorDefinition[index][flag] = app.nonColorSynonyms[i].resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                                } else {
                                    var index = app.nonColorDetails.indexOf(nonColor);
                                    app.nonColorDefinition[index][flag] = null;
                                }
                            }
                        } else if (app.searchNonColor.find(eachValue => eachValue.term == nonColor[flag])) {
                            app.searchNonColorFlag = 2;
                            app.exactNonColor = app.searchNonColor.find(eachValue => eachValue.term == nonColor[flag]);
                            if (app.exactNonColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115'))) {
                                app.exactNonColor.definition = app.exactNonColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                                var index = app.nonColorDetails.indexOf(nonColor);
                                app.nonColorDefinition[index][flag] = app.exactNonColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                            } else {
                                var index = app.nonColorDetails.indexOf(nonColor);
                                app.nonColorDefinition[index][flag] = null;
                            }
                        } else if (app.searchNonColor.find(eachValue => eachValue.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('hasExactSynonym') && eachProperty.value == nonColor[flag]))) {
                            app.searchNonColorFlag = 2;
                            app.exactNonColor = app.searchNonColor.find(eachValue => eachValue.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('hasExactSynonym') && eachProperty.value == nonColor[flag]));
                            if (app.exactNonColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115'))) {
                                app.exactNonColor.definition = app.exactNonColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                                var index = app.nonColorDetails.indexOf(nonColor);
                                app.nonColorDefinition[index][flag] = app.exactNonColor.resultAnnotations.find(eachProperty => eachProperty.property.endsWith('IAO_0000115')).value;
                            } else {
                                var index = app.nonColorDetails.indexOf(nonColor);
                                app.nonColorDefinition[index][flag] = null;
                            }
                        }
                        console.log('app.searchNonColorFlag', app.searchNonColorFlag);
                    });
            },
            onTreeNodeSelected(node) {
                var app = this;
                app.colorDetailsFlag = false;
                console.log('treeNode', node);
                if (app.colorDetails.length == 1) {
                    app.colorDetailsFlag = true;
                    app.colorDetails[0][app.colorDetails[0].detailFlag] = app.colorDetails[0][app.colorDetails[0].detailFlag] + ';';
                    app.colorDetails[0][app.colorDetails[0].detailFlag] = app.colorDetails[0][app.colorDetails[0].detailFlag].substring(0, app.colorDetails[0][app.colorDetails[0].detailFlag].length - 1);
                    app.colorDetails[0][app.colorDetails[0].detailFlag] = node.data.text;
                } else {
                    for (var i = 0; i < app.colorDetails.length; i++) {

                    }
                }

            },
            onTextureTreeNodeSelected(node) {
                var app = this;
                app.nonColorDetailsFlag = false;
                if (app.nonColorDetails.length == 1) {
                    app.nonColorDetailsFlag = true;
                    app.nonColorDetails[0]['main_value'] = app.nonColorDetails[0]['main_value'] + ';';
                    app.nonColorDetails[0]['main_value'] = app.nonColorDetails[0]['main_value'].substring(0, app.nonColorDetails[0]['main_value'].length - 1);
                    app.nonColorDetails[0]['main_value'] = node.data.text;

                } else {
                    for (var i = 0; i < app.colorDetails.length; i++) {

                    }
                }
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

                if (app.colorDetails.length == 1) {

                }
                console.log('color', color);
                console.log('flag', flag);
                console.log('value', value);
                console.log('test');
            },
            importMatrix() {

            },
        },
        watch: {
            colorTreeData(newData) {
                this.treeData = newData;
                // do data transformations etc
                // trigger UI refresh
            },
            nonColorTreeData(newData) {
                this.textureTreeData = newData;
            }
        },
        created() {
            var app = this;
            axios.get('/chrecorder/public/api/v1/standard_characters')
                .then(function (resp) {
                    console.log('standardCharacters', resp);
                    app.defaultCharacters = resp.data;
                    for (var i = 0; i < resp.data.length; i++) {
                        var temp = {};
                        temp.name = resp.data[i].name;
                        if (resp.data[i].standard == 1) {
                            app.standardCharactersTooltip = app.standardCharactersTooltip + resp.data[i].name + '; ';
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
                        app.standardCharacters.push(temp);
                    }
                    axios.get("/chrecorder/public/api/v1/character/" + app.user.id)
                        .then(function (resp) {
                            console.log('resp character', resp.data);
                            app.userCharacters = resp.data.characters;
                            app.headers = resp.data.headers;
                            app.values = resp.data.values;
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
                                    app.showTableForTab(app.userTags[0].tag_name);

                                }
                            }

                            app.refreshUserCharacters();
                        });
                });

            axios.get("/chrecorder/public/api/v1/user-tag/" + app.user.id)
                .then(function (resp) {
                    app.userTags = resp.data;
                    console.log('userTags', app.userTags);
                });
        },
        mounted() {
            var app = this;
            app.user.name = app.user.email.split('@')[0];
            app.characterUsername = app.user.name;
            sessionStorage.setItem('userId', app.user.id);


        },
    }


</script>