<script setup lang="ts">
import Sidebar from '@/Layouts/components/Sidebar.vue';
import FlashMessage from '@/Components/Message/FlashMessage.vue';
</script>

<template>
    <Sidebar>
        <template #header>
            <slot name="header" />
        </template>

        <FlashMessage v-if="$page.props.flash" :flash="$page.props.flash"/>
        <slot />
    </Sidebar>
</template>
