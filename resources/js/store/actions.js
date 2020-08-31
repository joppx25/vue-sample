let actions = {
    GET_CALENDAR_LIST({commit}, params) {
        axios.get(`/admin/event/calendarList`).then ( (response) => {
            commit('SET_EVENT_CALENDAR_LIST', response.data);
        });
    },
    GET_EVENT_LIST({commit}, params) {
        commit('SET_EVENT_LIST_LOADING', true);
        axios.get('/admin/events/list', { params }).then(response => {
            commit('SET_EVENT_LIST', response.data);
        }).finally(() => {
            commit('SET_EVENT_LIST_LOADING', false);
        });
    },

    REMOVE_EVENT({commit}, params) {
        commit('SET_EVENT_LIST_LOADING', true);
        axios.post('/admin/events/remove', { id: params.event_id }).then(response => {
            if (response.data && response.status === 200) {
                commit('REMOVE_EVENT', params.index)
            }
        }).finally('SET_EVENT_LIST_LOADING', false);
    },

    CANCEL_BOOKING({commit}, params) {
        axios.post('/cancel', { booking_id: params.booking_id }).then((response) => {
            if (response.status === 200) {
                if(response.data.status) {
                    toastr.success(params.message);
                    setTimeout(() => {
                        location.href = `/events/${params.event_id}`;
                    }, 1000);
                } else {
                    commit('BOOKING_MODIFIABLE', response.data.status);
                    toastr.error(response.data.message);
                }
            }
        });
    },

    GET_EVENT_DETAILS({commit}, params) {
        axios.get(`/event-details`, { params }).then((response) => {
            commit('EVENT_DETAILS', response.data)
        })
        .catch(err => console.log(err));
    },

    GET_BOOKING_LIST({commit}, params) {
        commit('SET_BOOKING_IS_LOADING', true);
        axios.get(`/admin/booking/list`, {
            params
        }).then((response) => {
            commit('SET_BOOKING_LIST', response.data);
        }).finally(() => {
            commit('SET_BOOKING_IS_LOADING', false);
        });
    },

}

export default actions;
