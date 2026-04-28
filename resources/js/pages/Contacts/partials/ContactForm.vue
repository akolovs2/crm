<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Company, Contact } from '@/types';

defineProps<{
    contact?: Contact;
    companies: Pick<Company, 'id' | 'name'>[];
    errors: Record<string, string>;
    processing: boolean;
    cancelHref: string;
}>();
</script>

<template>
    <div class="grid gap-5 sm:grid-cols-2">
        <div class="grid gap-2">
            <Label for="first_name">First name <span class="text-destructive">*</span></Label>
            <Input
                id="first_name"
                name="first_name"
                :default-value="contact?.first_name"
                placeholder="Jane"
                required
            />
            <InputError :message="errors.first_name" />
        </div>

        <div class="grid gap-2">
            <Label for="last_name">Last name <span class="text-destructive">*</span></Label>
            <Input
                id="last_name"
                name="last_name"
                :default-value="contact?.last_name"
                placeholder="Doe"
                required
            />
            <InputError :message="errors.last_name" />
        </div>

        <div class="grid gap-2">
            <Label for="email">Email</Label>
            <Input
                id="email"
                type="email"
                name="email"
                :default-value="contact?.email ?? undefined"
                placeholder="jane@example.com"
            />
            <InputError :message="errors.email" />
        </div>

        <div class="grid gap-2">
            <Label for="phone">Phone</Label>
            <Input
                id="phone"
                name="phone"
                :default-value="contact?.phone ?? undefined"
                placeholder="+1 555 000 0000"
            />
            <InputError :message="errors.phone" />
        </div>

        <div class="grid gap-2">
            <Label for="job_title">Job title</Label>
            <Input
                id="job_title"
                name="job_title"
                :default-value="contact?.job_title ?? undefined"
                placeholder="Sales Manager"
            />
            <InputError :message="errors.job_title" />
        </div>

        <div class="grid gap-2">
            <Label for="company_id">Company</Label>
            <Select name="company_id" :default-value="contact?.company_id?.toString() ?? 'null'">
                <SelectTrigger id="company_id">
                    <SelectValue placeholder="Select company…" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="null">No company</SelectItem>
                    <SelectItem
                        v-for="company in companies"
                        :key="company.id"
                        :value="company.id.toString()"
                    >
                        {{ company.name }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <InputError :message="errors.company_id" />
        </div>
    </div>

    <div class="mt-6 flex items-center gap-3">
        <Button type="submit" :disabled="processing">
            {{ processing ? 'Saving…' : 'Save contact' }}
        </Button>
        <Button variant="outline" as-child>
            <a :href="cancelHref">Cancel</a>
        </Button>
    </div>
</template>
