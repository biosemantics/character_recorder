<template>
  <div>
    <div class="modal-header">
      <b>Color Palette for {{ paletteKey }}</b>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="row color-pattern">
          <div class="col-md-4">
            <div class="ruler-area">
                <h3>Basic Colors</h3>
                <div class="color-outer">
                  <span class="ColorName">0,0,255</span>
                  <span class="ColorName">0,255,255</span>
                  <span class="ColorName">0,255,0</span>
                  <span class="ColorName">255.255.0</span>
                  <span class="ColorName">255.0.0</span>
                  <span class="ColorName">255.0.255</span>
                  <span class="ColorName">255.255.255</span>
                </div>
            </div>
            <div class="pallet-basic-color">
              <div class="boxes"></div>
              <div class="boxes"></div>
              <div class="boxes"></div>
              <div class="boxes"></div>
              <div class="boxes"></div>
              <div class="boxes"></div>
              <div class="boxes"></div> 
            </div>        
          </div>

          <div class="col-md-4">
            <div class="ruler-area">
               <h3>Color Control Patches</h3>
              <div class="color-outer">
                <span class="ColorName">Blue</span>
                <span class="ColorName">Cyan</span>
                <span class="ColorName">Green</span>
                <span class="ColorName">Yellow</span>
                <span class="ColorName">Red</span>
                <span class="ColorName">Magenta</span>
                <span class="ColorName">White</span>
                <span class="ColorName">3/Color</span>
                <span class="ColorName">Black</span>
              </div>
            </div>
            <div class="pallet-color-control">
              <div class="pallet-color-light">
                <div class="boxes"></div>
                <div class="boxes"></div>
                <div class="boxes"></div>
                <div class="boxes"></div>
                <div class="boxes"></div>
                <div class="boxes"></div>
                <div class="boxes"></div>       
                <div class="boxes"></div>        
                <div class="boxes"></div>
              </div>
              <div class="pallet-color-dark">
                <div class="boxes"></div>
                <div class="boxes"></div>
                <div class="boxes"></div>
                <div class="boxes"></div>
                <div class="boxes"></div>
                <div class="boxes"></div>
                <div class="boxes"></div>       
                <div class="boxes"></div>        
                <div class="boxes"></div>
              </div>
            </div>    
          </div>
          <div class="col-md-4">           
            <div class="ruler-area">
               <h3>Gray Scale</h3>
              <div class="color-outer">
                <span class="ColorName"><b>A</b></span>
                <span class="ColorName">1</span>
                <span class="ColorName">2</span>
                <span class="ColorName">3</span>
                <span class="ColorName">4</span>
                <span class="ColorName">5</span>
                <span class="ColorName">6</span>
                <span class="ColorName"><b>M</b></span>
                <span class="ColorName">8</span>
                <span class="ColorName">9</span>
                <span class="ColorName">10</span>
                <span class="ColorName">11</span>
                <span class="ColorName">12</span>
                <span class="ColorName">13</span>
                <span class="ColorName">14</span>
                <span class="ColorName">15</span>
                <span class="ColorName"><b>B</b></span>
                <span class="ColorName">16</span>
                <span class="ColorName">17</span>
                <span class="ColorName">18</span>
                <span class="ColorName">19</span>
              </div>
            </div>
              <div data-theme="black" class="light-to-dark">
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
                  <div class="boxes"></div>
            </div>
          </div>
        </div>

        <div v-for="(object, key) in colorData" class="col-md-2" style="text-align: center;">
          {{ key }}
         <!--  <div v-for="(color, index) in object" v-if="index % Math.floor(object.length / 10) === 0 && index < Math.floor(object.length / 10) * 10" class="color-cell" v-bind:style="{ 'background-color': 'rgb(' + color['Red'] + ',' + color['Green'] +',' + color['Blue'] + ')'}" v-on:click="selectColor(key, color)" :title="color['Red'] + ',' + color['Green'] +',' + color['Blue']"> -->
        <div v-for="(color, index) in object" class="color-cell"  v-if="color['show'] == 1" v-bind:style="{ 'background-color': 'rgb(' + color['Red'] + ',' + color['Green'] +',' + color['Blue'] + ')'}" v-on:click="selectColor(key, color)" v-tooltip="color['Red'] + ', ' + color['Green'] +', ' + color['Blue']">
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
    for(const i in app.paletteData){
      var child = app.paletteData[i];
      for(var j = 0; j < child.length; j++){
        if(j % Math.floor(child.length / 10) === 0 && j < Math.floor(child.length / 10) * 10){
          var item = [];
          app.paletteData[i][j].cal= (0.299*child[j].Red)+(0.587*child[j].Green)+(0.114*child[j].Blue);
          app.paletteData[i][j].show = 1;
        }else {
          app.paletteData[i][j].cal= (0.299*child[j].Red)+(0.587*child[j].Green)+(0.114*child[j].Blue);
          app.paletteData[i][j].show = 0;
        }
      }
      app.paletteData[i].sort(function(a, b) {
        return b.cal - a.cal;
      });
    }
    app.colorData = app.paletteData;
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
