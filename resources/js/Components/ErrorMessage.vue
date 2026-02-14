<template>
    <div v-if="errorMessage">
        <p class="small text-danger mt-1">
            {{ errorMessage }}
        </p>
    </div>
</template>

<script setup lang="ts">
const props = defineProps({
    errorMessage: String
});
</script>