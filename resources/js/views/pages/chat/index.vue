<script>
import Layout from "../../layout/mainLayout";
import PageHeader from "@/components/page-header";
import simplebar from "simplebar-vue";
import Loading from "@/components/table-loading";
import { required } from "vuelidate/lib/validators";
import appConfig from "@/app.config";
/**
 * My Chat Component
 */
export default {
  page: {
    title: "My Chat",
    meta: [
      {
        name: "description",
      },
    ],
  },
  components: {
    Layout,
    PageHeader,
    simplebar,
    Loading,
  },
  data() {
    return {
      title: "My Chat",
      items: [],
      submitted: false,
      fetching: false,
      fetchingUserLogs: false,
      endingSession: false,
      errors: "",
      tabIndex: 0,
      form: {
        message: "",
      },
      activeUsers: [],
      pendingUsers: [],
      activeSessions: [],
      selectedGroup: "active",
      selectedSession: "",
      selectedUserLogs: [],
      sessionStart: false,
      sessionStartId: "",
      authUser: "",
      searchQuery: "",
      newUserAddToPendingList: false,
      newUserAddToActiveSessionList: false,
      previewImage: null,
      activeClicked: false,
      isRecordHidden: false,
      recordedAudio: undefined,
      showError: false,
      enableMsg: false,
      disableMsg: false,
      user_name: "",
      errors_limit: 0,
    };
  },
  validations: {
    form: {
      message: {
        required,
      },
    },
  },
  computed: {
    filterActiveUsersList() {
      if (this.searchQuery.length > 0 && this.activeUsers.length > 0) {
        if (this.tabIndex === 0) {
          return this.activeUsers.filter((item) => {
            return this.searchQuery
                .toLowerCase()
                .split(" ")
                .every((v) => item.name.toLowerCase().includes(v));
          });
        }
      }
      return this.activeUsers;
    },
  },
  methods: {
    async getActiveUsers() {
      let that = this;
      await that.axios.get("messages").then(function (response) {
        that.activeUsers = response.data.users;
      });
    },
    async getUserLogs(id) {
      let that = this;
      that.fetchingUserLogs = true;
      await that.axios.get(`log/${id}`).then(function (response) {
        that.selectedUserLogs = response.data.chat_history;
        that.fetchingUserLogs = false;
        that.handleScroll();
      });
    },

    selectUser(session, groupType) {
      if (groupType === "active") {
        this.selectedSession = this._.filter(this.activeUsers, function (o) {
          return o.id === session.id;
        })[0];
        this.selectedGroup = "active";
      }

      this.getUserLogs(this.selectedSession.id);
    },

    updateSession(obj, msg) {
      let newObject = obj;
      newObject.last_message = msg;
      return newObject;
    },

    async formSubmit() {
      let that = this;
      that.$v.$touch();

      if (that.$v.$invalid) {
        window.scrollTo(0, 0);
        return;
      } else {
        that.submitted = true;
        const config = {
          headers: {
            "content-type": "multipart/form-data",
          },
        };

        let formData = new FormData();
        formData.append("content", that.form.message);
        formData.append("receiver_id", that.selectedSession.id);

        await that.axios
            .post(`messages`, formData, config)
            .then(function (res) {
              that.submitted = false;
              // update users list
              that.activeUsers = that._.map(that.activeUsers, function (o) {
                return o.id === that.selectedSession.id
                    ? that.updateSession(o, that.form.message)
                    : o;
              });
              // update chat logs
              that.selectedUserLogs.push({
                created_at: res.data.created_at,
                name: res.data.name,
                text: that.form.message,
                message: that.form.message,
              });

              that.form.message = "";
              that.handleScroll();
            })
            .catch(function (error) {
              that.submitted = false;
              if (error.response) {
                that.errors = error.response.data.message;
                console.log(that.errors);
              }
            });
      }
    },
    getUserFromList(session_id, list) {
      return this._.filter(this[list], function (o) {
        return o.id === session_id;
      })[0];
    },
    removeUserFromList(session, list) {
      let that = this;
      that[list] = that._.filter(that[list], function (o) {
        return o.id != session;
      });
    },
    newMessageFromUser(data) {
      let that = this;
      // update users list
      if(data.receiver_id == that.authUser.id) {
        console.log('ANAAAAAAA', that.selectedSession.id);
      }
      console.log(that.activeUsers, data);
      that.activeUsers = that._.map(that.activeUsers, function (o) {
        return o.id === data.sender_id ? that.updateSession(o, data.message) : o;
      });
      that.activeSessions = that._.map(that.activeSessions, function (o) {
        return o.id === data.sender_id ? that.updateSession(o, data.message) : o;
      });
      that.pendingUsers = that._.map(that.pendingUsers, function (o) {
        return o.id === data.sender_id ? that.updateSession(o, data.message) : o;
      });
      // update chat history if user selected
      if (
          that.selectedSession.id == data.sender_id
      ) {
        that.getUserLogs(data.sender_id);
      }
    },

    handleScroll() {
      if (this.$refs.current.$el) {
        setTimeout(() => {
          this.$refs.current.SimpleBar.getScrollElement().scrollTop =
              this.$refs.current.SimpleBar.getScrollElement().scrollHeight + 1500;
          if (this.sessionStart) this.$refs.chat_input.focus();
        }, 300);
      }
    },

    makeAttention() {
      this.newUserAddToPendingList = true;
      this.newUserAddToActiveSessionList = true;
      setTimeout(() => {
        this.newUserAddToPendingList = false;
        this.newUserAddToActiveSessionList = false;
      }, 550);
    },
  },
  created() {
    //
    let that = this;
    that.axios.get("messages").then(function (response) {
      that.user_name = response.data.user.name;
      that.activeUsers = response.data.users;
    });
  },
  mounted() {
    window.addEventListener("load", () => {});

    let that = this;

    that.authUser = that.$store.state.auth.user;

    that.fetching = true;
    that.axios
        .all([that.getActiveUsers()])
        .finally(() => (that.fetching = false));

    // // new mssage from user
    // window.Echo.channel("Message").listen("MessageEvent", e => {
    //   console.log(e);
    // });
    // window.Echo.channel(appConfig.title + "_" + "Message").listen(
    //     "MessageEvent",
    //     (data) => {
    //       console.log("HHHHHHHHHHHHHHHH");
    //       console.log(data);
    //       that.newMessageFromUser(data);
    //     }
    // );

    window.io.on('message', (data) => {
      that.newMessageFromUser(data);
    });

    var container = document.querySelector("#scrollElement .simplebar-content-wrapper");
    if (container != null)
      container.scrollTo({
        top: 200,
        behavior: "smooth",
      });

    var container2 = document.querySelector(
        "#containerElement .simplebar-content-wrapper"
    );

    if (container2 != null)
      container2.scrollTo({
        top: 500,
        behavior: "smooth",
      });
  },

  destroyed() {
    // window.Echo.leave(appConfig.title + "_" + "Message");
  },
};
</script>

<template>
  <Layout>
    <PageHeader :title="title" :items="items" />
    <div class="d-lg-flex mb-4">
      <div class="chat-leftsidebar">
        <div class="card-body border-bottom py-1 px-0">
          <div class="search-box chat-search-box">
            <div class="position-relative">
              <b-form-input
                  v-model="searchQuery"
                  type="search"
                  placeholder="Search ..."
                  class="form-control border-right"
              >
              </b-form-input>
              <i class="ri-search-line search-icon"></i>
            </div>
          </div>
        </div>
        <div class="chat-leftsidebar-nav">
          <b-tabs
              pills
              fill
              content-class=""
              justified
              v-model="tabIndex"
          >
            <b-tab title="Tab 1" active>
              <template v-slot:title>
                <span class="mt-2 d-none d-sm-block">
                  Active&nbsp;
                  <b-badge variant="primary">{{ activeUsers.length }}</b-badge>
                </span>
              </template>

              <b-card-text>
                <div class="border-top">
                  <simplebar style="" id="scrollElement">
                    <div v-if="fetching">
                      <br />
                      <Loading></Loading>
                    </div>

                    <ul class="list-unstyled chat-list" v-if="!fetching">
                      <li v-for="session in filterActiveUsersList" :key="session.id">
                        <b-container fluid>
                          <b-row
                              class="pt-2 pb-1 border-bottom user_row"
                              :class="{
                              active_row:
                                selectedSession != '' &&
                                selectedSession.id == session.id,
                            }"
                          >
                            <b-col cols="8">
                              <div
                                  class="user_tab p-1"
                                  @click="selectUser(session, 'active')"
                              >
                                <h5 class="text-truncate font-size-14 mb-1">
                                  {{ session.name }}
                                </h5>
                                <p class="text-truncate mb-0 last_message">
                                  {{ session.last_message }}
                                </p>
                              </div>
                              <!-- user_tab -->
                            </b-col>
                          </b-row>
                        </b-container>
                      </li>
                    </ul>
                  </simplebar>
                </div>
              </b-card-text>
            </b-tab>
          </b-tabs>
        </div>
      </div>
      <div class="w-100 user-chat mt-4 mt-sm-0">
        <div class="p-3 px-lg-4 user-chat-border" v-if="selectedSession != ''">
          <div class="row">
            <div class="col-md-4 col-6">
              <h5 class="font-size-15 mb-1 text-truncate">
                {{ selectedSession.name }}
              </h5>
              <p class="text-muted text-truncate mb-0">
                <a
                    v-if="selectedSession.phone"
                    :href="'tel:' + selectedSession.phone"
                >
                  {{ selectedSession.phone }}
                </a>
              </p>
            </div>
          </div>
        </div>

        <div class="px-lg-2 chat-users">
          <div class="chat-conversation p-3">
            <simplebar style="max-height: 450px" id="containerElement" ref="current">
              <ul class="list-unstyled mb-0 pr-3" v-if="selectedUserLogs.length > 0">
                <li
                    v-for="(message, key) in selectedUserLogs"
                    :key="key"
                    :class="{ right: `${message.sender_id}` === `${selectedSession.id}` }"
                >
                  <div class="conversation-list">
                    <div class="ctext-wrap">
                      <div class="conversation-name">{{ message.name }}</div>
                      <div class="ctext-wrap-content">
                        <p class="mb-0 mtext">
                          {{ message.message }}
                        </p>
                      </div>

                      <p class="chat-time mb-0">
                        <i class="bx bx-time-five align-middle mr-1"></i>
                        {{ message.created_at }}
                      </p>
                    </div>
                  </div>
                </li>
              </ul>
            </simplebar>
          </div>
          <div class="px-lg-3">
            <div
                class="p-3 chat-input-section"
                v-if="selectedGroup === 'active' && selectedSession !== ''"
            >
              <fieldset :disabled="submitted || endingSession">
                <form
                    @submit.prevent="formSubmit"
                    enctype="multipart/form-data"
                    class="row"
                >
                  <div class="col">
                    <div class="position-relative">
                      <input
                          type="text"
                          ref="chat_input"
                          v-model="form.message"
                          class="form-control chat-input"
                          placeholder="Enter Message..."
                          :class="{
                          'is-invalid': submitted && $v.form.message.$error,
                          smallInput: isRecordHidden,
                        }"
                      />
                      <div
                          v-if="submitted && $v.form.message.$error"
                          class="invalid-feedback"
                      >
                        <span v-if="!$v.form.message.required"
                        >This value is required.</span
                        >
                      </div>
                    </div>
                  </div>

                  <div class="col-auto">
                    <button
                        class="btn btn-primary chat-send waves-effect waves-light"
                        type="submit"
                    >
                      <span
                          v-show="submitted || endingSession"
                          class="spinner-border spinner-border-sm"
                          role="status"
                          aria-hidden="true"
                      ></span>
                      <span v-show="!submitted && !endingSession">Send</span>
                    </button>
                  </div>
                </form>
              </fieldset>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- end row -->
  </Layout>
</template>

<style scoped>
.white-layout .active_row {
  background-color: rgba(241, 245, 247, 0.7);
}

.dark-layout .active_row {
  background-color: #2d3448;
}

.user_row:hover {
  background-color: rgba(241, 245, 247, 0.7);
  cursor: pointer;
}

.dark-layout .user_row:hover {
  background-color: #2d3448;
  cursor: pointer;
}

.dark-layout .button_tab .session_date,
.dark-layout .user_tab .last_message,
.dark-layout .ctext-wrap .mtext,
.dark-layout .chat-leftsidebar .chat-leftsidebar-nav .nav .nav-link.active span {
  color: #fff !important;
}

.chat-leftsidebar .chat-search-box .form-control {
  border-radius: 0;
}

.bounce-enter-active {
  animation: bounce-in 0.5s;
}

.escalated-flag {
  display: none;
}

.bounce-leave-active {
  animation: bounce-in 0.5s reverse;
}

@keyframes bounce-in {
  0% {
    transform: scale(0);
  }

  50% {
    transform: scale(1.5);
  }

  100% {
    transform: scale(1);
  }
}

.chat-input {
  padding-left: 3rem;
}

.btnAttach {
  position: absolute;
  top: 5%;
  font-size: 1.5rem;
  width: 40px;
  height: 37px;
}

.btnAttach i {
  padding: 0 0.5rem;
}

.btnAttach input {
  display: none;
  width: 100px;
  height: 37px;
}

.btnAttach label {
  width: 40px;
  height: 37px;
  margin: unset;
  color: unset;
  cursor: pointer;
}

.actions {
  display: block;
  text-align: center;
}

.imagePreviewWrapper {
  width: 90%;
  height: 250px;
  display: block;
  cursor: pointer;
  margin: 0 auto;
  background-size: cover;
  background-position: center center;
}

.modal-footer {
  margin-top: 0.5rem;
}

.chat-input-section {
  position: relative;
}

.ar-icon {
  padding: 0 !important;
}

.ar-records {
  min-height: 0px !important;
}

.maxSizeError {
  color: red;
}

.form-control.smallInput {
  width: 58%;
}
.alertMsg h5 {
  position: absolute;
  background-color: #5664d2;
  width: 360px;
  text-align: center;
  padding: 1rem 0;
  color: #fff;
  top: 20%;
  opacity: 0.7;
}
.enableNameCheck {
  position: absolute;
  top: 14%;
  right: 20%;
  border-right: 1px solid #252b3b;
  padding-right: 2rem;
}
.errorsLimit {
  position: absolute;
  top: 12.7%;
  right: 3%;
}
.errorsLimit .bv-no-focus-ring {
  width:70px;
}
.errorsLimit .form-control {
  height: 30px;
  padding: 0 0.2rem;
  position: relative;
  top: 8%;
}
.errorsLimit button.btn{
  min-width: 50px;
  height: 30px;
  position: relative;
  top: 3px;
  padding: 0;
  left: 5%;
}
.form-row > .col, .form-row > [class*=col-] {
  padding-right: 15px;

}
</style>
