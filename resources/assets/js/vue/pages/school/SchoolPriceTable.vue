<template>
  <div class="comparison-table" v-if="activeVehicle">
    <ul class="nav nav-tabs nav-tabs-lined">
      <li class="nav-item" v-for="vehicle in vehicles" @click="changedVehicle(vehicle)">
        <a class="nav-link" v-bind:class="{ active: activeVehicle.id === vehicle.id }">
          <icon :name="vehicle.name.toLowerCase()"/>
          <span v-text="vehicle.label"></span>
        </a>
      </li>
    </ul>
    <table class="table active">
      <tbody>
      <tr class="comparison-segment" v-for="segment in activeSegments">
        <th v-if="prices && prices[segment.name]" class="description">
          <label class="custom-control custom-checkbox">
            <input class="custom-control-input" :id="segment.name" :value="segment.name"
                   v-model="selectedSegments" type="checkbox">
            <span class="custom-control-indicator"></span>
            <span class="custom-control-description">
                                <h4 class="label"> <span v-text="segment.label"></span>
                                    <popup v-if="segment.description"
                                           :content="`Varje person behöver i genomsnitt ${getLessonsCount} st körlektioner x 40min`"></popup>
                                </h4>

                            <template v-if="segment.name === 'DRIVING_LESSON_CAR'">
                                <div class="comment small"
                                     v-text="40 * getLessonsCount + ' minuter körlektion'"></div>
                            </template>

                            <template v-else>
                                <div class="comment small" v-text="segment['default_comment'] || ''"></div>
                            </template>

                        </span>
          </label>
        </th>
        <td class="price" v-if="prices && prices[segment.name]">
          <template v-if="prices[segment.name] && (!prices[segment.name].comment && prices[segment.name]['price_per_lesson'])">
                        <span class="h3 text-numerical"
                              v-text="(segment.name === 'DRIVING_LESSON_CAR' ? parseInt((parseInt(prices[segment.name]['price_per_lesson']) / parseInt(prices[segment.name]['quantity'])) * 40 * getLessonsCount) : parseInt(parseInt(prices[segment.name]['price_per_lesson']) * getLessonsCount)) + ' kr'"></span>
          </template>

          <template v-else>
            <span class="h3 text-numerical" v-text="!prices[segment.name]['price'] ? segment.default_price + ' kr' : prices[segment.name]['price_suffix']"></span>
          </template>

          <div v-show="prices[segment.name] && prices[segment.name].comment" class="comment small"
               v-text="prices[segment.name]['comment']"></div>
          <div v-show="prices[segment.name] && (!prices[segment.name].comment && prices[segment.name]['price_per_lesson'])"
               class="comment small" v-text="`${prices[segment.name]['price_per_lesson']} kr/körlektion`">
          </div>
        </td>
      </tr>
      <tr class="comparison-table-sum">
        <th>
          <h4>Jämförpris <span v-text="activeVehicle.identifier"></span>-körkort</h4>
        </th>
        <td>
          <span class="h2 text-numerical" v-text="`${totalPrice} kr total`"></span>
        </td>
      </tr>
      </tbody>
    </table>

    <div class="clearfix">
      <a :href="routes.route('shared::pages.contact', {subject: 'rapportera', school: school})"
         class="invalid-info small text-muted float-xs-right">
        Rapportera felaktig prisinformation
        <icon size="sm" name="arrow-right"></icon>
      </a>
    </div>
  </div>
</template>

<script type="text/babel">
    import _ from 'lodash';
    import Icon from 'vue-components/Icon';
    import Popup from 'vue-components/Popup';
    import routes from 'build/routes';
    import {mapActions, mapGetters} from "vuex";

    export default {
        props: ['prices', 'vehicles', 'vehicleSegments', 'school'],
        components: {
            'icon': Icon,
            'popup': Popup
        },
        data() {
            return {
                activeVehicle: {},
                selectedSegments: [],
                routes
            }
        },
        watch: {
            vehicles() {
                this.changedVehicle(this.vehicles[0]);
            },
            vehicleSegments() {
                if (this.vehicles) {
                    this.changedVehicle(this.vehicles[0]);
                }
            }
        },
        computed: {
            ...mapGetters('config', ['getLessonsCount']),
            activeSegments() {
                if (this.activeVehicle) {
                        return _.filter(this.vehicleSegments, segment => {return segment.vehicle_id === this.activeVehicle.id && segment['comparable'] && this.prices[segment.name] && (this.prices[segment.name]['price'] ||  segment.default_price);
                    });
                }

                return [];
            },
            totalPrice() {
                if (this.prices) {
                    return _.reduce(this.selectedSegments, (total, name) => {
                        let price = 0;

                        if (this.prices[name] && this.prices[name].price) {
                            price = parseInt(
                                name !== "DRIVING_LESSON_CAR" ?
                                    this.prices[name].price :
                                    this.prices[name]['price_per_lesson'] * this.getLessonsCount
                            );
                        }

                        if (!price) {
                          var filterName = name;
                          let currentSegment = _.filter(this.vehicleSegments, segment => { return segment.name === filterName; });
                          price = parseInt(currentSegment[0].default_price ? currentSegment[0].default_price : 0);
                        }

                        return total + price;
                    }, 0);
                }

                return 0;
            }
        },
        methods: {
            ...mapActions('config', ['setLessonsCount']),
            changedVehicle(vehicle) {
                this.activeVehicle = vehicle;
                this.selectedSegments = _
                    .filter(this.vehicleSegments, segment => {
                        return segment.vehicle_id === this.activeVehicle.id && segment['comparable'];
                    })
                    .map(segment => {
                        return segment.name;
                    });
            },
            activeSegment(name) {
                return _.includes(this.selectedSegments, name);
            },
        },
        created() {
            this.setLessonsCount();

            if (this.vehicles.length) {
                this.changedVehicle(this.vehicles[0]);
            }
        }
    }
</script>
