<template>
    <form v-on:submit.prevent="createOrUpdateEvent">
        <div class="form-group row">
            <label for="title" class="col-3 col-form-label">{{ $_('labels.title', null, 'jp') }}</label>
            <div class="col-9">
                <input type="text" name="title" class="form-control" :class="{'is-invalid': errors.hasOwnProperty('title')}" id="title" v-model="inputValues.title">
                <ErrorField v-if="errors.hasOwnProperty('title')">
                    {{ errors.title[0] }}
                </ErrorField>
            </div>
        </div>
        <div class="form-group row">
            <label for="place" class="col-3 col-form-label">{{ $_('labels.place', null, 'jp') }}</label>
            <div class="col-9">
                <textarea name="place" id="place" class="h-lg form-control" :class="{'is-invalid': errors.hasOwnProperty('place')}" v-model="inputValues.place"></textarea>
                <ErrorField v-if="errors.hasOwnProperty('place')">
                    {{ errors.place[0] }}
                </ErrorField>
            </div>
        </div>
        <div class="form-group row">
            <label for="access_direction" class="col-3 col-form-label">{{ $_('labels.access_direction', null, 'jp') }}</label>
            <div class="col-9">
                <textarea name="access_direction" id="access_direction" class="h-lg form-control" :class="{'is-invalid': errors.hasOwnProperty('access_direction')}" v-model="inputValues.access_direction"></textarea>
                <ErrorField v-if="errors.hasOwnProperty('access_direction')">
                    {{ errors.access_direction[0] }}
                </ErrorField>
            </div>
        </div>
        <div class="form-group row">
            <label for="description" class="col-3 col-form-label">{{ $_('labels.description', null, 'jp') }}</label>
            <div class="col-9">
                <textarea name="description" id="description" class="h-lg form-control" :class="{'is-invalid': errors.hasOwnProperty('description')}" v-model="inputValues.description"></textarea>
                <ErrorField v-if="errors.hasOwnProperty('description')">
                    {{ errors.description[0] }}
                </ErrorField>
            </div>
        </div>
        <div class="form-group row">
            <label for="cancel_time" class="col-3 col-form-label">{{ $_('labels.cancel_time', null, 'jp') }}</label>
            <div class="col-9">
                <input type="number" name="cancel_time" class="form-control" :class="{'is-invalid': errors.hasOwnProperty('cancel_time')}" id="cancel_time" v-model="inputValues.cancel_time" oninput="validity.valid||(value='');" pattern="/^-?\d+\.?\d*$/">
                <ErrorField v-if="errors.hasOwnProperty('cancel_time')">
                    {{ errors.cancel_time[0] }}
                </ErrorField>
            </div>
        </div>
        <div class="form-group row">
            <label for="filename" class="col-3 col-form-label">{{ $_('labels.filename', null, 'jp') }}</label>
            <div class="col-9">
                <button type="button" @click="addImage">{{ $_('labels.select_a_file', null, 'jp') }}</button> <span id="fileTitle">{{ fileNameLabel }}</span>
                <input type="file" name="filename" ref="files" id="filename" class="form-control-file" :class="{'is-invalid': errors.hasOwnProperty('image')}" @change="onFileChange" accept="image/x-png,image/gif,image/jpeg">
                <ErrorField v-if="errors.hasOwnProperty('image')">
                    {{ errors.image[0] }}
                </ErrorField>
            </div>
        </div>
        <div class="form-group row">
            <label for="staff_id" class="col-3 col-form-label">{{ $_('labels.staff', null, 'jp') }}</label>
            <div class="col-9">
                <select name="staff_id" class="form-control" :class="{'is-invalid': errors.hasOwnProperty('staff_id')}" id="staff_id" v-model="inputValues.staff_id">
                    <option value=""></option>
                    <option v-for="staff in staffs" :key="staff.id" :value="staff.id" :selected="inputValues.staff_id === staff.id">{{ staff.full_name }}</option>
                </select>
                <ErrorField v-if="errors.hasOwnProperty('staff_id')">
                    {{ errors.staff_id[0] }}
                </ErrorField>
            </div>
        </div>
        <div class="form-group row">
            <label for="google_map_url" class="col-3 col-form-label">{{ $_('labels.google_map_url', null, 'jp') }}</label>
            <div class="col-9">
                <input type="text" name="google_map_url" class="form-control" :class="{'is-invalid': errors.hasOwnProperty('google_map_url')}" id="google_map_url" v-model="inputValues.google_map_url">
                <ErrorField v-if="errors.hasOwnProperty('google_map_url')">
                    {{ errors.google_map_url[0] }}
                </ErrorField>
            </div>
        </div>
        <ScheduleTable :schedules="schedules" :error="errors.hasOwnProperty('schedules')? errors.schedules[0] : ''"/>
        <button type="submit" class="btn btn-primary" :disabled="isComplete" id="submitBtn">{{ btnText }}</button>
    </form>
</template>
<style scoped>
    #filename {
        display: none;
    }
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
<script>
    import ScheduleTable from './ScheduleTable';
    import ErrorField    from '../../Helper/ErrorField';
    const STATUS_OK                  = 200;
    const ENTITY_TOO_LARGE           = 413;
    const STATUS_ERROR               = 500;
    const STATUS_UNPROCESSABLE_ERROR = 422;
    export default {
        props: {
            event: {
                type: [Object, String],
                required: true,
            },
            schedules: {
                type: Array
            },
            cloneDetails: {
                type: [Object, String],
            },
            staffs: {
                type: [Object, Array]
            }
        },
        data() {
            return {
                errors: {
                    image: [],
                },
                isEdit: false,
                inputValues: {
                    title: this.cloneDetails? this.cloneDetails.title : '',
                    place: this.cloneDetails? this.cloneDetails.place : '',
                    access_direction: this.cloneDetails? this.cloneDetails.access_direction : '',
                    description: this.cloneDetails? this.cloneDetails.description : '',
                    cancel_time: this.cloneDetails? this.cloneDetails.cancel_time : process.env.MIX_DEFAULT_CANCEL_TIME,
                    image: '',
                    staff_id: this.cloneDetails ? this.cloneDetails.staff_id : '',
                    google_map_url: this.cloneDetails ? this.cloneDetails.google_map_url : '',
                },
            }
        },
        created() {
            if (this.event instanceof Object) {
                this.isEdit = true;
                this.inputValues = this.event;
            }
        },
        computed: {
            btnText() {
                return this.isEdit ? this.$t('labels.update', null, 'jp') : this.$t('labels.create', null, 'jp');
            },
            isComplete() {
                return this.inputValues.title === '' || this.inputValues.place === '' || this.inputValues.access_direction === '' || this.inputValues.description === '';
            },
            fileNameLabel() {
                return this.cloneDetails? this.cloneDetails.filename : this.$t('labels.no_file_selected', null, 'jp');
            }
        },
        methods: {
            addImage() {
                this.$refs.files.click();
            },
            createOrUpdateEvent() {
                document.getElementById('submitBtn').setAttribute('disabled', true);
                let form           = new FormData();
                let imageData      = this.inputValues.hasOwnProperty('image')? this.inputValues.image : null;
                let clonedImage    = this.cloneDetails? this.cloneDetails.filename : '';
                for (let [key, value] of Object.entries(this.inputValues)) {
                    form.append(key, value);
                }
                if (clonedImage && !imageData) {
                    form.append('clonedImage', clonedImage);
                }
                form.append('image', imageData);
                form.append('schedules', JSON.stringify(this.schedules));
                axios.post('/admin/events/createOrUpdateEvent', form).then(response => {
                    this.errors = {
                        image: [],
                    };
                    if (response.status === STATUS_OK) {
                        toastr.success(response.data.message);
                        setTimeout(() => {
                            location.href = '/admin/events/';
                        }, 1000);
                    }
                }).catch(error => {
                    document.getElementById('submitBtn').removeAttribute('disabled');
                    let statusCode = error.response.status;
                    if (statusCode === STATUS_UNPROCESSABLE_ERROR) {
                        this.errors = error.response.data.errors;
                        toastr.error(this.$t('common.error', null, 'jp'));
                    } else if (statusCode === ENTITY_TOO_LARGE) {
                        this.errors.image = [this.$t('common.validation.file_too_large', null, 'jp')];
                        toastr.error(this.$t('common.error', null, 'jp'));
                    }
                });
            },
            onFileChange(e) {
                let files = e.target.files || e.dataTransfer.files;
                let fileLabel = document.getElementById('fileTitle');
                if (!files.length) {
                    this.inputValues.image = '';
                    fileLabel.innerHTML = '選択されていません';
                    return;
                }
                let filename           = files.item(0).name;
                fileLabel.innerHTML    = filename;
                this.inputValues.image = files.item(0);
            },
        },
        components: {
            ScheduleTable,
            ErrorField,
        }
    }
</script>
