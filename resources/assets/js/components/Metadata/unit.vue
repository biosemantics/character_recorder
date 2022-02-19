<template>
    <div class="row">
        <div class="col-md-12" style="font-size: 15px;">
            <b>2. Unit: Please select a unit for character "{{ character_name }}". You can change the unit later.</b>
        </div>
        <div class="col-md-12">
            <select v-if="viewFlag == false" style="width: 100%;" v-model="childData" @change="handleDataFc()" :disabled="edit_created_other">
                <option value="m">m</option>
                <option value="dm">dm</option>
                <option value="cm">cm</option>
                <option value="mm">mm</option>
                <option value="μm">μm</option>
            </select>
            <div v-if="viewFlag == true" style="border: 1px solid grey;">
                {{ childData ? childData : '&nbsp;' }}
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
            this.edit_created_other = (sessionStorage.getItem('edit_created_other')=='true');
        }
    }
</script>

