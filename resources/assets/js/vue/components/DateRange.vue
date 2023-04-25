<template>
    <div :id="id" class="daterange" :class="{double: 'daterange--double'}"></div>
</template>

<script type="text/babel">
    import $ from 'jquery';
    import moment from 'moment';
    import Calendar from 'vendor/Calendar.js';

    export default {
        props: ['id', 'double', 'onChange'],
        data() {
            return {};
        },
        methods: {
        },
        watch: {},
        mounted () {
            var $element = $(this.$el);
            var vm = this;

            new Calendar({
                element: $element,
                format: {
                    input: 'dddd D MMMM, YYYY', // Format for the input fields
                    jump_month: 'MMMM', // Format for the month switcher
                    jump_year: 'YYYY' // Format for the year switcher
                },
                earliest_date: moment().format('YYYY-MM-DD'),
                latest_date: moment().add(1, 'year').format('YYYY-MM-DD'),
                start_date: moment().format('YYYY-MM-DD'),
                end_date: moment().add(3, 'month').format('YYYY-MM-DD'),
                days_array: ['Sön','Mån','Tis','Ons','Tors','Fre','Lör'],
                same_day_range: true,
                presets: [
                    {
                        label: 'En vecka',
                        start: moment(),
                        end: moment().add(1, 'week')
                    },
                    {
                        label: 'Två veckor',
                        start: moment(),
                        end: moment().add(2, 'week')
                    },
                    {
                        label: 'En månad',
                        start: moment(),
                        end: moment().add(1, 'month')
                    },
                    {
                        label: 'Denna månad',
                        start: moment(),
                        end: moment().endOf('month')
                    },
                ],
                callback: function() {
                    var from = moment(this.start_date).format('YYYY-MM-DD');
                    var to = moment(this.end_date).format('YYYY-MM-DD');

                    vm.$set(vm.onChange, 'from', from);
                    vm.$set(vm.onChange, 'to', to);
                },
            });

            $('body').on('mouseout click', '.dr-day', function() {
                $(this).closest('.dr-day-list').removeClass('dr-new-period-hover');
            }).on('mouseover', '.dr-day', function() {
                if ($(this).hasClass('dr-maybe') || ($(this).hasClass('dr-hover') && (!$(this).hasClass('dr-selected')) || ($(this).hasClass('dr-selected') && ($(this).hasClass('dr-hover-before') || $(this).hasClass('dr-hover-after'))) || $(this).hasClass('dr-end dr-hover dr-hover-before'))) {
                $(this).closest('.dr-day-list').addClass('dr-new-period-hover');
                } else {
                    $(this).closest('.dr-day-list').removeClass('dr-new-period-hover');
                }
            });
        },
        destroyed () {
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>
