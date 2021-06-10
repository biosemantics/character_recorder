<template>
    <div class="row">
        <div class="col-md-12" style="font-size: 20px;">
            "{{ character_name }}" has gone through the following changes since its creation:
        </div>
        <div class="col-md-12">
            <select style="width: 100%; height: 300px;" multiple>
                <option v-for="(eachData, index) in childData" :key="index">
                    {{ eachData }}
                </option>
            </select>
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
            }
        },
        props: {
            parentData: {
                type: Array,
                default () {
                    return []
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
        }
    }
</script>

