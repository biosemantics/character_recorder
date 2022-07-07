<template>
  <div>
    <div class="modal-header">
      <b>Color Palette for {{ paletteKey }}</b>
    </div>
    <div class="modal-body">
      <div class="row">
        <div v-for="(object, key) in colorData" class="col-md-2" style="text-align: center;">
          {{ key }}
         <!--  <div v-for="(color, index) in object" v-if="index % Math.floor(object.length / 10) === 0 && index < Math.floor(object.length / 10) * 10" class="color-cell" v-bind:style="{ 'background-color': 'rgb(' + color['Red'] + ',' + color['Green'] +',' + color['Blue'] + ')'}" v-on:click="selectColor(key, color)" :title="color['Red'] + ',' + color['Green'] +',' + color['Blue']"> -->
        <div v-for="(color, index) in object" class="color-cell" v-bind:style="{ 'background-color': 'rgb(' + color['Red'] + ',' + color['Green'] +',' + color['Blue'] + ')'}" v-on:click="selectColor(key, color)" v-tooltip="color['Red'] + ', ' + color['Green'] +', ' + color['Blue']">
            <!--        <span />-->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "color-palette",

  data: function() {
    return {
      colorData: ''
    }
  },
  props: {
    paletteData: {
      type: Object,
      default () {
        return {}
      }
    },
    paletteKey: {
      type: String,
      default() {
        return '';
      }
    }
  },
  methods: {
    selectColor(key, color) {
      var app = this;
      app.colorData = key + ' (' + color.Red + ', ' + color.Green + ', ' + color.Blue + ')';

      this.$emit('selectedColor', app.colorData)
    },
  },
  beforeMount() {
    var app = this;
    app.colorData = app.paletteData;
    for(const i in app.colorData){
      var child = app.colorData[i];
      for(var j = 0; j < child.length; j++){
        var item = [];
        app.colorData[i][j].cal= (0.299*child[j].Red)+(0.587*child[j].Green)+(0.114*child[j].Blue);
      }
      app.colorData[i].sort(function(a, b) {
        return b.cal - a.cal;
      });
    }
  }
}
</script>

<style scoped>
.color-cell {
  width: 50%;
  height: 25px;
  margin: 5px auto;
  border: 1px solid black;
}
</style>
