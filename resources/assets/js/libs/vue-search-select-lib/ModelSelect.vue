<template>
    <div class="ui fluid search selection dropdown"
         :class="{ 'active visible':showMenu, 'error': isError, 'disabled': isDisabled }"
         @click="openOptions"
         @focus="openOptions">
        <i class="dropdown icon"></i>
        <input class="search"
               autocomplete="off"
               tabindex="0"
               v-model="searchText"
               ref="input"
               @focus.prevent="openOptions"
               @keyup.esc="closeOptions"
               @blur="blurInput"
               @keydown.up="prevItem"
               @keydown.down="nextItem"
               @keydown.enter.prevent=""
               @keyup.enter.prevent="enterItem"
               @keydown.delete="deleteTextOrItem"
        />
        <div class="text"
             :class="textClass" :data-vss-custom-attr="searchTextCustomAttr">{{inputText}}
        </div>
        <div class="menu"
             ref="menu"
             @mousedown.prevent
             :class="menuClass"
             :style="menuStyle"
             tabindex="-1">
            <template v-if="filteredOptions.length > 0">
                <div class="item"
                     @click.stop="selectItem(null)"
                     @mousedown="mousedownItem"
                >
                    Click <strong style="color: #3097D0;">HERE</strong> to create a new character
                </div>
            </template>
            <template v-for="(option, idx) in filteredOptions">
                <div class="item"
                     :class="{ 'selected': option.selected, 'current': pointer === idx }"
                     :data-vss-custom-attr="customAttrs[idx] ? customAttrs[idx] : ''"
                     @mousedown="mousedownItem"
                     @mouseenter="pointerSet(idx)"
                     v-tooltip.top-center="option.tooltip"
                     >
                    <label :style="{ 'font-weight': option.deprecated >= 0 ? '700' : '300' }" @click="selectItem(option)">{{option.text}}</label>
                    <a style="margin-left: 5px;" @click="resolveItem(option)"><span v-if="option.deprecated >= 0" class="glyphicon glyphicon-wrench"></span></a>
                </div>
            </template>
            <template v-if="filteredOptions.length == 0">
                <div class="item"
                     @click.stop="selectItem(null)"
                     @mousedown="mousedownItem"
                >
                    Nothing found, click <strong style="color: #3097D0;">HERE</strong> to create new
                    character
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import common from './common'
    import {baseMixin, commonMixin, optionAwareMixin} from './mixins'
    import Vue from 'vue'
    import VTooltip from 'v-tooltip'

    Vue.use(VTooltip)
    export default {
        mixins: [baseMixin, commonMixin, optionAwareMixin],
        props: {
            value: {
                type: [String, Number, Object]
            }
        },
        data () {
            return {
                showMenu: false,
                searchText: '',
                mousedownState: false, // mousedown on option menu
                pointer: 0
            }
        },
        computed: {
            searchTextCustomAttr () {
                if (this.selectedOption && this.selectedOption.value) {
                    return this.customAttr(this.selectedOption)
                }
                return ''
            },
            inputText () {
                if (this.searchText) {
                    return ''
                } else {
                    let text = this.placeholder
                    if (this.selectedOption) {
                        text = this.selectedOption.text
                    }
                    return text
                }
            },
            customAttrs () {
                try {
                    if (Array.isArray(this.options)) {
                        return this.options.map(o => this.customAttr(o))
                    }
                } catch (e) {
                    // if there is an error, just return an empty array
                }
                return []
            },
            textClass () {
                if (!this.selectedOption && this.placeholder) {
                    return 'default'
                } else {
                    return ''
                }
            },
            menuClass () {
                return {
                    visible: this.showMenu,
                    hidden: !this.showMenu
                }
            },
            menuStyle () {
                return {
                    display: this.showMenu ? 'block' : 'none'
                }
            },
            filteredOptions () {
                if (this.searchText) {
                    return this.options.filter((option) => {
                        try {
                            return this.filterPredicate(option.text, this.searchText)
                        } catch (e) {
                            return true
                        }
                    }).sort(function (a, b) {
                        return a.text < b.text ? -1 : 1;
                    });
                } else {
                    return this.options.sort(function (a, b) {
                        return a.text < b.text ? -1 : 1;
                    });
                }
            },
            optionValue () {
                if (typeof this.value === 'object' && this.value !== null) {
                    return this.value.value
                } else {
                    return this.value
                }
            },
            selectedOption () {
                return this.options.find(option => {
                    return option.value === this.optionValue
                })
            }
        },
        methods: {
            deleteTextOrItem () {
                if (!this.searchText && this.value) {
//                    this.selectItem({})
                    this.openOptions()
                }
            },
            openOptions () {
                common.openOptions(this)
            },
            blurInput () {
                common.blurInput(this)
            },
            closeOptions () {
                common.closeOptions(this)
            },
            prevItem () {
                common.prevItem(this)
            },
            nextItem () {
                common.nextItem(this)
            },
            enterItem () {
                common.enterItem(this)
            },
            pointerSet (index) {
                common.pointerSet(this, index)
            },
            pointerAdjust () {
                common.pointerAdjust(this)
            },
            mousedownItem () {
                common.mousedownItem(this)
            },
            selectItem (option) {
                var temp = this.searchText;
                this.searchText = '';
                this.closeOptions();
                if (option == null) {
                    this.$emit('select', temp);
                }
                else if (Object.keys(option).length === 0 && option.constructor === Object) {
                    this.$emit('select', option)
                } else {
                    const item = this.options.find((e, i) => {
                        return e[this.optionValue] === option.value
                    })
                    this.$emit('select', option.value)
                }
//        if (typeof this.value === 'object' && this.value) {
//          this.$emit('input', option);
//        } else {
//          if (option.value) {
//            this.$emit('input', option.value);
//          } else {
//            this.$emit('input', '');
//          }
//        }
            },
            overItem(option) {
                this.$emit('over', option)
            },
            resolveItem(option) {
                this.$emit('resolve', option)
            }
        }
    }
</script>

<style scoped src="./dropdown.css"></style>
<style scoped src="./common.css"></style>
<style>
    /* Menu Item Hover */
    .ui.dropdown .menu > .item:hover {
        background: none transparent !important;
    }

    /* Menu Item Hover for Key event */
    .ui.dropdown .menu > .item.current {
        background: rgba(0, 0, 0, 0.05) !important;
    }
</style>
