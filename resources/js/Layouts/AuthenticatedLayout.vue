<script setup lang="ts">
import Sidebar from '@/Layouts/components/Sidebar.vue';
import FlashMessage from '@/Components/Message/FlashMessage.vue';
</script>

<template>
    <div style="background-color: #f8f9f8;">
        <Sidebar>
            <template #header>
                <slot name="header" />
            </template>

            <FlashMessage v-if="$page.props.flash" :flash="$page.props.flash"/>
            <slot />
        </Sidebar>
    </div>
    
</template>
