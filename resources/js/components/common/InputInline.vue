<template>
    <div class="form-group row">
        <label :for="getIdAttr" :class="classes.label">{{ input.name }}</label>
        <TextAreaField v-if="input.type === 'textarea'" :id="getIdAttr" :name="getIdAttr" :wrapperClass="classes.inner" :inputClass="classInput" v-model="getIdAttr"/>
        <input v-else-if="input.type === 'file'" type="file" @change="onFileChange"/>
        <Input v-else :inputType="input.type" :id="getIdAttr" :name="getIdAttr" :wrapperClass="classes.inner" :inputClass="classInput" v-model="getIdAttr"/>
    </div>
</template>

<script>
    import Input from './Input';
    import TextAreaField from './TextAreaField';

    export default {
        props: {
            input: {
                type: Object,
                required: true,
            },
            classes: {
                type: Object,
                required: true,
            },
        },
        data() {
            return {
                name: this.input.name.toLowerCase(),
                classInput: this.input.hasOwnProperty('class')? this.input.class : this.classes.input,
                inputValues: {},
            }
        },
        computed: {
            getIdAttr () {
                return this.name.replace(/\s/, '_');
            },
        },
        methods: {
            onFileChange(e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                // this.createImage(files[0]);
            },
        },
        components: {
            Input,
            TextAreaField,
        },
    }
</script>
