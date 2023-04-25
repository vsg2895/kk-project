<template>
    <div>
        <div class="form-group">
            <label>Trafikskola</label>

            <semantic-dropdown :on-item-selected="schoolChanged" form-name="school_id" :search="true" :initial-item="selectedSchool" placeholder="Välj trafikskola" :data="schools">
                <template slot="dropdown-item" slot-scope="props">
                    <div class="item" :data-value="props.item.id">
                        <div class="item-text">{{ props.item.name }} ({{ props.item.city_name }})</div>
                    </div>
                </template>
            </semantic-dropdown>
        </div>

        <div class="form-group">
            <label for="usps">Erbjudande eller fördel</label>
            <input type="text" class="form-control" name="text" id="usps" v-model="usps.text" />
        </div>
    </div>
</template>

<script type="text/babel">
    import Api from './Api';
    import SemanticDropdown from './SemanticDropdown';
    import _ from 'lodash';
    import { required } from 'vuelidate/lib/validators';

    export default {
        props: {
            initialUsps: {
                type: [Object],
                default() {
                    return {}
                }
            },
            oldData: {
                default: []
            },
            isAdmin: {
                default: false
            },
            initialSchool: {
                default: null
            }
        },
        components: {
            'semantic-dropdown': SemanticDropdown,
        },
        validations: {
            usps: {
                text: { required }
            }
        },
        data() {
            const usps = {...this.initialUsps, ...this.oldData};

            return {
                usps: usps,
                schools: [],
                selectedSchool: null,
                initialized: false
            };
        },
        methods: {
            async getCities () {
                this.cities = await Api.getCities();
                if(this.usps.city_id) {
                    let city = _.find(this.cities, (city) => {
                        return city.id == this.usps.city_id;
                    });

                    if (city) {
                        this.selectedCity = city;
                    }
                }
            },
            async getSchools () {
                if (this.isAdmin) {
                    this.schools = await Api.getSchools();
                } else {
                    this.schools = await Api.getSchoolsForLoggedInUser();
                }
                if(this.usps.school_id || this.initialSchool) {
                    const id = this.usps.school_id ? this.usps.school_id : this.initialSchool;

                    let school = _.find(this.schools, (school) => {
                        return school.id == id;
                    });

                    if (school) {
                        this.selectedSchool = school;
                    }
                }
            },
            schoolChanged(school) {
                if (this.initialized || !this.usps.id) {
                    this.selectedSchool = school;
                    this.$set(this.usps, 'school_id', school.id);
                }

                this.initialized = true;
            },
        },
        mounted () {
            this.getSchools();
        },
        destroyed () {
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>
