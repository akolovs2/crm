<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { index, create, store } from '@/routes/companies';
import type { Company } from '@/types';
import CompanyForm from './partials/CompanyForm.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Companies', href: index() },
            { title: 'New company', href: create() },
        ],
    },
});
</script>

<template>
    <Head title="New Company" />

    <div class="flex flex-col gap-6 p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">New company</h1>
            <p class="text-sm text-muted-foreground">Add a new company to your CRM.</p>
        </div>

        <div class="max-w-2xl rounded-lg border p-6">
            <Form :action="store().url" method="post" v-slot="{ errors, processing }">
                <CompanyForm
                    :errors="errors"
                    :processing="processing"
                    :cancel-href="index().url"
                />
            </Form>
        </div>
    </div>
</template>
