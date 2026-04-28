<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Company } from '@/types';

defineProps<{
    company?: Company;
    errors: Record<string, string>;
    processing: boolean;
    cancelHref: string;
}>();
</script>

<template>
    <div class="grid gap-5 sm:grid-cols-2">
        <div class="grid gap-2">
            <Label for="name">Company name <span class="text-destructive">*</span></Label>
            <Input id="name" name="name" :default-value="company?.name" placeholder="Acme Inc." required />
            <InputError :message="errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="industry">Industry</Label>
            <Input id="industry" name="industry" :default-value="company?.industry ?? undefined" placeholder="Technology" />
            <InputError :message="errors.industry" />
        </div>

        <div class="grid gap-2">
            <Label for="email">Email</Label>
            <Input id="email" type="email" name="email" :default-value="company?.email ?? undefined" placeholder="info@acme.com" />
            <InputError :message="errors.email" />
        </div>

        <div class="grid gap-2">
            <Label for="phone">Phone</Label>
            <Input id="phone" name="phone" :default-value="company?.phone ?? undefined" placeholder="+1 555 000 0000" />
            <InputError :message="errors.phone" />
        </div>

        <div class="grid gap-2">
            <Label for="website">Website</Label>
            <Input id="website" name="website" :default-value="company?.website ?? undefined" placeholder="https://acme.com" />
            <InputError :message="errors.website" />
        </div>

        <div class="grid gap-2">
            <Label for="address">Address</Label>
            <Input id="address" name="address" :default-value="company?.address ?? undefined" placeholder="123 Main St" />
            <InputError :message="errors.address" />
        </div>

        <div class="grid gap-2">
            <Label for="city">City</Label>
            <Input id="city" name="city" :default-value="company?.city ?? undefined" placeholder="New York" />
            <InputError :message="errors.city" />
        </div>

        <div class="grid gap-2">
            <Label for="country">Country</Label>
            <Input id="country" name="country" :default-value="company?.country ?? undefined" placeholder="United States" />
            <InputError :message="errors.country" />
        </div>
    </div>

    <div class="mt-6 flex items-center gap-3">
        <Button type="submit" :disabled="processing">
            {{ processing ? 'Saving…' : 'Save company' }}
        </Button>
        <Button variant="outline" as-child>
            <a :href="cancelHref">Cancel</a>
        </Button>
    </div>
</template>
