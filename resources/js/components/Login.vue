<script setup>
import { ref } from 'vue';
import { login } from '../services/auth';

const email = ref('');
const password = ref('');
const remember = ref(false);
const loading = ref(false);
const error = ref('');

const submit = async () => {
    loading.value = true;
    error.value = '';

    try {
        await login(
            email.value,
            password.value,
            remember.value
        );

        window.location.href = '/';
    } catch (e) {
        error.value =
            e?.message ||
            Object.values(e?.errors || {}).flat().join(' ') ||
            'Unable to login.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-md rounded-xl bg-white p-8 shadow">
            <h1 class="text-2xl font-semibold text-gray-800">
                Login
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Sign in to your account
            </p>

            <div
                v-if="error"
                class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700"
            >
                {{ error }}
            </div>

            <form @submit.prevent="submit" class="mt-6 space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">
                        Email
                    </label>

                    <input
                        v-model="email"
                        type="email"
                        required
                        autocomplete="email"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2
                               outline-none focus:border-slate-500"
                    />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">
                        Password
                    </label>

                    <input
                        v-model="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2
                               outline-none focus:border-slate-500"
                    />
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input
                        v-model="remember"
                        type="checkbox"
                    />
                    Remember me
                </label>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full rounded-lg bg-slate-800 px-4 py-2
                           font-medium text-white hover:bg-slate-700
                           disabled:opacity-50"
                >
                    {{ loading ? 'Signing in...' : 'Sign In' }}
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Don't have an account?
                <a
                    href="/register"
                    class="font-medium text-slate-800 hover:underline"
                >
                    Register
                </a>
            </p>
        </div>
    </div>
</template>