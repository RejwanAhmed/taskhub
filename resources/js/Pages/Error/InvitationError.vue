<template>
    <div class="error-page d-flex flex-column justify-content-center align-items-center vh-100 text-center">
        <p class="error-message">{{ props.message }}</p>
    </div>
</template>

<script setup lang="ts">

const props = defineProps<{
    message: string,
}>();
</script>

<style scoped>
.error-page {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
}

.error-code {
    font-size: 100px;
    font-weight: 800;
    color: #0d6efd;
    margin: 0;
}

.error-message {
    font-size: 20px;
    color: #6c757d;
    max-width: 400px;
}
</style>