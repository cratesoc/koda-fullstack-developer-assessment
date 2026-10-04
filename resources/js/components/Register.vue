<script setup>
import { ref } from 'vue';
import { register } from '../services/auth';

const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');

const loading = ref(false);
const error = ref('');

const submit = async () => {
    loading.value = true;
    error.value = '';

    try {
        await register(
            name.value,
            email.value,
            password.value,
            passwordConfirmation.value
        );

        window.location.href = '/';
    } catch (e) {
        error.value =
            e?.message ||
            Object.values(e?.errors || {}).flat().join(' ') ||
            'Unable to create account.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-md rounded-xl bg-white p-8 shadow">
            <h1 class="text-2xl font-semibold text-gray-800">
                Create Account
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Create an account to manage your tasks
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
                        Name
                    </label>

                    <input
                        v-model="name"
                        type="text"
                        required
                        autocomplete="name"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2
                               outline-none focus:border-slate-500"
                    />
                </div>

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
                        minlength="8"
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2
                               outline-none focus:border-slate-500"
                    />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">
                        Confirm Password
                    </label>

                    <input
                        v-model="passwordConfirmation"
                        type="password"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2
                               outline-none focus:border-slate-500"
                    />
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full rounded-lg bg-slate-800 px-4 py-2
                           font-medium text-white hover:bg-slate-700
                           disabled:opacity-50"
                >
                    {{ loading ? 'Creating account...' : 'Register' }}
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Already have an account?
                <a
                    href="/login"
                    class="font-medium text-slate-800 hover:underline"
                >
                    Sign In
                </a>
            </p>
        </div>
    </div>
</template>