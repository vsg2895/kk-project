<template>
    <div id="course-page">
        <template v-if="school && order.courses">
            <div class="container" v-if="user.role_id === 1">
                <div class="checkout-step">
                    <h1 class="text-xs-center">Boka {{ order.course.segment && order.course.segment.label }}</h1>
                    <div class="row">
                        <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                            <div v-if="school" class="course-card has-map">
                                <div class="card">
                                    <map-component v-if="mapReady" :center="mapCenter"
                                                   :marker-data="markerData"></map-component>
                                    <div class="card-block">
                                        <icon :name="vehicleIconName(order.course.segment && order.course.segment.vehicle_id)"></icon>
                                        <div class="type tag tag-pill tag-default">{{ order.course.segment &&
                                            order.course.segment.label }}
                                        </div>
                                        <div class="date">{{ order.course.start_time | courseStartDate }}</div>
                                        <div class="time text-numerical">{{order.course.start_hour}} -
                                            {{order.course.end_hour}}
                                        </div>
                                        <div class="">Tillgängliga platser: {{ order.course.seats }}</div>
                                        <div class="address">{{order.course.address}}
                                            <br>{{order.course.zip}} {{order.course.postal_city}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="checkout-step row" v-if="order.course.description">
                    <div class="col-lg-8 offset-lg-2">
                        <p class="text-center">{{ order.course.description }}</p>
                    </div>
                </div>
                <div class="checkout-step row">
                    <div class="col-lg-12">
                        <div v-if="order.course.segment && order.course.segment.id === 7">
                            <introduction-participants :tutors="order.tutors" :onAddTutor="addPersonByType"
                                                       :onRemoveTutor="removePersonByType" :students="order.students"
                                                       :onAddStudent="addPersonByType" :transmissionShow="false"
                                                       :onRemoveStudent="removePersonByType"></introduction-participants>
                        </div>
                        <div v-else>
                            <risk-one-participants :students="order.students"
                                                   :transmissionShow="order.course.segment && order.course.segment.id === 16"
                                                   :transmission="order.course.transmission"
                                                   :onAddStudent="addPersonByType"
                                                   :onRemoveStudent="removePersonByType"></risk-one-participants>
                        </div>
                    </div>
                </div>

                <template v-if="order.course.school && order.course.school.addons.length">
                    <div class="checkout-step-addons checkout-step row">
                        <div class="col-md-10 offset-md-1">
                            <div class="row">
                                <div class="col-sm-6 col-xl-4" v-for="addon in school.addons">
                                    <addon :addon="addon" :onAdd="addAddon" :onRemove="removeAddon"
                                           :quantity="addon | quantity(order)"></addon>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>


                <template v-if="klarnaAvailable">
                    <div v-if="useGiftCards" id="checkout-step-gift-card" class="checkout-step row"
                         :class="{ 'disabled' : !klarnaOrderValid || checkingGiftCard }">
                        <div class="col-md-8 offset-md-2">
                            <div v-if="checkingGiftCard" class="loader-indicator"></div>
                            <h3 v-if="giftCards.length">Har du ett presentkort eller en rabattkod som du vill använda?</h3>
                            <h3 v-else>Har du ett presentkort eller en rabattkod som du vill använda?</h3>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="gift-card" v-for="giftCard in giftCards">
                                        <icon name="gift-red"></icon>
                                        <span class="value">{{ giftCard.remaining_balance }}</span>
                                        <input type="button" value="Använd ej" class="btn btn-danger btn-sm"
                                               @click="removeGiftCard(giftCard.id)">
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="gift-card-token"
                                           placeholder="Presentkorts kod" v-model="pendingToken">
                                </div>
                                <div class="col-sm-4">
                                    <input class="btn btn-primary" :class="{ 'disabled' : !klarnaOrderValid }"
                                           type="button" value="Använd" @click="checkGiftCard">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-step row">
                        <cart :courses="[order.course]" :addons="order.addons"
                              :participants="order.students.length + order.tutors.length"
                              :gift-cards="uniqueGiftCards" :mountCollapsed="true" :enableCheckout="true"
                              :hasKlarna="true" :bookingFee="bookingFee"
                              :disabled="!klarnaOrderValid || !hasEnoughSeats" @toCheckout="toPayment"></cart>
                    </div>

                    <div id="checkout-step-payment" class="checkout-step text-xs-center" v-if="visibleNextAction">
                        <div class="form-block">
                            <button @click="toPayment()"
                                    :disabled="!klarnaOrderValid || (state === 'processing') || !hasEnoughSeats"
                                    class="btn btn-lg btn-success book-btn-coursepage">
                                Gå vidare
                            </button>
                            <p class="error-message text-danger"></p>
                            <p class="text-danger" v-if="!hasEnoughSeats">Du försöker boka fler platser än vad som finns
                                tillgängligt. Just nu finns det {{ order.course.seats
                                }} platser.</p>
                            <p class="text-danger" v-if="!klarnaOrderValid">Se till så att alla fält är ifyllda med
                                korrekt information för att genomföra bokningen.</p>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <div id="checkout-step-login" class="checkout-step row row-inline row-inline-center"
                         v-if="!user.id">
                        <div class="col-xs-12 col-md-6 col-lg-4">
                            <h4>Redan medlem? Logga in och boka</h4>

                            <div class="form-group"
                                 :class="{ 'has-danger': $v.loginUser.email.$error && !isNewUserFormActive, 'has-success': !$v.loginUser.email.$invalid  }">
                                <label class="form-control-label" for="user-email">E-post</label>
                                <input type="email" name="email" class="form-control" id="user-email"
                                       placeholder="namn@exempel.se" v-model="loginUser.email"
                                       @input="$v.loginUser.email.$touch()">
                            </div>
                            <div class="form-group"
                                 :class="{ 'has-danger': $v.loginUser.password.$error && !isNewUserFormActive, 'has-success': !$v.loginUser.password.$invalid  }">
                                <label class="form-control-label" for="password">Lösenord</label>
                                <input type="password" class="form-control" id="password" v-model="loginUser.password"
                                       @input="$v.loginUser.password.$touch()">
                            </div>

                            <button class="btn btn-primary login-btn-coursepage mr-1" :disabled="$v.loginUser.$invalid"
                                    @click="logIn">Logga in
                            </button>
                            <a :href="routes.route('auth::password.forgot')" target="_blank">Glömt lösenord?</a>

                            <button v-if="state=='user-already-exists'" type="button"
                                    class="btn btn-sm btn-secondary mt-1" @click="state='neutral'">
                                Tillbaka
                            </button>
                        </div>
                        <div class="col-xs-12 col-md-6 col-lg-4" v-if="state!='user-already-exists'">
                            <h4>Registrera och boka</h4>

                            <div class="form-group"
                                 :class="{ 'has-danger': $v.user.given_name.$error && !isUserFormActive, 'has-success': !$v.user.given_name.$invalid  }">
                                <label class="form-control-label" for="given_name">Förnamn</label>
                                <input class="form-control" type="text" id="given_name" v-model="user.given_name"
                                       @input="$v.user.given_name.$touch()"/>
                            </div>

                            <div class="form-group"
                                 :class="{ 'has-danger': $v.user.family_name.$error && !isUserFormActive, 'has-success': !$v.user.family_name.$invalid  }">
                                <label class="form-control-label" for="family_name">Efternamn</label>
                                <input class="form-control" type="text" id="family_name" v-model="user.family_name"
                                       @input="$v.user.family_name.$touch()"
                                />
                            </div>

                            <div class="form-group"
                                 :class="{ 'has-danger': $v.user.email.$error && !isUserFormActive, 'has-success': !$v.user.email.$invalid  }">
                                <label class="form-control-label" for="email">E-post</label>
                                <input class="form-control" name="email" type="email" id="email"
                                       placeholder="namn@exempel.se" v-model="user.email"
                                       @input="$v.user.email.$touch()"
                                />
                            </div>
                            <div class="form-group"
                                 :class="{ 'has-danger': $v.user.phone_number.$error && !isUserFormActive, 'has-success': !$v.user.phone_number.$invalid  }">
                                <label class="form-control-label" for="phone_number">Mobilnummer</label>
                                <input class="form-control" type="tel" id="phone_number" placeholder="070 123 45 67"
                                       v-model="user.phone_number" @input="$v.user.phone_number.$touch()"
                                />
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="alert alert-danger" role="alert" v-show="state === 'failed'">
                        <button type="button" class="close" @click="state = 'neutral'" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Något gick fel, var god försök igen senare.</strong>
                    </div>

                    <div class="alert alert-danger" role="alert" v-show="state === 'user-already-exists'">
                        <button type="button" class="close" @click="state = 'neutral'" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>En användare med denna E-postadress existerar redan. Vänligen logga in.</strong>
                    </div>

                    <div class="loader-block" v-if="state === 'processing'">
                        <div class="loader-indicator"></div>
                    </div>

                    <div id="checkout-step-payment" class="checkout-step text-xs-center" v-else>
                        <div class="form-block">
                            <h4 class="mb-2">Betalning sker när du kommer till {{ order.course.school &&
                                order.course.school.name }}.</h4>
                            <button class="btn btn-lg btn-success book-btn-coursepage"
                                    v-if="visibleNextAction" @click="book()"
                                    :disabled="!orderValid || (state === 'processing') || !hasEnoughSeats"
                                    data-loading-text="<i class='icon-spinner icon-spin icon-large'></i>">
                                Boka
                            </button>

                            <p class="error-message text-danger"></p>
                            <p class="text-danger" v-if="!hasEnoughSeats">Du försöker boka fler platser än vad som finns
                                tillgängligt. Just nu finns det {{ order.course.seats
                                }} platser.</p>
                            <p class="text-danger" v-if="!orderValid">Se till så att alla fält är ifyllda med korrekt
                                information för att genomföra bokningen.</p>
                        </div>
                    </div>
                </template>

            </div>
            <div class="page-pages" v-else>
                <div class="container text-xs-center">
                    <h3>Logga ut för att boka kurser</h3>
                    <a class="btn btn-primary logout-btn-coursepage" :href="routes.route('auth::logout')">Logga ut</a>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import Api from 'vue-helpers/Api';
    import _ from 'lodash';
    import Checkout from 'vue-components/Checkout';
    import routes from 'build/routes.js';
    import Map from 'vue-components/Map';
    import Introduction from 'vue-pages/courses/Introduction';
    import RiskOne from 'vue-pages/courses/RiskOne';
    import Cart from 'vue-pages/courses/Cart';
    import Icon from 'vue-components/Icon';
    import Addon from 'vue-pages/courses/Addon';
    import moment from 'moment';

    import {
        required,
        requiredIf,
        minLength,
        between,
        email,
        numeric
    } from 'vuelidate/lib/validators';
    import {
        socialSecurityNumber
    } from 'components/CustomValidators.js';

    export default {
        components: {
            'klarna-checkout': Checkout,
            'map-component': Map,
            'introduction-participants': Introduction,
            'risk-one-participants': RiskOne,
            'cart': Cart,
            Icon,
            'addon': Addon
        },
        validations: {
            user: {
                email: {
                    required,
                    email
                },
                given_name: {
                    required
                },
                family_name: {
                    required
                },
                phone_number: {
                    required,
                    numeric
                }
            },
            loginUser: {
                email: {
                    required,
                    email
                },
                password: {
                    required
                }
            },
            order: {
                tutors: {
                    $each: {
                        given_name: {
                            required,
                            minLength: minLength(2)
                        },
                        family_name: {
                            required,
                            minLength: minLength(2)
                        },
                        social_security_number: {
                            socialSecurityNumber
                        },
                        email: {
                            required,
                            email
                        },
                    }
                },
                students: {
                    $each: {
                        given_name: {
                            required,
                            minLength: minLength(2)
                        },
                        family_name: {
                            required,
                            minLength: minLength(2)
                        },
                        social_security_number: {
                            socialSecurityNumber
                        },
                        transmission: {
                            requiredIf: function (val) {
                              return ((typeof this.order !== 'undefined' && this.order && this.order.course && this.order.course.vehicle_segment_id === 16)
                                  && (val && val.length)) || (typeof this.order !== 'undefined' && this.order && this.order.course && this.order.course.vehicle_segment_id !== 16);
                            }
                        },
                        email: {
                            required,
                            email
                        },
                    }
                }
            }
        },
        props: {
            klarnaOrderId: {
                default: null
            },
            bookingFee: {
                required: true
            },
            courseIds: {
                default: () => []
            }
        },
        data() {
            return {
                checkingGiftCard: false,
                pendingToken: '',
                state: 'neutral',
                routes: routes,
                start_time: '',
                showMap: true,
                map: null,
                mapInited: false,
                markerData: [],
                school: {},
                order: {
                    addons: [],
                    courses: [],
                    course: {},
                    tutors: [],
                    students: [],
                    payment_method: 'KLARNA',
                    new_user: {},
                    gift_card_tokens: [],
                },
                giftCards: [],
                user: {
                    role_id: 1,
                    email: '',
                    given_name: '',
                    family_name: '',
                    phone_number: ''
                },
                loginUser: {
                    email: '',
                    password: ''
                }
            };
        },
        computed: {
            visibleNextAction() {
                return (this.order.students.length + this.order.tutors.length) > 0;
            },
            mapReady() {
                return !!this.markerData && !!this.school.longitude && !!this.school.latitude;
            },
            schoolLink() {
                if (this.school && this.school.slug && this.school.city.slug)
                    return routes.route('shared::schools.show', {
                        schoolSlug: this.school.slug,
                        citySlug: this.school.city.slug
                    })
            },
            isUserFormActive() {
                return !!this.loginUser.password || !!this.loginUser.email
            },
            isNewUserFormActive() {
                return !!this.user.given_name || !!this.user.family_name || !!this.user.phone_number || !!this.user.email
            },
            orderValid() {
                return !this.$v.order.$invalid && (this.user.id || !this.$v.user.$invalid)
            },
            klarnaOrderValid() {
                return !this.$v.order.$invalid
            },
            hasEnoughSeats() {
                return (this.order.students.length + this.order.tutors.length) <= this.order.course.seats
            },
            klarnaAvailable() {
                return true;
            },
            mapCenter() {
                if (this.school.longitude && this.school.latitude) {
                    return {
                        lat: parseFloat(this.school.latitude),
                        lng: parseFloat(this.school.longitude)
                    };
                }
                return {
                    lat: 0,
                    lng: 0
                };
            },
            uniqueGiftCards() {
                return _.uniq(this.giftCards);
            },
            useGiftCards() {
                return this.school && this.school.accepts_gift_card;
            }
        },
        filters: {
            courseStartDate(rawDate) {
                let date = moment(rawDate);
                if (date) {
                    return date.format('dddd [den] D MMMM')
                } else {
                    return ''
                }
            },
            quantity(addon, order) {
                let orderAddon = order.addons.find(a => a.id === addon.id);
                return orderAddon ? orderAddon.quantity : 0;
            }
        },
        watch: {
            user: {
                handler() {
                    if (this.useGiftCards) {
                        this.giftCards.push(...this.user.gift_cards.filter(gc => !!parseInt(gc.remaining_balance)));
                    }
                },
                deep: true
            },
            giftCards: {
                handler() {
                    if (this.useGiftCards) {
                        this.order.gift_card_tokens = this.uniqueGiftCards.map(gc => gc.token);
                    }
                },
                deep: true
            }
        },
        methods: {
            removeGiftCard(id) {
                this.giftCards = this.giftCards.filter(gc => gc.id !== id);
            },
            vehicleIconName(id) {
                const icons = {
                    2: 'mc',
                    3: 'moped'
                };

                return icons[id] || 'car';
            },
            async checkGiftCard() {
                if (!this.klarnaOrderValid) {
                    return //'adding gift cards disabled'
                }
                if (this.order.gift_card_tokens.includes(this.pendingToken)) {
                    return //'token already in order'
                }
                this.checkingGiftCard = true;
                let response = await Api.checkGiftCard(this.pendingToken);
                if (response.exists && !response.claimed) {
                    let giftCard = response.giftCard;
                    giftCard.token = this.pendingToken;
                    this.giftCards.push(giftCard)
                }
                this.checkingGiftCard = false;
                this.pendingToken = '';
            },
            removeCourse(index) {
                this.order.courses.splice(index, 1);
            },
            addPersonByType(type) {
                if (this.order.hasOwnProperty(type)) {
                    this.order[type].push({
                        given_name: '',
                        family_name: '',
                        social_security_number: '',
                        email: '',
                        transmission: type == 'students' ? '' : '',
                        courseId: this.order.course.id,
                    });
                }
            },
            async pendingExternarOrder() {
                if (this.state !== 'processing') {
                    this.state = 'processing';
                    if (this.klarnaOrderValid) {
                        await Api.pendingExternarOrder(this.order);
                    }
                    this.state = 'neutral';
                }
            },
            async book() {
                if (this.state !== 'processing') {
                    this.state = 'processing';
                    if (this.orderValid && this.hasEnoughSeats) {
                        if (this.user) {
                            this.order.new_user = this.user
                        }
                        let order = await Api.storeOrder(this.order);
                        if (order) {
                            var transactionProducts = [];
                            order.items.forEach((item) => {
                                transactionProducts.push({
                                    'sku': String(item.id),
                                    'name': String(item.name),
                                    'price': parseInt(item.amount), // DOES THIS FIX GA PRICE?
                                    'quantity': item.quantity
                                });
                            });
                            dataLayer.push({
                                'transactionId': String(order.id),
                                'transactionTotal': order.order_value,
                                'transactionProducts': transactionProducts
                            });
                            window.location = routes.route('shared::courses.confirmed', {
                                citySlug: this.order.course.city.slug,
                                schoolSlug: this.order.course.school.slug,
                                courseId: this.order.course.id
                            });
                        } else {
                            this.state = 'neutral';
                        }
                    }
                    this.state = 'neutral';
                }
            },
            toPayment() {
                if (this.klarnaAvailable && this.schoolLink && this.klarnaOrderValid && this.hasEnoughSeats) {
                    let paymentRoute = routes.route('shared::courses.payment', {
                        citySlug: this.order.course.city.slug,
                        schoolSlug: this.order.course.school.slug,
                        courseId: this.order.course.id
                    });

                    this.$localStorage.remove('order');
                    this.$localStorage.set('order', JSON.stringify(this.order), Object);

                    this.pendingExternarOrder().then(function () {
                        window.location = paymentRoute;
                    });
                }
            },
            removePersonByType(type, index) {
                if (this.order.hasOwnProperty(type) && (this.order.students.length > 1 || this.order.tutors.length > 1)) {
                    if (this.order[type][index]) {
                        this.order[type].splice(index, 1);
                    }
                }
            },
            addAddon() {
                return addon => {
                    let addons = this.order.addons;
                    let existingIndex = _.findIndex(addons, (existingAddon) => {
                        return addon.name === existingAddon.name;
                    });

                    existingIndex !== -1 ?
                        addons[existingIndex].quantity++ :
                        addons.push({
                            ...addon,
                            price: addon.pivot.price,
                            quantity: 1
                        });

                    this.$set(this.order, 'addons', addons);
                }
            },
            removeAddon() {
                return (addon) => {

                    let addons = this.order.addons;
                    let existingIndex = _.findIndex(addons, (existingAddon) => {
                        return addon.name === existingAddon.name;
                    });

                    existingIndex !== -1 ?
                        addons[existingIndex].quantity-- :
                        addons.splice(existingIndex, 1);

                    this.$set(this.order, 'addons', addons);
                }
            },
            async logIn() {
                let response = await Api.logIn(this.loginUser);
                if (response) {
                    this.user = response;
                    this.authenticated = true;

                    if (!this.order.students.length) {
                        return this.order.students[0] = {
                            given_name: this.user.given_name,
                            family_name: this.user.family_name,
                            email: this.user.email,
                            social_security_number: '',
                        };
                    }
                }
            },
            async getData() {
                let id = _.last(window.location.pathname.split('/'));
                let [course, user] = await Promise.all([Api.findCourse(id), Api.getLoggedInUser()]);
                if (parseInt(course.segment.vehicle_id) === 2) {
                    course.segment.label += ' MC';
                }
                this.markerData = [course];
                this.$set(this.order, 'course', course);
                this.school = course.school;

                if (course.school.has_klarna) {
                    this.$set(this.order, 'payment_method', 'KLARNA');
                }

                //this.start_time = this.order.course.start_time;

                if (user.status === 200) {
                    this.order.students.push({
                        given_name: user.given_name,
                        family_name: user.family_name,
                        email: user.email,
                        social_security_number: '',
                    });

                    this.$set(this, 'user', _.assign(this.user, user));
                }

                this.order.students.forEach(student => {
                    student.courseId = this.order.course.id;
                });
            },
        },
        updated() {
            if (!this.mapInited && this.order.course && parseInt(this.user.role_id) === 1 && window.initMap) {
                window.initMap();
                this.mapInited = true;
            }
        },
        created() {
            this.getData();
        }
    }
</script>
