<template>
  <div>
      <div class="form-group">
        <label for="start_time">Date</label>
        <div>
          <el-date-picker v-model="selectedDay" type="date" :picker-options="pickerOptions"
                          placeholder="Välj datum och tid"></el-date-picker>
        </div>
        <input type="hidden" name="start_time" id="start_time" v-model="selectedDay">
      </div>

  </div>
</template>

<script type="text/babel">
    import moment from 'moment';

    export default {
        props: {
          selectedDay: null
        },
        data() {
            return {
                pickerOptions: {
                    disabledDate: d => d <= new Date()
                },
            };
        },
        watch: {
          selectedDay() {
            window.location = window.location.protocol + "//" + window.location.host + window.location.pathname +
                '?date=' + moment(this.selectedDay).add(10, 'hour').format('YYYY-MM-DD');
          }
        }
    }
</script>
