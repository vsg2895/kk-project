<template>
    <div class="form-group">
        <label>Från:</label>
        <el-date-picker v-model="startTime" type="date" format="yyyy-MM-dd" placeholder="Välj datum och tid"></el-date-picker>
        <input type="hidden" name="start_time" id="start_time" v-model="startTime">

        <label>Till:</label>
        <el-date-picker v-model="endTime" type="date" format="yyyy-MM-dd" placeholder="Välj datum och tid"></el-date-picker>
        <input type="hidden" name="end_time" id="end_time" v-model="endTime">
    </div>
</template>

<script type="text/babel">
    import moment from 'moment';

    export default {
        props: ['from', 'to'],
        data() {
            return {
                startTime: '',
                endTime: ''
            }
        },
        watch: {
            startTime(value) {
                this.startTime = moment(value).set('second', 0).format('YYYY-MM-DD');
            },
            endTime(value) {
                this.endTime = moment(value).set('second', 0).format('YYYY-MM-DD');
            }
        },
        mounted() {
            this.startTime = this.from;
            this.endTime = this.to;
        }
    }
</script>