<template>
    <div class="course-participant" :class="{'iframe-cart-addon-participant-block':iframe}">
      <div>
        <div class="d-flex justify-content-end mx-1">
          <button
              v-if="onRemove"
              @click="onRemove(type, index)"
              class="course-participant-remove btn btn-link btn-sm">Ta bort
          </button>
        </div>
      </div>
      <div class="row">
<!--        Check in site checkout page working correct in participant block top Ta bort button-->
<!--        <div class="d-flex justify-content-end mx-1">-->
<!--            <button-->
<!--              v-if="onRemove"-->
<!--              @click="onRemove(type, index)"-->
<!--              class="course-participant-remove btn btn-link btn-sm">Ta bort-->
<!--            </button>-->
<!--        </div>-->
            <div class="col-md-6">
              <div
                :class="{ 'has-danger': $v.participant.given_name.$error, 'has-success': !$v.participant.given_name.$invalid  }"
                class="form-group">
                <span class="far fa-user form-control-icon"></span>
                <input 
                  v-model="participant.given_name"
                  type="text"
                  placeholder="Förnamn"
                  class="form-control form-control-lg"
                  @input="$v.participant.given_name.$touch()">
              </div>
            </div>
            <div class="col-md-6">
              <div
                :class="{ 'has-danger': $v.participant.family_name.$error, 'has-success': !$v.participant.family_name.$invalid  }"
                class="form-group">
                <span class="far fa-user form-control-icon"></span>
                <input
                  v-model="participant.family_name"
                  type="text"
                  placeholder="Efternamn"
                  class="form-control form-control-lg"
                  @input="$v.participant.family_name.$touch()">
              </div>
            </div>
            <div class="col-md-6">
              <div
                :class="{ 'has-danger': $v.participant.social_security_number.$error, 'has-success': !$v.participant.social_security_number.$invalid  }"
                class="form-group">
                  <span class="far fa-id-card form-control-icon"></span>
                  <input
                    v-model="participant.social_security_number"
                    placeholder="Personnummer (ååmmdd-nnnn)"
                    type="text"
                    class="form-control form-control-lg"
                    @keyup="$v.participant.social_security_number.$touch()"/>
                  <span class="form-control-feedback is-danger" v-show="$v.participant.social_security_number.$error">Exempel: ååmmdd-nnnn</span>
              </div>
            </div>
            <div class="col-md-6">
              <div
                :class="{ 'has-danger': $v.participant.email.$error, 'has-success': !$v.participant.email.$invalid  }"
                class="form-group">
                  <span class="far fa fa-envelope form-control-icon"></span>
                  <input
                    v-model="participant.email"
                    type="email"
                    placeholder="E-post"
                    class="form-control form-control-lg"
                    @input="$v.participant.email.$touch()">
              </div>
            </div>
            <div class="col-md-6">
              <div
                :class="{ 'has-danger': $v.participant.phone_number.$error, 'has-success': !$v.participant.phone_number.$invalid  }"
                class="form-group">
                  <span class="far fa fa-phone form-control-icon"></span>
                  <input
                    v-model="participant.phone_number"
                    type="tel"
                    placeholder="Telefonnummer"
                    class="form-control form-control-lg"
                    @input="$v.participant.phone_number.$touch()">
              </div>
            </div>
        </div>
      <div class="row">
        <div
          v-if="transmissionShow"
          class="form-group d-flex flex-row ml-1"
          :class="{ 'has-danger': $v.participant.transmission.$error, 'has-success': !$v.participant.transmission.$invalid  }">
            <label class="custom-control custom-checkbox" :class="{ 'disabled': transmission }">
                <input
                  :checked="participant.transmission === 'manual'"
                  :disabled="transmission"
                  type="radio"
                  @change="participant.transmission = 'manual'"
                  class="custom-control-input">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Manuell</span>
            </label>
            <label class="custom-control custom-checkbox" :class="{ 'disabled': transmission }">
                <input
                  :checked="participant.transmission === 'automatic'"
                  :disabled="transmission"
                  type="radio"
                  @change="participant.transmission = 'automatic'"
                  class="custom-control-input">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Automat</span>
            </label>
          </div>
        <div
          v-if="showCategories"
          :class="{ 'has-danger': $v.participant.category.$error, 'has-success': !$v.participant.category.$invalid  }"
          class="ml-1">
          <div>
            <h4>Välj MC typ nedan:</h4>
          </div>
          <div class="form-group d-flex flex-md-row">
            <label v-for="category in MCCategories" :key="category" class="custom-control custom-checkbox">
              <input
                  :checked="participant.category === category"
                  @change="participant.category = category"
                  type="radio"
                  class="custom-control-input">
              <span class="custom-control-indicator"></span>
              <span class="custom-control-description">{{ category }}</span>
            </label>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
import { debounce } from 'lodash';
import { email, minLength, required, requiredIf } from 'vuelidate/lib/validators';
import { socialSecurityNumber } from 'components/CustomValidators';

export default {
  props: ['index', 'onRemove', 'type', 'transmissionShow', 'transmission', 'showCategories', 'iframe'],
  validations: {
    participant: {
      given_name: { required, minLength: minLength(2) },
      family_name: { required, minLength: minLength(2) },
      transmission: { required: requiredIf(function () { return this.transmissionShow; }) },
      category: { required: requiredIf(function () { return this.showCategories; }) },
      social_security_number: { socialSecurityNumber },
      email: { required, email },
      phone_number: { required, minLength: minLength(7) },
    }
  },
  data: () => ({
    participant: {
      given_name: '',
      family_name: '',
      transmission: '',
      category: '',
      social_security_number: '',
      email: '',
      phone_number: ''
    }
  }),
  computed: {
    MCCategories() {
      return ['A1 Lätt MC', 'A2 Mellan MC', 'A3 Tung MC']
    },
    participantUpdatedDebounced() {
      return debounce(() => this.$emit('updated', this.participant), 200);
    },
  },
  watch: {
    participant: {
      handler() {
        this.participantUpdatedDebounced();
      },
      deep: true,
    }
  },
}
</script>

