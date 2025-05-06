<template>
    <div class="transaction-list">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">История операций</h2>
            <div class="d-flex">
                <input
                    v-model="searchQuery"
                    type="search"
                    class="form-control me-2 mt-1"
                    placeholder="Поиск по описанию"
                    @input="handleSearch"
                />
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th
                                    @click="sortBy('created_at')"
                                    class="cursor-pointer"
                                >
                                    Дата
                                    <i
                                        class="bi ms-1"
                                        :class="sortIcon('created_at')"
                                    ></i>
                                </th>
                                <th>Тип</th>
                                <th
                                    @click="sortBy('amount')"
                                    class="cursor-pointer"
                                >
                                    Сумма
                                    <i
                                        class="bi ms-1"
                                        :class="sortIcon('amount')"
                                    ></i>
                                </th>
                                <th>Описание</th>
                            </tr>
                        </thead>
                        <tbody v-if="loading">
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div
                                        class="spinner-border text-primary"
                                        role="status"
                                    >
                                        <span class="visually-hidden"
                                            >Загрузка...</span
                                        >
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else-if="transactions.data.length">
                            <tr
                                v-for="transaction in transactions.data"
                                :key="transaction.id"
                                @click="selectTransaction(transaction.id)"
                                class="cursor-pointer"
                            >
                                <td>
                                    {{ formatDate(transaction.created_at) }}
                                </td>
                                <td>
                                    <span
                                        class="badge"
                                        :class="
                                            typeClass(
                                                transaction.type,
                                                transaction.status
                                            )
                                        "
                                    >
                                        {{
                                            transaction.type === "credit"
                                                ? "Пополнение"
                                                : "Списание"
                                        }}
                                    </span>
                                </td>
                                <td :class="amountClass(transaction.type)">
                                    {{ formatCurrency(transaction.amount) }}
                                </td>
                                <td>{{ transaction.description }}</td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td
                                    colspan="4"
                                    class="text-center py-4 text-muted"
                                >
                                    Операции не найдены
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    class="d-flex justify-content-between align-items-center px-3 py-2 border-top"
                >
                    <small class="text-muted">
                        Показано {{ transactions.from }} -
                        {{ transactions.to }} из
                        {{ transactions.total }} операций
                    </small>
                    <Pagination
                        :current-page="transactions.current_page"
                        :total-pages="transactions.last_page"
                        @page-changed="changePage"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { debounce } from "lodash";
import Pagination from "./Pagination.vue";

const router = useRouter();

const transactions = ref({
    data: [],
    current_page: 1,
    last_page: 1,
    from: 0,
    to: 0,
    total: 0,
});
const loading = ref(true);
const searchQuery = ref("");
const sortField = ref("created_at");
const sortDirection = ref("desc");
const itemsPerPage = 10;

const fetchTransactions = async () => {
    try {
        loading.value = true;
        const response = await axios.get("/api/transactions", {
            params: {
                page: transactions.value.current_page,
                description: searchQuery.value,
                sort: sortField.value,
                order: sortDirection.value,
                per_page: itemsPerPage,
            },
        });

        transactions.value = {
            ...response.data,
            data: response.data.data.map((t) => ({
                ...t,
                created_at: new Date(t.created_at),
            })),
        };
    } catch (err) {
        console.error("Ошибка загрузки:", err);
    } finally {
        loading.value = false;
    }
};

const sortBy = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortDirection.value = "asc";
    }
    transactions.value.current_page = 1;
};

const sortIcon = (field) => {
    if (sortField.value !== field) return "bi-filter";
    return sortDirection.value === "asc" ? "bi-sort-up" : "bi-sort-down";
};

const typeClass = (type, status) => ({
    "bg-success": type === "credit",
    "bg-danger": type === "debit" && status !== "failed",
    "bg-warning": status === "failed",
});

const amountClass = (type) => ({
    "text-success": type === "credit",
    "text-danger": type === "debit",
    "fw-bold": true,
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString("ru-RU", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("ru-RU", {
        style: "currency",
        currency: "RUB",
        minimumFractionDigits: 2,
    }).format(amount);
};

const selectTransaction = (id) => {
    router.push(`/transactions/${id}`);
};

const changePage = (page) => {
    transactions.value.current_page = page;
};

const handleSearch = debounce(() => {
    transactions.value.current_page = 1;
    fetchTransactions();
}, 300);

watch([() => transactions.value.current_page, sortField, sortDirection], () => {
    fetchTransactions();
});

onMounted(fetchTransactions);
</script>
