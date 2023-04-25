<template>
    <div>
        <form ref="loginForm" id="login-form" method="POST" :action="routes.route('auth::login.do')" :submit.prevent="validateForm">
            <input type="hidden" name="_token" :value="csrfToken">

            <div class="form-group" :class="{ 'has-danger': validationErrors && validationErrors.email, 'has-success': !$v.user.email.$invalid && shouldValidate  }">
                <label class="label-hidden" for="email">E-post</label>
                <span class="fa fa-envelope form-control-icon"></span>
                <input class="form-control " placeholder="E-post" type="email" id="email"
                       name="email" @input="$v.user.email.$touch()" v-model="user.email" />
                <span v-if="validationErrors && validationErrors.email" class="form-control-feedback is-danger">
                    {{ validationErrors.email[0] }}
                </span>
            </div>

            <div class="form-group" :class="{ 'has-danger': validationErrors && validationErrors.password, 'has-success': !$v.user.password.$invalid && shouldValidate  }">
                <label class="label-hidden" for="password">Lösenord</label>
                <span class="fa fa-lock form-control-icon"></span>
                <input class="form-control" placeholder="Lösenord" type="password" id="password"
                       name="password" @input="$v.user.password.$touch()" v-model="user.password" />
                <span v-if="validationErrors && validationErrors.password" class="form-control-feedback is-danger">
                    {{ validationErrors.password[0] }}
                </span>
            </div>

            <button class="btn btn-black" type="submit">Logga in</button>
        </form>

        <a :href="routes.route('auth::password.forgot')">Glömt lösenord?</a>
    </div>
</template>

<script type="text/babel">
    import { required, email, minLength } from 'vuelidate/lib/validators';
    import routes from 'build/routes.js';

    export default {
        props: {
            oldEmail: {
                type: String,
                default: ''
            },
            csrfToken: {
                type: String,
                required: true
            },
            errors: {
                type: String,
                default: '{}'
            }
        },
        validations: {
            user: {
                email: { required, email },
                password: { required, minLength: minLength(6) }
            }
        },
        methods: {
            validateForm() {
                var formIsValid = !this.$v.user.email.$invalid && !this.$v.user.password.$invalid

                if (formIsValid) {
                    this.$refs.loginForm.submit()
                } else {
                    this.shouldValidate = true
                }
            }
        },
        watch: {
            user: {
                handler() {
                    this.shouldValidate = false
                },
                deep: true
            }
        },
        data() {
            return {
                shouldValidate: false,
                routes: routes,
                user: {
                    email: this.oldEmail,
                    password: ''
                },
                validationErrors: JSON.parse(this.errors)
            }
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>
