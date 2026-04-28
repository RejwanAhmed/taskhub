<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import FlashMessage from '@/Components/Message/FlashMessage.vue';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >
        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
        <FlashMessage v-if="$page.props.flash" :flash="$page.props.flash"/>
            <slot />
        </div>
    </div>
</template>
