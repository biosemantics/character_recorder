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
      <template v-for="(option, idx) in filteredOptions">
        <div class="item"
             :class="{ 'selected': option.selected, 'current': pointer === idx }"
             :data-vss-custom-attr="customAttrs[idx] ? customAttrs[idx] : ''"
             @click.stop="selectItem(option)"
             @mousedown="mousedownItem"
             @mouseenter="pointerSet(idx)">
          {{option.text}}
        </div>
      </template>
      <template v-if="filteredOptions.length == 0">
        <div class="item"
             @click.stop="selectItem(null)"
             @mousedown="mousedownItem"
             >
          Nothing found
        </div>
      </template>
    </div>
  </div>
</template>

<script>
  /* event : select */
  import common from './common'
  import { baseMixin, commonMixin, optionAwareMixin } from './mixins'

  export default {
    mixins: [baseMixin, commonMixin, optionAwareMixin],
    props: {
      selectedOption: {
        type: Object,
        default: () => { return { value: '', text: '' } }
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
          if (this.selectedOption.text) {
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
        if (!this.selectedOption.text && this.placeholder) {
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
          })
        } else {
          return this.options
        }
      }
    },
    methods: {
      deleteTextOrItem () {
        console.log("deleteTextOrItem");
        if (!this.searchText && this.selectedOption) {
          this.selectItem({})
          this.openOptions()
        }
      },
      openOptions () {
        console.log("openOptions");
        common.openOptions(this)
      },
      blurInput () {
        console.log("blur");
        if (this.filteredOptions.length == 0) {
          this.selectItem(null);
        } else {
          if (this.selectedOption) {
            this.selectItem(this.selectedOption);
          } else {
            this.selectItem(this.option);
          }
        }
        common.blurInputName(this)
      },
      closeOptions () {
        console.log("close");
        common.closeOptions(this)
      },
      prevItem () {
        console.log("prevItem");
        common.prevItem(this)
      },
      nextItem () {
        console.log("nextItem");
        common.nextItem(this)
      },
      enterItem () {
        console.log("enterItem");
        common.enterItem(this)
      },
      pointerSet (index) {
        console.log("pointerSet");
        common.pointerSet(this, index)
      },
      pointerAdjust () {
        console.log("pointerAdjust");
        common.pointerAdjust(this)
      },
      mousedownItem () {
        console.log("mousedownItem");
        common.mousedownItem(this)
      },
      selectItem (option) {
        console.log("selectItem", option);
        console.log("selectItem", this.selectedOption);
        if (option) {
          // this.searchText = '' // reset text when select item
        }
        this.closeOptions()
        this.$emit('select', option, this.searchText)
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
