<script>
import { mapActions } from "vuex";
import { required, phone } from "vuelidate/lib/validators";
import axios from "axios";
import appConfig from "@/app.config";
export default {
  name: "login",
  data() {
    return {
      currentYear: new Date().getFullYear(),
      auth: {
        phone: "",
        password: "",
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
    phone: {
      required,
    },
    password: {
      required,
    },
  },
  methods: {
    ...mapActions({
      signIn: "auth/login",
    }),
    async login() {
      this.processing = true;
      this.submitted = true;
      await axios.get(appConfig.csrf_cookies);
      await axios
        .post(appConfig.login, this.auth)
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

                      <h4 class="font-size-18 mt-4">Welcome Back !</h4>
                      <p class="text-muted">Sign in to continue ...</p>
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
                        <div class="mt-4 text-center">
                          <button
                            class="btn btn-primary w-md waves-effect waves-light"
                            type="submit"
                            :disabled="processing"
                            @click="login"
                          >
                            <span
                              v-show="submitted || pending"
                              class="spinner-border spinner-border-sm"
                              role="status"
                              aria-hidden="true"
                            ></span>
                            <span v-show="!submitted && !pending">Login</span>
                          </button>
                        </div>
                        <div class="mt-4 text-center">
                          <router-link to="/register" class="nav-link side-nav-link-ref">
                              <i class="ri-dashboard-line"></i>
                              <span>New User..register..</span>
                          </router-link>
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
