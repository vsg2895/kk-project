<template>
    <iframe v-if="widgetCanBeDisplayed" :src="widgetUrl" frameborder="0" width="100%" height="40" scrolling="no"
            style="border:0;display:block;" data-reactroot=""></iframe>
</template>

<script>
    export default {
        name: "RecoComparisonWidget",
        props: {
            school: {
                type: Object,
                required: true
            }
        },
        computed: {
            widgetCanBeDisplayed() {
                return this.school.reco_enabled;
            },
            widgetUrl() {
                return `https://widget.reco.se/v2/widget/${this.school.reco_id}?mode=KORKORTSJAKTEN_3`;
            }
        },
        mounted() {
            console.log('Comparison Widget | RecoID :', this.school.reco_id, '. Displayable? :', this.widgetCanBeDisplayed);
        }
    }
</script>

<style scoped>

</style>
