import Vue from 'vue';
import $ from 'jquery';
import _ from 'lodash';
import i18n from 'vue-i18n';
import VueHead from 'vue-head';
import Vuelidate from 'vuelidate';
import BlockUI from 'vue-blockui';
import wysiwyg from "vue-wysiwyg";
import moment from 'moment-timezone';
import {DatePicker} from 'element-ui';
import VueResource from 'vue-resource';
import VueLocalStorage from 'vue-localstorage';
import lang from 'element-ui/lib/locale/lang/sv-SE';
import VueScrollTo from 'vue-scrollto';
import VueSweetalert2 from 'vue-sweetalert2';
import VueNotifications from 'vue-notification';
import vTitle from 'vuejs-title';

require('bootstrap-loader');
require("babel-polyfill");
require("babel-core/register");

let VueCookie = require('vue-cookie');
$.fn.dropdown = require('semantic-ui-dropdown');
$.fn.transition = require('semantic-ui-transition');

Vue.use(i18n);
Vue.use(VueHead);
Vue.use(BlockUI);
Vue.use(Vuelidate);
Vue.use(VueCookie);
Vue.use(VueResource);
Vue.use(CartGlobal);
Vue.use(wysiwyg, {});
Vue.use(VueSweetalert2);
Vue.use(VueLocalStorage);
Vue.use(VueScrollTo);
Vue.use(VueNotifications);
Vue.use(vTitle, {
    round: "7px",
    maxHeight: "500px",
    maxWidth: "300px",
    padding: "5px 10px"
});

Vue.prototype.window = window;
Vue.prototype.ONLINE_LICENSE_THEORY = 32;
Vue.prototype.YKB_SPECIALS = [33,34,35,36,37,38,39];

Vue.config.lang = 'sv';
Vue.locale('sv', lang);

Vue.component(DatePicker.name, DatePicker);

moment.tz.setDefault('Europe/Stockholm');
moment.locale('sv');

import Icon from 'vue-components/Icon';
import GiftCard from './vuecomponents/pages/gift-card/GiftCard.vue';
import Smileys from 'vue-components/Smileys';
import UspsForm from 'vue-components/UspsForm';
import NoResults from 'vue-components/NoResults';
import SearchBar from 'vue-components/SearchBar';
import PromoBanner from 'vue-components/PromoBanner';
import SmileysBar from 'vue-components/SmileysBar';
import Summary from 'vue-components/charts/Summary';
import UserTypes from 'vue-components/charts/UserTypes';
import SemanticDropdown from 'vue-components/SemanticDropdown';
import CourseStatisticsTable from 'vue-components/CourseStatisticsTable';

import LoginPage from 'vue-pages/LoginPage';
import SchoolPage from 'vue-pages/school/SchoolPage';
import CoursePage from 'vue-pages/CoursePage';
import SchoolEdit from 'vue-pages/SchoolEdit';
import CourseForm from 'vue-pages/courses/Form';
import CourseFormSchedule from 'vue-pages/courses/FormSchedule';
import CoursePaymentPage from 'vue-pages/courses/CoursePaymentPage';
import ContactPage from 'vue-pages/ContactPage';
import DatePickerForm from 'vue-pages/courses/DatePickerForm';
import ReportsDates from 'vue-pages/ReportsDates';
import InvoiceCreate from 'vue-pages/invoice/Create';
import SearchPage from 'vue-pages/search-page/SearchPage';
import PaymentPage from 'vue-pages/payment/PaymentPage';
import PaymentIframePage from "vue-pages/payment/PaymentIframePage";
import SchoolCreate from 'vue-pages/organization/CreateSchool';
import OrderPageOrganization from 'vue-pages/organization/OrderPage';
import OrganizationDashboard from 'vue-pages/organization/Dashboard';
import IntensiveCoursePage from 'vue-pages/IntensiveCoursePage';
import PostsPage from "vue-pages/blog/PostsPage";
import PostsPageShow from "vue-pages/blog/PostsPageShow";
import CommentsBlock from "vue-pages/blog/CommentsBlock";
import CommentCard from "vue-pages/blog/CommentCard";
import CommentForm from "vue-pages/blog/CommentForm";
import PostCreateForm from "vue-pages/blog/admin/PostCreateForm";
import PostEditForm from "vue-pages/blog/admin/PostEditForm";
import {store} from "./store";
import Partners from "vue-components/Partners";
import ImageUrlOrFile from "vue-components/ImageUrlOrFile";
import RecoReviews from "vue-components/RecoReviews";
import RecoComparisonWidget from "vue-components/RecoComparisonWidget";
import CoursesScheduler from "vue-components/CoursesScheduler";
import LoyaltyProgress from "vue-components/LoyaltyProgress";
import CartGlobal from "vue-pages/courses/CartGlobal";
import Api from "vue-helpers/Api";
import { mapGetters, mapState } from 'vuex';
import LandingCreateForm from "./vue/pages/blog/admin/LandingCreateForm";
import LandingEditForm from "./vue/pages/blog/admin/LandingEditForm";
import LeftSideFilter from "vue-pages/organization/LeftSideFilter";

$(function () {
    // csrf token protection
    window.Laravel = {csrfToken: $('meta[name="csrf-token"]').attr('content')};
    Vue.http.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');

    // time component tooltip
    $('time.time-tag').tooltip();

    // add confirmation to delete buttons. use data-confirm attribute.
    $('a,button,.btn').filter('[data-confirm]').on('click', function (e) {
        return confirm($(this).data('confirm'));
    });

    // user creation in admin view
    if ($('#user-create').length) {
        $('#role_id').on('change', function () {
            if ($(this).val() == 2) {
                $('#organization_id').parents('.form-group').show();
            } else {
                $('#organization_id').parents('.form-group').hide();
            }
        });
    }

    // filter toggle
    $('.search-filter-vehicle-wrapper').on("click", function() {
        const parent = $(this).parent();
        const elements = parent.find('.search-filter-vehicle-wrapper');
        elements.removeAttr('style');
        elements.removeClass('active');
        $(this).toggleClass('active');
    });

    // outdated browser
    $('#btnCloseUpdateBrowser').on('click', function (e) {
        $('#outdated').remove();
    });

    //School edit tabs
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        let hash = $(this)[0].hash;
        window.location.hash = hash.replace('#tab-', '');
    });

    $('.form-group .custom-control input[type=checkbox]').on('click', function () {
        let idName = $(this).attr("rel") === 'Addons' ? '#input-addon-id-' : '#input-customAddons-id-';
        $(idName + this.value).toggle();
    });

    //Redirect to course with city
    $('.search-page .search-filters .hitta').on('click', function (e) {
        e.preventDefault();
        top.location.href = $(this).attr('href') + '/' + $('#city_id').val();
        //top.location.href = routes.route('shared::' + $(this)., {citySlug: course.school.city.slug});
    });

    // read hash from page load and change tab
    let hash = document.location.hash;
    hash = hash.replace('#', '');
    let prefix = "#tab-";
    hash = prefix + hash;
    if (hash) {
        let selector = '.nav-tabs a[href="' + hash + '"]';
        $(selector).tab('show');
    }
});

const bodyEl = $('body');
let lastScroll = 0;

function fixHeaderOnScrollUp() {
    const currentScroll = bodyEl.scrollTop();
    if (currentScroll <= 0) {
        bodyEl.removeClass('scroll-up');
        return;
    }

    if (currentScroll > lastScroll && !bodyEl.hasClass('scroll-down')) {
        bodyEl.removeClass('scroll-up');
        bodyEl.addClass('scroll-down');
    } else if (currentScroll < lastScroll && bodyEl.hasClass('scroll-down')) {
        bodyEl.removeClass('scroll-down');
        bodyEl.addClass('scroll-up');
    }
    lastScroll = currentScroll;
};

new Vue({
    el: '#app',
    data() {
        return {
            isCollapsed: false,
            isCollapsedCart: false,
            start_time: '',
            windowWidth: window.innerWidth,
            console: console,
            _: _,
        }
    },
    watch: {
        isCollapsed: function () {
            $('#page-main').toggle('slow');

            $('#page-footer > div > div > div.row.road.road-right.hidden-md-up.mb-2').toggle('slow');
            $('#page-footer > div > div > div.row.road.road-center.mb-2').toggle('slow');
            $('#page-footer > div > div > div > div.col-xs-12.col-lg-4.text-xs-center.text-lg-left.order-lg-1.order-2.mb-2').toggle('slow');
            $('#page-footer > div > div > div.row-flex > div.col-xs-12.col-lg-4.text-xs-center.order-lg-2.order-3.mb-2').toggle('slow');

            $('.header-item.header-item-adjust:not(.header-search)').toggleClass('road-menu');
        },
        isCollapsedCart: function () {
            if (this.windowWidth <= 992) {
                $('#page-main').toggle('slow');


                $('#page-footer > div > div > div.row.road.road-right.hidden-md-up.mb-2').toggle('slow');
                $('#page-footer > div > div > div.row.road.road-center.mb-2').toggle('slow');
                $('#page-footer > div > div > div > div.col-xs-12.col-lg-4.text-xs-center.text-lg-left.order-lg-1.order-2.mb-2').toggle('slow');
                $('#page-footer > div > div > div.row-flex > div.col-xs-12.col-lg-4.text-xs-center.order-lg-2.order-3.mb-2').toggle('slow');
            }
            $('.navbar-cart').slideToggle(200);
        },
        start_time(newValue) {
            this.$refs.start_time.value = moment(String(newValue)).format('YYYY-MM-DD HH:mm:ss');
        }
    },
    methods: {
        setValue(id, value) {
            $('#' + id).val(value);
        },
        setSemanticDropdownValue(id, value) {
            let $dropdown = $('#' + id).closest('.dropdown');
            $dropdown.dropdown('set selected', value);
        },
        disableUnavailableOptions(id, options) {
            let $select = $('#' + id);
            let $options = $select.find('option');
            $options.removeAttr('disabled');

            $options.each(function () {
                let $element = $(this);
                let value = parseInt($element.val());

                if (isNaN(value)) {
                    return;
                }

                if ($.inArray(value, options) === -1) {
                    $element.prop('disabled', true);

                    if ($element.is(':selected')) {
                        $options.first().prop('selected', true);
                    }
                }
            });
        }
    },
    mounted() {
        let startTimeRef = this.$refs.start_time;
        if (startTimeRef && startTimeRef.value) {
            this.$set(this, 'start_time', startTimeRef.value)
        }
    },
    components: {
        Icon,
        'cart-global': CartGlobal,
        'gift-card': GiftCard,
        'search-page': SearchPage,
        'school-page': SchoolPage,
        'contact-page': ContactPage,
        'date-picker-form': DatePickerForm,
        'login-page': LoginPage,
        'smileys': Smileys,
        'smileys-bar': SmileysBar,
        'search-bar': SearchBar,
        'promo-banner': PromoBanner,
        'semantic-dropdown': SemanticDropdown,
        'course-page': CoursePage,
        'order-page-organization': OrderPageOrganization,
        'no-results': NoResults,
        'invoice-create': InvoiceCreate,
        'school-create': SchoolCreate,
        'school-edit': SchoolEdit,
        'organization-dashboard': OrganizationDashboard,
        'course-form': CourseForm,
        'course-form-schedule': CourseFormSchedule,
        'course-payment-page': CoursePaymentPage,
        'usps-form': UspsForm,
        'user-types-chart': UserTypes,
        'summary-chart': Summary,
        'course-statistics-table': CourseStatisticsTable,
        'payment-page': PaymentPage,
        'payment-iframe-page': PaymentIframePage,
        'reports-dates': ReportsDates,
        'intensive-page': IntensiveCoursePage,
        'posts-page': PostsPage,
        'posts-page-show': PostsPageShow,
        'comments-block': CommentsBlock,
        'comment-card': CommentCard,
        'comment-form': CommentForm,
        'post-create-form': PostCreateForm,
        'post-edit-form': PostEditForm,
        'landing-create-form': LandingCreateForm,
        'landing-edit-form': LandingEditForm,
        'partners': Partners,
        'image-or-url-file': ImageUrlOrFile,
        'reco-reviews': RecoReviews,
        'reco-comparison': RecoComparisonWidget,
        'courses-scheduler': CoursesScheduler,
        'loyalty-progress': LoyaltyProgress,
        'left-side-filter': LeftSideFilter
    },
    localStorage: {
        order: {},
        school: 0,
        qty: 0,
        students: {},
        selectedCourses: {},
        selectedAddons: {},
    },
    store
});

new Vue({
    el: '#page-header',
    data() {
        return {
            isCollapsed: false,
            isCollapsedCart: false,
            showSearchBar: false,
            windowWidth: window.innerWidth,
        }
    },
    computed: {
        ...mapState('cart', ['students']),
        ...mapGetters('cart', ['qty', 'courseIds', 'addonIds', 'customIds']),
    },
    watch: {
        isCollapsed: function () {
            this.isCollapsedCart = false;
            if (!this.isDesktop) {
                $('#page-main').toggle('slow');
                $('#page-footer').toggle('slow');

                $('#page-footer > div > div > div.row.road.road-right.hidden-md-up.mb-2').toggle('slow');
                $('#page-footer > div > div > div.row.road.road-center.mb-2').toggle('slow');
                $('#page-footer > div > div > div > div.col-xs-12.col-lg-4.text-xs-center.text-lg-left.order-lg-1.order-2.mb-2').toggle('slow');
                $('#page-footer > div > div > div.row-flex > div.col-xs-12.col-lg-4.text-xs-center.order-lg-2.order-3.mb-2').toggle('slow');

            }
            $('.header-item.header-item-adjust:not(.header-search)').toggleClass('road-menu');
            $('.navbar-container').toggleClass('hidden-md-down');
        },
        isCollapsedCart: function () {
            if (this.windowWidth <= 992) {
                $('#page-main').toggle('slow');
                $('#page-footer').toggle('slow');

                $('#page-footer > div > div > div.row.road.road-right.hidden-md-up.mb-2').toggle('slow');
                $('#page-footer > div > div > div.row.road.road-center.mb-2').toggle('slow');
                $('#page-footer > div > div > div > div.col-xs-12.col-lg-4.text-xs-center.text-lg-left.order-lg-1.order-2.mb-2').toggle('slow');
                $('#page-footer > div > div > div.row-flex > div.col-xs-12.col-lg-4.text-xs-center.order-lg-2.order-3.mb-2').toggle('slow');
            }
            $('.header-item.header-item-adjust:not(.header-search)').removeClass('road-menu');
            $('.navbar-cart').slideToggle(100);
        },
        showSearchBar: function () {
            $('.header-search.hidden-lg-down').toggleClass('active');
            $('.btn-search').toggleClass('active');
        },
    },
    mounted() {
        document.body.addEventListener('scroll', fixHeaderOnScrollUp);
    },
    beforeDestroy () {
        document.body.removeEventListener('scroll', fixHeaderOnScrollUp);
    },
    methods: {
        navigationChanged(url) {
            window.location = url;
        },
        changeCollapsedCart() {
            this.isCollapsedCart=!this.isCollapsedCart;
        },
        toCheckoutPage() {
            if (this.qty > 0) {
                window.onbeforeunload = null;
                Api.toCheckout(window.localStorage.school, this.courseIds, this.addonIds, this.customIds, this.students);
            }
        }
    },
    components: {
        'smileys': Smileys,
        'search-bar': SearchBar,
        'cart-global': CartGlobal,
        'semantic-dropdown': SemanticDropdown,
        'promo-banner': PromoBanner,
    },
    store
});
