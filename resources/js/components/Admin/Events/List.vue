<template>
    <div class="content-wrapper">
        <div class="row">
            <div class="header">
                <h1>{{ $_('labels.event_list', null, 'jp') }}</h1>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12 p-0">
                <input type="text" :placeholder="$_('labels.search_by_event_name', null, 'jp')" class="form-control d-inline" v-model="searchData.eventName" @keyup="searchEvent"/>
            </div>
        </div>
        <div class="row">
            <div class="col-6 p-0" v-if="!getEventListLoading" :class="{'mb-3': getEventList.total <= 10 }">
                <a href="/admin/events/form" class="btn btn-info float-left">{{ $_('labels.create_event', null, 'jp') }}</a>
            </div>
            <div class="col-6 pagination-nav p-0" v-if="!getEventListLoading && getEventList.total > 0">
                <Pagination :pagination="getEventList" @paginate="getEventListUpdate" :offset="10" :custom-class="'float-right'"/>
            </div>
            <table class="table table-bordered">
                <thead>
                    <th>{{ $_('labels.title', null, 'jp') }}</th>
                    <th>{{ $_('labels.place', null, 'jp') }}</th>
                    <th>{{ $_('labels.url', null, 'jp') }}</th>
                    <th>{{ $_('labels.event_capacity', null, 'jp') }}</th>
                    <th></th>
                </thead>
                <tbody>
                    <tr v-if="getEventListLoading">
                        <td colspan="5" class="text-center">
                            <stretch></stretch>
                        </td>
                    </tr>
                    <tr v-else-if="getEventList.total === 0">
                        <td colspan="4" class="text-center"><h5>{{ $_('common.no_records', null, 'jp') }}</h5></td>
                    </tr>
                    <tr v-for="(event, index) in getEventList.data" :key="index" v-else>
                        <td>{{ event.title }}</td>
                        <td>{{ event.place }}</td>
                        <td><a :href="`${baseURL}/events/${event.id}`">{{ `${baseURL}/events/${event.id}` }}</a></td>
                        <td>{{ `${event.count_booking}/${event.capacity}` }}</td>
                        <td style="text-align: center">
                            <a :href="`/admin/events/edit/${event.id}`" style="margin-right: 12px;"><i class="fa fa-pencil"></i></a>
                            <a :href="`/admin/events/form/${event.id}`" :style="isRemoveAllowed(event.schedules)? 'margin-right: 12px;' : ''"><i class="fa fa-clone"></i></a>
                            <a href="javascript:void(0)" v-if="isRemoveAllowed(event.count_booking)" @click="removeItem(event.id, index)"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="pagination-nav col-6 offset-6 p-0" v-if="getEventList.total > 0">
                <Pagination :pagination="getEventList" @paginate="getEventListUpdate" :offset="10" :custom-class="'float-right'"/>
            </div>
        </div>


    </div>
</template>

<style scoped>
    th:nth-of-type(1) {
        width: 400px;
    }

    th:nth-of-type(2) {
        width: 500px;
    }

    th:nth-of-type(3) {
        width: 220px;
    }

    td:nth-of-type(2) {
        word-break: break-all;
    }

    th:last-of-type {
        width: 150px;
    }
</style>

<script>
    import { mapGetters } from 'vuex';
    import _ from 'lodash';
    import Pagination from '../../Helper/Pagination';
    import { Stretch } from 'vue-loading-spinner';

    export default {
        data() {
            return {
                baseURL: '',
                page: 1,
                searchData: {
                    page: 1,
                    eventName: null,
                },
            };
        },
        created() {
            this.baseURL = window.location.origin;
            this.getEventListUpdate(this.page);
        },
        computed: {
            ...mapGetters(['getEventList', 'getEventListLoading'])
        },
        methods: {
            getEventListUpdate(page) {
                this.$store.dispatch('GET_EVENT_LIST', { page: page })
            },

            searchEvent: _.debounce(function() {
                this.$store.dispatch('GET_EVENT_LIST', this.searchData);
            }, 1000),

            isRemoveAllowed(count) {
                return count <= 0; // set to true if no schedules or schedules.count_booking is all 0
            },

            removeItem(eventId, index) {
                this.$swal({
                    title: 'イベントを削除してもよろしいですか？',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'はい、削除します',
                    cancelButtonText: 'いいえ',
                    showCloseButton: true,
                    showLoaderOnConfirm: true
                }).then((result) => {
                    if(result.value) {
                        this.$store.dispatch('REMOVE_EVENT', { event_id: eventId, index});
                        this.getEventListUpdate(this.page);
                    }
                })
            }
        },
        components: {
            Pagination,
            Stretch,
        }
    }
</script>
