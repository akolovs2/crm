<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import UserForm from './partials/UserForm.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Users', href: '/users' },
            { title: 'New user', href: '#' },
        ],
    },
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    is_admin: false,
});

function submit(e: Event) {
    const data = new FormData(e.target as HTMLFormElement);
    form.name = data.get('name') as string;
    form.email = data.get('email') as string;
    form.password = data.get('password') as string;
    form.password_confirmation = data.get('password_confirmation') as string;
    form.is_admin = data.has('is_admin');
    form.post('/users');
}
</script>

<template>
    <Head title="New user" />

    <div class="p-6 max-w-2xl">
        <h1 class="mb-6 text-2xl font-semibold tracking-tight">New user</h1>

        <form @submit.prevent="submit">
            <UserForm
                :errors="form.errors"
                :processing="form.processing"
                cancel-href="/users"
            />
        </form>
    </div>
</template>
