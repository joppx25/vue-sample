let getters = {
    getEventList: state => {
        return state.eventList;
    },
    getEventListLoading: state => {
        return state.eventListLoading;
    },
    getEventDetails: state => {
        return state.eventDetails;
    },
    getEventCalendarList: state => {
        return state.eventCalendarList;
    },
    getBookingList: state => {
        return state.bookingList;
    },
    getBookingLisLoading: state => {
        return state.bookingListLoading;
    },
    getBookingModifiable: state => {
        return state.bookingModifiable;
    }
};

export default getters;
