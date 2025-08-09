import { defineStore } from "pinia";
import {ref, watch} from 'vue';

export const useUiStore = defineStore('ui', () => {
    const stored = localStorage.getItem('sidebarCollapsed');
    const sidebarCollapsed = ref(
        stored !== null ? stored === 'true' : window.innerWidth < 768
    );

    watch(sidebarCollapsed, (val) => {
        localStorage.setItem('sidebarCollapsed', val.toString());
    });

    function toggleSidebar() {
        sidebarCollapsed.value = !sidebarCollapsed.value;
    }

    function setCollapsed(value: boolean) {
        sidebarCollapsed.value = value;
    }

    return { sidebarCollapsed, toggleSidebar, setCollapsed };
});