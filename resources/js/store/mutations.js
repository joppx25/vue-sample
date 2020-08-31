let mutations = {
    SET_EVENT_CALENDAR_LIST(state, eventCalendarList) {
        state.eventCalendarList = eventCalendarList;
    },

    SET_EVENT_LIST(state, eventList) {
        state.eventList = eventList;
    },

    REMOVE_EVENT(state, index) {
        let events = state.eventList.data;
        events.splice(index, 1)
    },

    SET_EVENT_LIST_LOADING(state, status) {
        state.eventListLoading = status;
    },

    EVENT_DETAILS(state, details) {
        state.eventDetails = details;
    },

    SET_BOOKING_LIST(state, bookingList) {
        state.bookingList = bookingList;
    },

    SET_BOOKING_IS_LOADING(state, status) {
        state.bookingListLoading = status;
    },

    BOOKING_MODIFIABLE(state, status) {
        state.bookingModifiable = status;
    }

}

export default mutations;
