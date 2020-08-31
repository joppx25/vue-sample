<template>
    <div :class="wrapperClass">
        <input :type="inputType" :name="name" :class="inputClass" :value="name" @input="updateInput($event.target.value)">
    </div>
</template>

<script>
    export default {
        props: {
            inputType: {
                required: true,
                defaultValue: 'text',
            },
            name: {
                required: true,
            },
            wrapperClass: {
                required: true,
                defaultValue: [
                    'form-group'
                ],
            },
            inputClass: {
                type: Array
            },
        },
        methods: {
            updateInput(value) {
                this.$emit('input', value);
            },
        },
    }
</script>
