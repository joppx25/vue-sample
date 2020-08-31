<template>
    <div :class="wrapperClass">
        <textarea :name="name" :class="inputClass"></textarea>
    </div>
</template>

<script>
    export default {
        props: {
            name: {
                required: true,
            },
            wrapperClass: {
                type: Array,
                required: true,
            },
            inputClass: {
                type: Array,
                required: true,
            },
        }
    }
</script>
