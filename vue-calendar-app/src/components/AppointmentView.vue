<template>
  <div class="text-center section">
    <div class="container">
      <div class="header">
        <span>Schedule your dance with death</span>
      </div>

      <div class="body">
        <el-row :gutter="20">
          <el-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12">
            <vc-date-picker
              v-model="date_"
              locale="en"
              is-inline
              :available-dates="{
                start: moment(new Date()).add(1, 'day').format('YYYY-MM-DD'),
                end: null,
              }"
              :masks="{ input: 'YYYY-MM-DD', weekdays: 'WWW' }"
              is-expanded
              :attributes="[
                {
                  key: 'today',
                  highlight: true,
                  // dates: {
                  //   start: moment(new Date()),
                  //   end: moment(new Date()),
                  //   monthlyInterval: 1,
                  // },
                },
              ]"
              @dayclick="dayClick"
            />
          </el-col>

          <el-col
            v-if="Data.length > 0"
            :xs="24"
            :sm="24"
            :md="24"
            :lg="12"
            :xl="12"
          >
            <collection>
              <collection-item
                v-for="item in Data"
                :key="item.id"
                style="
                  display: flex;
                  justify-content: space-between;
                  align-items: center;
                "
              >
                <div>
                  <p>{{ item.user.name }}</p>
                  <p>{{ item.user.email }}</p>
                  <p>
                    {{
                      moment(item.appointmentDate).format("MM/DD/YYYY hh:mma")
                    }}
                  </p>
                </div>

                <div>
                  <li
                    @click="getItem(item)"
                    class="el-icon-edit-outline"
                    style="cursor: pointer; margin-right: 1em"
                  ></li>

                  <el-popconfirm
                    :hide-icon="true"
                    title="Delete this item?"
                    confirm-button-text="Delete"
                    cancel-button-text="Cancel"
                    @confirm="removeItem(item.id)"
                  >
                    <li
                      slot="reference"
                      class="el-icon-delete-solid"
                      style="cursor: pointer; color: red"
                    ></li>
                  </el-popconfirm>
                </div>
              </collection-item>
            </collection>
          </el-col>

          <el-col v-else :xs="24" :sm="24" :md="24" :lg="12" :xl="12">
            <div class="empty">
              <img src="../assets/no-data.svg" width="150"/>
              <div>
                <span>No data</span>
              </div>
            </div>
          </el-col>
        </el-row>
      </div>
    </div>

    <el-dialog
      :title="id === '' ? 'Schedule dance' : 'Update'"
      :visible.sync="dialogVisible"
      width="30%"
      :close-on-click-modal="false"
      :before-close="closeDialog"
    >
      <el-row :gutter="24">
        <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
          <el-form :model="ruleForm" :ref="$options.name">
            <el-col
              style="margin-bottom: 1em"
              :xs="24"
              :sm="24"
              :md="24"
              :lg="24"
              :xl="24"
            >
              <el-input
                v-model="ruleForm.email"
                :disabled="input"
                placeholder="Ingrese su email para continuar"
              >
                <el-button
                  @click="verifyUser"
                  slot="append"
                  icon="el-icon-search"
                  :disabled="input"
                ></el-button>
              </el-input>
            </el-col>

            <div v-show="Success">
              <el-col
                :xs="24"
                :sm="24"
                :md="24"
                :lg="id === '' ? 16 : 24"
                :xl="id === '' ? 16 : 24"
              >
                <el-form-item
                  prop="name"
                  :rules="[{ required: true, message: '', trigger: 'change' }]"
                >
                  <el-input
                    v-model="ruleForm.name"
                    :disabled="input"
                    placeholder="Ingresa tu nombre"
                  ></el-input>
                </el-form-item>
              </el-col>

              <el-col
                :xs="24"
                :sm="24"
                :md="24"
                :lg="12"
                :xl="12"
                v-if="id !== ''"
              >
                <el-form-item>
                  <el-date-picker
                    v-model="ruleForm.date"
                    type="date"
                    placeholder="Pick a day"
                    style="width: 100%"
                    format="yyyy-MM-dd"
                    value-format="yyyy-MM-dd"
                    :picker-options="pickerBeginDateBefore"
                  >
                  </el-date-picker>
                </el-form-item>
              </el-col>

              <el-col
                :xs="24"
                :sm="24"
                :md="24"
                :lg="id === '' ? 8 : 12"
                :xl="id === '' ? 8 : 12"
              >
                <el-form-item
                  prop="startTime"
                  :rules="[{ required: true, message: '', trigger: 'change' }]"
                >
                  <el-time-picker
                    v-model="ruleForm.startTime"
                    value-format="HH:mm:ss"
                    :picker-options="{
                      selectableRange: '00:00:00 - 23:59:59',
                      format: 'HH:mm:ss',
                    }"
                    placeholder="Arbitrary time"
                    style="width: 100%"
                  >
                  </el-time-picker>
                </el-form-item>
              </el-col>
            </div>
          </el-form>
        </el-col>
      </el-row>
      <span slot="footer" class="dialog-footer">
        <el-row type="flex" justify="space-between">
          <el-button @click="clear">Cancel</el-button>
          <el-button type="primary" @click="create">Save</el-button>
        </el-row>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import moment from "moment";
import {http} from "@/api/ApiClient";
import Collection from "@/components/collection/Collection";
import CollectionItem from "@/components/collection/CollectionItem";

export default {
  name: "AppointmentView",
  components: {CollectionItem, Collection},
  data() {
    const now = new Date();
    const month = now.getMonth();
    const year = now.getFullYear();
    const day = now.getDate();
    return {
      id: "",
      date_: "",
      ruleForm: {
        id: null,
        name: "",
        email: "",
        appointmentDate: "",
        startTime: "",
        date: "",
      },
      Data: [],
      Success: false,
      input: false,
      today: new Date(year, month, day) * 1,
      masks: {
        weekdays: "WWW",
      },
      pickerBeginDateBefore: {
        disabledDate(time) {
          let yestoday = new Date();
          yestoday.setDate(yestoday.getDate() - 1);
          return time.getTime() < yestoday.getTime();
        },
      },
      pickerBeginDateAfter: {
        disabledDate(time) {
          return time.getTime() > Date.now();
        },
      },
      dayEvents: {
        click: (day) => {
          // eslint-disable-next-line no-console
          console.log("dayEvents:", day);
        },
      },
      dialogVisible: false,
      moment,
    };
  },
  created() {
    this.scheduleList();
  },
  methods: {
    dayClick(data) {
      // eslint-disable-next-line no-console
      this.date_ = data.id;
      // console.log("origin DOM click", data.id);
      this.dialogVisible = !this.dialogVisible;
    },
    verifyUser() {
      http("post", "Verify/User", {email: this.ruleForm.email}).then(
        (response) => {
          const {Success, Data} = response;
          if (Success) {
            this.ruleForm.id = Data.id;
            this.ruleForm.name = Data.name;
          } else {
            this.ruleForm.id = null;
            this.ruleForm.name = "";
            this.ruleForm.startTime = "";
          }
          this.Success = true;
        }
      );
    },
    create() {
      this.$refs[this.$options.name].validate((valid) => {
        if (valid) {
          const person =
            this.ruleForm.id !== null
              ? {id: this.ruleForm.id}
              : {
                id: this.ruleForm.id,
                name: this.ruleForm.name,
                email: this.ruleForm.email,
              };

          const data = {
            appointmentDate: `${
              this.id === "" ? this.date_ : this.ruleForm.date
            } ${this.ruleForm.startTime}`,
            exist: this.ruleForm.id !== null,
            person,
          };

          let method = this.id === "" ? "post" : "put";
          let baseURL =
            this.id === ""
              ? "Schedule/Appointment"
              : `Schedule/Appointment/${this.id}`;

          http(method, baseURL, data).then((response) => {
            const {Success, Errors, Message} = response;
            if (Success) {
              this.$notify({
                title: "Success",
                message: Message,
                type: "success",
              });
              this.scheduleList();
              this.clear();
              this.dialogVisible = false;
            } else {
              this.$alert(Errors[0], "Error", {
                confirmButtonText: "OK",
              });
            }
          });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    getItem(item) {
      const {id, appointmentDate, user} = item;
      this.id = id;
      this.ruleForm.id = user.id;
      this.ruleForm.name = user.name;
      this.ruleForm.email = user.email;
      this.ruleForm.startTime = moment(appointmentDate).format("HH:mm:ss");
      this.Success = true;
      this.input = true;
      this.dialogVisible = true;
    },
    removeItem(id) {
      http("delete", `Schedule/Appointment/${id}`).then((response) => {
        const {Success} = response;

        if (Success) {
          this.scheduleList();
        }
      });
    },
    scheduleList() {
      http("get", "Schedule").then((response) => {
        console.log(response);
        const {Success, Data} = response;
        if (Success) {
          this.Data = Data;
        } else {
          this.Data = [];
        }
      });
    },
    closeDialog() {
      this.dialogVisible = false;
      this.clear();
    },
    clear() {
      this.id = "";
      this.date_ = "";
      this.Success = false;
      this.input = false;
      this.ruleForm.email = "";
      this.$refs[this.$options.name].resetFields();
    },
  },
};
</script>

<style>
.vc-day-content.is-disabled {
  pointer-events: none;
}

.container {
  max-width: 1300px;
  margin: 2rem auto 0;
  border: 1px solid #e2dfdf;
  border-radius: 4px;
}

.header {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 73px;
  background: rgba(248, 248, 248, 0.31);
  border-bottom: 1px solid #e2dfdf;
}

.header span {
  font-weight: 600;
  font-size: 16px;
}

.body {
  padding: 20px;
}

.empty {
  height: 250px;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
}

.empty div {
  margin-top: 15px;
  color: #909399;
}
</style>
