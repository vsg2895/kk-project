<template>
    <div :id="id" class="daterange daterange--double"></div>
</template>

<script>
    import $ from 'jquery';
    import moment from 'moment';
    import Calendar from 'vendor/Calendar.js';

    export default {
        props: ['id', 'startDate', 'endDate'],
        data() {
            return {
                calendar: new Calendar({ same_day_range: true }),
                range: {
                    startDate: moment().subtract(6, 'months').format('YYYY-MM-DD'),
                    endDate: moment().format('YYYY-MM-DD'),
                }
            };
        },
        mounted() {
            this.makeCalendar();
        },
        methods: {
            updateRange() {
                this.range.startDate = this.startDate;
                this.range.endDate = this.endDate;
            },
            onCalendarChange() {
                let start = this.calendar.start_date.format('YYYY-MM-DD');
                let end = this.calendar.end_date.format('YYYY-MM-DD');
                if (start !== this.range.startDate || end !== this.range.endDate) {
                    this.range.startDate = start;
                    this.range.endDate = end;
                    this.$emit('onRangeChange', this.range)
                }
            },
            updateCalendar() {
                if (this.range.startDate && this.range.endDate) {
                    this.makeCalendar()
                }
            },
            makeCalendar() {
                let vm = this;
                vm.updateRange();
                $(vm.$el).empty()
                this.calendar = new Calendar({
                    element: $(vm.$el),
                    format: {
                        input: 'dddd D MMMM, YYYY', // Format for the input fields
                        jump_month: 'MMMM', // Format for the month switcher
                        jump_year: 'YYYY' // Format for the year switcher
                    },
                    earliest_date: moment().subtract(5, 'years').format('YYYY-MM-DD'),
                    latest_date: moment().add(1, 'year').format('YYYY-MM-DD'),
                    start_date: vm.range.startDate,
                    end_date: vm.range.endDate,
                    days_array: ['Sön', 'Mån', 'Tis', 'Ons', 'Tors', 'Fre', 'Lör'],
                    same_day_range: true,
                    presets: [],
                    callback() {
                        vm.onCalendarChange()
                    }
                })
                $('body').on('mouseout click', '.dr-day', function () {
                    $(this).closest('.dr-day-list').removeClass('dr-new-period-hover');
                }).on('mouseover', '.dr-day', function () {
                    if ($(this).hasClass('dr-maybe') || ($(this).hasClass('dr-hover') && (!$(this).hasClass('dr-selected')) || ($(this).hasClass('dr-selected') && ($(this).hasClass('dr-hover-before') || $(this).hasClass('dr-hover-after'))) || $(this).hasClass('dr-end dr-hover dr-hover-before'))) {
                        $(this).closest('.dr-day-list').addClass('dr-new-period-hover');
                    } else {
                        $(this).closest('.dr-day-list').removeClass('dr-new-period-hover');
                    }
                });
            }
        },
        watch: {
            startDate() {
                this.updateCalendar();
            },
            endDate() {
                this.updateCalendar()
            }
        },
        destroyed() {
        }
    }

</script>
