<template>
    <div class="school-info-block" :class="{'no-courses' : noCourses}" v-if="school">
        <div v-show="showSuccess || showFailure || hasGallery() || hasDescription" class="container mt-3">
            <div class="alert alert-success" role="alert" v-show="showSuccess">
                <button type="button" class="close" @click="showSuccess = !showSuccess" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>Tack för ditt email!</strong>
            </div>
            <div class="alert alert-danger" role="alert" v-show="showFailure">
                <button type="button" class="close" @click="showFailure = !showFailure" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>Något gick fel, var god försök igen senare.</strong>
            </div>
            <div class="row row-padding">
                <div class="col-md-12">
                    <div v-if="hasGallery()">
                        <slick :options="forSlickOptions" class="slider-for">
                            <div v-for="image in school.images">
                                <div class="slider-image-container">
                                    <img class="slider-image" :src="image.url.replace('storage/upload//storage/upload', 'storage/upload')" :alt="image.alt_text">
                                </div>
                            </div>
                        </slick>
                        <slick :options="navSlickOptions" class="slider-nav">
                            <div v-for="image in school.images">
                                <div class="slider-thumb-container">
                                    <img class="slider-thumb" :src="image.url.replace('storage/upload//storage/upload', 'storage/upload')" :alt="image.alt_text">
                                </div>
                            </div>
                        </slick>
                    </div>
                    <div>
                        <div id="hero-left">
                            <button @click="claimSchool()" v-if="!school.organization_id"
                                    class="btn btn-sm btn-outline-primary">Är detta din trafikskola?
                            </button>
                            <p v-if="claimSent">Förfrågan har skickats</p>
                        </div>
                        <p class="school-description">{{school.description}}</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-4 offset-md-4 hidden-md-down my-3">
          <div v-if="hasRating()" class="card ratings-card p-1">
            <slick :options="ratingSlickOptions" class="slider-for">
              <div v-for="rating in school.ratings" :key="rating.id">
                <h5 class="mb-2 text-muted">
                    <stars :rating="rating.rating"></stars>
                </h5>
                <h5 class="card-title">{{ rating.title ? rating.title : school.courses[0].name }}</h5>
                <p class="card-text">{{ rating.content }}</p>
<!--                <p class="card-text">{{ rating.content.length > 28 ? rating.content.substring(0, 28) + '...' : rating.content }}</p>-->
                <span class="card-link" v-if="rating.user && rating.user.name">{{ rating.user.name }} {{ getFormattedData(rating.created_at) }}</span>
              </div>
            </slick>
            <div class="card-footer font-weight-bold" aria-label="Average Rating">
                <div class="average-rating-score">{{ averageRating}}</div>
                <div>Skolans betyg {{ school.ratings.length }} Recensioner</div>
            </div>
          </div>
        </div>

      <div class="school-details">
        <div class="school-map card">
            <google-map
                :onInteract="boundsChanged"
                :onBoundsChanged="boundsChanged"
                :center="mapCenter"
                :marker-data="markerData" />
        </div>
        <div class="school-contacts">
          <h2 class="school-contacts__head">{{ school.name }}</h2>

          <div v-if="school.organization" class="school-contacts__registration">
            <div>{{ school.organization.name }}</div>
            <div v-if="school.organization && school.organization.org_number">Org.nr.
              {{ school.organization.org_number }}
            </div>
            <div v-if="school.address">{{ school.address }}</div>
          </div>
        </div>

      </div>
        <div class="container">
            <div class="row row-padding">
                <div class="offset-xl-2 col-lg-12 col-xl-8">
                    <div class="feedback">
                        <h2 class="feedback__question">Har du några frågor?</h2>
                        <div class="feedback__text">Ställ en fråga till Körkortsjakten om {{school.name}} och få svar
                            via mail
                        </div>
                        <a :href="routes.route('shared::pages.contact', { subject: 'school', school: school.id })">
                            <button class="btn btn-black feedback__button ">Skicka meddelande</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import Icon from 'vue-components/Icon';
    import GoogleMap from 'vue-components/Map';
    import SmileysBar from 'vue-components/SmileysBar';
    import SemanticDropdown from 'vue-components/SemanticDropdown';
    import Stars from 'vue-components/Stars';
    import routes from 'build/routes';
    import Api from 'vue-helpers/Api';
    import {required, email} from 'vuelidate/lib/validators';
    import moment from 'moment';
    import Slick from 'vue-slick';
    import RecoReviews from "vue-components/RecoReviews";

    export default {
        props: ['school', 'user', 'userRating', 'noCourses', 'averageRating'],
        components: {
            Icon,
            GoogleMap,
            SmileysBar,
            SemanticDropdown,
            Stars,
            Slick,
            RecoReviews
        },
        validations: {
            contact: {
                name: {required},
                email: {required, email},
                title: {required},
                message: {required}
            },
            review: {
                title: {required},
                content: {required},
                rating: {}
            }
        },
        data() {
            return {
                defaultTitles: ['Allmän fråga', 'Fråga om gjord bokning'],
                showSuccess: false,
                showFailure: false,
                boundsChanged: () => null,
                hideContactsOnMobile: true,
                claimSent: false,
                showContact: false,
                showReviewPanel: false,
                contact: {
                    name: '',
                    email: '',
                    title: '',
                    message: ''
                },
                review: {
                    rating: '',
                    title: '',
                    content: ''
                },
                routes,
                ratingSlickOptions: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                    autoplay: true,
                    autoplaySpeed: 2000,
                },
                forSlickOptions: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    asNavFor: '.slider-nav',
                    arrows: false,
                    fade: true,
                },
                navSlickOptions: {
                    slidesToShow: 8,
                    slidesToScroll: 8,
                    dots: false,
                    focusOnSelect: true,
                    infinite: false,
                    asNavFor: '.slider-for',
                    arrows: false,
                    responsive: [{
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 7,
                            slidesToScroll: 7,
                        }
                    }, {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 5,
                            slidesToScroll: 5,
                        }
                    }, {
                        breakpoint: 420,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                        }
                    }]
                }
            }
        },
        computed: {
            mapCenter() {
                let lat = parseFloat(this.school.latitude)
                let lng = parseFloat(this.school.longitude)
                return this.school && lat && lng ? {lat, lng} : {lat: 0, lng: 0};
            },
            markerData() {
                return this.school ? [this.school] : null;
            },
            locationLink() {
                return this.school ? `https://maps.google.com/?q=${this.school.latitude},${this.school.longitude}&ll=${this.school.latitude},${this.school.longitude}&z=14` : '#'
            },
        },
        methods: {
            titleChanged(data) {
                this.$set(this.contact, 'title', data);
            },
            async makeContact() {
                if (!this.$v.contact.$invalid && this.school.id) {
                    this.contact.school_id = this.school.id;
                    try {
                        var response = await Api.contactStore(this.contact);
                        this.contact = {
                            name: '',
                            email: '',
                            title: '',
                            message: '',
                            school_id: ''
                        };
                        this.$v.$reset();
                        this.showSuccess = true;
                        this.showFailure = false;
                        this.showContact = false;
                    } catch (err) {
                        console.error(err);
                        this.showSuccess = false;
                        this.showFailure = true;
                    }
                }
            },
            sendReview() {
                if (!this.$v.review.$invalid) {
                    try {
                        const review = {...this.review};
                        this.$emit('rateSchool', review);
                        this.review = {
                            rating: '',
                            title: '',
                            content: ''
                        };
                        this.$v.$reset();
                        this.showReviewPanel = false;
                        this.showSuccess = true;
                        this.showFailure = false;
                        this.showContact = false;
                    } catch (err) {
                        console.error(err);
                        this.showSuccess = false;
                        this.showFailure = true;
                    }
                }
                this.rateSchool(this.review.rating);
            },
            rateSchool(rating) {
                this.review.rating = rating;
            },
            async deleteRate() {
                this.$emit('deleteRate');
            },
            async claimSchool() {
                this.$emit('claimSchool');
            },
            getFormattedData(date) {
                return moment(date).format('DD MMM, YYYY');
            },
            readMore(event) {
                console.log();
            },
            hasGallery() {
                return this.school && this.school.images && this.school.images.length > 0;
            },
            hasRating() {
              return this.school && this.school.ratings && this.school.ratings.length > 0;
            },
            hasDescription() {
              return this.school && this.school.description;
            }
        }
    }
</script>
