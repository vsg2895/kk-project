<template>
    <span v-html="glyph"></span>
</template>

<script>
    export default {
        name: 'icon',
        props: {
            name: {
                required: true
            },
            className: {
                type: String
            },
            size: {
                type: String,
                default: 'md'
            }
        },
        data() {
            return {
                icons: {
                    'angle-down': require('./../../../svg/icons/angle-down.svg'),
                    'angle-left': require('./../../../svg/icons/angle-left.svg'),
                    'angle-up': require('./../../../svg/icons/angle-up.svg'),
                    'angle-right': require('./../../../svg/icons/angle-right.svg'),
                    'arrow-left': require('./../../../svg/icons/arrow-left.svg'),
                    'arrow-right': require('./../../../svg/icons/arrow-right.svg'),
                    'asterisk': require('./../../../svg/icons/asterisk.svg'),
                    'book-driving': require('./../../../svg/icons/book-driving.svg'),
                    'book-tutor': require('./../../../svg/icons/book-tutor.svg'),
                    'calendar': require('./../../../svg/icons/calendar.svg'),
                    'car': require('./../../../svg/icons/car.svg'),
                    'check': require('./../../../svg/icons/check.svg'),
                    'check-tiny': require('./../../../svg/icons/check-tiny.svg'),
                    'clock': require('./../../../svg/icons/clock.svg'),
                    'course': require('./../../../svg/icons/course.svg'),
                    'cross': require('./../../../svg/icons/cross.svg'),
                    'directions': require('./../../../svg/icons/directions.svg'),
                    'driving-sign': require('./../../../svg/icons/driving-sign.svg'),
                    'dropdown': require('./../../../svg/icons/dropdown.svg'),
                    'dropup': require('./../../../svg/icons/dropup.svg'),
                    'glasses': require('./../../../svg/icons/glasses.svg'),
                    'hamburger-menu': require('./../../../svg/icons/hamburger-menu.svg'),
                    'home': require('./../../../svg/icons/home.svg'),
                    'info': require('./../../../svg/icons/info.svg'),
                    'klarna-logo': require('./../../../svg/icons/klarna-logo.svg'),
                    'klarna-logo-tagline': require('./../../../svg/icons/klarna-logo-tagline.svg'),
                    'letter': require('./../../../svg/icons/letter.svg'),
                    'link-open': require('./../../../svg/icons/link-open.svg'),
                    'location': require('./../../../svg/icons/location.svg'),
                    'map-marker': require('./../../../svg/icons/map-marker.svg'),
                    'mc': require('./../../../svg/icons/mc.svg'),
                    'minus': require('./../../../svg/icons/minus.svg'),
                    'moped': require('./../../../svg/icons/moped.svg'),
                    'ykb': require('./../../../svg/icons/ykb.svg'),
                    'offer': require('./../../../svg/icons/offer.svg'),
                    'order': require('./../../../svg/icons/order.svg'),
                    'organization': require('./../../../svg/icons/organization.svg'),
                    'pages-edit': require('./../../../svg/icons/pages-edit.svg'),
                    'permission-driving': require('./../../../svg/icons/permission-driving.svg'),
                    'permission-mc': require('./../../../svg/icons/permission-mc.svg'),
                    'permission-tutor': require('./../../../svg/icons/permission-tutor.svg'),
                    'plus': require('./../../../svg/icons/plus.svg'),
                    'rating': require('./../../../svg/icons/rating.svg'),
                    'report': require('./../../../svg/icons/report.svg'),
                    'reflex-vest': require('./../../../svg/icons/reflex-vest.svg'),
                    'school': require('./../../../svg/icons/school.svg'),
                    'settings': require('./../../../svg/icons/settings.svg'),
                    'smiley1': require('./../../../svg/icons/smiley-1.svg'),
                    'smiley2': require('./../../../svg/icons/smiley-2.svg'),
                    'smiley3': require('./../../../svg/icons/smiley-3.svg'),
                    'smiley4': require('./../../../svg/icons/smiley-4.svg'),
                    'smiley5': require('./../../../svg/icons/smiley-5.svg'),
                    'statistics': require('./../../../svg/icons/statistics.svg'),
                    'ticket': require('./../../../svg/icons/ticket.svg'),
                    'user': require('./../../../svg/icons/user.svg'),
                    'users': require('./../../../svg/icons/users.svg'),
                    'star': require('./../../../svg/icons/star.svg'),
                    'danger-triangle': require('./../../../svg/icons/danger-triangle.svg'),
                    'gift': require('./../../../svg/icons/gift.svg'),
                    'gift-red': require('./../../../svg/icons/gift-red.svg'),
                    'money-bag': require('./../../../svg/icons/money-bag.svg'),
                    'snowflake-1': require('./../../../svg/icons/snowflake-1.svg'),
                    'snowflake-2': require('./../../../svg/icons/snowflake-2.svg'),
                    'snowflake-3': require('./../../../svg/icons/snowflake-3.svg'),
                    'dustbin': require('./../../../svg/icons/dustbin.svg'),
                    'edit': require('./../../../svg/icons/edit.svg'),
                    'floppy-disk': require('./../../../svg/icons/floppy-disk.svg'),
                    'loupe': require('./../../../svg/icons/loupe.svg'),
                }
            }
        },
        computed: {
            glyph() {
                return this.icons[this.name.toLowerCase()];
            }
        },
        mounted: function () {
            let $element = $(this.$el).children('svg'),
                css = 'ico-' + this.size;

            if (this.className) {
                css = `${css} ${this.className}`;
            }

            $element.addClass(css);
        },
    };
</script>
