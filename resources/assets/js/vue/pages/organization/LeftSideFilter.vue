<template>
  <div class="row middle-size-custom-filter-row">
    <div class="col-xs-12 col-md-12 col-sm-6 col-xxl-12 pb-1">
      <select v-if="this.schools.length > 1" class="custom-school-filter-button" @change="customFilter($event)">
        <option v-for="(row,i) in this.schools" :key="i"
                :value="row.id">{{ row.name + '(' + row.postal_city + ')' }}
        </option>
      </select>
    </div>
    <div class="col-xs-12 col-sm-6 col-xxl-12">
      <div class="card card-block">
        <div class="grey">Antal upplagda kurser</div>
        <div class="total">{{ this.mutableTotalCourses }} st</div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-xxl-12">
      <div class="card card-block">
        <div class="grey">Antal förmedlade kontakter via Körkortsjakten</div>
        <div class="total">{{ this.mutableTotalContacts }} st</div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-xxl-12">
      <div class="card card-block">
        <div class="grey" style="text-decoration: underline!important; font-weight: 600; ">Area Average:</div>
        <div v-for="(row,i) in this.validTypesAvgCourses" :key="i" class="grey">
          <div>
            <span style="float: left">{{ row.name }}</span>
            <span style="float: right">{{ row.avg }} </span>
          </div>
          <br/>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-xxl-12">
      <div class="card card-block">
        <div class="grey" style="text-decoration: underline; font-weight: 600; ">School Average:</div>
        <div v-for="(row,i) in this.mutableAvgCoursesOrg" :key="i" class="grey">
          <div>
            <span style="float: left">{{ row.name }}</span>
            <span style="float: right">{{ row.avg }} </span>
          </div>
          <br/>
        </div>
      </div>
    </div>
    <div class="col-xs-12 bookingArea">
      <div class="card card-block mx-0">
        <user-types-chart :data="this.mutableBookingInfo" :key="componentKey"
                          title="Bokningsinformation"></user-types-chart>
      </div>
    </div>
  </div>

</template>

<script>
import Api from "vue-helpers/Api";
import UserTypes from "vue-components/charts/UserTypes.vue";

export default {
  props: ['schools', 'totalCourses', 'totalContacts', 'avgCourses', 'avgCoursesOrg', 'bookingInfo', 'neededCoursesTypes'],
  components: {
    'user-types-chart': UserTypes
  },
  data: function () {
    return {
      componentKey: "",
      mutableTotalCourses: this.totalCourses,
      mutableTotalContacts: this.totalContacts,
      mutableAvgCourses: this.avgCourses,
      mutableAvgCoursesOrg: this.avgCoursesOrg,
      mutableBookingInfo: this.bookingInfo
    }
  },
  computed: {
    validTypesAvgCourses() {
      return this.mutableAvgCourses.filter((course) => {
        return !this.neededCoursesTypes.includes(course.id)
      })
    }
  },
  methods: {
    customFilter(event) {
      Api.customOrganizationFilter(event.target.value)
          .then(response => {
            if (response.status === 200) {
              let data = response.data.data
              this.mutableTotalCourses = data.totalCourses
              this.mutableTotalContacts = data.totalContacts
              this.mutableAvgCourses = data.avgCourses
              this.mutableAvgCoursesOrg = data.avgCoursesOrg
              this.mutableBookingInfo = JSON.parse(data.bookingInfo)
              this.componentKey = Math.random().toString(36).slice(2)
            }
          }).catch(error => {
        alert(error.toString())
        location.reload()

      });
    }
  }
}
</script>

<style scoped>

</style>