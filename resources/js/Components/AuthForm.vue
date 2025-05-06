<template>
    <div class="auth-container">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="mb-4 text-center">Авторизация</h3>

                <form @submit.prevent="handleSubmit">
                    <div class="mb-3">
                        <label for="login" class="form-label">Логин</label>
                        <input
                            v-model="form.login"
                            type="text"
                            class="form-control"
                            id="login"
                            required
                            :class="{ 'is-invalid': errors.login }"
                        />
                        <div v-if="errors.login" class="invalid-feedback">
                            {{ errors.login }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input
                            v-model="form.password"
                            type="password"
                            class="form-control"
                            id="password"
                            required
                            :class="{ 'is-invalid': errors.password }"
                            minlength="8"
                        />
                        <div v-if="errors.password" class="invalid-feedback">
                            {{ errors.password }}
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary w-100 py-2"
                        :disabled="loading"
                    >
                        <span
                            v-if="loading"
                            class="spinner-border spinner-border-sm me-2"
                        ></span>
                        Войти
                    </button>

                    <div v-if="authError" class="alert alert-danger mt-3 mb-0">
                        {{ authError }}
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

const loading = ref(false);
const authError = ref(null);

const form = ref({
    login: "",
    password: "",
});

const errors = ref({
    login: null,
    password: null,
});

const validateForm = () => {
    let isValid = true;
    errors.value = { login: null, password: null };

    if (!form.value.login.trim()) {
        errors.value.login = "Введите логин";
        isValid = false;
    }

    if (!form.value.password) {
        errors.value.password = "Введите пароль";
        isValid = false;
    } else if (form.value.password.length < 8) {
        errors.value.password = "Пароль должен быть не менее 8 символов";
        isValid = false;
    }

    return isValid;
};

const handleSubmit = async () => {
    if (!validateForm()) return;

    loading.value = true;
    authError.value = null;

    try {
        const { data } = await axios.post("/api/login", {
            login: form.value.login,
            password: form.value.password,
        });

        localStorage.setItem("auth_token", data.token);
        localStorage.setItem("user_login", form.value.login);
        axios.defaults.headers.common["Authorization"] = `Bearer ${data.token}`;
        router.push("/");
    } catch (error) {
        handleAuthError(error);
    } finally {
        loading.value = false;
    }
};

const handleAuthError = (error) => {
    if (error.response?.status === 422) {
        const apiErrors = error.response.data.errors;
        for (const field in apiErrors) {
            if (errors.value.hasOwnProperty(field)) {
                errors.value[field] = apiErrors[field][0];
            }
        }
        authError.value = "Пожалуйста, исправьте ошибки в форме";
    } else {
        authError.value = error.response?.data?.message || "Ошибка сервера";
    }
};
</script>
