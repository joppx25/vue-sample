<template>
    <div class="details">
        <div class="row">
            <div class="col-12">
                <div class="title">
                    <h3 class="title-default title-default-underline">{{ $_('labels.thanks_booking', null, 'jp') }}</h3>
                    <div class="sub-header">
                        {{ $_('labels.booking_is', null, 'jp') }}
                        <a :href="'/details/' + details.booking_id">{{ details.booking_id }}</a>
                        {{ $_('labels.is', null, 'jp') }}
                    </div>
                </div>
                <div class="content">
                    <div class="content-header">{{ $_('labels.event_information', null, 'jp') }}</div>
                    <div class="content-body">
                        <div class="item row">
                            <div class="col-lg-4 col-md-3 title">{{ $_('labels.title', null, 'jp') }}</div>
                            <div class="col-lg-8 col-md-9 description text-break">{{ details.schedule_info.event_info.title }}</div>
                        </div>
                        <div class="item row">
                            <div class="col-lg-4 col-md-3 title">{{ $_('labels.place', null, 'jp') }}</div>
                            <div class="col-lg-8 col-md-9 description text-break pre-line" v-html="details.schedule_info.event_info.place"></div>
                        </div>
                        <div class="item row">
                            <div class="col-lg-4 col-md-3 title">{{ $_('labels.direction', null, 'jp') }}</div>
                            <div class="col-lg-8 col-md-9 description text-break pre-line" v-html="details.schedule_info.event_info.access_direction"></div>
                        </div>
                        <div class="item row">
                            <div class="col-lg-4 col-md-3 title">{{ $_('labels.date_time', null, 'jp') }}</div>
                            <div class="col-lg-8 col-md-9 description">{{ `${details.schedule_info.date} ${details.schedule_info.start_time} ~ ${details.schedule_info.end_time}` }}</div>
                        </div>
                    </div>
                </div>
                <div class="content">
                    <div class="content-header">{{ $_('labels.contact_information', null, 'jp') }}</div>
                    <div class="content-body">
                        <div class="item row">
                            <div class="col-lg-4 col-md-3 title">{{ $_('labels.full_name', null, 'jp') }}</div>
                            <div class="col-lg-8 col-md-9 description text-break">{{ details.full_name }}</div>
                        </div>
                        <div class="item row">
                            <div class="col-lg-4 col-md-3 title">{{ $_('labels.email', null, 'jp') }}</div>
                            <div class="col-lg-8 col-md-9 description text-break">{{ details.email }}</div>
                        </div>
                        <div class="item row">
                            <div class="col-lg-4 col-md-3 title">{{ $_('labels.tel', null, 'jp') }}</div>
                            <div class="col-lg-8 col-md-9 description">{{ details.tel }}</div>
                        </div>
                        <div class="item row">
                            <div class="col-lg-4 col-md-3 title">{{ $_('labels.introducer', null, 'jp') }}</div>
                            <div class="col-lg-8 col-md-9 description">{{ details.introducer }}</div>
                        </div>
                    </div>
                </div>
                <div class="btn-wrapper">
                    <a :href="'/events/' + details.schedule_info.event_info.id" class="pb-btn pb-btn-primary">{{ $_('labels.return_main_page', null, 'jp') }}</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name: "ThankYou",
        props: {
            details: {
                type:     Object,
                required: true
            }
        },
    }
</script>
