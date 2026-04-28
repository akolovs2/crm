<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { index, create, store } from '@/routes/contacts';
import type { Company } from '@/types';
import ContactForm from './partials/ContactForm.vue';

defineProps<{ companies: Pick<Company, 'id' | 'name'>[] }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Contacts', href: index() },
            { title: 'New contact', href: create() },
        ],
    },
});
</script>

<template>
    <Head title="New Contact" />

    <div class="flex flex-col gap-6 p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">New contact</h1>
            <p class="text-sm text-muted-foreground">Add a new contact to your CRM.</p>
        </div>

        <div class="max-w-2xl rounded-lg border p-6">
            <Form :action="store().url" method="post" v-slot="{ errors, processing }">
                <ContactForm
                    :companies="companies"
                    :errors="errors"
                    :processing="processing"
                    :cancel-href="index().url"
                />
            </Form>
        </div>
    </div>
</template>
