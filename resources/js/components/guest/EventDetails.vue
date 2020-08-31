<template>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="title">
                    {{ getEventDetails.title + ' ' + getEventDetails.mobile_date }}
                </div>
                <div class="col-12 p-0 text-center" v-if="getEventDetails.filename != null">
                    <img :src="`/storage/images/event/${getEventDetails.filename}`" class="img-fluid" alt="Banner">
                </div>
                <div class="description-container">
                    <div class="description-title">開催日</div>
                    <div class="description-content" v-html="getEventDetails.mobile_ymd"></div>
                </div>
                <div class="description-container">
                    <div class="description-title">住所</div>
                    <div class="description-content" v-html="getEventDetails.place"></div>
                </div>
                <div class="description-container">
                    <div class="description-title">アクセス</div>
                    <div class="description-content" v-html="getEventDetails.access_direction"></div>
                </div>
                <div class="alert alert-danger error-validation" role="alert" v-if="errors">{{ errors }}</div>
                <form @submit="checkForm" action="/contact" method="post" class="schedules">
                    <input type="hidden" name="_token" :value="csrf">
                    <input type="hidden" name="event_id" :value="getEventDetails.id">
                    <input type="hidden" value="" name="schedule_id" v-model="scheduleId">
                    <div class="item col-md-4 col-6 mx-auto" v-if="isReservationClose">
                        <div class="card">
                            <div class="card-body text-center text-danger">
                                ( {{ $_('labels.reservation_closed', null, 'jp') }} )
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div v-if="Object.keys(availableSchedule).length" class="mobile-date">希望の時間帯をお選びください</div>
                        <div class="row ml-0 mr-0">
                            <div :class="`item col-lg-4 col-md-6 col-6 ${isFetching ? 'd-none' : ''}`" v-for="(schedule, index) in availableSchedule" v-bind:key="index">
                                <a href="javascript:void(0);" v-on:click="schedule.capacity !== schedule.count_booking && selectSchedule(schedule.id)" :class="[{disabled: submitted || schedule.capacity === schedule.count_booking}, {selected: scheduleId == schedule.id }]">
                                    <div class="card" :class="{'is-fully-booked': schedule.capacity === schedule.count_booking}">
                                        <div class="card-body">
                                            <div class="full-indicator" v-if="schedule.capacity === schedule.count_booking">{{ $_('common.full', null, 'jp') }}</div>
                                            <div class="time">{{ `${schedule.start_time} ~ ${schedule.end_time}` }}</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div v-if="!Object.keys(availableSchedule).length && !isFetching" class="item col-md-4 col-6 mx-auto">
                            <div class="card">
                                <div class="card-body text-center text-danger">
                                    ( {{ $_('labels.booking_full', null, 'jp') }} )
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-wrapper">
                        <a v-if="bookingDetails" :class="`pb-btn pb-btn-submit ${scheduleId == null || submitted ? 'isDisabled' : '' }`" v-on:click="updateBooking">{{ btnUpdate }}</a>
                        <button v-else class="pb-btn pb-btn-submit icon-caret" type="submit" :disabled="scheduleId == null || submitted || isReservationClose">{{ $_('labels.next', null, 'jp') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import _ from 'lodash';

    export default {
        name: "EventDetails",
        props: {
            eventId: {
                type:     Number,
                required: true
            },
            details: {
                type:     Object
            }
        },
        data() {
            return {
                csrf: csrf,
                bookingDetails: this.details ? this.details : null,
                scheduleId: this.details ? this.details.schedule_id : null,
                oldScheduleId: this.details ? this.details.schedule_id : null,
                submitted: false,
                isFetching: false,
                errors: null,
                btnUpdate: this.$_('labels.update', null, 'jp'),
            }
        },
        created() {
            this.$store.subscribe((mutation, state) => {
                if (mutation.type === 'EVENT_DETAILS') {
                    this.isFetching = false;
                }
            });
        },
        methods: {
            fetchEventDetails() {
                this.isFetching = true;
                this.$store.dispatch('GET_EVENT_DETAILS', { id: this.eventId });
            },
            checkForm: function (e) {
                if (this.scheduleId) {
                    this.errors = null;
                    this.submitted = true;

                    return true;
                }

                this.errors = this.$_('labels.schedule_required', null, 'jp');
                this.submitted = false;

                e.preventDefault();
            },
            updateBooking() {
                this.submitted = true;
                this.btnUpdate = this.$_('labels.please_wait', null, 'jp');

                axios.post('/update-booking', {
                    id: this.details.id,
                    schedule_id: this.scheduleId,
                    old_schedule_id: this.details.schedule_id,
                    booking_id: this.details.booking_id,
                })
                .then((res) => {
                    if(res.data.message) {
                        if(res.data.flag == 'full') {
                            this.fetchEventDetails();
                            this.scheduleId = this.details.schedule_id;
                        }
                        if(res.data.flag == 'past') {
                            setTimeout(() => {
                                location.href = '/details/' + this.bookingDetails.booking_id;
                            }, 1000);
                        }
                        if(res.data.flag == 'booked') {
                            this.btnUpdate = this.$_('labels.update', null, 'jp');
                            this.submitted = false;
                        }

                        toastr.error(res.data.message);
                    } else {
                        this.btnUpdate = this.$_('labels.updated', null, 'jp');
                        toastr.success(this.$_('labels.successfully_update', null, 'jp'));

                        setTimeout(() => {
                            location.href = '/details/' + this.bookingDetails.booking_id;
                        }, 1000);
                    }
                })
                .catch(error => {
                    let statusCode = error.response.status;
                    if (statusCode === 422) {
                        this.errors = error.response.data.errors;
                    }
                    this.btnUpdate = this.$_('labels.update', null, 'jp');
                    this.submitted = false;
                });
            },
            selectSchedule(scheduleId) {
                if(!this.submitted) {
                    this.scheduleId = scheduleId;
                }
            }
        },
        mounted() {
            this.fetchEventDetails();
        },
        watch: {
            isFetching: function(val) {
                if(this.isFetching && this.bookingDetails) {
                    this.btnUpdate = this.$_('labels.update', null, 'jp');
                    this.scheduleId = this.details.schedule_id;
                    this.submitted = false;
                }
            }
        },
        computed: {
            ...mapGetters(['getEventDetails']),
            availableSchedule: function() {
                let oldScheduleId = this.oldScheduleId;
                let availableSchedule = _.pickBy(this.getEventDetails.schedules, function (schedule) {
                    return oldScheduleId == schedule.id || Object.values(schedule).length;
                });
                // Set the first sched that is not yet fully booked as the default selected sched
                for (const [key, sched] of Object.entries(availableSchedule)) {
                    if (sched.count_booking < sched.capacity) {
                        this.scheduleId = sched.id;
                        break;
                    }
                }

                return availableSchedule;
            },
            isReservationClose: function() {
                let firstSchedule  = this.getEventDetails.schedules? this.getEventDetails.schedules[0] : null;
                let dueDate        = moment().add(2,'d').format('YYYY-MM-DD');
                let firstSchedDate = null;
                let firstBookCount = 0;

                if (firstSchedule) {
                    firstSchedDate = moment(firstSchedule.date.match(/[a-zA-Z0-9]/g).join("")).format('YYYY-MM-DD');
                    firstBookCount = firstSchedule.count_booking;
                }

                return firstSchedDate != null && !firstBookCount && moment(dueDate).isAfter(firstSchedDate);;
            }
        },
    }
</script>
