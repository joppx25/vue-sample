<template>
    <div class="contact-form">
        <div class="row">
            <div class="col-12">
                <form v-on:submit.prevent>
                    <div class="item form-group has-icon">
                        <div class="item-title">
                            <label>{{ $_('labels.full_name', null, 'jp') }}<span class="badge">{{ $_('common.required', null, 'jp') }}</span></label>
                        </div>
                        <div class="item-content">
                            <span class="form-control-icon" :class="{'has-error': errors['full_name'] }">
                                <img src="/images/icons/user.png">
                            </span>
                            <input :class="{'has-error': errors['full_name'] }" type="text" class="form-control item-input" name="full_name" v-model="formFields.full_name" placeholder="山田花子">
                            <p v-if="errors['full_name']">{{ errors['full_name'][0] }}</p>
                        </div>
                    </div>
                    <div class="item form-group has-icon">
                        <div class="item-title">
                            <label>{{ $_('labels.email', null, 'jp') }}<span class="badge">{{ $_('common.required', null, 'jp') }}</span></label>
                        </div>
                        <div class="item-content">
                            <span class="form-control-icon" :class="{'has-error': errors['tel'] }">
                                <img src="/images/icons/envelope.png">
                            </span>
                            <input :class="{'has-error': errors['email'] }" type="email" class="form-control item-input" name="email" v-model="formFields.email" placeholder="yamada@papimami.jp">
                            <p v-if="errors['email']">{{ errors['email'][0] }}</p>
                        </div>
                    </div>
                    <div class="item form-group has-icon">
                        <div class="item-title">
                            <label>{{ $_('labels.tel', null, 'jp') }}<span class="badge">{{ $_('common.required', null, 'jp') }}</span></label>
                        </div>
                        <div class="item-content">
                            <span class="form-control-icon" :class="{'has-error': errors['email'] }">
                                <img src="/images/icons/phone.png">
                            </span>
                            <input :class="{'has-error': errors['tel'] }" type="tel" class="form-control item-input" name="tel" v-model="formFields.tel" placeholder="08012345678">
                            <div class="form-info">ハイフンなしで入力して下さい</div>
                            <p v-if="errors['tel']">{{ errors['tel'][0] }}</p>
                        </div>
                    </div>
                    <div class="item form-group">
                        <div class="item-title">
                            <label>{{ $_('labels.introducer', null, 'jp') }}</label>
                        </div>
                        <div class="item-content">
                            <input :class="{'has-error': errors['introducer'] }" type="text" class="form-control item-input" name="introducer" v-model="formFields.introducer" placeholder="フルネームでご記載ください">
                            <p v-if="errors['introducer']">{{ errors['introducer'][0] }}</p>
                        </div>
                    </div>
                    <div class="row term-condition">
                        <div class="col-12 px-0">
                            <label class="checkmark-container checkmark-container-sm-circle d-inline-flex">
                                <input type="checkbox" name="agree" value="agree" v-model="terms">
                                <span class="checkmark"></span>
                                <div class="description">
                                    <a href="https://papimami.jp/term_event/" style="text-decoration: underline;">
                                        {{ $_('labels.terms_of_service', null, 'jp')}}
                                    </a>
                                        {{ $_('labels.agree_to', null, 'jp') }}
                                    <span class="badge">{{ $_('common.required', null, 'jp') }}</span><br/>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="btn-wrapper">
                        <button v-if="fullyBooked" v-on:click="returnEvent" class="pb-btn pb-btn-primary">{{ btnSubmit }}</button>
                        <button v-else class="pb-btn pb-btn-submit icon-caret" v-on:click="submitBooking" :disabled="submitted || !isComplete">{{ btnSubmit }}</button>
                    </div>
                    <div class="text-center mt-5">
                        <a class="back-link" :href="'/events/' + this.formFields.event_id">←1つ前のページへ戻る</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name: "ContactForm",
        props: {
            datas: {
                type:     Object,
                required: true
            }
        },
        data() {
            return {
                errors: [],
                submitted: false,
                fullyBooked: false,
                btnSubmit: this.$_('labels.submit', null, 'jp'),
                terms: true,
                formFields: {
                    event_id: this.datas.event_id,
                    schedule_id: this.datas.schedule_id,
                    full_name: null,
                    email: null,
                    tel: null,
                    introducer: null,
                    booking_id: null,
                }
            }
        },
        methods: {
            submitBooking(e) {
                this.submitted = true;
                this.btnSubmit = this.$_('labels.please_wait', null, 'jp');
                this.errors = [];

                axios.post('/submit-booking', {
                    schedule_id: this.formFields.schedule_id,
                    full_name: this.formFields.full_name,
                    email: this.formFields.email,
                    tel: this.formFields.tel,
                    introducer: this.formFields.introducer,
                })
                .then((res) => {
                    if(res.data.message) {
                        if(res.data.status){
                            this.btnSubmit = this.$_('labels.return_event', null, 'jp');
                            this.fullyBooked = true;
                        } else {
                            this.btnSubmit = this.$_('labels.submit', null, 'jp');
                            this.submitted = false;
                        }

                        toastr.error(res.data.message);
                    } else {
                        this.formFields.full_name = null;
                        this.formFields.email = null;
                        this.formFields.tel = null;
                        this.formFields.introducer = null;
                        this.formFields.booking_id = res.data.booking_id;
                        this.btnSubmit = this.$_('labels.booked', null, 'jp');
                        toastr.success(this.$_('labels.successfully_submitted', null, 'jp'));

                        setTimeout(() => {
                            location.href = '/thank-you/' + this.formFields.booking_id;
                        }, 1000);
                    }
                })
                .catch(error => {
                    let statusCode = error.response.status;
                    if (statusCode === 422) {
                        this.errors = error.response.data.errors;
                    }
                    this.btnSubmit = this.$_('labels.submit', null, 'jp');
                    this.submitted = false;
                });

                e.preventDefault();
            },
            returnEvent() {
                location.href = '/events/' + this.formFields.event_id;
            }
        },
        computed: {
            isComplete () {
                return this.formFields.full_name && this.formFields.email && this.formFields.tel && this.terms;
            }
        }
    }
</script>
