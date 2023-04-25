<template>
  <div class="school-course">
    <span v-if="course.course.part" class="part bold text-accent hidden-sm-down pr-1">{{ course.course.part }}</span>
    <div v-if="course.segment !== ONLINE_LICENSE_THEORY" class="calendar-date-secion">
      <div class="calendar-date-secion-child">
        <div>{{ course.date }}</div>
        <div class="time">
          <icon name="clock" class="hidden-md-down"></icon>
          <span>{{ course.time }}</span>
        </div>
      </div>
      <span v-if="course.course.part" class="bold text-accent">{{ course.course.part }}</span>
      <div v-if="availableSeats > 0">
        <div class="availability-warning">
          <icon v-if="course.availableSeats < 6" name="danger-triangle hidden-md-down"></icon>
          <span>{{ `${availableSeats} ${availableSeats > 1 ? 'platser' : 'plats'}` }}</span>
        </div>
        <div class="calendar-qty">
          <button :disabled="selectedSeats === 0" @click="removeAttendee" class="remove-attendee">
            <i class="fas fa-minus"></i>
          </button>
          <div class="quantity">{{ selectedSeats }} st</div>
          <button :disabled="selectedSeats === availableSeats" @click="addAttendee" class="add-attendee">
            <i class="fas fa-plus"></i>
          </button>
        </div>
      </div>
    </div>
    <span v-if="course.segment === ONLINE_LICENSE_THEORY" class="description pr-1">{{ course.description }}</span>
    <div>
      <button :class="buttonClass" @click="handleButtonClick" class="btn btn-sm" v-html="buttonText"></button>
<!--      <button v-if="course.old_price" :class="buttonClass" @click="handleButtonClick" class="btn btn-sm">{{ buttonText }}</button>
      <button v-else :class="buttonClass" @click="handleButtonClick" class="btn btn-sm">
        <span class="price-with-new">{{priceValueWithOldPrice}}</span>
      </button>-->
    </div>
  </div>
</template>

<script>
import Icon from 'vue-components/Icon';

export default {
  props: {
    course: { type: Object, required: true },
    isSelected: { type: Boolean, default: false },
    students: { type: Array, default: () => ({}) },
  },
  data: () => ({
    selectedSeats: 1,
  }),
  computed: {
    availableSeats() {
      return this.course.availableSeats - this.students.filter(s => s.courseId === this.course.id).length;
    },
    isOnlineCourse() {
      return this.course.segment === this.ONLINE_LICENSE_THEORY;
    },
    buttonClass() {
      return (this.isOnlineCourse && this.isSelected) || this.availableSeats === 0 ? 'btn-danger' : 'btn-success'
    },
    buttonText() {
      if ((this.isOnlineCourse && this.isSelected)) return 'Ta bort';
      if (this.course.course.old_price) {
        return this.selectedSeats > 1
            ? `${this.selectedSeats}x &nbsp;<span class="price-with-new">${this.course.course.price_with_currency}</span>
                &nbsp;<span class="price-with-old">${this.course.course.old_price} kr</span> &nbsp; Boka`
            : `<span class="price-with-new">${this.course.course.price_with_currency}</span>
                &nbsp;<span class="price-with-old">${this.course.course.old_price} kr</span> &nbsp; Boka`;
      } else {
        return this.selectedSeats > 1
            ? `${this.selectedSeats}x ${this.course.course.price_with_currency} Boka`
            : `${this.course.course.price_with_currency} Boka`;
      }
    },
    /*priceValueWithOldPrice() {
      if ((this.isOnlineCourse && this.isSelected)) return 'Ta bort';
      if (this.course.course.old_price) {
        return this.selectedSeats > 1
            ? `${this.selectedSeats}x ${this.course.course.price_with_currency} Boka`
            : `${this.course.course.price_with_currency} Boka`;
      }
    },*/
  },
  methods: {
    handleButtonClick() {
      this.isOnlineCourse ? this.toggleCourse() : this.addCourse();
    },
    addCourse() {
      this.$emit('addCourse', { ...this.course, selectedSeats: this.selectedSeats });
      this.selectedSeats = 1;
    },
    toggleCourse() {
      this.$emit('toggleCourse', { ...this.course, selectedSeats: this.selectedSeats });
    },
    addAttendee() {
      if (this.selectedSeats < this.course.availableSeats) {
        this.selectedSeats += 1;
      }
    },
    removeAttendee() {
      if (this.selectedSeats >= 1) {
        this.selectedSeats -= 1;
      }
    },
  },
  components: { Icon },
}
</script>

<style lang="scss" scoped>
.school-course {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.part {
  flex-basis: 40%;
  flex-shrink: 0;
}

.calendar-date-secion {
  display: flex;
  flex-direction: column;
  font-family: "Open Sans";
  font-size: 14px;
  letter-spacing: 0;
  line-height: 19px;
  text-transform: capitalize;

  // md-up
  @media (min-width: 768px) {
    flex-direction: row;
    flex-grow: 1;
    //justify-content: space-around;
    justify-content: start;
    padding: 0 24px;
  }

  .availability-warning {
    color: #F3664F;
  }

  .calendar-qty {
    display: flex;

    .remove-attendee,
    .add-attendee {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 4px;
      background-color: #fff;
      border: 0px;
    }

    .btn-add-to-cart {
      margin-left: 31%;
      min-width: 120px;
    }

    .quantity {
      padding: 0 8px;
      text-transform: none;
    }
  }
}

.calendar-date-secion-child {
  margin-right: 20px;
  @media (min-width: 768px) {
    min-width: 160px;
  }
}

.description {
  flex-basis: 60%;
  font-family: Roboto, serif;
  font-size: 14px;
  letter-spacing: 0;
  line-height: 19px;
  text-transform: uppercase;
  font-weight: 700;

  // md-up
  @media (min-width: 768px) {
    font-weight: 400;
    text-transform: none;
    font-size: 1rem;
  }
}
</style>