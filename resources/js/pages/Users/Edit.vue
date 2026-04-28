<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import UserForm from './partials/UserForm.vue';
import type { User } from '@/types';

const props = defineProps<{ user: User }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Users', href: '/users' },
            { title: 'Edit user', href: '#' },
        ],
    },
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    is_admin: props.user.is_admin,
});

function submit(e: Event) {
    const data = new FormData(e.target as HTMLFormElement);
    form.name = data.get('name') as string;
    form.email = data.get('email') as string;
    form.password = (data.get('password') as string) ?? '';
    form.password_confirmation = (data.get('password_confirmation') as string) ?? '';
    form.is_admin = data.has('is_admin');
    form.put(`/users/${props.user.id}`);
}
</script>

<template>
    <Head :title="`Edit ${user.name}`" />

    <div class="p-6 max-w-2xl">
        <h1 class="mb-6 text-2xl font-semibold tracking-tight">Edit {{ user.name }}</h1>

        <form @submit.prevent="submit">
            <UserForm
                :user="user"
                :errors="form.errors"
                :processing="form.processing"
                cancel-href="/users"
            />
        </form>
    </div>
</template>
