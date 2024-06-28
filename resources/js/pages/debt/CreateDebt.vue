<template>
    <CCard class="p-4">
        <CCardBody>
            <form v-on:submit.prevent>
                <h4>{{ $t("create") }}{{ $t("debt") }}</h4>
                <hr />
                <v-text-field
                    v-model="item"
                    :label="$t('item')"
                    :error="errors.item ? true : false"
                    :error-messages="errors.item"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-select
                    v-model="type"
                    :items="debts"
                    item-text="name"
                    item-value="value"
                    :label="$t('type')"
                    required
                    outlined
                    dense
                    :error="errors['type'] ? true : false"
                    :error-messages="errors['type']"
                ></v-select>
                <v-text-field
                    v-model="amount"
                    :label="$t('amount')"
                    type="number"
                    :error="errors.amount ? true : false"
                    :error-messages="errors.amount"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="remark"
                    :label="$t('remark')"
                    required
                    outlined
                    dense
                    clearable
                >
                </v-text-field>
                <v-text-field
                    v-model="color.value"
                    class="mb-4"
                    :label="$t('eventcolor')"
                    hide-details
                    outlined
                    dense
                >
                    <template v-slot:append>
                        <v-menu
                            v-model="color.menu"
                            top
                            nudge-bottom="105"
                            nudge-left="16"
                            :close-on-content-click="false"
                        >
                            <template v-slot:activator="{ on }">
                                <div
                                    v-bind:style="{
                                        backgroundColor: color.value,
                                        cursor: 'pointer',
                                        height: '26px',
                                        width: '26px',
                                        borderRadius: color.menu
                                            ? '50%'
                                            : '4px',
                                        transition:
                                            'border-radius 200ms ease-in-out'
                                    }"
                                    v-on="on"
                                />
                            </template>
                            <v-card>
                                <v-card-text class="pa-0">
                                    <v-color-picker
                                        v-model="color.value"
                                        hide-canvas
                                        hide-inputs
                                        hide-mode-switch
                                        show-swatches
                                        flat
                                    />
                                </v-card-text>
                            </v-card>
                        </v-menu>
                    </template>
                </v-text-field>

                <div v-if="schedule.type === 'daily'">
                    <h4>Daily Due Time</h4>
                    <v-menu
                        ref="dueDailyMenu"
                        v-model="dueDate.daily.menu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        :return-value.sync="dueDate.daily.time"
                        transition="scale-transition"
                        offset-y
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="dueDate.daily.time"
                                label="Daily Due Time"
                                readonly
                                required
                                outlined
                                dense
                                :error="
                                    errors['due_date.daily.time'] ? true : false
                                "
                                :error-messages="errors['due_date.daily.time']"
                                v-bind="attrs"
                                v-on="on"
                            ></v-text-field>
                        </template>
                        <v-time-picker
                            v-if="dueDate.daily.menu"
                            v-model="dueDate.daily.time"
                            full-width
                            @click:minute="
                                $refs.dueDailyMenu.save(dueDate.daily.time)
                            "
                        ></v-time-picker>
                    </v-menu>
                </div>
                <div v-if="schedule.type == 'weekly'">
                    <h4>Weekly Due Date</h4>
                    <v-select
                        v-model="dueDate.weekly.day"
                        :items="weekDays"
                        item-text="name"
                        item-value="value"
                        label="Day"
                        required
                        outlined
                        dense
                        :error="errors['due_date.weekly.day'] ? true : false"
                        :error-messages="errors['due_date.weekly.day']"
                    ></v-select>
                </div>
                <div v-if="schedule.type == 'monthly'">
                    <h4>Monthly Due Date</h4>
                    <v-select
                        v-model="dueDate.monthly.day_of_month"
                        :items="days"
                        label="Day"
                        required
                        outlined
                        dense
                        :error="errors['due_date.monthly.date'] ? true : false"
                        :error-messages="errors['due_date.monthly.date']"
                    ></v-select>
                </div>
                <div v-if="schedule.type == 'yearly'">
                    <h4>Yearly Due Date</h4>
                    <v-row>
                        <c-col md="6">
                            <v-select
                                v-model="dueDate.yearly.month_of_year"
                                :items="months"
                                label="Month"
                                :error="
                                    errors['due_date.yearly.date']
                                        ? true
                                        : false
                                "
                                :error-messages="errors['due_date.yearly.date']"
                                required
                                outlined
                                dense
                            ></v-select>
                        </c-col>
                        <c-col md="6">
                            <v-select
                                v-model="dueDate.yearly.day_of_year"
                                :items="days"
                                label="Day"
                                :error="
                                    errors['due_date.yearly.date']
                                        ? true
                                        : false
                                "
                                :error-messages="errors['due_date.yearly.date']"
                                required
                                outlined
                                dense
                            ></v-select>
                        </c-col>
                    </v-row>
                </div>
                <div v-if="schedule.type == 'specific'">
                    <h4>Specific Due Date</h4>
                    <v-menu
                        v-model="dueDate.specific.menu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="290px"
                    >
                        <template v-slot:activator="{ on }">
                            <v-text-field
                                v-model="dueDate.specific.date"
                                :label="$t('date')"
                                required
                                outlined
                                dense
                                readonly
                                :error="
                                    errors['due_date.specific.date']
                                        ? true
                                        : false
                                "
                                :error-messages="
                                    errors['due_date.specific.date']
                                "
                                v-on="on"
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="dueDate.specific.date"
                            @input="dueDate.specific.menu = false"
                        ></v-date-picker>
                    </v-menu>
                </div>

                <h4>{{ $t("schedule.title") }}</h4>
                <hr />

                <v-select
                    v-model="schedule.type"
                    :items="schedules"
                    :label="$t('type')"
                    :error="errors['schedule.type'] ? true : false"
                    :error-messages="errors['schedule.type']"
                    item-text="name"
                    item-value="value"
                    required
                    outlined
                    dense
                ></v-select>
                <div
                    v-if="
                        schedule.type === 'daily' ||
                            schedule.type === 'weekly' ||
                            schedule.type === 'monthly' ||
                            schedule.type === 'yearly'
                    "
                >
                    <v-row>
                        <c-col md="6">
                            <v-menu
                                v-model="schedule.start_date.menu"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="schedule.start_date.date"
                                        :label="$t('startdate')"
                                        :error="
                                            errors['schedule.start_date.date']
                                                ? true
                                                : false
                                        "
                                        :error-messages="
                                            errors['schedule.start_date.date']
                                        "
                                        required
                                        outlined
                                        dense
                                        readonly
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="schedule.start_date.date"
                                    @input="schedule.start_date.menu = false"
                                ></v-date-picker>
                            </v-menu>
                        </c-col>
                        <c-col md="6">
                            <v-menu
                                v-model="schedule.end_date.menu"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="schedule.end_date.date"
                                        :label="$t('enddate')"
                                        :error="
                                            errors['schedule.end_date.date']
                                                ? true
                                                : false
                                        "
                                        :error-messages="
                                            errors['schedule.end_date.date']
                                        "
                                        required
                                        outlined
                                        dense
                                        readonly
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="schedule.end_date.date"
                                    @input="schedule.end_date.menu = false"
                                ></v-date-picker>
                            </v-menu>
                        </c-col>
                    </v-row>
                </div>

                <div v-if="schedule.type === 'daily'">
                    <h4>{{ $t("schedule.daily") }}</h4>
                    <v-menu
                        ref="scheduleDailyMenu"
                        v-model="schedule.daily.menu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        :return-value.sync="schedule.daily.time"
                        transition="scale-transition"
                        offset-y
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="schedule.daily.time"
                                label="Daily Schedule Time"
                                readonly
                                required
                                outlined
                                dense
                                :error="
                                    errors['schedule.daily.time'] ? true : false
                                "
                                :error-messages="errors['schedule.daily.time']"
                                v-bind="attrs"
                                v-on="on"
                            ></v-text-field>
                        </template>
                        <v-time-picker
                            v-if="schedule.daily.menu"
                            v-model="schedule.daily.time"
                            full-width
                            @click:minute="
                                $refs.scheduleDailyMenu.save(
                                    schedule.daily.time
                                )
                            "
                        ></v-time-picker>
                    </v-menu>
                </div>
                <div v-if="schedule.type == 'weekly'">
                    <h4>{{ $t("schedule.weekly") }}</h4>
                    <v-select
                        v-model="schedule.weekly.day"
                        :items="weekDays"
                        label="Day"
                        item-text="name"
                        item-value="value"
                        required
                        outlined
                        dense
                        :error="errors['schedule.weekly.day'] ? true : false"
                        :error-messages="errors['schedule.weekly.day']"
                    ></v-select>
                </div>
                <div v-if="schedule.type == 'monthly'">
                    <h4>{{ $t("schedule.monthly") }}</h4>
                    <v-select
                        v-model="schedule.monthly.day_of_month"
                        :items="days"
                        label="Day"
                        :error="
                            schedule['schedule.monthly.date'] ? true : false
                        "
                        :error-messages="errors['schedule.monthly.date']"
                        required
                        outlined
                        dense
                    ></v-select>
                </div>
                <div v-if="schedule.type == 'yearly'">
                    <h4>{{ $t("schedule.yearly") }}</h4>
                    <v-row>
                        <c-col md="6">
                            <v-select
                                v-model="schedule.yearly.month_of_year"
                                :items="months"
                                label="Month"
                                :error="
                                    errors['schedule.yearly.date']
                                        ? true
                                        : false
                                "
                                :error-messages="errors['schedule.yearly.date']"
                                required
                                outlined
                                dense
                            ></v-select>
                        </c-col>
                        <c-col md="6">
                            <v-select
                                v-model="schedule.yearly.day_of_year"
                                :items="days"
                                :error="
                                    errors['schedule.yearly.date']
                                        ? true
                                        : false
                                "
                                :error-messages="errors['schedule.yearly.date']"
                                label="Day"
                                required
                                outlined
                                dense
                            ></v-select>
                        </c-col>
                    </v-row>
                </div>
                <div v-if="schedule.type == 'specific'">
                    <h4>{{ $t("schedule.specific") }}</h4>
                    <v-menu
                        v-model="schedule.specific.menu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="290px"
                    >
                        <template v-slot:activator="{ on }">
                            <v-text-field
                                v-model="schedule.specific.date"
                                label="Date"
                                required
                                outlined
                                dense
                                readonly
                                :error="
                                    errors['schedule.specific.date']
                                        ? true
                                        : false
                                "
                                :error-messages="
                                    errors['schedule.specific.date']
                                "
                                v-on="on"
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="schedule.specific.date"
                            @input="schedule.specific.menu = false"
                        ></v-date-picker>
                    </v-menu>
                </div>

                <hr />
                <CButton @click="submit" color="primary" class="px-4">
                    {{ $t("button.submit") }}
                    <v-progress-circular
                        v-if="loading"
                        indeterminate
                        color="primary"
                        :size="15"
                    ></v-progress-circular>
                </CButton>
            </form>
        </CCardBody>
    </CCard>
</template>

<script>
import { schedule, debts } from "../../constants";

export default {
    name: "CreateDebt",
    components: {},
    data() {
        return {
            item: "",
            type: "",
            amount: "",
            remark: "",
            color: {
                value: "#00BCD0FF",
                menu: false
            },
            dueDate: {
                specific: { menu: false, date: null },
                daily: {
                    menu: false,
                    time: null
                },
                weekly: { day: null },
                monthly: { day_of_month: "", date: "" },
                yearly: { day_of_year: "", month_of_year: "", date: "" }
            },
            schedule: {
                type: "",
                start_date: { menu: false, date: null },
                end_date: { menu: false, date: null },
                specific: { menu: false, date: null },
                daily: {
                    menu: false,
                    time: null
                },
                weekly: { day: null },
                monthly: { day_of_month: "", date: "" },
                yearly: { day_of_year: "", month_of_year: "", date: "" }
            },
            schedules: schedule.type,
            months: schedule.months,
            weekDays: schedule.weekDays,
            days: schedule.days,
            debts: debts,
            errors: {},
            loading: false
        };
    },
    watch: {
        "dueDate.monthly.day_of_month": function(val) {
            this.dueDate.monthly.date = `01-${this.dueDate.monthly.day_of_month}`;
        },
        "schedule.monthly.day_of_month": function(val) {
            this.schedule.monthly.date = `01-${this.schedule.monthly.day_of_month}`;
        },
        "dueDate.yearly.month_of_year": function(val) {
            this.dueDate.yearly.date = `${this.dueDate.yearly.month_of_year}-${this.dueDate.yearly.day_of_year}`;
        },
        "dueDate.yearly.day_of_year": function(val) {
            this.dueDate.yearly.date = `${this.dueDate.yearly.month_of_year}-${this.dueDate.yearly.day_of_year}`;
        },
        "schedule.yearly.month_of_year": function(val) {
            this.schedule.yearly.date = `${this.schedule.yearly.month_of_year}-${this.schedule.yearly.day_of_year}`;
        },
        "schedule.yearly.day_of_year": function(val) {
            this.schedule.yearly.date = `${this.schedule.yearly.month_of_year}-${this.schedule.yearly.day_of_year}`;
        }
    },
    methods: {
        submit() {
            let self = this;
            if (self.loading) {
                return;
            }
            self.loading = true;
            let data = {
                type: self.type,
                item: self.item,
                amount: self.amount,
                remark: self.remark,
                due_date: self.dueDate,
                schedule: self.schedule,
                color: self.color.value
            };
            this.$store
                .dispatch("debt/create", data)
                .then(() => {
                    self.loading = false;
                    self.errors = {};
                    self.$router.back();
                })
                .catch(error => {
                    self.loading = false;
                    self.errors = error.response.data?.data;
                });
        },
        switchColorStyle() {
            const { color } = this;
            return {
                backgroundColor: color.value,
                cursor: "pointer",
                height: "30px",
                width: "30px",
                borderRadius: color.menu ? "50%" : "4px",
                transition: "border-radius 200ms ease-in-out"
            };
        }
    }
};
</script>

<style scoped>
.v-text-field >>> input {
    font-size: 0.8em;
    font-weight: 100;
}
.v-text-field >>> label {
    font-size: 0.8em;
}
.v-text-field >>> button {
    font-size: 0.8em;
}
</style>
