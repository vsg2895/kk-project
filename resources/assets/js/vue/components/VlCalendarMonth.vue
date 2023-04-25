<template>
  <div class="vl-calendar-month">
    <div class="vl-calendar-month__title d-flex justify-content-between mb-1">
      <span
        class="fas fa-chevron-left"
        @click="$emit('backward')" />
      <span>
        {{ monthName }} {{ year }}
      </span>
      <span
        class="fas fa-chevron-right"
        @click="$emit('forward')" />
    </div>

    <div class="vl-flex">
      <div
        v-if="showWeeksNumber"
        class="vl-calendar-month__week-numbers-column"
      >
        <div
          v-for="number in weekNumbers"
          class="vl-calendar-month__week-number"
          :key="number"
        >
          {{ number }}
        </div>
      </div>

      <div>
        <div class="vl-flex">
          <span
            v-for="name in daysNames"
            :key="name"
            class="vl-calendar-month__week-day"
          >{{ name }}</span>
        </div>

        <div class="vl-flex vl-flex-wrap">
          <div
            v-for="day in days"
            :key="day"
            @click="$emit('input', getDate(day))"
            class="vl-calendar-month__day"
            :class="calculateClasses(day)"
            :data-day="day"
            :data-month="month"
            :data-year="year"
          >
            <span>{{ day }}</span>
            <span class="vl-calendar-month__day__price" v-text="calculatePrice(getDate(day))"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { createRange, transpose } from './utils/CollectionUtils'
import { countDays, formatDate, getMonthName, getWeekNumbers } from './utils/DatesUtils'
import { DAYS_NAMES, DAYS_SHORTCUTS } from './constants/days'
import _ from "lodash";

export default {
  name: 'VlCalendarMonth',
  props: {
    showWeeksNumber: Boolean,
    month: Number,
    year: Number,
    isSelected: Function,
    isDisabled: Function,
    customClasses: Object,
    courses: Array,
    firstDayOfWeek: {
      type: String,
      validator: v =>  DAYS_SHORTCUTS.includes(v),
      default: 'mon'
    }
  },

  computed: {
    monthName () {
      return getMonthName(this.month)
    },

    days () {
      return createRange(1, countDays(this.month, this.year))
    },

    daysNames () {
      return transpose(DAYS_NAMES, this.daysOffset)
    },

    daysOffset () {
      return DAYS_SHORTCUTS.indexOf(this.firstDayOfWeek)
    },

    weekNumbers () {
      return getWeekNumbers(this.month, this.year)
    }
  },

  methods: {
    getDate (day) {
      return formatDate(day, this.month, this.year)
    },

    calculatePrice(date){
      let coursesForDate = _.filter(this.courses, (course) => {
        return course.course.start_time.indexOf(date) + 1;
      });

      if (coursesForDate.length) {
        var lowestPrice = coursesForDate[0].course.price;
        var lowestPriceText = coursesForDate[0].course.price_with_currency;

        coursesForDate.forEach(course => {
          if (lowestPrice > course.course.price) {
            lowestPrice = course.course.price;
            lowestPriceText = course.course.price_with_currency;
          }
        });

        return lowestPriceText;
      }
    },
    calculateClasses (day) {
      const classes = []
      if (day === 1) {
        let offset = (new Date(this.year, this.month, 1).getDay() + 7 - this.daysOffset) % 7
        if (offset > 0) {
          classes.push(`vl-calendar-month__day--offset-${offset}`)
        }
      }

      const date = this.getDate(day)
      if (this.isSelected && this.isSelected(date)) {
        classes.push('selected')

        if (!this.isSelected(this.getDate(day - 1))){
          classes.push('selected--first')
        }

        if (!this.isSelected(this.getDate(day + 1))){
          classes.push('selected--last')
        }
      }

      if (this.isDisabled && this.isDisabled(date)) {
        classes.push('disabled')

        if (!this.isDisabled(this.getDate(day - 1))){
          classes.push('disabled--first')
        }

        if (!this.isDisabled(this.getDate(day + 1))){
          classes.push('disabled--last')
        }
      }

      Object.keys(this.customClasses || {}).forEach(cl => {
        const fn = this.customClasses[cl]
        if (fn(date)) {
          classes.push(cl)
        }
      })

      return classes
    }
  }
}
</script>
