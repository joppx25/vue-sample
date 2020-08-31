<script>
    import { mapGetters } from "vuex";
    import Pagination from '../../Helper/Pagination';
    import Datepicker from 'vuejs-datepicker';
    import { ja } from 'vuejs-datepicker/dist/locale';
    import { Stretch } from 'vue-loading-spinner'
    import _ from 'lodash';
    import TableRow from './TableRow';

    export default {
        data() {
            return {
                page: 1,
                datepickerFrom: new Date(),
                datepickerTo: null,
                searchForm: {
                    eventName: null,
                    dateFrom: moment().format('YYYY-MM-DD'),
                    dateTo: null,
                    page: 1,
                },
                memo: {
                    eventBookId: null,
                    memo: null
                },
                ja: ja,
            }
        },
        created() {
            this.getBookingListUpdate(this.page)
        },
        computed: {
            ...mapGetters(["getBookingList", "getBookingLisLoading"])
        },
        methods: {
            getBookingListUpdate(page) {
                this.searchForm.page = page;
                this.$store.dispatch('GET_BOOKING_LIST', this.searchForm);
                // scroll back to top
                window.scrollTo(0, 0);
            },
            customFormatter(date) {
                return moment(date).format('YYYY-MM-DD');
            },
            searchFormSubmit: _.debounce( function() {
                this.$store.dispatch('GET_BOOKING_LIST', this.searchForm);
                window.scrollTo(0, 0);
            }, 1000),
            downloadCsv: _.debounce( function () {
                window.location = `/admin/bookings/download/csv?` + Object.keys(this.searchForm).map(key => key + '=' + this.searchForm[key]).join('&');
            }),
            downloadSpreedSheet: _.debounce( function () {
                document.getElementById("spread-sheet").disabled = true;
                axios.get(`/admin/bookings/download/spreadSheet`).then((response) => {
                    if (response.status === 200) {
                        document.getElementById("spread-sheet").disabled = false;
                        toastr.success(response.data.message);
                    }
                })
                .catch(err => console.log(err));
            }),
            bookingMemo(eventBookingId) {
                this.memo.eventBookId = eventBookingId;
            },
        },
        components: {
            Pagination,
            Datepicker,
            Stretch,
            TableRow
        }
    }
</script>
<template>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-3">
                <span>{{ $_('labels.event_name', null, 'jp') }}</span>
                <div>
                    <input type="text" class="form-control d-inline" v-model="searchForm.eventName" @keyup="searchFormSubmit"/>
                </div>
            </div>
            <div class="col-md-6">
                <span>{{ $_('labels.schedule', null, 'jp') }}</span>
                <div>
                    <datepicker v-model="datepickerFrom" @input="searchForm.dateFrom = customFormatter($event)" :value="searchForm.dateFrom" :placeholder="$_('labels.start_date', null, 'jp')" :input-class="'form-control w-auto d-inline bg-white'" :language="ja" :format="customFormatter" @selected="searchFormSubmit" :disabled-dates="{from: datepickerTo}" :clear-button="true" :clear-button-icon="'fa fa-times'"></datepicker>
                    ~
                    <datepicker v-model="datepickerTo" @input="searchForm.dateTo = customFormatter($event)" :value="searchForm.dateTo" :placeholder="$_('labels.end_date', null, 'jp')" :input-class="'form-control w-auto d-inline bg-white'" :language="ja" :format="customFormatter" @selected="searchFormSubmit" :disabled-dates="{to: datepickerFrom}" :clear-button="true" :clear-button-icon="'fa fa-times'"></datepicker>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-2">
                <button class="btn btn-info float-left" @click="downloadCsv">{{ $_('labels.download_csv', null, 'jp') }}</button>
            </div>
            <div class="col-md-3">
                <button id="spread-sheet" class="btn btn-info float-left" @click="downloadSpreedSheet">{{ $_('labels.download_spread_sheet', null, 'jp') }}</button>
            </div>
        </div>
        <div class="row mt-4">
            <div class="header">
                <h1>{{ $_('labels.booking_list', null, 'jp') }}</h1>
            </div>
            <div class="mt-4 col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td class="w-150px">{{ $_('labels.status', null, 'jp') }}</td>
                            <td>{{ $_('labels.booking_id', null, 'jp') }}</td>
                            <td>{{ $_('labels.schedule', null, 'jp') }}</td>
                            <td>{{ $_('labels.event_name', null, 'jp') }}</td>
                            <td>{{ $_('labels.full_name', null, 'jp') }}</td>
                            <td>{{ $_('labels.email', null, 'jp') }}</td>
                            <td>{{ $_('labels.contact_number', null, 'jp') }}</td>
                            <td>{{ $_('labels.booking_date', null, 'jp') }}</td>
                            <td>{{ $_('labels.introducer_book', null, 'jp') }}</td>
                            <td class="w-150px">{{ $_('labels.memo', null, 'jp') }}</td>
                            <td class="w-150px">{{ $_('labels.adcode', null, 'jp') }}</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="getBookingLisLoading">
                            <td colspan="11" class="text-center">
                                <stretch></stretch>
                            </td>
                        </tr>
                        <tr v-else-if="getBookingList.total === 0">
                            <td colspan="11" class="text-center"><h5>{{ $_('common.no_records', null, 'jp') }}</h5></td>
                        </tr>
                        <TableRow v-for="(booking, index) in getBookingList.data" :key="index" v-else :booking="booking"></TableRow>
                    </tbody>
                </table>

                <div class="pagination-nav float-right" v-if="getBookingList.total > 0">
                    <Pagination :pagination="getBookingList" @paginate="getBookingListUpdate" :offset="10"/>
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
    .vdp-datepicker {
        display: inline-block;
        width: auto;
    }
    .spinner {
        div {
            background-color: #00A2B4 !important;
        }
    }
    .w-150px {
        width: 150px;
    }
</style>
