<template>
    <div class="details">
        <div class="row">
            <div class="col-12">
                <div class="title">
                    <h3 class="title-default title-default-underline">{{ $_('labels.booking_details', null, 'jp') }}</h3>
                </div>
                <div class="content">
                    <div class="content-header">{{ $_('labels.event_information', null, 'jp') }}</div>
                    <div class="content-body">
                        <div class="item row">
                            <div class="col-md-4 title">{{ $_('labels.title', null, 'jp') }}</div>
                            <div class="col-md-8 description text-break">{{ details.schedule_info.event_info.title }}</div>
                        </div>
                        <div class="item row">
                            <div class="col-md-4 title">{{ $_('labels.place', null, 'jp') }}</div>
                            <div class="col-md-8 description text-break pre-line" v-html="details.schedule_info.event_info.place"></div>
                        </div>
                        <div class="item row">
                            <div class="col-md-4 title">{{ $_('labels.direction', null, 'jp') }}</div>
                            <div class="col-md-8 description text-break pre-line" v-html="details.schedule_info.event_info.access_direction"></div>
                        </div>
                        <div class="item row">
                            <div class="col-md-4 title">{{ $_('labels.date_time', null, 'jp') }}</div>
                            <div class="col-md-8 description">{{ `${details.schedule_info.date} ${details.schedule_info.start_time} ~ ${details.schedule_info.end_time}` }}</div>
                        </div>
                    </div>
                </div>
                <div class="content">
                    <div class="content-header">{{ $_('labels.contact_information', null, 'jp') }}</div>
                    <div class="content-body">
                        <div class="item row">
                            <div class="col-md-4 title">{{ $_('labels.full_name', null, 'jp') }}</div>
                            <div class="col-md-8 description text-break">{{ details.full_name }}</div>
                        </div>
                        <div class="item row">
                            <div class="col-md-4 title">{{ $_('labels.email', null, 'jp') }}</div>
                            <div class="col-md-8 description text-break">{{ details.email }}</div>
                        </div>
                        <div class="item row">
                            <div class="col-md-4 title">{{ $_('labels.tel', null, 'jp') }}</div>
                            <div class="col-md-8 description">{{ details.tel }}</div>
                        </div>
                        <div class="item row">
                            <div class="col-md-4 title">{{ $_('labels.introducer', null, 'jp') }}</div>
                            <div class="col-md-8 description">{{ details.introducer }}</div>
                        </div>
                    </div>
                </div>
                <div class="btn-wrapper" v-if="getBookingModifiable">
                    <a :href="'/booking/edit/' + details.booking_id" :class="`mr-2 pb-btn pb-btn-submit icon-caret ${submitted ? 'isDisabled' : '' }`">{{ $_('labels.update', null, 'jp') }}</a>
                    <button @click="cancelBooking" class="ml-2 pb-btn pb-btn-primary icon-caret" :disabled="submitted">{{ btnCancel }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        name: "BookingDetails",
        props: {
            details: {
                type:     Object,
                required: true
            }
        },
        data() {
            return {
                submitted: false,
                btnCancel: this.$_('labels.cancel', null, 'jp'),
            }
        },
        created() {
            this.$store.commit('BOOKING_MODIFIABLE', this.details.is_modified.status);
        },
        methods: {
            cancelBooking() {
                this.$swal({
                    title: this.$_('labels.cancel_booking', null, 'jp'),
                    text: this.$_('labels.revert_action', null, 'jp'),
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: this.$_('labels.cancel_it', null, 'jp'),
                    cancelButtonText: this.$_('labels.keep_it', null, 'jp'),
                    showCloseButton: true,
                    showLoaderOnConfirm: true
                }).then((result) => {
                    if(result.value) {
                        this.submitted = true;
                        this.btnCancel = this.$_('labels.please_wait', null, 'jp');
                        this.$store.dispatch('CANCEL_BOOKING', { booking_id: this.details.booking_id, event_id: this.details.schedule_info.event_info.id, message: this.$_('labels.successfully_cancelled', null, 'jp') });
                    }
                })
            }
        },
        computed: {
            ...mapGetters(['getBookingModifiable'])
        },
    }
</script>
