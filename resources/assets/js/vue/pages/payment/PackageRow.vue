<template>
    <div class="card course-card card-block text-left" :class="{'iframe-payment-page-block':this.iframe}">
<!--      <span v-if="deletable" class="btn btn-sm float-xs-right btn-danger" @click="remove">Ta bort</span>-->
      <div class="type tag tag-pill tag-default">{{ addon.name }}</div>
      <p v-if="addon.description">{{ addon.description ? addon.description : addon.pivot.description }}</p>
        <div>
          <template v-for="(student, index) in students">
            <participant
                :key="index"
                :index="index"
                :transmissionShow="false"
                type="students"
                :data="student"
                :number-of-participants="1"
                :iframe="true"
                @updated="handleAttendeeUpdated(...arguments)" />
          </template>
        </div>
    </div>
</template>
<script>

    import Participant from './../courses/Participant';

    export default {
        props: {
            addon: Object,
            order: Object,
            deletable: Boolean,
            iframe:Boolean
        },
        components: {
          'participant': Participant
        },
        computed: {
            students() {
                return this.order.students.length ?
                    this.order.students.filter(student => {
                        return student.addonId === this.addon.id
                    }) : [{
                        given_name: '',
                        family_name: '',
                        social_security_number: '',
                        email: '',
                        transmission: '',
                        addonId: this.addon.id,
                    }]
            }
        },
        methods: {
            handleAttendeeUpdated(attendee, index) {
              this.$emit('attendeeUpdated', { ...attendee, addonId: this.addon.id }, index, 'students')
            },
            addAttendee(target = false) {
                this.order[target].push({
                    given_name: '',
                    family_name: '',
                    social_security_number: '',
                    email: '',
                    transmission: '',
                    addonId: this.addon.id,
                })
            },
            mounted() {
                this.addAttendee(true);
            }
        }
    }
</script>
