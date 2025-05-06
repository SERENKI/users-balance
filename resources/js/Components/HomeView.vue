<template>
    <div class="home-view container py-4">
        <h1 class="mb-4">Добро пожаловать, {{ username }}</h1>

        <div class="card balance-card mb-4">
            <div class="card-body">
                <h2 class="card-title">Текущий баланс</h2>
                <div class="balance-display">
                    <span class="balance-amount">{{ formattedBalance }}</span>
                    <button
                        @click="refreshData"
                        class="btn btn-sm btn-outline-secondary"
                        :disabled="loading"
                    >
                        <span
                            v-if="loading"
                            class="spinner-border spinner-border-sm"
                        ></span>
                        Обновить
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-title mb-3">Последние операции</h2>

                <div v-if="loading" class="text-center py-3">
                    <div
                        class="spinner-border text-primary"
                        role="status"
                    ></div>
                </div>

                <div v-if="error" class="alert alert-danger">
                    {{ error }}
                </div>

                <div
                    v-if="!loading && transactions.length"
                    class="transaction-list"
                >
                    <div
                        v-for="transaction in transactions"
                        :key="transaction.id"
                        class="transaction-item mb-3 p-3 border rounded"
                        :class="
                            transaction.type === 'credit'
                                ? 'bg-success-light'
                                : 'bg-danger-light'
                        "
                    >
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <div>
                                <h5 class="mb-1">
                                    {{ transaction.description }}
                                </h5>
                                <small class="text-muted">
                                    {{ formatDate(transaction.created_at) }}
                                </small>
                            </div>
                            <span class="transaction-amount">
                                {{ transaction.type === "credit" ? "+" : "-" }}
                                {{ formatCurrency(transaction.amount) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div
                    v-if="!loading && !transactions.length"
                    class="alert alert-info"
                >
                    Нет операций для отображения
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

const balance = ref(0);
const transactions = ref([]);
const loading = ref(false);
const error = ref(null);
const refreshInterval = ref(null);
const username = ref(localStorage.getItem("username") || "Пользователь");

const formattedBalance = computed(() => {
    return new Intl.NumberFormat("ru-RU", {
        style: "currency",
        currency: "RUB",
        minimumFractionDigits: 2,
    }).format(balance.value);
});

const fetchData = async () => {
    try {
        loading.value = true;
        error.value = null;

        const response = await axios.get("/api/balance");
        balance.value = response.data.balance;
        transactions.value = response.data.transactions.slice(0, 5);
    } catch (err) {
        error.value = "Ошибка загрузки данных. Попробуйте позже.";
        console.error("Ошибка:", err);
    } finally {
        loading.value = false;
    }
};

const refreshData = () => {
    clearInterval(refreshInterval.value);
    fetchData();
    startAutoRefresh();
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("ru-RU", {
        day: "2-digit",
        month: "long",
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

const startAutoRefresh = () => {
    refreshInterval.value = setInterval(fetchData, 10000);
};

onMounted(() => {
    fetchData();
    startAutoRefresh();
});

onBeforeUnmount(() => {
    clearInterval(refreshInterval.value);
});
</script>
