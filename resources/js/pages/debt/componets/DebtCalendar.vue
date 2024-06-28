<template>
    <CCard>
        <v-progress-linear
            :active="loading"
            indeterminate
            color="cyan"
        ></v-progress-linear>
        <CCardBody>
            <h4>{{ $t("debt") }}{{ $t("calendar") }}</h4>
            <h4 v-if="$refs.calendar">
                {{ $refs.calendar.title }}
            </h4>
            <v-sheet tile height="54" class="d-flex">
                <v-btn icon class="ma-2" @click="$refs.calendar.prev()">
                    <v-icon>mdi-chevron-left</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn icon class="ma-2" @click="$refs.calendar.next()">
                    <v-icon>mdi-chevron-right</v-icon>
                </v-btn>
            </v-sheet>
            <CRow class="p-2">
                <v-calendar
                    ref="calendar"
                    color="primary"
                    v-model="value"
                    :type="type"
                    :events="events"
                    :weekdays="weekday"
                    :event-overlap-mode="mode"
                    :event-color="getEventColor"
                    :event-overlap-threshold="30"
                    @change="getEvents"
                >
                </v-calendar>
            </CRow>
        </CCardBody>
    </CCard>
</template>

<script>
export default {
    name: "DebtCalendar",
    data() {
        return {
            types: ["month", "week", "day"],
            weekday: [0, 1, 2, 3, 4, 5, 6],
            weekdays: [
                { text: "Sun - Sat", value: [0, 1, 2, 3, 4, 5, 6] },
                { text: "Mon - Sun", value: [1, 2, 3, 4, 5, 6, 0] },
                { text: "Mon - Fri", value: [1, 2, 3, 4, 5] },
                { text: "Mon, Wed, Fri", value: [1, 3, 5] }
            ],
            mode: "stack",
            type: "month",
            value: "",
            events: [{ start: "1900-01-01", name: "" }],
            colors: [
                "blue",
                "indigo",
                "deep-purple",
                "cyan",
                "green",
                "orange",
                "grey darken-1"
            ],
            loading: false
        };
    },
    mounted() {
        // this.getEvents();
    },
    methods: {
        getEvents({ start, end }) {
            // console.log(start);
            // console.log(end);
            let self = this;
            self.loading = true;
            let data = {
                from_date: start.date,
                to_date: end.date
            };
            this.$store
                .dispatch("debt/events", data)
                .then(response => {
                    self.loading = false;
                    self.events = response.data.data;
                    self.$refs.calendar.checkChange();
                })
                .catch(error => {
                    self.loading = false;
                });
        },
        getEventColor(event) {
            return event.color;
        }
    }
};
</script>
