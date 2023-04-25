<template>
    <div class="page-pages">
        <div class="container">

            <div class="alert alert-success" role="alert" v-show="showSuccess">
                <button type="button" class="close" @click="showSuccess = !showSuccess" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <strong>Tack för ditt email!</strong>
            </div>

            <div class="alert alert-danger" role="alert" v-show="showFailure">
                <button type="button" class="close" @click="showFailure = !showFailure" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <strong>Något gick fel, var god försök igen senare.</strong>
            </div>

            <h1 class="page-title">Kontakta oss</h1>

            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="form-group mb-1"
                         :class="{ 'has-danger': $v.contact.name.$error, 'has-success': !$v.contact.name.$invalid  }">
                        <span class="far fa-user form-control-icon"></span>
                        <input class="form-control form-control-lg" id="name" type="text" v-model="contact.name"
                               placeholder="Namn" @input="$v.contact.name.$touch()">
                    </div>

                    <div class="form-group mb-1"
                         :class="{ 'has-danger': $v.contact.email.$error, 'has-success': !$v.contact.email.$invalid  }">
                        <span class="far fa fa-envelope form-control-icon"></span>
                        <input class="form-control form-control-lg" id="email" type="text" v-model="contact.email"
                               placeholder="E-post" @input="$v.contact.email.$touch()">
                    </div>

                    <div class="form-group mb-1"
                         :class="{ 'has-danger': $v.contact.title.$error, 'has-success': !$v.contact.title.$invalid  }">
                        <label class="form-control-label mr-1">Ämne</label>
                        <semantic-dropdown :initial-item="contact.title" value-field="subject" placeholder="Välj ämne"
                                           :on-item-selected="titleChanged" :data="defaultTitles">
                            <template slot="dropdown-item" slot-scope="props">
                                <div class="item" :data-value="props.item.subject">
                                    <div @click="ieFix" class="item-text">{{ props.item.value }}</div>
                                </div>
                            </template>
                        </semantic-dropdown>
                        <input id="title" type="hidden" v-model="contact.title" @input="$v.contact.title.$touch()">
                    </div>

                    <div v-if="isConcerningSchool" class="form-group mb-1" :class="[ selectedSchool ? 'has-success' : 'has-danger' ]">
                        <label class="form-control-label mr-1">Trafikskola</label>
                        <semantic-dropdown :initial-item="selectedSchool" search="true" placeholder="Välj trafikskola"
                                           :on-item-selected="schoolChanged" :data="schools">
                            <template slot="dropdown-item" slot-scope="props">
                                <div class="item" :data-value="props.item.id">
                                    <div class="item-text">{{ props.item.name }}</div>
                                </div>
                            </template>
                        </semantic-dropdown>
                        <input id="school_id" type="hidden" v-model="contact.school_id">
                    </div>

                    <div class="mb-1" :class="{ 'has-danger': $v.contact.message.$error, 'has-success': !$v.contact.message.$invalid  }">
                        <textarea class="form-control" id="message" type="text" v-model="contact.message"
                                  placeholder="Meddelande" @input="$v.contact.message.$touch()">
                        </textarea>
                    </div>
                    <div class="loader-block" v-if="sending">
                        <div class="loader-indicator"></div>
                    </div>
                    <button v-else :disabled="$v.contact.$invalid" @click="makeContact" class="btn btn-sm btn-success send-btn-contactpage">Skicka</button>
                    <div class="col-sm-12 text-center">
                        <h2 class="font-weight-bold mt-3" v-text="`Eller ring oss på ${getPhones.customers.text}`"></h2>
                        <h2 class="mt-3">
                            Kundtjänst Öppettider: måndag-fredag 9.30-16.30
                            <br>
                            Lunch stängt 13.15-14.00
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    import SemanticDropdown from 'vue-components/SemanticDropdown';
    import Api from 'vue-helpers/Api';
    import _ from 'lodash';
    import {required, email} from 'vuelidate/lib/validators';
    import {mapGetters} from "vuex";

    export default {
        props: {
            subject: {
                type: String,
                default: 'false'
            },
            schoolId: {
                type: String,
                default: null
            }
        },
        components: {
            'semantic-dropdown': SemanticDropdown,
        },
        validations: {
            contact: {
                name: {required},
                email: {required, email},
                title: {required},
                message: {required}
            }
        },
        data() {
            return {
                defaultTitles: [
                    {
                        value: 'Allmän fråga',
                        subject: 'ask-question'
                    },
                    {
                        value: 'Fråga om gjord bokning',
                        subject: 'booking-question'
                    },
                    {
                        value: 'Felaktig prisinformation',
                        subject: 'incorrect-price'
                    }
                ],
                isConcerningSchool: !!this.schoolId,
                schools: [],
                showSuccess: false,
                showFailure: false,
                sending: false,
                contact: {
                    name: '',
                    email: '',
                    title: '',
                    message: '',
                    school_id: this.schoolId
                }
            }
        },
        methods: {
            ieFix(event) {
                $(document.activeElement).blur();
            },
            async makeContact() {
                var vm = this;
                if (!vm.$v.contact.$invalid) {
                    this.sending = true;
                    vm.showSuccess = false;
                    vm.showFailure = false;

                    var data = {
                        name: vm.contact.name,
                        email: vm.contact.email,
                        message: vm.contact.message,
                    };

                    var qa = _.find(vm.defaultTitles, function (qa) {
                        return qa.subject == vm.contact.title;
                    });

                    if (vm.contact.school_id && vm.isConcerningSchool) {
                        data.school_id = vm.contact.school_id;
                    }

                    data.title = qa.value;

                    if (vm.selectedSchool && vm.contact.school_id) {
                      data.title += ' (Customer came from school ' + vm.selectedSchool.name + ' ' + vm.selectedSchool.slug + ')' ;
                    }

                    var response = await Api.contactStore(data);

                    this.sending = false;

                    if (response.success === true) {
                        vm.contact = {
                            name: '',
                            email: '',
                            title: '',
                            message: '',
                            school_id: ''
                        };
                        vm.$v.$reset();
                        vm.showSuccess = true;
                        vm.showFailure = false;
                        window.scrollTo(0, 0);
                        return;
                    }
                    vm.showSuccess = false;
                    vm.showFailure = true;
                }
            },
            titleChanged(item) {
                let subject = item.subject;
                this.isConcerningSchool = subject == 'incorrect-price';
                this.$set(this.contact, 'title', subject);
            },
            schoolChanged(item) {
                this.$set(this.contact, 'school_id', item.id);
            },
            async getData() {
                let [schools] = await Promise.all([Api.getSchools()]);
                this.$set(this, 'schools', schools);
                this.preSelect();
            },
            preSelect() {

                if (this.subject == 'incorrect-price') {
                    this.titleChanged({subject: 'incorrect-price'});
                }
            }
        },
        computed: {
            ...mapGetters('config', ['getPhones']),
            selectedSchool() {
                var vm = this;
                return _.find(vm.schools, function (school) {
                    return school.id == vm.contact.school_id;
                })
            }
        },
        mounted() {
            this.getData();
        },
    }
</script>

<style lang="scss" type="text/scss">

</style>
