<template>
  <div>
<!--     <div class="gc-header">
        <div class="container text-xs-center">
            <div class="row">
                <div class="col-md-12">
                    <h1>Årets present</h1>
                    <div>
                        <img src="/build/img/presentkort-page.png" class="clearfix" alt="presentkort">
                    </div>
                    <input type="button" value="KÖP PRESENTKORT NU" class="btn btn-success" @click="scrollToBody">
                </div>
            </div>
        </div>
    </div >-->
    <div class="gc-blurb">
    </div>
    <div class="gc-body">
      <div class="text-xs-center text-md-left">
        <div class="row">
          <div class="col-md-5 offset-md-1">
            <div class="connected-schools mb-3">
              <h2>Digitalt Presentkort</h2>
              <label>Köp ett presentkort till någon du tycker om!</label>
              <p style="word-break: break-word;">
                Ett smart och smidigt val! Presentkort är den perfekta presenten om du är osäker på vad du ska köpa, då kan den som får det själv välja bland våra kurser och trafikskolor.
                Här kan du snabbt och enkelt köpa ett digitalt presentkort med valfritt belopp. Få presentkortet direkt till din mail, för att sedan skicka det vidare till personen du vill ge det till, när du vill. Du kan även välja att skriva ut presentkortet och ge det direkt i handen.
                <br><br>Presentkortet kan nyttjas fritt hos alla trafikskolor som är anslutna till Körkortsjakten. Presentkorten fungerar som vilket betalmedel som helst och kan användas på flera av de anslutna trafikskolor om så önskas.
                <br><br>Digitala presentkort kan endast lösas in på körkortsjakten.se. Presentkort kan inte bytas eller lösas in mot kontanter, check eller kredit. Presentkort behandlas som kontanter; om detta kort tappas bort eller blir stulet kommer det inte att ersättas. Digitala presentkort är giltigt i 1 år från köpdatumet.
                <br>
                <br>
                <ul>
                  <li>Skriv in önskat belopp
                  </li>
                  <li>Lägg i varukorg</li>
                </ul>
              </p>
            </div>
          </div>
          <div class="col-md-6" style="padding-top: 6rem; padding-left: 6rem; padding-right: 6rem;">
            <div class="value mb-3">
              <h2>Värde</h2>
              <!-- <label for="">Välj värde</label> -->
              <!-- <semantic-dropdown :onItemSelected="onGiftCardTypeSelected" placeholder="Välj värde"
                                 :data="selectListPrices"></semantic-dropdown> -->
              <div class="form-group gift-card-section">
                <semantic-dropdown placeholder="Välj värde"
                                   :onItemSelected="onGiftCardTypeSelected" :data="selectListPrices">
                  <template slot="dropdown-item" slot-scope="props">
                    <div  class="item" :data-value="props.item.id">
                      <div class="item-text" v-text="props.item.name"></div>
                    </div>
                  </template>
                </semantic-dropdown>
              </div>
            </div>
          </div>
          <klarna-checkout v-show="false" :giftCardType="selectedGiftCardType" :validation="$v"
                           :order-id="klarnaOrderId"/>
        </div>
      </div>
    </div>
    <div id="klarna-checkout"></div>
  </div>
</template>
<script>
    import $ from 'jquery'
    import _ from 'lodash';
    import moment from 'moment';
    import Api from 'vue-helpers/Api'
    import Icon from 'vue-components/Icon.vue';
    import Checkout from 'vue-components/Checkout.vue'
    import SemanticSearch from 'vue-components/SemanticSearch.vue';
    import SemanticDropdown from 'vue-components/SemanticDropdown.vue';
    import SchoolTable from './SchoolTable.vue'
    import Smileys from 'vue-components/Smileys.vue';
    import routes from 'build/routes.js';
    import {
        required,
        email
    } from 'vuelidate/lib/validators';
    import AnalyticsService from 'services/AnalyticsService';

    export default {
        components: {
            Icon,
            SemanticSearch,
            SemanticDropdown,
            SchoolTable,
            Smileys,
            'klarna-checkout': Checkout
        },
        data() {
            return {
                greenPieValue: 37.5,
                grayPieValue: 19.4,
                cities: [],
                selectedCity: {
                    realId: 143
                },
                schools: [],
                searchingSchools: false,
                selectedSchool: false,
                giftCardTypes: [],
                selectedGiftCardType: null
            }
        },
        props: {
            klarnaOrderId: {
                default: null
            },
            bonus: Number
        },
        validations: {
            selectedGiftCardType: {
                required
            }
        },
        computed: {
            selectListPrices() {
                return this.giftCardTypes.map((giftCardType) => {
                    let bonusSum = (1 + this.bonus / 100) * giftCardType.price;
                    return {
                        ...giftCardType,
                        // name: `Presentkort ${giftCardType.price} kr (bonus ${this.bonus}%) Att handla för ${bonusSum} kr`
                        name: `Presentkort ${giftCardType.price} kr`
                    };
                });
            },
            searchListCities() {
                return this.cities ?
                    _.map(this.cities, city => {
                        return {
                            id: 'city-' + city.id,
                            realId: city.id,
                            name: city.name,
                            category: 'CITY',
                            slug: city.slug
                        }
                    }) : [];
            },
        },
        methods: {
            onCitySelect(city) {
                this.selectedCity = city;
                this.getSchools(city.realId)
            },
            getSelectedGiftCardType(giftCardTypeId) {
                for (let i = 0; i < this.giftCardTypes.length; i++) {
                    let giftCardType = this.giftCardTypes[i];
                    if (giftCardType.id === giftCardTypeId) {
                        return giftCardType;
                    }
                }

                return null;
            },
            onGiftCardTypeSelected(giftCardTypeItem) {
                this.selectedGiftCardType = giftCardTypeItem;
                this.scrollToCheckout();

                AnalyticsService.eventGift('selected', giftCardTypeItem);
            },
            scrollToBody() {
                let target = $('html,body');
                let scrollTop = Math.min(target[0].scrollHeight - window.innerHeight, $('.gc-body').offset().top);
                target.animate({
                    scrollTop
                }, 1000, 'swing');
            },
            scrollToCheckout() {
                let target = $('html,body');
                // let scrollTop = Math.min(target[0].scrollHeight - window.innerHeight, $('#klarna-checkout').offset().top);
                let scrollTop = $('#klarna-checkout').offset().top;
                target.animate({
                    scrollTop
                }, 1000, 'swing');
            },
            getSchools(schoolId = 0) {
                this.searchingSchools = true;
                Api.giftCardSchools(schoolId).then(results => {
                    this.schools = results;
                    this.searchingSchools = false;
                }).finally(() => {
                    this.searchingSchools = false;
                });
            },
            selectSchool(id) {
                let school = this.schools.find(s => {
                    return s.id === id;
                });
                this.selectedSchool = school ? {
                    id: school.id,
                    name: school.name,
                    rating: school.average_rating,
                    location: school.postal_city,
                    url: routes.route('shared::schools.show', {
                        citySlug: school.city.slug,
                        schoolSlug: school.slug
                    })
                } : false;
            },
            resetSelectedSchool() {
                this.selectedSchool = false;
            }
        },
        created() {
            Api.getCities().then(cities => {
                this.cities = cities;
            });

            Api.getGiftCardTypes().then(giftCardTypes => {
                this.giftCardTypes = giftCardTypes;
            });

            this.getSchools();
        }
    }
</script>
