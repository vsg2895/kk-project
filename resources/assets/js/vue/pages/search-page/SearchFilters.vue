<template>
    <div v-if="resultType !== 'COURSE'">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="decrease-font" v-text="header1"></h1>
            </div>
        </div>
        <div class="row hide-for-small">
            <div class="col-lg-12">
                <h6 v-html="head_desc "></h6>
            </div>
        </div>
        <div :class="{ hidden: collapsed }">
            <div class="col-lg-6 mb-1 p-0">
                <div class="form-group">
                    <semantic-dropdown id="cities" search="true" :initial-item="selectedCity" placeholder="Sök stad"
                                       formName="city_id" :on-item-selected="cityChanged" :data="cities" class="mt-0">
                        <template slot="dropdown-item" slot-scope="props">
                            <div class="item" :data-value="props.item.id">
                                <div class="item-text" v-text="props.item.name"></div>
                            </div>
                        </template>
                    </semantic-dropdown>
                </div>
            </div>
            <div class="col-lg-6 mb-1 p-0">
                <div class="form-group schools-search-fixer">
                    <semantic-dropdown id="schools" search="true" :initial-item="selectedSchools" multiple="true"
                                       placeholder="Välj trafikskolor" formName="school_id"
                                       :on-item-selected="schoolsChanged" :data="selectableSchools">
                        <template slot="dropdown-item" slot-scope="props">
                            <div class="d-flex align-items-center item" :data-value="props.item.id">
                                <span class="item-text">{{ props.item.name }}</span>
                                <span class="item-description text-muted">i {{ props.item.city_name }}</span>
                            </div>
                        </template>
                    </semantic-dropdown>
                </div>
            </div>
            <div class="col-lg-6 p-0">
                <div class="search-filter-vehicle d-flex justify-content-between">
                    <div v-for="vehicle in vehicles"
                        :key="vehicle.id"
                        class="search-filter-vehicle-wrapper"
                        :class="{ active: filter.vehicle_id === vehicle.id }">
                        <div class="form-checkbutton" :class="{ checked: filter.vehicle_id === vehicle.id }">
                            <input v-model="filter.vehicle_id" :id="vehicle.label" type="radio" name="vehicle_id" :value="vehicle.id" />
                            <label :for="vehicle.label">
                                <img v-if="vehicle.name" :src="`/build/img/${vehicle.label}.svg`">
                                <div v-if="vehicle.identifier === 'B'" class="vehicle-label">
                                    <span>{{ vehicle.identifier }}</span>
                                    <span>Personbil</span>
                                </div>
                                <div v-if="vehicle.identifier === 'A'" class="vehicle-label">
                                    <span>{{ vehicle.identifier }} Tung</span>
                                    <span>motorcykel</span>
                                </div>
                                <div v-if="vehicle.identifier === 'AM'" class="vehicle-label">
                                    <span>{{ vehicle.identifier }}</span>
                                    <span>Moped klass I</span>
                                </div>
                                <div v-if="vehicle.identifier === 'YKB'" class="vehicle-label">
                                    <span>{{ vehicle.identifier }}</span>
                                    <span>Förarutbildning</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-show='showFilters' class="filters-footer">
            <button id="apply-filters" class="btn btn-success float-xs-right" @click="showFiltersToggle()">Filtrera
            </button>
        </div>
    </div>
</template>


<script>
    import Icon from 'vue-components/Icon';
    import SemanticDropdown from 'vue-components/SemanticDropdown';
    import {mapActions, mapGetters} from "vuex";
    import $ from "jquery";

    export default {
        props: [
            'cityChanged',
            'cities',
            'schoolsChanged',
            'selectableSchools',
            'vehicles',
            'filter',
            'showFilters',
            'selectedCity',
            'selectedSchools',
            'showFiltersToggle',
            'resultType',
            'selectedVehicle',
            'selectedCourse'
        ],
        components: {
            Icon,
            'semantic-dropdown': SemanticDropdown,
        },
        data: function () {
            return {
                collapsed: true,
                header1: '',
                head_desc: '',
                headers1: {
                    'SCHOOL-1': 'Trafikskolor körkort',
                    'SCHOOL-2': 'Trafikskolor körkort',
                    'SCHOOL-3': 'Trafikskolor körkort',
                    'SCHOOL-1-city': 'Trafikskolor i $city',
                    'SCHOOL-2-city': 'Trafikskolor i $city',
                    'SCHOOL-3-city': 'Trafikskolor i $city',
                    'COURSE-1': 'Boka kurser för Ditt körkort',
                    'COURSE-1-6': 'Riskettan för Bil',
                    'COURSE-1-6-city': 'Riskettan för Bil i $city',
                    'COURSE-1-7': 'Introduktionskurs körkort',
                    'COURSE-1-7-city': 'Introduktionskurs $city',
                    'COURSE-1-13': 'Risktvåan körkort',
                    'COURSE-1-13-city': 'Risktvåan $city',
                    'COURSE-1-16': 'Teoriutbildning Bil',
                    'COURSE-1-16-city': 'Teoriutbildning Bil i $city',
                    'COURSE-2-10': 'Riskettan MC',
                    'COURSE-2-10-city': 'Riskettan MC $city',
                    'COURSE-3-15': 'Mopedkurs AM',
                    'COURSE-3-15-city': 'Mopedkurs AM $city'
                },
                header_descs: {
                    'SCHOOL-1': 'Jämför trafikskolor. Vi har information om kurser och priser för alla tarfikskolor. Hitta lediga tider, boka & betala online med Klarna. Snart har du ditt körkort!',
                    'SCHOOL-2': 'Jämför trafikskolor. Vi har information om kurser och priser för alla tarfikskolor. Hitta lediga tider, boka & betala online med Klarna. Snart har du ditt körkort!',
                    'SCHOOL-3': 'Jämför trafikskolor. Vi har information om kurser och priser för alla tarfikskolor. Hitta lediga tider, boka & betala online med Klarna. Snart har du ditt körkort!',
                    'SCHOOL-1-city': 'Jämför trafikskolor i $city. Vi har information om kurser och priser för alla trafikskolor i $city. Hitta lediga tider, boka & betala online med Klarna. Snart har du ditt körkort!',
                    'SCHOOL-2-city': 'Jämför trafikskolor i $city. Vi har information om kurser och priser för alla trafikskolor i $city. Hitta lediga tider, boka & betala online med Klarna. Snart har du ditt körkort!',
                    'SCHOOL-3-city': 'Jämför trafikskolor i $city. Vi har information om kurser och priser för alla trafikskolor i $city. Hitta lediga tider, boka & betala online med Klarna. Snart har du ditt körkort!',
                    'COURSE-1': ' Vi har information om kurser och priser för alla trafikskolor i Sverige. Hitta lediga tider, boka & betala online med Klarna. Snart har du ditt körkort!',
                    'COURSE-1-6': 'Hitta tider, boka & betala online! Riskettan är del 1 i den obligatoriska riskutbildningen för B-körkort (del 2 är halkbanan).',
                    'COURSE-1-6-city': 'Hitta tider i $city boka & betala online! Riskettan är del 1 i den obligatoriska riskutbildningen för B-körkort (del 2 är halkbanan).',
                    'COURSE-1-7': 'Introduktionsutbildning (tidigare kallad handledarutbildning) är ett krav för att få övningsköra privat. Både du och den du ska köra med måste gå kursen.',
                    'COURSE-1-7-city': 'Boka och betala online! Handledarutbildning (Introduktionsutbildning) i $city är ett krav för att få övningsköra privat. Både du och den du ska köra med måste gå kursen.',
                    'COURSE-1-13': 'Boka och betala online! Risktvåan (halkan) är en obligatorisk del av din körkortsutbildning.',
                    'COURSE-1-13-city': 'Boka och betala online! Risktvåan (halkan) i $city är en obligatorisk del av din körkortsutbildning.',
                    'COURSE-1-16': 'Förbered dig på teoriprovet med klassrumsledd undervisning. Många tycker att det är enklare än att läsa i boken.',
                    'COURSE-1-16-city': 'Tycker du att det är svårt att att läsa körkortsboken hemma? Låt en lärare undervisa och ta ut det viktigaste! Hitta kurstillfällen i $city',
                    'COURSE-2-10': 'Hitta tid, boka & betala online!Riskettan är del 1 i den obligatoriska riskutbildningen för B-körkort (del 2 är halkbanan).',
                    'COURSE-2-10-city': 'Boka och betala online! Riskettan för MC i $city. Ta Ditt MC-körkort nu.',
                    'COURSE-3-15': 'Boka och betala online! Mopedkurs. Ta Ditt moppe-körkort nu.',
                    'COURSE-3-15-city': 'Boka och betala online! Mopedkurs i $city. Ta Ditt moppe-körkort nu.'
                },
                courses: {
                    'introduktionskurser': 7,
                    'riskettan': 6,
                    'teorilektion': 16,
                    'risktvaan': 13,
                    'mopedkurs': 15,
                    'riskettanmc': 10
                },
                segments: window.location.pathname.split('/')
            }
        },
        watch: {
            resultType: function (value) {
                this.createSlug();
            },
            selectedCity: function (value) {
                this.createSlug();
            },
            selectedVehicle: function (value) {
                this.createSlug();
            },
            selectedCourse: function (value) {
                this.createSlug();
            }
        },
        methods: {
            ...mapActions('config', ['setLessonsCount']),
            createSlug() {
                let slug = this.resultType;

                if (typeof this.selectedVehicle !== "undefined") {
                    slug += '-' + this.selectedVehicle.id;
                }

                if (this.resultType === 'COURSE') {
                    slug += '-' + this.courses[this.segments[1]];
                } else {
                    this.collapsed = false;
                }

                if (typeof this.selectedCity !== "undefined") {
                    slug += '-' + 'city';
                }

                this.setHeaders(slug);
            },
            setVariables(text) {
                if (typeof this.selectedCity !== "undefined" && text) {
                    return text.replace(/\$city/g, this.selectedCity.name);
                }
                return text;
            },
            setHeaders(slug) {
                this.header1 = this.setVariables(this.headers1[slug]);

                if (this.selectedCity && this.selectedCity.info && this.selectedCity.info[this.segments[1]]) {
                    this.head_desc = this.selectedCity.info[this.segments[1]];
                } else {
                    this.head_desc = this.setVariables(this.header_descs[slug]);
                }
            }
        },
        mounted: function () {
          this.setLessonsCount();

          setTimeout(
            // filter toggle
            function () {
                $('.search-filter-vehicle-wrapper').on( "click", function() {
                    $('.search-filter-vehicle-wrapper').removeAttr('style');
                    $('.search-filter-vehicle-wrapper').removeClass('active');
                    $(this).toggleClass('active');
              })
            }, 5000
          );
        },
        computed: {
            ...mapGetters('config', ['getLessonsCountPlural', 'getLessonsCount']),
        }
    }
</script>

<style lang="scss">

    input {
        &.lessons-input {
            border: none;
            border-bottom: 1px solid black;
            background-color: #FFE8E4;
            text-align: center;

            &:focus, &:hover {
                outline: none;
            }

            &:focus {

            }

            &::-webkit-inner-spin-button,
            &::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
        }
    }
</style>
