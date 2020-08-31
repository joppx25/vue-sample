<template>
    <div class="event-form">
        <div class="row">
            <div class="col-12 mt-3">
                <EventDetails :eventId="eventId"/>
            </div>
        </div>
    </div>
</template>

<script>
    import EventDetails from './EventDetails';

    export default {
        name: "EventForm",
        props: {
            eventId: {
                type:     Number,
                required: true
            }
        },
        components: {
            EventDetails
        }
    }
</script>
