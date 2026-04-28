<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { index, show, update } from '@/routes/contacts';
import type { Company, Contact } from '@/types';
import ContactForm from './partials/ContactForm.vue';

const props = defineProps<{
    contact: Contact;
    companies: Pick<Company, 'id' | 'name'>[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Contacts', href: '/contacts' },
            { title: 'Edit contact', href: '#' },
        ],
    },
});
</script>

<template>
    <Head :title="`Edit ${contact.full_name}`" />

    <div class="flex flex-col gap-6 p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Edit contact</h1>
            <p class="text-sm text-muted-foreground">Update {{ contact.full_name }}'s information.</p>
        </div>

        <div class="max-w-2xl rounded-lg border p-6">
            <Form :action="update(contact).url" method="put" v-slot="{ errors, processing }">
                <ContactForm
                    :contact="contact"
                    :companies="companies"
                    :errors="errors"
                    :processing="processing"
                    :cancel-href="show(contact).url"
                />
            </Form>
        </div>
    </div>
</template>
