<script>
    import FullCalendar from '@fullcalendar/vue'
    import {Calendar} from '@fullcalendar/core';
    import dayGridPlugin from '@fullcalendar/daygrid'
    import timeGridPlugin from '@fullcalendar/timegrid';
    import momentTimezonePlugin from '@fullcalendar/moment-timezone';
    import { mapGetters } from "vuex";

    export default {
        components: {
            FullCalendar // make the <FullCalendar> tag available
        },
        methods:    {
            handleDateClick(info) {
                info.jsEvent.preventDefault();

                if (info.event.url) {
                    window.open(info.event.url);
                }
            }
        },
        computed: {
            ...mapGetters(["getEventCalendarList"])
        },
        created() {
            this.$store.dispatch('GET_CALENDAR_LIST');
        },
        data() {
            return {
                calendarPlugins: [dayGridPlugin, timeGridPlugin, momentTimezonePlugin],
                currentDate: moment()
            }
        }
    }

</script>

<template>
    <FullCalendar
            ref="fullCalendar"
            :plugins="calendarPlugins"
            @eventClick="handleDateClick"
            :locale="'ja'"
            defaultView="timeGridWeek"
            :header="{
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            }"
            :weekNumbers="true"
            :navLinks="true"
            :editable="true"
            :eventLimit="true"
            :allDaySlot="false"
            :events="getEventCalendarList"
            :nowIndicator="true"
            :timeZone="'Asia/Tokyo'"
    />
</template>

<style lang='scss'>

    @import '~@fullcalendar/core/main.css';
    @import '~@fullcalendar/daygrid/main.css';
    @import '~@fullcalendar/timegrid/main.css';

</style>
