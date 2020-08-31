<template>
    <div>
        <button type="button" class="btn btn-primary float-right mb-2" @click="addScheduleTime">{{ $_('labels.add_schedule', null, 'jp') }}</button>
        <table class="table table-bordered" :class="{'is-invalid': error}">
            <thead>
                <th>{{ $_('labels.date', null, 'jp') }}</th>
                <th>{{ $_('labels.start_time', null, 'jp') }}</th>
                <th>{{ $_('labels.end_time', null, 'jp') }}</th>
                <th>{{ $_('labels.capacity', null, 'jp') }}</th>
                <th>{{ $_('labels.num_of_guests_booked', null, 'jp') }}</th>
                <th></th>
            </thead>
            <ScheduleItem :schedules="schedules" @updateSchedule="updateScheduleTimeTable" @deleteSchedule="removeItemSchedule"/>
        </table>
        <div class="error-message" v-if="error">
            {{ error }}
        </div>
    </div>
</template>

<style scoped>
    .table {
        margin-bottom: 0.7rem;
    }

    thead > th:nth-of-type(1), th:nth-of-type(2), th:nth-of-type(3) {
        width: 20%;
    }

    thead > th:nth-of-type(4) {
        width: 18%;
    }

    thead > th:nth-of-type(5) {
        width: 11%;
    }

    .error-message {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
        margin-bottom: 1rem;
    }
</style>

<script>
    import ScheduleItem from './ScheduleItem';

    export default {
        props: {
            schedules: {
                type: Array,
                required: true,
            },
            error: {
                type: String,
                required: true,
            }
        },
        data() {
            return {
                schedule: this.schedules
            }
        },
        methods: {
            addScheduleTime() {
                let startHour = mtz().tz('Asia/Tokyo').format('HH');
                let endHour   = mtz().tz('Asia/Tokyo').format('HH');
                let startMin  = mtz().tz('Asia/Tokyo').format('mm');
                let endMin    = mtz().tz('Asia/Tokyo').format('mm');
                let date      = mtz.tz('Asia/Tokyo').format('YYYY-MM-DD');

                if (this.schedules.length) {
                    let prevSched         = this.schedules[this.schedules.length - 1];
                    let prevStartDateTime = mtz(`${prevSched.date} ${prevSched.start_hour}:${prevSched.start_min}`);
                    let prevEndDateTime   = mtz(`${prevSched.date} ${prevSched.end_hour}:${prevSched.end_min}`);
                    let prevTimeDiff      = mtz.duration(prevStartDateTime.diff(prevEndDateTime)).asMinutes();

                    startHour  = ('0' + prevEndDateTime.hours()).slice(-2);
                    startMin   = ('0' + prevEndDateTime.minutes()).slice(-2);
                    console.log(`is previous start hour: ${prevSched.start_hour} less than current hour: ${startHour} = ${prevSched.start_hour < startHour}`);
                    let endDateTime = mtz(`${prevSched.date} ${startHour}:${startMin}`).add(Math.abs(prevTimeDiff), 'minutes');
                    endHour         = ('0' + endDateTime.hours()).slice(-2);
                    endMin          = ('0' + endDateTime.minutes()).slice(-2);
                    date            = prevSched.end_hour > endHour? date : endDateTime.format('YYYY-MM-DD');
                }

                this.schedules.push({
                    date            : date,
                    start_hour      : startHour,
                    end_hour        : endHour,
                    start_min       : startMin,
                    end_min         : endMin,
                    start_time      : '00:00',
                    end_time        : '00:00',
                    capacity        : 1,
                    originalCapacity: 1,
                    isEditMode      : true, // set to true for edit mode first
                    count_booking   : 0,
                })
            },

            updateScheduleTimeTable(value) {
                this.schedules.concat(value);
            },

            removeItemSchedule(index) {
                this.schedules.splice(index, 1);
            }
        },
        components: {
            ScheduleItem,
        }
    }
</script>
