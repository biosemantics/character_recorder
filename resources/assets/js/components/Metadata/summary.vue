<template>
    <div class="row">
        <div class="col-md-12" style="font-size: 15px;">
         <b>4. Summary function: Please select a summary function for "{{ character_name }}": </b>
        </div>
        <div class="col-md-12">
            <select v-if="viewFlag == false" style="width: 100%;" v-model="childData" @change="handleDataFc()" :disabled="edit_created_other">
                <option value="range-percentile">range-percentile</option>
                <option value="mean">mean</option>
                <option value="median">median</option>
            </select>
            <div v-if="viewFlag == true" style="border: 1px solid grey;">
                {{ childData ? childData : '&nbsp;' }}
            </div>
        </div>

         <div class="col-md-12" style="font-size: 12px;">
             <br/>
            <b>How will the summary function be used?</b><br/>
            
            <div style="text-indent: 10px">For numerical characters only, the selected function will be used to compute an aggregate value from a set of values recorded for specimens, to be used in the description of the related taxon.
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
            }
        },
        props: {
            parentData: {
                type: String,
                default () {
                    return ''
                }
            }
        },
        methods: {
            // maybe onchagne may onclick whatever..
            handleDataFc: function () {
                this.$emit('interface', this.childData); // handle data and give it back to parent by interface
            }
        },
        beforeMount () {
            this.character_name = sessionStorage.getItem("characterName");
            this.viewFlag = (sessionStorage.getItem('viewFlag') == 'true');
            this.childData = this.parentData; // save props data to itself's data and deal with it
            if (this.childData == '') {
                this.childData = 'range-percentile';
//                this.$emit('interface', this.childData); // handle data and give it back to parent by interface
            }
            this.edit_created_other = (sessionStorage.getItem('edit_created_other')=='true');
        }
    }
</script>

