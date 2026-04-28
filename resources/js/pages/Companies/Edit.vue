<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { index, show, update } from '@/routes/companies';
import type { Company } from '@/types';
import CompanyForm from './partials/CompanyForm.vue';

const props = defineProps<{
    company: Company;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Companies', href: '/companies' },
            { title: 'Edit company', href: '#' },
        ],
    },
});
</script>

<template>
    <Head :title="`Edit ${company.name}`" />

    <div class="flex flex-col gap-6 p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Edit company</h1>
            <p class="text-sm text-muted-foreground">Update {{ company.name }}'s information.</p>
        </div>

        <div class="max-w-2xl rounded-lg border p-6">
            <Form :action="update(company.id).url" method="put" v-slot="{ errors, processing }">
                <CompanyForm
                    :company="company"
                    :errors="errors"
                    :processing="processing"
                    :cancel-href="show(company).url"
                />
            </Form>
        </div>
    </div>
</template>
