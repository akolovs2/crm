<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { User } from '@/types';

defineProps<{
    user?: User;
    errors: Record<string, string>;
    processing: boolean;
    cancelHref: string;
}>();
</script>

<template>
    <div class="grid gap-5 sm:grid-cols-2">
        <div class="grid gap-2">
            <Label for="name">Name <span class="text-destructive">*</span></Label>
            <Input id="name" name="name" :default-value="user?.name" placeholder="Jane Smith" required />
            <InputError :message="errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="email">Email <span class="text-destructive">*</span></Label>
            <Input id="email" type="email" name="email" :default-value="user?.email" placeholder="jane@example.com" required />
            <InputError :message="errors.email" />
        </div>

        <div class="grid gap-2">
            <Label for="password">
                Password
                <span v-if="!user" class="text-destructive">*</span>
                <span v-else class="text-xs text-muted-foreground">(leave blank to keep current)</span>
            </Label>
            <Input id="password" type="password" name="password" autocomplete="new-password" />
            <InputError :message="errors.password" />
        </div>

        <div class="grid gap-2">
            <Label for="password_confirmation">Confirm password</Label>
            <Input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" />
        </div>

        <div class="col-span-full flex items-center gap-3">
            <input
                id="is_admin"
                type="checkbox"
                name="is_admin"
                :checked="user?.is_admin"
                value="1"
                class="h-4 w-4 rounded border-input accent-primary"
            />
            <Label for="is_admin" class="cursor-pointer font-normal">Administrator — can manage users</Label>
        </div>
    </div>

    <div class="mt-6 flex items-center gap-3">
        <Button type="submit" :disabled="processing">
            {{ processing ? 'Saving…' : 'Save user' }}
        </Button>
        <Button variant="outline" as-child>
            <a :href="cancelHref">Cancel</a>
        </Button>
    </div>
</template>
