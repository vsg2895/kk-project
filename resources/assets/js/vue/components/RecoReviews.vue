<template>
    <div class="col-sm-12" v-if="isEnabled">
        <iframe :src="recoWidgetSrc" width="100%" height="225" scrolling="no"
                style="border:0;display:block;" data-reactroot=""></iframe>
    </div>
    <div class="col-sm-12" v-else>
      <div style="height:225px; width:100%; clear:both;"></div>
    </div>
</template>

<script>
    export default {
        name: "RecoReviews",
        props: {
            customUrl: {
                type: Boolean,
                default: false,
            },
            url: {
                type: String
            },
            school: {
                type: Object,
                required: true
            }
        },
        computed: {
            isEnabled() {
                console.log('Reviews Widget | RecoID :', this.school.reco_id, '. Enabled :', this.school.reco_enabled);

                if (this.customUrl) {
                    return true;
                }

                return this.school.reco_enabled;
            },
            recoWidgetSrc() {
                return this.customUrl ?
                    this.url :
                    `https://widget.reco.se/v2/widget/${this.school.reco_id}?mode=KORKORTSJAKTEN`;
            }
        },
        mounted() {
            console.log('Comparison Widget | RecoID :', this.school.reco_id, '. Enabled :', this.isEnabled);
        }
    }
</script>

<style scoped>

</style>
