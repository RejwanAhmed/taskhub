<template>
    <div>
        <div class="input-group mb-3" style="max-width: 300px;">
            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
            <input v-model="search" type="text" class="form-control border-start-0" :placeholder="searchPlaceholder" />
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light text-uppercase text-muted fw-semibold small py-3">
                    <tr>
                        <th v-for="col in columns" :key="col.key" :role="col.sortable ? 'button' : undefined" @click="col.sortable && sort(col.key)">
                            {{ col.label }}
                            <i v-if="col.sortable" class="bi small ms-1" :class="sortKey === col.key ? (sortDir === 'asc' ? 'bi-arrow-up text-teal opacity-40' : 'bi-arrow-down text-teal opacity-40') : 'bi-arrow-down-up opacity-30'"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, i) in paginated" :key="i">
                        <td v-for="col in columns" :key="col.key">
                            <slot :name="col.key" :row="row">
                                {{ row[col.key] }}
                            </slot>
                        </td>
                    </tr>
                    <tr v-if="paginated.length === 0">
                        <td :colspan="columns.length" class="text-center text-muted py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                            <div>No records found</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
            <small class="text-muted">Showing {{ paginated.length }} of {{ filtered.length }} records</small>
            <nav v-if="totalPages > 1">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item" :class="{ disabled: currentPage === 1 }">
                        <button class="page-link" @click="currentPage--">‹</button>
                    </li>
                    <li v-for="p in totalPages" :key="p" class="page-item" :class="{ active: currentPage === p }">
                        <button class="page-link" @click="currentPage = p">{{ p }}</button>
                    </li>
                    <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                        <button class="page-link" @click="currentPage++">›</button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    data: Record<string, any>[],
    columns: { key: string; label: string; sortable?: boolean; searchable?: boolean }[],
    searchPlaceholder?: string,
    perPage?: number,
}>();

const search = ref('');
const currentPage = ref(1);
const sortKey = ref('');
const sortDir = ref<'asc' | 'desc'>('asc');
const perPage = computed(() => props.perPage ?? 10);

watch(search, () => currentPage.value = 1);

const sort = (key: string) => {
    if (sortKey.value === key) sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    else { sortKey.value = key; sortDir.value = 'asc'; }
    currentPage.value = 1;
};

const filtered = computed(() => {
    const q = search.value.toLowerCase();
    const searchableCols = props.columns.filter(c => c.searchable !== false).map(c => c.key);
    return props.data
        .filter(row => searchableCols.some(key => String(row[key] ?? '').toLowerCase().includes(q)))
        .sort((a, b) => {
            if (!sortKey.value) return 0;
            const av = a[sortKey.value] ?? '';
            const bv = b[sortKey.value] ?? '';
            return (av > bv ? 1 : -1) * (sortDir.value === 'asc' ? 1 : -1);
        });
});

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage.value)));
const paginated = computed(() => filtered.value.slice((currentPage.value - 1) * perPage.value, currentPage.value * perPage.value));
</script>

<style scoped>
.active > .page-link, .page-link.active {
    background-color: #0d9488;
}

tbody tr:hover td {
    background-color: #f0fdfa;
}
</style>