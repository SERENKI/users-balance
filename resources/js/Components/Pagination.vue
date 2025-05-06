<template>
    <div>
        <ul class="pagination pagination-sm mb-0">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <button
                    class="page-link"
                    @click="$emit('page-changed', currentPage - 1)"
                >
                    &laquo;
                </button>
            </li>
            <li
                v-for="page in visiblePages"
                :key="page"
                class="page-item"
                :class="{ active: page === currentPage }"
            >
                <button class="page-link" @click="$emit('page-changed', page)">
                    {{ page }}
                </button>
            </li>
            <li
                class="page-item"
                :class="{ disabled: currentPage === totalPages }"
            >
                <button
                    class="page-link"
                    @click="$emit('page-changed', currentPage + 1)"
                >
                    &raquo;
                </button>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    currentPage: {
        type: Number,
        required: true,
        validator: (value) => value > 0,
    },
    totalPages: {
        type: Number,
        required: true,
        validator: (value) => value > 0,
    },
});

const visiblePages = computed(() => {
    const range = 2;
    const start = Math.max(1, props.currentPage - range);
    const end = Math.min(props.totalPages, props.currentPage + range);

    const pages = [];
    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    return pages;
});
</script>
