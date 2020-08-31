<template>
    <div class="event-form">
        <div class="row">
            <div class="col-12 mt-3">
                <EventDetails :eventId="details.schedule_info.event_id" :details="details"/>
            </div>
        </div>
    </div>
</template>

<script>
    import EventDetails from './EventDetails';

    export default {
        name: "EditForm",
        props: {
            details: {
                type:     Object,
                required: true
            }
        },
        components: {
            EventDetails
        }
    }
</script>
