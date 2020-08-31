<template>
    <tr>
        <td>
            <span v-if="booking.status == 1">キャンセル</span>
            <span v-else-if="showSelect"></span>
            <select v-else v-model="status" v-on:change="changeStatus" class="form-control">
                <option value="0">参加済み</option>
                <option value="2">当日キャンセル</option>
            </select>
        </td>
        <td>
            <a :href="`/details/${booking.booking_id}`">{{ booking.booking_id }}</a>
        </td>
        <td>{{ booking.schedule_info.date + ' ' + booking.schedule_info.start_time }}</td>
        <td>
            <a :href="`/events/${booking.schedule_info.event_info.id}`">{{ booking.schedule_info.event_info.title }}</a>
        </td>
        <td>{{ booking.full_name }}</td>
        <td>{{ booking.email }}</td>
        <td>{{ booking.tel }}</td>
        <td>{{ booking.created_at }}</td>
        <td>{{ booking.introducer }}</td>
        <td v-on:click.self="clickMemo">
            <div class="memo-text text-break text-left" v-on:click.self="clickMemo" v-if="!clicked">{{ booking.memo }}</div>
            <div class="memo-text-area text-right" v-else>
                <textarea class="form-control" v-model="memoText" :maxlength="maxChar" @keydown="enterMemo"></textarea>
                <small class="mt-2" v-text="(maxChar - (memoText === null ? 0 : memoText.length)) + ` / ${maxChar}`"></small>
            </div>
        </td>
        <td>{{ booking.adcode }}</td>
    </tr>

</template>

<script>
    import _ from 'lodash';

    export default {
        props:   {
            booking: {
                required: true,
            }
        },
        methods: {
            clickMemo() {
                this.clicked = !this.clicked;
            },
            updateCloseMemo() {
                this.clicked = false;
            },
            enterMemo(e) {
                if (e.keyCode === 13 && !e.shiftKey) {
                    e.preventDefault();
                    this.submitMemo();
                }
            },
            submitMemo:   _.debounce(function () {
                axios.post('/admin/book/memo', {
                    id:   this.booking.id,
                    memo: this.memoText
                }).then((response) => {
                    if (response.status === 200) {
                        if (response.status) {
                            toastr.success(response.data.message);
                            this.booking.memo = this.memoText;
                            this.clicked      = false;
                        } else {
                            toastr.error(response.data.message);
                        }
                    }
                });
            }, 1000),
            changeStatus: _.debounce(function () {
                axios.post('/admin/book/status', {
                    id:     this.booking.id,
                    status: this.status
                }).then((response) => {
                    if (response.status === 200) {
                        toastr.success(response.data.message);
                        this.booking.status = this.status;
                    } else {
                        toastr.error(response.data.message);
                    }
                });
            }, 1000)
        },
        mounted() {
            let self = this;
            window.addEventListener('keyup', (event) => {
                // If  ESC key was pressed...
                if (event.keyCode === 27) {
                    this.updateCloseMemo();
                }
            });

            window.addEventListener('click', (event) => {
                // close dropdown when clicked outside
                if (!self.$el.contains(event.target)) {
                    this.updateCloseMemo();
                }
            });
        },
        data() {
            return {
                clicked:  null,
                memoText: this.booking.memo,
                maxChar:  1000,
                status:   this.booking.status,
                showSelect: moment(`${this.booking.schedule_info.date} ${this.booking.schedule_info.start_time}`).diff(moment(), 'minute') > 0
            }
        }
    }
</script>
