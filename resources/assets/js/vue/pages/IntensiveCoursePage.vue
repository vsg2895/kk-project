<template>
    <div id="intensive" class="intensive">
        <div id="intensive-hero" class="container-fluid intensive-hero">
            <div class="slogan">
                <div class="h1 display-4">Intensivkurser och paket för körkort</div>
                <div class="h2">Ta ditt körkort snabbt och effektivt med en intensivkurs eller ett paket</div>
            </div>
            <a href="#schools" class="btn btn-success">
                Visa kurser
            </a>
            <div class="usp-bubbles">
                <a class="usp-bubble usp-student" :href="routes.url('delbetalning', [])">
                    <div class="usp-title">
                        <div class="h2">
                            Betala med
                            <span class="klarna-span">Klarna.</span>
                            Betala direkt, senare eller dela upp betalningen.
                        </div>
                    </div>
                </a>
                <a class="usp-bubble usp-xmas">
                    <div class="usp-title">
                        <div class="h2">Boka redan nu -
                            Begränsat antal
                            <span class="kurser-span">kurser</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="container courses-section">
            <div class="row margin-top15px" >
                <div class="courses-cities-select offset-md-3 offset-xl-4 col-md-6 col-xl-4 col-12">
                    <template v-if="citiesData">
                        <p class="font-weight-bold">Sök stad</p>
                        <semantic-dropdown :on-item-selected="cityChanged" placeholder="Allt" :data="citiesData" :search="true" :initial-item="initialCity">
                            <template slot="dropdown-item" slot-scope="props">
                                <div class="item" :data-value="props.item.id">
                                    <div class="item-text">{{ props.item.name }}</div>
                                </div>
                            </template>
                        </semantic-dropdown>
                    </template>
                </div>
            </div>
            <div class="row">
                <div class="offset-lg-2 col-lg-8 padding15px text-center margin-top15px">
                    Hos följande trafikskolor kan du köpa intensivkurser och paket för körkort direkt hos Körkortsjakten.
                </div>
            </div>
            <div class="row course margin-top15px" id="schools">
                <div class="col-sm-12 col-md-6" v-for="school in schools">
                    <div class="course-item" :class="{active: school.top_deal}">
                        <div class="course-item-img" :class="{'empty-image': !school.image}">
                            <img class="image" :src="school.image" alt="">
                        </div>
                        <div class="course-item-content">
                            <div class="school-info">
                                <a class="school-link" :href="routes.route('shared::schools.show', {citySlug: school.city.slug, schoolSlug: school.slug})"><h2>{{school.name}}</h2></a>
                                <p>{{school.address}} {{school.postal_city}} {{school.zip}}, {{school.city.name}}</p>
                                <p class="attention" v-if="school.show_left_seats">Endast {{school.left_seats}} kvar!</p>
                            </div>
                            <div class="school-price">
                                <div>{{school.custom_addons.length}} paket från <span class="price">{{school.minimal_price}} kr</span></div>
                                <div>
                                    <span class="not-enough">Mer information?</span>
                                    <a :href="routes.route('shared::pages.contact')">
                                        <span class="ask">Skicka meddelande</span>
                                    </a>
                                </div>
                            </div>
                            <div class="top-deal">
                                <div class="top-deal-strip"></div>
                                <span>Top deal</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 offset-md-4">
                    <button class="intensive-button" v-on:click="loadMore" v-if="!disabledButton">
                        Visa fler erbjudanden
                    </button>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row m-3">
                <div class="col-lg-12">
                    <div class="h2 text-center">
                        Kör bil inom 2 veckor!
                    </div>
                </div>
            </div>
            <div class="row handle-car align-center">
                <div class="col-md-4 offset-md-2 offset-lg-0">
                    <div class="handle-car-item">
                        <div class="check-icon"></div>
                        <div class="handle-car-item-info">
                            <div class="h3">Ta ditt körkort snabbt</div>
                            <div>Du får körkort mycket snabbt, om 2-3 veckor</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="handle-car-item">
                        <div class="check-icon"></div>
                        <div class="handle-car-item-info">
                            <div class="h3">Fokusera</div>
                            <div>Du får inte uppehåll mellan dina körningar och hinner inte glömma det du lärt dig</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 offset-md-4 offset-lg-0">
                    <div class="handle-car-item">
                        <div class="check-icon"></div>
                        <div class="handle-car-item-info">
                            <div class="h3">Personligt upplägg</div>
                            <div>Individanpassade lösningar</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    import SemanticDropdown from 'vue-components/SemanticDropdown';
    import Api from 'vue-helpers/Api';
    import routes from 'build/routes.js';
    import { required, email } from 'vuelidate/lib/validators';

    export default {
        components: {
            'semantic-dropdown': SemanticDropdown
        },
        props: {
            initialCity: undefined
        },
        data() {
            return {
                page: 0,
                schools: [],
                cityId: '',
                citiesData: undefined,
                routes,
                disabledButton: 'false'
            }
        },
        methods: {
            loadMore: function () {
                this.page++;
                this.getSchools(this.makeQuery());
            },
            getSchools: function (query) {
                Api.searchSchools(query).then((response) => {
                    this.disabledButton = response.data.last_page === this.page;
                    this.addSchools(response.data.schools);
                });
            },
            getCities: function(){
                Api.getCities().then((response) => {
                    this.citiesData = response;
                });
            },
            addSchools: function (newSchools) {
                this.schools = this.schools.concat(newSchools)
            },
            cityChanged: function (city) {
                if (window.location.pathname === '/intensivkurser') {
                    history.pushState(null, null, '/intensivkurser/' + city.slug);
                } else {
                    history.pushState(null, null, city.slug);
                }
                this.page = 1;
                this.cityId = city.id;
                this.schools = [];
                this.getSchools(this.makeQuery());
            },
            makeQuery: function () {
                const query = {page: this.page};
                if (this.cityId) {
                    query.city_id = this.cityId;
                }
                return query;
            }
        },
        mounted: function () {
            this.getCities();

            if (this.initialCity) {
                this.cityId = this.initialCity.id;
                this.loadMore();

                return;
            }

            this.loadMore();
        }
    }
</script>

<style lang="scss" type="text/scss">
</style>
