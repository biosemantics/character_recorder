<template>
  <div class="row">
    <div class="col-md-12" style="font-size: 15px;">
      <b>3. Tag: Please choose a tag for "{{ character_name }}": </b>
    </div>
    <div class="col-md-12" style="font-size: 12px;">
      You can change the tag later, but always choose a meaningful tag for a character! For an example, a group of
      characters about leaves should use the tag "Leaves".
      <br/>
    </div>
    <div class="col-md-12">
      <input v-if="viewFlag == false" style="width: 100%;" @change="handleDataFc()" :disabled="edit_created_other"
             type="text" list="user_tags" v-model="childData"/>
      <datalist id="user_tags" v-if="userTags.length > 0">
        <option v-for="(each, index) in userTags" :value="each.tag_name" :key="index">{{ each.tag_name }}</option>
      </datalist>
      <!--<select v-if="viewFlag == false" style="width: 100%;" v-model="childData" @change="handleDataFc()" :disabled="edit_created_other">-->
      <!--<option value="m">m</option>-->
      <!--<option value="dm">dm</option>-->
      <!--<option value="cm">cm</option>-->
      <!--<option value="mm">mm</option>-->
      <!--</select>-->
      <div v-if="viewFlag == true" style="border: 1px solid grey;">
        {{ childData ? childData : '&nbsp;' }}
      </div>
    </div>

    <div class="col-md-12" style="font-size: 12px;">
      <br/>
      <b>How will the tags be used?</b><br/>

      <div style="text-indent: 10px"> Tags are used to organize characters in the matrix view. Characters with the same
        tag will be in the same group and included on the same tab in the matrix.
      </div>
      <div style="text-indent: 10px"> Tags are also used to as the headings for a group of characters in the text
        description generated from the matrix.
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data: function () {
    return {
      childData: '',
      character_name: null,
      viewFlag: false,
      edit_created_other: false,
      userTags: [],
      searchText: '',
    }
  },
  props: {
    parentData: {
      type: String,
      default() {
        return ''
      }
    }
  },
  methods: {
    // maybe onchagne may onclick whatever..
    handleDataFc: function () {
      this.$emit('interface', this.childData); // handle data and give it back to parent by interface
    },
  },
  beforeMount() {
    var app = this;
    var userId = sessionStorage.getItem('userId');
    axios.get("/chrecorder/public/api/v1/user-tag/" + userId)
      .then(function (resp) {
        console.log('userTags', app.userTags);
        app.userTags = resp.data;
      });
    this.character_name = sessionStorage.getItem("characterName");
    this.viewFlag = (sessionStorage.getItem('viewFlag') == 'true');
    this.childData = this.parentData; // save props data to itself's data and deal with it
    this.edit_created_other = (sessionStorage.getItem('edit_created_other') == 'true');
  }
}
</script>

