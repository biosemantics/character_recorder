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
              <div class="boxes" v-tooltip='"RGB(0, 0, 255)"'></div>
              <div class="boxes" v-tooltip='"RGB(0, 255, 255)"'></div>
              <div class="boxes" v-tooltip='"RGB(0, 255, 0)"'></div>
              <div class="boxes" v-tooltip='"RGB(255, 255, 0)"'></div>
              <div class="boxes" v-tooltip='"RGB(255, 0, 0)"'></div>
              <div class="boxes" v-tooltip='"RGB(255, 0, 255)"'></div>
              <div class="boxes" v-tooltip='"RGB(255, 255, 255)"'></div> 
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
                <span class="ColorName">Brown</span>
                <span class="ColorName">Black</span>
              </div>
            </div>
            <div class="pallet-color-control">
              <div class="pallet-color-light">
                <div class="boxes" v-tooltip="'RGB(236, 242, 243)'"></div>
                <div class="boxes" v-tooltip="'RGB(222, 238, 236)'"></div>
                <div class="boxes" v-tooltip="'RGB(213, 229, 203)'"></div>
                <div class="boxes" v-tooltip="'RGB(243, 238, 171)'"></div>
                <div class="boxes" v-tooltip="'RGB(239, 202, 164)'"></div>
                <div class="boxes" v-tooltip="'RGB(243, 223, 205)'"></div>
                <div class="boxes" v-tooltip="'RGB(240, 247, 252)'"></div>       
                <div class="boxes" v-tooltip="'RGB(193, 185, 139)'"></div>        
                <div class="boxes" v-tooltip="'RGB(227, 232, 228)'"></div>
              </div>
              <div class="pallet-color-dark">
                <div class="boxes" v-tooltip="'RGB(114, 100, 162)'"></div>
                <div class="boxes" v-tooltip="'RGB(0, 159, 214)'"></div>
                <div class="boxes" v-tooltip="'RGB(0, 158, 78)'"></div>
                <div class="boxes" v-tooltip="'RGB(250, 223, 34)'"></div>
                <div class="boxes" v-tooltip="'RGB(214, 69, 76)'"></div>
                <div class="boxes" v-tooltip="'RGB(212, 27, 98)'"></div>
                <div class="boxes" v-tooltip="'RGB(240, 247 ,252)'"></div>       
                <div class="boxes" v-tooltip="'RGB(110, 90, 85)'"></div>        
                <div class="boxes" v-tooltip="'RGB(52, 51, 54)'"></div>
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
                <span class="ColorName">16</span>
                <span class="ColorName"><b>B</b></span>
                <span class="ColorName">17</span>
                <span class="ColorName">18</span>
                <span class="ColorName">19</span>
              </div>
            </div>
              <div data-theme="black" class="light-to-dark">
                  <div class="boxes" v-tooltip="'RGB(243, 243, 243)'"></div>
                  <div class="boxes" v-tooltip="'RGB(217, 217, 217)'"></div>
                  <div class="boxes" v-tooltip="'RGB(194, 194, 194)'"></div>
                  <div class="boxes" v-tooltip="'RGB(178, 178, 178)'"></div>
                  <div class="boxes" v-tooltip="'RGB(157, 157, 157)'"></div>
                  <div class="boxes" v-tooltip="'RGB(142, 142, 142)'"></div>
                  <div class="boxes" v-tooltip="'RGB(128, 128, 128)'"></div>
                  <div class="boxes" v-tooltip="'RGB(115, 115, 115)'"></div>
                  <div class="boxes" v-tooltip="'RGB(104, 104, 104)'"></div>
                  <div class="boxes" v-tooltip="'RGB(93, 93, 93)'"></div>
                  <div class="boxes" v-tooltip="'RGB(84, 84, 84)'"></div>
                  <div class="boxes" v-tooltip="'RGB(75, 75, 75)'"></div>
                  <div class="boxes" v-tooltip="'RGB(68, 68, 68)'"></div>
                  <div class="boxes" v-tooltip="'RGB(61, 61, 61)'"></div>
                  <div class="boxes" v-tooltip="'RGB(55, 55, 55)'"></div>
                  <div class="boxes" v-tooltip="'RGB(50, 50, 50)'"></div>
                  <div class="boxes" v-tooltip="'RGB(45, 45, 45)'"></div>
                  <div class="boxes" v-tooltip="'RGB(41, 41, 41)'"></div>
                  <div class="boxes" v-tooltip="'RGB(37, 37, 37)'"></div>
                  <div class="boxes" v-tooltip="'RGB(34, 34, 34)'"></div>
            </div>
          </div>
        </div>

        <div v-for="(object, key) in colorData" class="col-md-2" style="text-align: center;">
          {{ key }}
         <!--  <div v-for="(color, index) in object" v-if="index % Math.floor(object.length / 10) === 0 && index < Math.floor(object.length / 10) * 10" class="color-cell" v-bind:style="{ 'background-color': 'rgb(' + color['Red'] + ',' + color['Green'] +',' + color['Blue'] + ')'}" v-on:click="selectColor(key, color)" :title="color['Red'] + ',' + color['Green'] +',' + color['Blue']"> -->
        <div v-for="(color, index) in object" class="color-cell"  v-if="color['show'] == 1" v-bind:style="{ 'background-color': 'rgb(' + color['Red'] + ',' + color['Green'] +',' + color['Blue'] + ')'}" v-on:click="selectColor(key, color)" v-tooltip="'RGB('+color['Red'] + ', ' + color['Green'] +', ' + color['Blue']+')'">
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
      app.colorData = key + ' (' + Math.round(color.Red) + ', ' + Math.round(color.Green) + ', ' + Math.round(color.Blue) + ')';

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
