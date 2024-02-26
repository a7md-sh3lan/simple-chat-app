<script>
import simplebar from "simplebar-vue";
import { mapActions } from "vuex";
import appConfig from "@/app.config";
export default {
  data() {
    return {
      layoutColor: "white",
      userName: "",
    };
  },
  components: {
    simplebar,
  },
  computed: {
    currentRouteName() {
      return this.$route.name;
    },
  },
  methods: {
    switchLayout(layoutColor) {
      let that = this;
      that.$swal
        .fire({
          text: "This page will reload, Please save all your data",
          icon: "warning",
          showCancelButton: true,
          cancelButtonColor: "#34c38f",
          confirmButtonColor: "#f46a6a",
          confirmButtonText: "Ok, reload",
          cancelButtonText: "No, wait",
        })
        .then((result) => {
          if (result.isConfirmed) {
            localStorage.setItem("layoutColor", layoutColor);
            location.reload();
          }
        });
    },
    toggleMenu() {
      this.$parent.toggleMenu();
    },
    initFullScreen() {
      document.body.classList.toggle("fullscreen-enable");
      if (
        !document.fullscreenElement &&
        /* alternative standard method */ !document.mozFullScreenElement &&
        !document.webkitFullscreenElement
      ) {
        // current working methods
        if (document.documentElement.requestFullscreen) {
          document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
          document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
          document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        }
      } else {
        if (document.cancelFullScreen) {
          document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
          document.webkitCancelFullScreen();
        }
      }
    },
    toggleRightSidebar() {
      this.$parent.toggleRightSidebar();
    },

    checkIfAnyNotReededMSG(data) {
      let flag = false;
      for (let index = 0; index < data.length; index++) {
        if (data[index].seen == 0) {
          flag = true;
          break;
        }
      }
      return flag;
    },

    ...mapActions({
      signOut: "auth/logout",
    }),
    async logout() {
      await this.axios.post(appConfig.logout).then(({ data }) => {
        this.signOut();
        this.$router.push({ name: "login" });
      });
    },
  },
  created() {
    this.layoutColor = localStorage.getItem("layoutColor");
    this.userName = this.$store.state.auth.user.name;
  },
  mounted() {
    
  },
};
</script>

<template>
  <header id="page-topbar">
    <div class="navbar-header">
      <div class="d-flex">
        <!-- LOGO -->
        <div class="navbar-brand-box">
          <router-link to="/" class="logo logo-dark">
            <span class="logo-sm">
              <img
                :src="require('@/assets/images/logo-sm-dark.png')"
                height="22"
              />
            </span>
            <span class="logo-lg">
              <img :src="require('@/assets/images/logo-dark.png')" height="38" />
            </span>
          </router-link>

          <router-link to="/" class="logo logo-light">
            <span class="logo-sm">
              <img
                :src="require('@/assets/images/logo.png')"
                height="60"
              />
            </span>
            <span class="logo-lg">
              <img
                :src="require('@/assets/images/logo-light.png')"
                height="60"
              />
            </span>
          </router-link>
        </div>

        <button
          title="Main Menu"
          @click="toggleMenu"
          type="button"
          class="btn btn-sm px-3 font-size-24 header-item waves-effect"
          id="vertical-menu-btn"
        >
          <i class="ri-menu-2-line align-middle"></i>
        </button>
      </div>

      <div class="d-flex">
        <!-- <div class="dropdown d-lg-inline-block ml-1" v-if="layoutColor == 'dark'">
          <button
            type="button"
            title="Bright Mode"
            class="btn header-item noti-icon waves-effect"
            @click="switchLayout('white')"
          >
            <i class="ri-sun-line"></i>
          </button>
        </div> -->

        <!-- <div class="dropdown d-lg-inline-block ml-1" v-if="layoutColor != 'dark'">
          <button
            title="Dark Mode"
            type="button"
            class="btn header-item noti-icon waves-effect"
            @click="switchLayout('dark')"
          >
            <i class="ri-moon-fill"></i>
          </button>
        </div> -->

        <!-- <div class="dropdown d-lg-inline-block ml-1">
          <button
            title="Fullscreen"
            type="button"
            class="btn header-item noti-icon waves-effect"
            @click="initFullScreen"
          >
            <i class="ri-fullscreen-line"></i>
          </button>
        </div> -->

        <b-dropdown
          right
          variant="black"
          toggle-class="header-item"
          class="d-inline-block user-dropdown"
        >
          <template v-slot:button-content>
            <span class="d-xl-inline-block ml-1">{{ userName }}</span>
            <i class="mdi mdi-chevron-down d-xl-inline-block"></i>
          </template>
          <!-- item-->
          <a class="dropdown-item text-danger" href="javascript:void(0)" @click="logout">
            <i class="ri-shut-down-line align-middle mr-1 text-danger"></i>
            Logout
          </a>
        </b-dropdown>

        <!-- <div class="dropdown d-inline-block">
          <button
            title="Settings"
            type="button"
            class="btn header-item noti-icon right-bar-toggle waves-effect toggle-right"
            @click="toggleRightSidebar"
          >
            <i class="ri-settings-2-line toggle-right"></i>
          </button>
        </div> -->
      </div>
    </div>
  </header>
</template>

<style scoped>
.notification-item .media {
  cursor: pointer;
}

.notification-item .not_readed {
  background-color: #ffeaea;
}

.dark-layout .notification-item .not_readed {
  background-color: #620206;
}
body[data-sidebar="dark"] .navbar-brand-box {
  background: #fff;
}
</style>
