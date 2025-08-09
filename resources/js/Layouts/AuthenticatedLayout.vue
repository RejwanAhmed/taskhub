<script setup lang="ts">
import Sidebar from '@/Layouts/components/Sidebar.vue';
</script>

<template>
    <Sidebar>
        <template #header>
            <slot name="header" />
        </template>

        <slot />
    </Sidebar>
</template>
