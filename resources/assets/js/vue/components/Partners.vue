<template>
    <div v-if="partners.length">
        <div class="card">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-center p-1">
                <template v-if="partners.length > 4">
                    <div class="col-sm-1 nav-arrows" @click="$refs.slick.prev()">
                        <svg class="ico ico-angle-left"
                                xmlns="http://www.w3.org/2000/svg" width="5"
                                height="9" viewBox="0 0 5 9">
                            <path fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" vector-effect="non-scaling-stroke"
                                    d="M4.5.5l-4 4 4 4"></path>
                        </svg>
                    </div>
                    <div class="col-sm-10">
                        <slick :options="carouselOptions" ref="slick">
                            <Partner style="margin: 0 8px !important;" :id="partner.id"
                                        v-for="partner in partners"
                                        :img="partner.image"
                                        :partner="partner.partner"
                                        :description="partner['short_description']" :url="partner.url"
                                        :key="`partner__${partner.id}`"></Partner>
                        </slick>
                    </div>
                    <div class="col-sm-1 nav-arrows" @click="$refs.slick.next()">
                        <svg class="ico ico-angle-right"
                                xmlns="http://www.w3.org/2000/svg" width="5"
                                height="9" viewBox="0 0 5 9">
                            <path fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" vector-effect="non-scaling-stroke"
                                    d="M.5 8.5l4-4-4-4"></path>
                        </svg>
                    </div>
                </template>
                <template v-else>
                    <Partner :id="partner.id" v-for="partner in partners" :img="partner.image"
                                :partner="partner.partner"
                                :description="partner['short_description']" :url="partner.url"
                                :key="`partner__${partner.id}`"></Partner>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    import Partner from "vue-components/Partner";
    import Slick from 'vue-slick';

    import 'slick-carousel/slick/slick.scss';

    export default {
        name: "Partners",
        props: ['partners'],
        components: {
            Partner,
            Slick
        },
        data() {
            return {
                carouselOptions: {
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    dots: false,
                    arrows: false,
                    infinity: true,
                }
            }
        },
    }
</script>

<style scoped lang="scss">
    .nav-arrows {
        height: 174px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
