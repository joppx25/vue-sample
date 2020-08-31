<template>
    <tbody>
        <tr v-for="(schedule, index) in schedules" :key="index">
            <td>
                <span class="field-value" v-if="!schedule.isEditMode || schedule.hasOwnProperty('id')">{{ schedule.date }}</span>
                <DatePicker v-else v-model="schedule.date" @input="schedule.date = customFormatter($event)" :language="ja" :use-utc=true :format="customFormatter" :class="{'is-invalid': errorMessage.date}" :input-class="'form-control w-auto d-inline datepicker'" :disabledDates="disabledDates" :required="true"></DatePicker>
                <div class="invalid-feedback" v-if="errorMessage.date">
                    {{ errorMessage.date }}
                </div>
            </td>
            <td>
                <span class="field-value" v-if="!schedule.isEditMode || schedule.hasOwnProperty('id')">{{ `${schedule.start_hour}:${schedule.start_min}` }}</span>
                <div class="form-row align-items-center" v-else>
                    <select name="start_hour" id="start_hour" class="form-control col-5" :class="{'is-invalid': errorMessage.start_time}" v-model="schedule.start_hour">
                        <option value="00" selected>00</option>
                        <option v-for="hour in 23" :value="hour < 10 ? `0${hour}` : hour" :key="hour">{{ hour < 10 ? `0${hour}` : hour }}</option>
                    </select>
                    <label class="col-2 text-center">~</label>
                    <select name="start_min" id="start_min" class="form-control col-5" :class="{'is-invalid': errorMessage.start_time}" v-model="schedule.start_min">
                        <option value="00" selected>00</option>
                        <option v-for="min in 59" :value="min < 10 ? `0${min}` : min" :key="min">{{ min < 10 ? `0${min}` : min }}</option>
                    </select>
                    <div class="invalid-feedback text-center" v-if="errorMessage.start_time">
                        {{ errorMessage.start_time }}
                    </div>
                </div>
            </td>
            <td>
                <span class="field-value" v-if="!schedule.isEditMode || schedule.hasOwnProperty('id')">{{ `${schedule.end_hour}:${schedule.end_min}` }}</span>
                <div class="form-row align-items-center" v-else>
                    <select name="end_hour" id="end_hour" class="form-control col-5" v-model="schedule.end_hour">
                        <option value="00" selected>00</option>
                        <option v-for="hour in 23" :value="hour < 10 ? `0${hour}` : hour" :key="hour">{{ hour < 10 ? `0${hour}` : hour }}</option>
                    </select>
                    <label class="col-2 text-center">~</label>
                    <select name="end_min" id="end_min" class="form-control col-5" v-model="schedule.end_min">
                        <option value="00" selected>00</option>
                        <option v-for="min in 59" :value="min < 10 ? `0${min}` : min" :key="min">{{ min < 10 ? `0${min}` : min }}</option>
                    </select>
                </div>
            </td>
            <td>
                <span class="field-value" v-if="!schedule.isEditMode">{{ schedule.capacity }}</span>
                <input v-model="schedule.capacity" v-else type="number" class="field-value form-control" :class="{'is-invalid': errorMessage.capacity}" oninput="validity.valid||(value='');" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length == 5) return false;">
                <div class="invalid-feedback" v-if="errorMessage.capacity">
                    {{  errorMessage.capacity }}
                </div>
            </td>
            <td>
                <span class="field-value">{{ schedule.count_booking }}</span>
            </td>
            <td class="text-center">
                <a href="javascript:void(0)" class="fa fa-pencil mr-3" v-if="!schedule.isEditMode && schedule.date >= currentDate" @click="schedule.isEditMode = true"></a>
                <a href="javascript:void(0)" class="fa fa-check mr-3" v-else-if="schedule.isEditMode" @click="saveSchedule(schedule)"></a>
                <a href="javascript:void(0)" class="fa fa-trash" v-if="!schedule.count_booking"  @click="removeSchedule(schedule.id, index)"></a>
            </td>
        </tr>
    </tbody>
</template>

<style scoped>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<script>
    import DatePicker from 'vuejs-datepicker';
    import { ja } from 'vuejs-datepicker/dist/locale';
    import ErrorField from '../../Helper/ErrorField';

    export default {
        props: {
            schedules: {
                type: Array,
                required: true,
            },
        },
        data() {
            return {
                currentDate: moment().format('YYYY-MM-DD'),
                disabledDates: {
                    to: new Date(mtz.tz('Asia/Tokyo').format('YYYY-MM-DD'))
                },
                ja,
                errorMessage: {
                    date: null,
                    start_time: null,
                    capacity: null,
                },
            }
        },
        methods: {
            customFormatter(date) {
                return mtz(date).tz('Asia/Tokyo').format('YYYY-MM-DD');
            },

            saveSchedule(schedule) {
                let scheduleDateTime            = `${schedule.date} ${schedule.start_hour}:${schedule.start_min}`;
                schedule.start_time             = `${schedule.start_hour}:${schedule.start_min}`;
                schedule.end_time               = `${schedule.end_hour}:${schedule.end_min}`;
                this.errorMessage.date          = null;
                this.errorMessage.start_time    = null;
                this.errorMessage.capacity      = null;

                if (!schedule.hasOwnProperty('id') && !schedule.date) {
                    this.errorMessage.date = this.$t('common.validation.date', null, 'jp');
                    return;
                } else if (!schedule.hasOwnProperty('id') && schedule.date < this.currentDate) {
                    this.errorMessage.date = this.$t('common.validation.past_date_not_allowed', null, 'jp');
                    return;
                }

                if (!schedule.hasOwnProperty('id') && schedule.start_time > schedule.end_time) {
                    this.errorMessage.start_time = this.$t('common.validation.incorrect_format_or_invalid', null, 'jp');
                    return;
                } else if (!schedule.hasOwnProperty('id') && scheduleDateTime < mtz.tz('Asia/Tokyo').format('YYYY-MM-DD HH:mm')) {
                    this.errorMessage.start_time = this.$t('common.validation.start_time_less_than_current_time', null, 'jp');
                    return;
                }

                if (!schedule.capacity) {
                    this.errorMessage.capacity = this.$t('common.validation.capacity', null, 'jp');
                    return;
                } else if (schedule.capacity < schedule.count_booking) {
                    this.errorMessage.capacity = this.$t('common.validation.less_than_count_booking', null, 'jp');
                    return
                }

                this.$emit('updateSchedule', schedule);
                schedule.isEditMode = false;
            },

            removeSchedule(id, index) {
                this.$swal({
                    title: this.$t('common.delete_this_schedule_question', null, 'jp'),
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: this.$t('common.yes_delete_it', null, 'jp'),
                    cancelButtonText: this.$t('common.no_keep_it', null, 'jp'),
                    showCloseButton: true,
                }).then((result) => {
                    if(result.value) {
                        if (id) {
                            axios.post('/admin/schedules/remove', { id }).then(response => {
                                if (response.data) {
                                    toastr.success(this.$t('common.deleted', null, 'jp'));
                                }
                            }).catch(error => {
                                toastr.error(error);
                            });
                        }
                        this.$emit('deleteSchedule', index);
                    }

                    this.errorMessage.date          = null;
                    this.errorMessage.capacity      = null;
                    this.errorMessage.start_time    = null;
                })
            },
        },
        components: {
            DatePicker,
            ErrorField,
        },
    }
</script>
