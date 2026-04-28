<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, PlusCircle, Trash2 } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { index } from '@/routes/users';
import type { User } from '@/types';

defineProps<{ users: User[] }>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Users', href: '/users' }],
    },
});

function confirmDelete(user: User) {
    if (confirm(`Delete ${user.name}?`)) {
        router.delete(`/users/${user.id}`, {
            onSuccess: () => router.visit(index().url),
        });
    }
}
</script>

<template>
    <Head title="Users" />

    <div class="flex flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Users</h1>
                <p class="mt-1 text-sm text-muted-foreground">{{ users.length }} total</p>
            </div>
            <Button as-child size="sm">
                <Link href="/users/create">
                    <PlusCircle class="mr-1.5 h-4 w-4" />
                    Add user
                </Link>
            </Button>
        </div>

        <div class="rounded-lg border">
            <ul class="divide-y">
                <li v-for="user in users" :key="user.id" class="flex items-center justify-between gap-4 px-5 py-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="font-medium truncate">{{ user.name }}</p>
                            <Badge v-if="user.is_admin" class="bg-primary/10 text-primary text-xs">Admin</Badge>
                        </div>
                        <p class="text-sm text-muted-foreground truncate">{{ user.email }}</p>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        <Button variant="outline" size="sm" as-child>
                            <Link :href="`/users/${user.id}/edit`">
                                <Pencil class="h-3.5 w-3.5" />
                            </Link>
                        </Button>
                        <Button variant="destructive" size="sm" @click="confirmDelete(user)">
                            <Trash2 class="h-3.5 w-3.5" />
                        </Button>
                    </div>
                </li>
            </ul>
            <p v-if="!users.length" class="p-6 text-center text-sm text-muted-foreground">No users yet.</p>
        </div>
    </div>
</template>
