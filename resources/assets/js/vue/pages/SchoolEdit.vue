<template>
    <div class="row">
        <div class="col-xl-6">
            <div class="form-group" :class="{ 'has-danger': $v.school.name.$invalid }">
                <label for="name">Namn på trafikskolan</label>
                <input class="form-control" placeholder="Namn på din trafikskola" type="text" id="name" name="name"
                       v-model="school.name" @input="$v.school.name.$touch()"/>
            </div>
            <div class="form-group" :class="{ 'has-danger': $v.school.address.$invalid }">
                <label>Adress</label>
                <google-address :initial="school.address" :on-update="addressUpdated"></google-address>
                <input type="hidden" name="latitude" v-model="school.latitude">
                <input type="hidden" name="longitude" v-model="school.longitude">
            </div>
            <div class="form-group" :class="{ 'has-danger': $v.school.coaddress.$invalid }">
                <label>C/O Adress</label>
                <input class="form-control" placeholder="C/O Adress" type="text" id="coaddress" name="coaddress"
                       v-model="school.coaddress" @input="$v.school.coaddress.$touch()"/>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" :class="{ 'has-danger': $v.school.postal_city.$invalid }">
                        <label for="postal_city">Postort</label>
                        <input class="form-control" placeholder="Postort" type="text" id="postal_city"
                               name="postal_city" v-model="school.postal_city" @input="$v.school.postal_city.$touch()"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group" :class="{ 'has-danger': $v.school.zip.$invalid }">
                        <label for="zip">Postnummer</label>
                        <input class="form-control" placeholder="Postnummer" type="text" id="zip" name="zip"
                               v-model="school.zip" @input="$v.school.zip.$touch()"/>
                    </div>
                </div>
            </div>
            <div class="form-group" :class="{ 'has-danger': $v.school.city_id.$invalid }">
                <label>Stad</label>
                <small class="form-text text-muted">I vilken stad på Körkortsjakten vill du att trafikskolan ska
                    grupperas i?
                </small>
                <input name="city_id" type="hidden" v-model="school.city_id">
                <semantic-dropdown :search="true" :initial-item="selectedCity ? selectedCity.id : ''"
                                   placeholder="Sök stad" :on-item-selected="cityChanged" :data="cities">
                    <template slot="dropdown-item" slot-scope="props">
                        <div class="item" :data-value="props.item.id">
                            <div class="item-text">{{ props.item.name }}</div>
                        </div>
                    </template>
                </semantic-dropdown>
            </div>
            <div class="form-group">
                <p>Fordonstyper som erbjuds</p>
                <label class="custom-control custom-checkbox" v-for="vehicle in vehicles">
                    <input class="custom-control-input" type="checkbox" name="vehicles[]" :value="vehicle.id"
                           :checked="hasVehicle(vehicle)">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description text">{{ vehicle.label }}</span>
                </label>
            </div>
            <div class="form-group">
                <p>Körkortsjaktens presentkort</p>
                <label class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" name="accepts_gift_card" value="true"
                           :checked="school.accepts_gift_card">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description text">Skolan tar emot körkortsjaktens presentkort</span>
                </label>
            </div>
            <div class="form-group" v-if="isAdmin">
                <p>Värd för digitala kurser</p>
                <label class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" name="host_digital" value="true"
                           :checked="school.host_digital">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description text">Värd för digitala kurser</span>
                </label>
            </div>
          <div class="form-group" v-if="isAdmin">
                <p>Top Partner</p>
                <label class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" name="top_partner" value="true"
                           :checked="school.top_partner">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description text">Top Partner</span>
                </label>
          </div>
          <div class="form-group" v-if="isAdmin">
                <p>Connected</p>
                <label class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" name="connected_to" value="true"
                           :checked="school.connected_to">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description text">Connected</span>
                </label>
          </div>

          <div class="form-group">
            <label>Loyalty Fixed Amount</label>
            <input class="form-control" placeholder="Loyalty Fixed Amount" type="text" id="loyalty_fixed_amount" name="loyalty_fixed_amount"
                   v-model="school.loyalty_fixed_amount" @input="$v.school.loyalty_fixed_amount.$touch()"/>
          </div>

          <div class="form-group" :class="{ 'has-danger': $v.school.bankgiro_number.$invalid }">
            <label>Bankgironummer</label>
            <input class="form-control" placeholder="Bankgiro nummer" type="text" id="bankgiro_number" name="bankgiro_number"
                   v-model="school.bankgiro_number" @input="$v.school.bankgiro_number.$touch()"/>
          </div>

          <div class="form-group" :class="{ 'has-danger': $v.school.organization_number.$invalid }">
            <label>Organisationsnummer</label>
            <input class="form-control" placeholder="Organization number" type="text" id="organization_number" name="organization_number"
                   v-model="school.organization_number" @input="$v.school.organization_number.$touch()"/>
          </div>

          <div class="form-group" :class="{ 'has-danger': $v.school.moms_reg_nr.$invalid }">
            <label>Moms reg. nr</label>
            <input class="form-control" placeholder="Moms reg. nr" type="text" id="moms_reg_nr" name="moms_reg_nr"
                   v-model="school.moms_reg_nr" @input="$v.school.moms_reg_nr.$touch()"/>
          </div>

        </div>
        <div class="col-xl-6">
            <div class="form-group" :class="{ 'has-danger': $v.school.phone_number.$invalid }">
                <label for="phone_number">Telefonnummer</label>
                <input class="form-control" placeholder="Telefonnummer" type="text" id="phone_number"
                       name="phone_number" v-model="school.phone_number" @input="$v.school.phone_number.$touch()"/>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" :class="{ 'has-danger': $v.school.contact_email.$invalid }">
                        <label for="postal_city">Kontakt-e-post</label>
                        <input class="form-control" placeholder="Kontakt-email" id="contact_email"
                               name="contact_email" v-model="school.contact_email"
                               @input="$v.school.contact_email.$touch()"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group" :class="{ 'has-danger': $v.school.booking_email.$invalid }">
                        <label for="postal_city">Boknings-e-post</label>
                        <input class="form-control" placeholder="Boknings-email" id="booking_email"
                               name="booking_email" v-model="school.booking_email"
                               @input="$v.school.booking_email.$touch()"/>
                    </div>
                </div>
            </div>
            <div class="form-group" :class="{ 'has-danger': $v.school.website.$invalid }">
                <label for="website">Hemsida</label>
                <input class="form-control" placeholder="Hemsida" type="text" id="website" name="website"
                       v-model="school.website" @input="$v.school.website.$touch()"/>
            </div>
            <div class="form-group" :class="{ 'has-danger': $v.school.description.$invalid }">
                <label for="description">Beskrivning</label>
                <textarea id="description" name="description" placeholder="Beskrivning" class="form-control"
                          v-model="school.description">
                </textarea>
            </div>

            <div class="form-group">
                <label for="default_course_description">Standard kursbeskrivning</label>
                <textarea id="default_course_description" name="default_course_description"
                          placeholder="Standard kursbeskrivning" class="form-control"
                          v-model="school.default_course_description"></textarea>
            </div>
            <div class="form-group">
                <label for="default_course_confirmation_text">Standard kursbokningsbekräftelse</label>
                <textarea id="default_course_confirmation_text" name="default_course_confirmation_text"
                          placeholder="Standard kursbokningsbekräftelse" class="form-control"
                          v-model="school.default_course_confirmation_text"></textarea>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    import Api from 'vue-helpers/Api';
    import SemanticDropdown from 'vue-components/SemanticDropdown';
    import GoogleAddress from 'vue-components/GoogleAdress';
    import _ from 'lodash';
    import {required, minLength, email} from 'vuelidate/lib/validators';

    export default {
        props: {
            isAdmin: {
              default: false
            },
            initialSchool: {
                type: [Object],
                required: true,
                default: {}
            },
            oldData: {
                default: []
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
                name: {required, minLength: minLength(2)},
                address: {required},
                coaddress: {},
                postal_city: {required},
                zip: {required},
                city_id: {required},
                website: {required},
                phone_number: {required},
                contact_email: {required, email},
                booking_email: {required, email},
                description: {},
                default_course_confirmation_text: {},
                default_course_description: {},
                bankgiro_number: {required},
                moms_reg_nr: {required},
                organization_number: {required}
            }
        },
        data() {
            let school = {...this.initialSchool, ...this.oldData};
            return {
                cities: [],
                shouldValidate: false,
                initialCityId: null,
                school: school,
                selectedCity: null,
                vehicles: []
            };
        },
        methods: {
            async getCities() {
                this.cities = await Api.getCities();
                if (this.school.city_id) {
                    let city = _.find(this.cities, (city) => {
                        return city.id === this.school.city_id;
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
                this.$v.school.postal_city.$touch();

                let school = {...this.school, ...data};
                let city = _.head(_.filter(this.cities, (city) => {
                    return city.name.toLowerCase() === school.postal_city.toLowerCase();
                }));

                if (city) {
                    school.city_id = city.id;
                    this.selectedCity = city;
                }

                this.school = school;
            },
            cityChanged(city) {
                this.$set(this.school, 'city_id', city.id);
            },
            hasVehicle(vehicle) {
                return _.find(this.school.available_vehicles, (available) => {
                    return available.id === vehicle.id;
                });
            }
        },
        mounted() {
            this.getCities();
            this.getVehicles();
        },
        destroyed() {
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>
