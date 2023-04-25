<template>
  <div>
    <div class="form-group">
      <label>Trafikskola</label>

      <semantic-dropdown :on-item-selected="schoolChanged" form-name="school_id" :search="true"
                         :readonly="readonly" :initial-item="selectedSchool" placeholder="Välj trafikskola"
                         :data="schools">
        <template slot="dropdown-item" slot-scope="props">
          <div class="item" :data-value="props.item.id">
            <div class="item-text">{{ props.item.name }} ({{ props.item.city_name }})</div>
          </div>
        </template>
      </semantic-dropdown>
    </div>

    <div class="form-group">
      <label>Adress</label>
      <google-address :initial="course.address" :on-update="addressChanged" :readonly="readonly"></google-address>
      <input type="hidden" name="latitude" v-model="course.latitude">
      <input type="hidden" name="longitude" v-model="course.longitude">
      <input type="hidden" name="postal_city" v-model="course.postal_city">
      <input type="hidden" name="zip" v-model="course.zip">
    </div>

    <div class="form-group">
      <label>Stad</label>
      <small class="form-text text-muted">I vilken stad vill du att kursen ska grupperas i?</small>

      <semantic-dropdown :on-item-selected="cityChanged" form-name="city_id" :search="true" :readonly="readonly"
                         :initial-item="selectedCity" :data="cities">
        <template slot="dropdown-item" slot-scope="props">
          <div class="item" :data-value="props.item.id">
            <div class="item-text">{{ props.item.name }}</div>
          </div>
        </template>
      </semantic-dropdown>
    </div>

    <div class="form-group ">
      <label for="address_description">Adressbeskrivning</label>
      <textarea class="form-control" name="address_description" id="address_description"
                v-model="course.address_description" :readonly="readonly"></textarea>
    </div>

    <h3>Typ av kurs</h3>
    <div class="form-group">
      <label>Kurs</label>
      <semantic-dropdown ref="segments" :on-item-selected="vehicleSegmentChanged" form-name="vehicle_segment_id"
                         :readonly="readonly" :initial-item="selectedVehicleSegment" :data="vehicleSegments">
        <template slot="dropdown-item" slot-scope="props">
          <div class="item" :data-value="props.item.id">
            <div class="item-text">{{ props.item.label }} ({{ props.item.vehicle.label}})</div>
          </div>
        </template>
      </semantic-dropdown>
    </div>

    <h3>Kursdetaljer</h3>

    <div class="form-group">
      <label for="start_time">Kursstart (ändras inte i framtiden)</label>
      <div>
        <el-date-picker v-model="startTime" type="datetime" :picker-options="pickerOptions" format="yyyy-MM-dd HH:mm"
                        placeholder="Välj datum och tid" :readonly="readonlyStartTime"></el-date-picker>
      </div>
      <input type="hidden" name="start_time" id="start_time" v-model="course.start_time">
    </div>

    <div class="form-group">
      <label for="length_minutes">Kurslängd (minuter)</label>
      <input type="number" class="form-control" name="length_minutes" id="length_minutes"
             v-model="course.length_minutes" :readonly="readonly"/>
    </div>

    <div class="form-group">
      <label for="price">Pris</label>
      <input type="number" class="form-control" name="price" id="price" v-model="course.price"
             :readonly="readonly"/>
    </div>

    <div class="form-group">
      <label for="price">Ordinarie Pris</label>
      <input type="number" class="form-control" name="old_price" id="old_price" v-model="course.old_price"
             :readonly="readonly"/>
    </div>

    <div class="form-group">
      <label for="description">Kursbeskrivning</label>
      <textarea class="form-control" name="description" id="description" v-model="course.description"
                :readonly="readonly"></textarea>
      <small>Du kan ändra standard meddelande för respektive skola, under redigeringen för skolan.</small>
    </div>

    <div class="form-group">
      <label for="confirmation_text">Bekäftelsemeddelande</label>
      <textarea class="form-control" name="confirmation_text" id="confirmation_text"
                v-model="course.confirmation_text" :readonly="readonly"></textarea>
      <small>Du kan ändra standard meddelande för respektive skola, under redigeringen för skolan.</small>
    </div>

    <div class="form-group">
      <label for="seats">Totalt antal platser</label>
      <input type="number" class="form-control" name="seats" id="seats" v-model="course.seats"/>
    </div>

    <div class="form-group" v-if="course.vehicle_segment_id === 32 && isAdmin">
      <label for="part">Part</label>
      <input type="text" class="form-control" name="part" id="part" v-model="course.part"/>
    </div>

    <div class="form-group" v-if="course.vehicle_segment_id === 16">
      <label for="transmission">Transmission type</label>
      <select id="transmission" name="transmission" class="form-control">
        <option value="" :selected="course.transmission == ''">All</option>
        <option value="manual" :selected="course.transmission == 'manual'">Manual</option>
        <option value="automatic" :selected="course.transmission == 'automatic'">Automatic</option>
      </select>
    </div>

  </div>
</template>

<script type="text/babel">
    import Api from 'vue-helpers/Api';
    import SemanticDropdown from 'vue-components/SemanticDropdown';
    import GoogleAddress from 'vue-components/GoogleAdress';
    import _ from 'lodash';
    import {required, minLength, email} from 'vuelidate/lib/validators';
    import moment from 'moment';

    export default {
        props: {
            initialCourse: {
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
        watch: {
            startTime(value) {
                this.$set(this.course, 'start_time', moment(value).set('second', 0).format('YYYY-MM-DD HH:mm'));
            }
        },
        components: {
            'semantic-dropdown': SemanticDropdown,
            'google-address': GoogleAddress
        },
        validations: {
            course: {
                address: {required},
                postal_city: {required},
                latitude: {required},
                longitude: {required},
                zip: {required},
                city_id: {required},
            }
        },
        data() {
            const course = {...this.initialCourse, ...this.oldData},
                date = new Date();

            return {
                pickerOptions: {
                    disabledDate: d => d < date
                },
                course,
                cities: [],
                selectedCity: null,
                schools: [],
                selectedSchool: null,
                vehicleSegments: [],
                selectedVehicleSegment: null,
                startTime: null,
                initialized: false,
                readonlyPrice: false,
                readonlyStartTime: false,
                readonly: false
            };
        },
        methods: {
            setStartTime() {
                let [yyyy, MM, dd, hh, mm] = moment(this.course.start_time).format('YYYY-MM-DD-HH-mm').split('-');
                this.startTime = new Date(parseInt(yyyy), (MM - 1), parseInt(dd), parseInt(hh), parseInt(mm));
            },
            async getCities() {
                this.cities = await Api.getCities();
                if (this.course.city_id) {
                    let city = _.find(this.cities, (city) => {
                        return city.id === this.course.city_id;
                    });

                    if (city) {
                        this.selectedCity = city;
                    }
                }
            },
            async getSchools() {
                if (this.isAdmin) {
                    this.schools = await Api.getSchools();
                } else {
                    this.schools = await Api.getSchoolsForLoggedInUser();
                }
                if (this.course.school_id || this.initialSchool) {
                    const id = this.course.school_id ? this.course.school_id : this.initialSchool;

                    let school = _.find(this.schools, (school) => {
                        return school.id === id;
                    });

                    if (school) {
                        this.selectedSchool = school;
                    }
                }
            },
            async getVehicleSegments() {
                if (this.course.school_id && location.search.indexOf('initialCourse') === -1) {
                    this.vehicleSegments = await Api.getVehicleSegmentsForSchool(this.course.school_id);
                } else {
                    this.vehicleSegments = await Api.getVehicleSegments(true);
                }

                if (!this.isAdmin) {
                  var segments = [];
                  this.vehicleSegments.forEach((vehicleSegment, i) => {
                    if (!vehicleSegment.admin_only) {
                      segments.push(vehicleSegment);
                    }
                  });
                  this.vehicleSegments = segments;
                }

                if (this.course.vehicle_segment_id) {
                    let vehicleSegment = _.find(this.vehicleSegments, (vehicleSegment) => {
                        return vehicleSegment.id === this.course.vehicle_segment_id;
                    });

                    if (vehicleSegment) {
                        this.selectedVehicleSegment = vehicleSegment;
                    } else {
                        this.selectedVehicleSegment = null;
                        this.vehicleSegmentChanged({id: null});
                        this.$refs.segments.applyInitial();
                    }
                } else {
                    this.selectedVehicleSegment = null;
                }
            },
            addressChanged(data) {
                this.$v.course.postal_city.$touch();

                let course = {...this.course, ...data};
                let city = _.head(_.filter(this.cities, (city) => {
                    return city.name.toLowerCase() === course.postal_city.toLowerCase();
                }));

                if (city) {
                    course.city_id = city.id;
                    this.selectedCity = city;
                }
                this.course = course;
            },
            cityChanged(city) {
                this.$set(this.course, 'city_id', city.id);
            },
            schoolChanged(school) {

                if (this.initialized || !this.course.id) {
                    this.selectedSchool = school;
                    this.$set(this.course, 'school_id', school.id);
                    this.$set(this.course, 'city_id', school.city_id);
                    this.$set(this.course, 'address', school.address);
                    this.$set(this.course, 'confirmation_text', this.course.confirmation_text ? this.course.confirmation_text : school.default_course_confirmation_text);
                    this.$set(this.course, 'description', this.course.description ? this.course.description : school.default_course_description);
                    this.addressChanged({
                        latitude: school.latitude,
                        longitude: school.longitude,
                        postal_city: school.city_name,
                        zip: school.zip,
                        address: school.address
                    });
                    this.getVehicleSegments();
                }

                this.initialized = true;
            },
            vehicleSegmentChanged(vehicleSegment) {
                this.$set(this.course, 'vehicle_segment_id', vehicleSegment.id);
            }
        },
        mounted() {
            if ((typeof this.course.start_time === 'string' && this.course.hasOwnProperty('id'))) {
              this.readonlyStartTime = true;
            }

            this.readonly = false;
            this.setStartTime();
            this.getCities();
            this.getSchools();
            this.getVehicleSegments();
        },
        destroyed() {
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>
