<template>
    <div class="row">
        <div class="col-xl-6">
            <div class="form-group" :class="{ 'has-danger': $v.school.name.$invalid && shouldValidate, 'has-success': !$v.school.name.$invalid  }">
                <label for="name">Namn på trafikskolan</label>
                <input class="form-control" placeholder="Namn på din trafikskola" type="text" id="name" name="name" v-model="school.name" @input="$v.school.name.$touch()" />
            </div>
            <div class="form-group" :class="{ 'has-danger': $v.school.address.$invalid && shouldValidate, 'has-success': !$v.school.address.$invalid  }">
                <label>Adress</label>
                <google-address :initial="school.address" :on-update="addressUpdated"></google-address>
                <input type="hidden" name="latitude" v-model="school.latitude">
                <input type="hidden" name="longitude" v-model="school.longitude">
            </div>
            <div class="form-group" :class="{ 'has-danger': $v.school.coaddress.$invalid && shouldValidate, 'has-success': !$v.school.coaddress.$invalid  }">
                <label>C/O Adress</label>
                <input class="form-control" placeholder="C/O Adress" type="text" id="coaddress" name="coaddress" v-model="school.coaddress" @input="$v.school.coaddress.$touch()" />
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" :class="{ 'has-danger': $v.school.postal_city.$invalid && shouldValidate, 'has-success': !$v.school.postal_city.$invalid  }">
                        <label for="postal_city">Postort</label>
                        <input class="form-control" placeholder="Postort" type="text" id="postal_city" name="postal_city" v-model="school.postal_city" @input="$v.school.postal_city.$touch()" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group" :class="{ 'has-danger': $v.school.zip.$invalid && shouldValidate, 'has-success': !$v.school.zip.$invalid  }">
                        <label for="zip">Postnummer</label>
                        <input class="form-control" placeholder="Postnummer" type="text" id="zip" name="zip" v-model="school.zip" @input="$v.school.zip.$touch()" />
                    </div>
                </div>
            </div>
            <div class="form-group" :class="{ 'has-danger': $v.school.city_id.$invalid && shouldValidate, 'has-success': !$v.school.city_id.$invalid  }">
                <label>Stad</label>
                <small class="form-text text-muted">I vilken stad på Körkortsjakten vill du att trafikskolan ska grupperas i?</small>
                <input name="city_id" type="hidden" v-model="school.city_id">
                <semantic-dropdown :search="true" :initial-item="selectedCity ? selectedCity.id : ''" placeholder="Sök stad" :on-item-selected="cityChanged" :data="cities">
                    <template slot="dropdown-item" slot-scope="props">
                        <div class="item" :data-value="props.item.id">
                            <div class="item-text">{{ props.item.name }}</div>
                        </div>
                    </template>
                </semantic-dropdown>
            </div>
            <div class="form-group">
                <label>Fordonstyper som erbjuds</label>
                <div class="checkboxes" :class="{ 'has-danger': $v.school.vehicles.$invalid && shouldValidate, 'has-success': !$v.school.vehicles.$invalid  }">
                    <label class="custom-control custom-checkbox" v-for="vehicle in vehicles">
                        <input class="custom-control-input" type="checkbox" name="vehicles[]" v-model="school.vehicles" :value="vehicle.id">
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description text">{{ vehicle.label }}</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="form-group" :class="{ 'has-danger': $v.school.phone_number.$invalid && shouldValidate, 'has-success': !$v.school.phone_number.$invalid  }">
                <label for="phone_number">Telefonnummer</label>
                <input class="form-control" placeholder="Telefonnummer" type="text" id="phone_number" name="phone_number" v-model="school.phone_number" @input="$v.school.phone_number.$touch()" />
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" :class="{ 'has-danger': $v.school.contact_email.$invalid && shouldValidate, 'has-success': !$v.school.contact_email.$invalid  }">
                        <label for="postal_city">Kontakt-e-post</label>
                        <input class="form-control" placeholder="Kontakt-email" type="email" id="contact_email" name="contact_email" v-model="school.contact_email" @input="$v.school.contact_email.$touch()" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group" :class="{ 'has-danger': $v.school.booking_email.$invalid && shouldValidate, 'has-success': !$v.school.booking_email.$invalid  }">
                        <label for="postal_city">Boknings-e-post</label>
                        <input class="form-control" placeholder="Boknings-email" type="email" id="booking_email" name="booking_email" v-model="school.booking_email" @input="$v.school.booking_email.$touch()" />
                    </div>
                </div>
            </div>
            <div class="form-group" :class="{ 'has-danger': $v.school.website.$invalid && shouldValidate, 'has-success': !$v.school.website.$invalid  }">
                <label for="website">Hemsida</label>
                <input class="form-control" placeholder="Hemsida" type="text" id="website" name="website" v-model="school.website" @input="$v.school.website.$touch()" />
            </div>
            <div class="form-group" :class="{ 'has-danger': $v.school.description.$invalid && shouldValidate, 'has-success': !$v.school.description.$invalid  }">
                <label for="description">Beskrivning</label>
                <textarea id="description" name="description" placeholder="Beskrivning" class="form-control" v-model="school.description">
                </textarea>
            </div>
        </div>
        <div class="col-md-12">
            <div class="loader-block" v-if="sending">
                <div class="loader-indicator"></div>
            </div>
            <button :disabled="sending" class="btn btn-success" v-if="onCreate" @click="create">Skapa</button>
        </div>
    </div>
</template>

<script type="text/babel">
    import Api from 'vue-helpers/Api';
    import SemanticDropdown from 'vue-components/SemanticDropdown';
    import GoogleAddress from 'vue-components/GoogleAdress';
    import _ from 'lodash';
    import { required, minLength, email } from 'vuelidate/lib/validators';
    export default {
        props: {
            organization: {
                type: [Object],
                default: () => {return {}},
            },
            onCreate: {
                type: [Function]
            },
            oldData: {
                default: () => {return []},
            },
            sending: {
                default: false
            }
        },
        watch: {
            organization: {
                handler: () => null,
                deep: true
            },
            school: {
                handler() {
                    this.shouldValidate = false
                },
                deep: true
            }
        },
        components: {
            'semantic-dropdown': SemanticDropdown,
            'google-address': GoogleAddress
        },
        validations: {
            school: {
                name: { required, minLength: minLength(2) },
                address: { required },
                coaddress: {  },
                postal_city: { required },
                zip: { required },
                city_id: { required },
                vehicles: { required, minLength: minLength(1) },
                website: { required },
                phone_number: {required},
                contact_email: { required, email },
                booking_email: {required, email},
                description: {},
            }
        },
        data() {
            let schoolData = {
                name: this.organization.name,
                address: '',
                coaddress: '',
                postal_city: '',
                zip: '',
                latitude: '',
                longitude: '',
                city_id: null,
                website: '',
                contact_email: '',
                booking_email: '',
                description: '',
                phone_number: '',
                vehicles: [],
            };
            let school = {...schoolData, ...this.oldData};
            return {
                cities: [],
                shouldValidate: false,
                school: school,
                selectedCity: null,
                vehicles: []
            };
        },
        methods: {
            async getCities () {
                this.cities = await Api.getCities();
                if(this.school.city_id) {
                    let city = _.find(this.cities, (city) => {
                        return city.id == this.school.city_id;
                    });

                    if (city) {
                        this.selectedCity = city;
                    }
                }
            },
            async getVehicles() {
                this.vehicles = await Api.getVehicleTypes();
            },
            addressUpdated(data) {
                this.$v.school.postal_city.$touch()

                let school = {...this.school, ...data};
                let city = _.head(_.filter(this.cities, (city) => {
                    return city.name.toLowerCase() == school.postal_city.toLowerCase();
                }));

                if (city) {
                    school.city_id = city.id;
                    this.selectedCity = city;
                }

                this.school = school;
            },
            cityChanged (city) {
                this.$set(this.school, 'city_id', city.id);
            },
            create() {
                var formIsValid = !this.$v.school.$invalid;
                if (formIsValid) {
                    this.onCreate(this.school);
                } else {
                    this.shouldValidate = true
                }
            },
        },
        mounted () {
            this.getCities();
            this.getVehicles();
        },
        destroyed () {
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>
