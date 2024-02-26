<script>
import { mapActions } from "vuex";
import { required, phone } from "vuelidate/lib/validators";
import axios from "axios";
import appConfig from "@/app.config";
export default {
  name: "register",
  data() {
    return {
      currentYear: new Date().getFullYear(),
      auth: {
        name: "",
        phone: "",
        password: "",
        password_confirmation: "",
      },
      processing: false,
      submitted: false,
      pending: false,
      error: "",
    };
  },
  computed: {
    
  },
  created() {
    document.body.classList.add("auth-body-bg");
  },
  validations: {
    name: {
        required,
    },
    phone: {
      required,
    },
    password: {
      required,
    },
    password_confirmation: {
        required,
    }
  },
  methods: {
    ...mapActions({
      register: "auth/register",
      signIn: "auth/login",
    }),
    async register() {
      this.processing = true;
      this.submitted = true;
      await axios.get(appConfig.csrf_cookies);
      await axios
        .post(appConfig.register, this.auth)
        .then(({ data }) => {
          console.log(data);
          this.submitted = false;
          this.signIn(data.data);
        })
        .catch(({ response: { data } }) => {
          this.error = data.message;
        })
        .finally(() => {
          this.processing = false;
          this.submitted = false;
        });
    },
  },
};
</script>

<template>
  <div>
    <div class="container-fluid p-0">
      <div class="row no-gutters">
        <div class="col-lg-4">
          <div
            class="authentication-page-content p-4 d-flex align-items-center min-vh-100"
          >
            <div class="w-100">
              <div class="row justify-content-center">
                <div class="col-lg-9">
                  <div>
                    <div class="text-center">
                      <div class="logo">
                        <img
                          :src="require('@/assets/images/chat.png')"
                          alt="Logo"
                        />
                      </div>

                      <h4 class="font-size-18 mt-4">Welcome !</h4>
                      <p class="text-muted">Register to continue ...</p>
                    </div>

                    <b-alert
                      variant="danger"
                      class="mt-3"
                      v-if="error"
                      show
                      dismissible
                      >{{ error }}</b-alert
                    >
                    <div class="p-2 mt-5">
                      <form
                        action="javascript:void(0)"
                        method="post"
                        class="form-horizontal"
                      >
                        <div class="form-group auth-form-group-custom mb-4">
                          <i class="ri-chat-smile-line auti-custom-input-icon"></i>
                          <label for="name">name</label>
                          <input
                            type="name"
                            v-model="auth.name"
                            class="form-control"
                            id="name"
                            placeholder="Enter name"
                          />
                        </div>

                        <div class="form-group auth-form-group-custom mb-4">
                          <i class="ri-phone-line auti-custom-input-icon"></i>
                          <label for="phone">phone</label>
                          <input
                            type="phone"
                            v-model="auth.phone"
                            class="form-control"
                            id="phone"
                            placeholder="Enter phone"
                          />
                        </div>

                        <div class="form-group auth-form-group-custom mb-4">
                          <i class="ri-lock-2-line auti-custom-input-icon"></i>
                          <label for="userpassword">Password</label>
                          <input
                            v-model="auth.password"
                            type="password"
                            class="form-control"
                            id="userpassword"
                            placeholder="Enter password"
                          />
                        </div>

                        <div class="form-group auth-form-group-custom mb-4">
                          <i class="ri-lock-2-line auti-custom-input-icon"></i>
                          <label for="userpasswordconfirm">Password Confirmation</label>
                          <input
                            v-model="auth.password_confirmation"
                            type="password"
                            class="form-control"
                            id="userpasswordconfirm"
                            placeholder="Enter password confirmation"
                          />
                        </div>
                        <div class="mt-4 text-center">
                          <button
                            class="btn btn-primary w-md waves-effect waves-light"
                            type="submit"
                            :disabled="processing"
                            @click="register"
                          >
                            <span
                              v-show="submitted || pending"
                              class="spinner-border spinner-border-sm"
                              role="status"
                              aria-hidden="true"
                            ></span>
                            <span v-show="!submitted && !pending">register</span>
                          </button>
                        </div>
                      </form>
                    </div>

                    <div class="mt-5 text-center">
                      <p>
                        Copyright © Ahmad Sha'lan {{ currentYear }},<br />
                        All Rights Reserved.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="authentication-bg">
            <div class="bg-overlay"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
.logo img {
  width: 180px;
}
.authentication-bg .bg-overlay {
  background-color: transparent;
}
</style>
