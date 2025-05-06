<template>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
            <div class="container">
                <div class="navbar-brand">
                    <i class="bi bi-cash-stack me-2"></i>
                </div>
                <div class="navbar-menu">
                    <div class="nav-item">
                        <router-link
                            to="/"
                            class="navbar-brand"
                            active-class="active"
                            exact
                        >
                            Главная
                        </router-link>
                    </div>
                    <div class="nav-item">
                        <router-link
                            to="/transactions"
                            class="navbar-brand"
                            active-class="active"
                        >
                            Операции
                        </router-link>
                    </div>
                </div>
                <button
                    @click="logout"
                    class="btn btn-outline-light"
                    :disabled="loggingOut"
                >
                    <span
                        v-if="loggingOut"
                        class="spinner-border spinner-border-sm me-1"
                    ></span>
                    Выйти
                </button>
            </div>
        </nav>

        <main class="container py-4">
            <div class="row">
                <div class="col-12">
                    <transition name="fade">
                        <div
                            v-if="globalMessage.text"
                            class="alert"
                            :class="`alert-${globalMessage.type}`"
                            role="alert"
                        >
                            {{ globalMessage.text }}
                            <button
                                type="button"
                                class="btn-close"
                                @click="clearGlobalMessage"
                                aria-label="Close"
                            ></button>
                        </div>
                    </transition>

                    <router-view @show-message="showGlobalMessage" />
                </div>
            </div>
        </main>

        <footer class="bg-light py-3 mt-5">
            <div class="container text-center text-muted">
                <small>
                    &copy; {{ currentYear }} Финансовый менеджер v{{
                        appVersion
                    }}
                </small>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const loggingOut = ref(false);
const globalMessage = ref({
    text: "",
    type: "info",
});

const currentYear = new Date().getFullYear();
const appVersion = import.meta.env.VITE_APP_VERSION || "1.0.0";

const isAuthenticated = computed(() => {
    return !!localStorage.getItem("auth_token");
});

const currentUser = computed(() => {
    return localStorage.getItem("user_login") || "Пользователь";
});

const logout = async () => {
    loggingOut.value = true;
    try {
        await axios.post("/api/logout");
        localStorage.removeItem("auth_token");
        localStorage.removeItem("user_login");
        delete axios.defaults.headers.common["Authorization"];
        router.push("/login");
        showGlobalMessage("Вы успешно вышли из системы", "success");
    } catch (error) {
        showGlobalMessage("Ошибка при выходе из системы", "danger");
        console.error("Logout error:", error);
    } finally {
        loggingOut.value = false;
    }
};

const showGlobalMessage = (text, type = "info") => {
    globalMessage.value = { text, type };
    if (type !== "danger") {
        setTimeout(() => {
            clearGlobalMessage();
        }, 5000);
    }
};

const clearGlobalMessage = () => {
    globalMessage.value = { text: "", type: "info" };
};

onMounted(() => {
    if (isAuthenticated.value) {
        axios.defaults.headers.common[
            "Authorization"
        ] = `Bearer ${localStorage.getItem("auth_token")}`;
    }
});
</script>
