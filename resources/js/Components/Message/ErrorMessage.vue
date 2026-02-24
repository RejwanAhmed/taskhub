<template>
    <div v-if="errorMessage">
        <p class="text-danger small mt-1">
            {{ errorMessage }}
        </p>
    </div>
</template>

<script setup>
const props = defineProps({
    errorMessage: String
});
</script>