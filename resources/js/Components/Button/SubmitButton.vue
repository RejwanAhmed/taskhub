<template>
    <button type="submit" class="btn bg-teal text-white" :disabled="props.processing">
        <span v-if="props.processing" class="spinner-border spinner-border-sm me-1"></span>
        {{ props.label }}
    </button>
</template>

<script setup lang="ts">
const props = defineProps({
    label: String,
    processing: Boolean,
});
</script>