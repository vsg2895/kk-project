<template>
    <div class="col-md-6" v-if="organization.schools && organization.schools.length">
        <div v-if="this.errors" class="alert alert-danger alert-klarna">
            <strong>Tyvärr kunde inte Klarna behandla din förfrågan.</strong>
            <p>Var god se över informationen du angett och försök igen eller kontakta Körkortsjakten.</p>
        </div>

        <div class="klarna-process" v-if="!closedKlarna || !hasCompleted('COMPLETED')"
             :class="{'show-full-process': showFullProcess}">
            <h2>Kom igång med Klarna!</h2>

            <div class="klarna-process-container">
                <transition appear v-on:enter="klarnaScroll">
                    <div id="klarna-process-scroller" class="klarna-process-inner">

                        <div class="process-step step-kkj card card-block"
                             :class="{'is-current-step': isStep('NOT_INITIATED'), 'is-completed': hasCompleted('NOT_INITIATED')}">
                            <div class="loader-block" v-if="sending">
                                <div class="loader-indicator"></div>
                            </div>

                            <h4 class="step-title">1. Kom i gång med din försäljning idag!</h4>
                            <div class="step-description">
                                <p>Börja med att fylla i trafikskolans uppgifter under organisation, användare och
                                    användarprofil i menyn till vänster. För att trafikskolans försäljning ska
                                    aktiveras, vänligen acceptera villkoren.</p>
                                <p>Vi har integrerat delbetalning, kort, och faktura via Klarna till marknadens lägsta
                                    priser. Alla populära betalsätt finns inbyggda i Klarna Checkout. Det som ingår är
                                    faktura, konto, delbetalning, kortbetalning samt internetbank. Ni får en komplett
                                    kassalösning där era kunder kan handla tryggt och enkelt.</p>
                            </div>

                            <template v-if="!hasCompleted('NOT_INITIATED')">
                                <div class="klarna-card card card-block">
                                    <icon name="klarna-logo" class-name="logo-size"/>
                                    <div>Ökar din försäljning
                                        <icon name="check" size="md"/>
                                    </div>
                                    <div>Ingen risk
                                        <icon name="check" size="md"/>
                                    </div>
                                    <div>Enkelt att komma igång och använda
                                        <icon name="check" size="md"/>
                                    </div>
                                </div>

                                <div class="alert alert-sm alert-danger"
                                     v-if="disabledKlarna(false, true, $v.klarnaInformationValidations.$invalid)">
                                    <p>Det saknas uppgifter under <a href="" data-toggle="modal"
                                                                     data-target="#klarnaOnboardingInfo">Min
                                        information</a>. Fyll i alla uppgifter under användarprofil och organisation
                                        innan du kan acceptera villkoren.</p>
                                </div>
                                <template v-else>
                                    <div>
                                        <label class="custom-control custom-checkbox custom-control-inverse">
                                            <input class="custom-control-input" type="checkbox" v-model="terms"
                                                   :disabled="disabledKlarna(false, true, $v.klarnaInformationValidations.$invalid)">
                                            <span class="custom-control-indicator"></span>
                                            Jag har läst och accepterar
                                            <a target="_blank"
                                               href="https://cdn.klarna.com/1.0/shared/content/legal/terms/0/xx_xx/scheme">Klarnas
                                                villkor</a> och
                                            <a target="_blank" href="/villkor-trafikskola">Körkortsjaktens villkor</a>
                                            samt granskat
                                            <a href="" data-toggle="modal" data-target="#klarnaOnboardingInfo">min
                                                information</a>
                                        </label>
                                    </div>
                                    <button :disabled="disabledKlarna(sending, terms, $v.klarnaInformationValidations.$invalid)"
                                            class="btn btn-primary" @click="initiateKlarna()">Sätt igång
                                    </button>
                                </template>

                                <div class="help">
                                    <h4>Behöver du hjälp?</h4>
                                    <div>Ring <a :href="`tel:${getPhones.customers.regular}`"
                                                 v-text="getPhones.customers.text"></a>
                                    </div>
                                </div>
                            </template>

                            <div class="completed-icon">
                                <icon name="check" size="lg"/>
                            </div>
                        </div>

                        <div class="process-step step-klarna card card-block"
                             :class="{'is-current-step': isStep('WAITING'), 'is-completed': hasCompleted('WAITING')}">
                            <icon name="klarna-logo" :class-name="'logo-size'"/>
                            <h4 class="step-title">2. Klarna kontrollerar trafikskolans information</h4>
                            <p class="step-description">Klarna granskar nu att era uppgifter stämmer. Så fort detta är
                                klart kommer ni att få ett mail med några frågor från Klarna som trafikskolan ska
                                besvara.</p>

                            <div class="completed-icon">
                                <icon name="check" size="lg"/>
                            </div>
                        </div>

                        <div class="process-step card card-block"
                             :class="{'is-current-step': isStep('SUCCESS'), 'is-completed': hasCompleted('SUCCESS')}">
                            <h4 class="step-title">3. Alla betalningsmetoder är snart aktiverade</h4>
                            <div class="step-description">
                                <p>Ni kan nu erbjuda kortbetalning och till era kunder. För att ni ska bli helt klara
                                    med registreringen krävs att ni fått ett första köp och att alla svar till Klarna är
                                    inskickade.</p>
                                <p>Trafikskolan behöver inte koppla på någon kassalösning med betalningsfunktion. Den är
                                    redan på plats och klar att användas. Med Klarnas säkra och enkla köpprocess kan ni
                                    öka er försäljning rejält. Klarna tar all kredit- och bedrägeririsk och garanterar
                                    att ni alltid får betalt. Uppstartsutbildning och support via chatt, telefon och
                                    mail ingår alltid.</p>
                            </div>

                            <div class="completed-icon">
                                <icon name="check" size="lg"/>

                            </div>
                        </div>

                        <div class="process-step step-kkj card card-block"
                             :class="{'is-current-step': isStep('SUCCESS'), 'is-completed': hasCompleted('SUCCESS')}">
                            <h4 class="step-title">4. Besvara kontrollfrågorna för din trafikskola!</h4>
                            <p class="step-description">Skicka in svar på de frågor ni fått av Klarna via mail. Det är
                                viktiga uppgifter för att ansökan till Klarna ska bli genomförd. Frågorna handlar bland
                                annat om ägare och företagsinformation. Klarna vill även veta IBAN och BIC nummer som ni
                                alltid hittar under kontoinformation på erat bankkonto.</p>

                            <div class="completed-icon">
                                <icon name="check" size="lg"/>

                            </div>
                        </div>

                        <div class="process-step step-klarna card card-block"
                             :class="{'is-current-step': isStep('SUBMITTED'), 'is-completed': hasCompleted('SUBMITTED')}">
                            <icon name="klarna-logo" class-name="logo-size"/>
                            <h4 class="step-title">5. Kontrollfrågorna granskas av Klarna</h4>
                            <p class="step-description">Dina uppgifter granskas. Observera att ni inte kan bli helt
                                klara om ni inte fyllt i dessa frågor eller fått erat första köp.</p>

                            <div class="completed-icon">
                                <icon name="check" size="lg"/>

                            </div>
                        </div>

                        <div class="process-step card card-block"
                             :class="{'is-current-step': isStep('COMPLETED'), 'is-completed': hasCompleted('COMPLETED')}">
                            <h4 class="step-title">6. Samtliga betalningsmetoder är nu tillgängliga!</h4>
                            <p class="step-description">De kurser och paket som ni erbjuder via Körkortsjakten kan nu
                                betalas med de betalningsalternativ som kunden vill ha. Utbetalningarna sker varje
                                måndag efter att kunden har gått sin kurs. Klarna håller alltså kundens pengar till
                                kurstillfället har passerat. Kunden kan alltid avboka 48 h innan kurstillfället och får
                                då tillbaka sina pengar.</p>

                            <div class="completed-icon">
                                <icon name="check" size="lg"/>
                            </div>
                        </div>

                    </div>
                </transition>
            </div>

            <modal id="klarnaOnboardingInfo" class="modal-klarna-onboarding" v-if="onboarding">
                <template slot="title">
                    <h5 class="modal-title">Min information som kommer att skickas till Klarna</h5>
                    <small>All information måste vara i fylld</small>
                </template>

                <template slot="body">
                    <div class="row">
                        <div v-for="(val, key) in klarnaInformation" class="col-md-6 klarna-info-item">
                            <span class="text-muted" v-bind:class="{rejected: val.invalid}">{{key}}</span>
                            <p>{{ val.v ? val.v : '-' }}</p>
                        </div>
                    </div>
                </template>

                <template slot="footer">
                    <a :href="routes.organization" class="btn btn-primary pull-left">Redigera min organisation</a>
                    <a :href="routes.school" class="btn btn-primary pull-left">Redigera mina skolor</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Ok</button>
                </template>
            </modal>

            <div class="process-toggle">
                <button @click="toggleFullProcess" class="process-toggle-btn">
                    <template v-if="!showFullProcess">Visa samtliga steg
                        <icon name="angle-down"/>
                    </template>
                    <template v-else>Minimera steg
                        <icon name="angle-up"/>
                    </template>
                </button>
            </div>

            <button v-if="hasCompleted('COMPLETED')" @click="hideKlarna" class="klarna-dismiss">
                <icon name="cross" size="lg"/>
            </button>

        </div>
    </div>
    <div class="col-md-12" v-else-if="organization.schools && !organization.schools.length">
        <h3 class="card-title mb-0">Skapa din trafikskola!</h3>
        <p class="lead text-muted">Sätt igång genom att sätta upp din skolprofil nedan</p>
        <div class="card card-block">
            <create-school :sending="sending" :organization="organization" :on-create="storeSchool"></create-school>
        </div>
    </div>
</template>

<script type="text/babel">
    import Api from 'vue-helpers/Api';
    import CreateSchool from 'vue-pages/school/Create';
    import _ from 'lodash';
    import Icon from 'vue-components/Icon';
    import Modal from 'vue-components/Modal';
    import routes from 'build/routes.js';
    import {required, minLength, email} from 'vuelidate/lib/validators';
    import {organizationSecurityNumber} from 'components/CustomValidators.js';
    import {mapGetters} from "vuex";

    export default {
        klarnaProcess: '#klarna-process-scroller',

        props: {
            initialOrganization: {
                type: [Object],
                required: true,
                default: {}
            },
            onboarding: {
                type: Object
            }
        },
        components: {
            'create-school': CreateSchool,
            'icon': Icon,
            'modal': Modal
        },
        validations: {
            klarnaInformationValidations: {
                company_identification_number: {organizationSecurityNumber},
                admin_email: {required, email},
                admin_phone: {required},
                admin_given_name: {required, minLength: minLength(2)},
                admin_family_name: {required, minLength: minLength(2)},
                stores_brand: {required, minLength: minLength(2)},
                customer_service_email: {required, email},
                customer_service_phone: {required}
            }
        },
        computed: {
            ...mapGetters('config', ['getPhones']),
            progress() {
                let status = this.organization.sign_up_status;

                switch (status) {
                    case 'NOT_INITIATED':
                        return 0;
                    case 'WAITING':
                    case 'REJECTED':
                        return 25;
                    case 'SUCCESS':
                    case 'ACCESSED':
                    case 'CANCELLED':
                        return 50;
                    case 'SUBMITTED':
                        return 75;
                    case 'COMPLETED':
                        return 100;
                }
            }
        },
        data() {
            return {
                school: {},
                organization: this.initialOrganization,
                sending: false,
                terms: false,
                errors: null,
                klarnaInformation: [],
                klarnaInformationValidations: {},
                routes: {
                    organization: routes.route('organization::organization.show'),
                    school: routes.route('organization::schools.index')
                },
                closedKlarna: document.cookie.replace(/(?:(?:^|.*;\s*)kkjCloseKlarna\s*\=\s*([^;]*).*$)|^.*$/, "$1") === "true",
                showFullProcess: false,
                readMore: false,
            };
        },
        methods: {
            disabledKlarna(sending, terms, valid) {
                if (!sending && terms && !valid) {
                    return false;
                }
                return true;
            },
            async storeSchool(school) {
                this.sending = true;
                this.school = await Api.storeSchool(school);
                window.location = routes.route('organization::dashboard');
            },
            isStep(step) {
                let status = this.organization.sign_up_status;

                if (step == 'NOT_INITIATED') {
                    return status == 'NOT_INITIATED'
                } else if (step == 'REJECTED') {
                    return status == 'REJECTED';
                } else if (step == 'WAITING') {
                    return status == 'WAITING' ||
                        status == 'REJECTED';
                } else if (step == 'SUCCESS') {
                    return status == 'SUCCESS' ||
                        status == 'ACCESSED' ||
                        status == 'CANCELLED';
                } else if (step == 'SUBMITTED') {
                    return status == 'SUBMITTED';
                } else if (step == 'COMPLETED') {
                    return status == 'COMPLETED';
                }
            },
            hasCompleted(step) {
                let status = this.organization.sign_up_status;

                if (step == 'NOT_INITIATED') {
                    return status !== 'NOT_INITIATED'
                } else if (step == 'REJECTED') {
                    return status == 'REJECTED';
                } else if (step == 'WAITING') {
                    return status !== 'NOT_INITIATED' &&
                        status !== 'WAITING' &&
                        status !== 'REJECTED';
                } else if (step == 'SUCCESS') {
                    return status == 'SUBMITTED' ||
                        status == 'COMPLETED';
                } else if (step == 'SUBMITTED') {
                    return status == 'COMPLETED';
                } else if (step == 'COMPLETED') {
                    return status == 'COMPLETED';
                }
            },
            async initiateKlarna() {
                this.errors = null;
                this.sending = true;
                var response = await Api.initiateKlarnaOnboarding(this.organization);
                this.sending = false;

                if (response.ok) {
                    this.organization = response.body;
                    return;
                }

                this.errors = response.body.errors;
            },
            parseKlarnaInformation() {
                if (!this.onboarding) {
                    return;
                }
                this.klarnaInformationValidations = {
                    company_identification_number: this.onboarding.company.company_identification_number,
                    admin_email: this.onboarding.admin_email,
                    admin_phone: this.onboarding.admin_phone,
                    admin_given_name: this.onboarding.admin_given_name,
                    admin_family_name: this.onboarding.admin_family_name,
                    stores_brand: this.onboarding.stores[0].brand,
                    customer_service_email: this.onboarding.stores[0].customer_service_email,
                    customer_service_phone: this.onboarding.stores[0].customer_service_phone,
                };

                this.klarnaInformation = {
                    'Ditt namn': {
                        v: this.onboarding.admin_given_name + ' ' + this.onboarding.admin_family_name,
                        invalid: this.$v.klarnaInformationValidations.admin_family_name.$invalid || this.$v.klarnaInformationValidations.admin_given_name.$invalid
                    },
                    'E-post': {
                        v: this.onboarding.admin_email,
                        invalid: this.$v.klarnaInformationValidations.admin_email.$invalid,
                    },
                    'Telefonnummer': {
                        v: this.onboarding.admin_phone,
                        invalid: this.$v.klarnaInformationValidations.admin_phone.$invalid
                    },
                    'Organisationsnummer': {
                        v: this.onboarding.company.company_identification_number,
                        invalid: this.$v.klarnaInformationValidations.company_identification_number.$invalid
                    },
                    'Namn på organisation': {
                        v: this.onboarding.stores[0].brand,
                        invalid: this.$v.klarnaInformationValidations.stores_brand.$invalid
                    },
                    'Kontakt-e-post': {
                        v: this.onboarding.stores[0].customer_service_email,
                        invalid: this.$v.klarnaInformationValidations.customer_service_email.$invalid
                    },
                    'Kontakt telefonnummer': {
                        v: this.onboarding.stores[0].customer_service_phone,
                        invalid: this.$v.klarnaInformationValidations.customer_service_phone.$invalid
                    },
                    'Hemsida': {
                        v: this.onboarding.stores[0].website_url,
                        invalid: false
                    }
                };
            },
            toggleFullProcess() {
                this.showFullProcess = !this.showFullProcess;
            },
            klarnaScroll: function (el, done) {
                done()
                var scroll = $('.klarna-process-inner .is-current-step').position().top - 25;
                $('.klarna-process-inner').animate({scrollTop: scroll + 'px'}, 800);
            },
            hideKlarna() {
                document.cookie = "kkjCloseKlarna=true; expires=Fri, 31 Dec 9999 23:59:59 GMT";
                this.closedKlarna = true;
            }
        },
        mounted() {
            this.parseKlarnaInformation();
        },
        destroyed() {
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>
