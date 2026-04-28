<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Building2, Mail, Pencil, Phone, Trash2, User } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { index } from '@/routes/contacts';
import type { Contact } from '@/types';

const props = defineProps<{ contact: Contact }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Contacts', href: '/contacts' },
            { title: 'Contact', href: '#' },
        ],
    },
});

const STAGE_COLORS: Record<string, string> = {
    lead: 'bg-slate-100 text-slate-700',
    qualified: 'bg-blue-100 text-blue-700',
    proposal: 'bg-yellow-100 text-yellow-700',
    negotiation: 'bg-orange-100 text-orange-700',
    won: 'bg-green-100 text-green-700',
    lost: 'bg-red-100 text-red-700',
};

const ACTIVITY_ICONS: Record<string, string> = {
    call: '📞',
    email: '✉️',
    meeting: '📅',
    task: '✓',
    note: '📝',
};

function confirmDelete() {
    if (confirm(`Delete ${props.contact.full_name}?`)) {
        router.delete(`/contacts/${props.contact.id}`, {
            onSuccess: () => router.visit(index()),
        });
    }
}

function initials(contact: Contact) {
    return (contact.first_name[0] + contact.last_name[0]).toUpperCase();
}
</script>

<template>
    <Head :title="contact.full_name" />

    <div class="flex flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex min-w-0 items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary/10 text-lg font-bold text-primary">
                    {{ initials(contact) }}
                </div>
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">{{ contact.full_name }}</h1>
                    <div class="mt-1 flex items-center gap-2 text-sm text-muted-foreground">
                        <span v-if="contact.job_title">{{ contact.job_title }}</span>
                        <span v-if="contact.job_title && contact.company">·</span>
                        <Link v-if="contact.company" :href="`/companies/${contact.company.id}`" class="hover:underline">
                            {{ contact.company.name }}
                        </Link>
                    </div>
                    <div v-if="contact.tags?.length" class="mt-2 flex flex-wrap gap-1">
                        <span
                            v-for="tag in contact.tags"
                            :key="tag.id"
                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium text-white"
                            :style="{ backgroundColor: tag.color }"
                        >
                            {{ tag.name }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex shrink-0 gap-2">
                <Button variant="outline" size="sm" as-child>
                    <Link :href="`/contacts/${contact.id}/edit`">
                        <Pencil class="mr-1.5 h-3.5 w-3.5" />
                        Edit
                    </Link>
                </Button>
                <Button variant="destructive" size="sm" @click="confirmDelete">
                    <Trash2 class="mr-1.5 h-3.5 w-3.5" />
                    Delete
                </Button>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Details card -->
            <div class="rounded-lg border p-5">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Details</h2>
                <ul class="space-y-3 text-sm">
                    <li class="flex min-w-0 items-center gap-3">
                        <Mail class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <a v-if="contact.email" :href="`mailto:${contact.email}`" class="min-w-0 truncate hover:underline">{{ contact.email }}</a>
                        <span v-else class="text-muted-foreground">—</span>
                    </li>
                    <li class="flex min-w-0 items-center gap-3">
                        <Phone class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <a v-if="contact.phone" :href="`tel:${contact.phone}`" class="min-w-0 truncate hover:underline">{{ contact.phone }}</a>
                        <span v-else class="text-muted-foreground">—</span>
                    </li>
                    <li class="flex min-w-0 items-center gap-3">
                        <Building2 class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span v-if="contact.company" class="min-w-0 truncate">{{ contact.company.name }}</span>
                        <span v-else class="text-muted-foreground">—</span>
                    </li>
                    <li class="flex min-w-0 items-center gap-3">
                        <User class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span class="text-muted-foreground">Owner:</span>
                        <span class="min-w-0 truncate">{{ contact.owner?.name }}</span>
                    </li>
                </ul>
            </div>

            <!-- Deals & Activities -->
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-lg border p-5">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                        Deals ({{ contact.deals?.length ?? 0 }})
                    </h2>
                    <ul v-if="contact.deals?.length" class="divide-y">
                        <li v-for="deal in contact.deals" :key="deal.id" class="flex items-center justify-between py-2.5">
                            <div>
                                <p class="font-medium">{{ deal.name }}</p>
                                <p class="text-xs text-muted-foreground">
                                    {{ new Intl.NumberFormat('en-US', { style: 'currency', currency: deal.currency }).format(Number(deal.value)) }}
                                </p>
                            </div>
                            <Badge :class="['text-xs', STAGE_COLORS[deal.stage]]">{{ deal.stage }}</Badge>
                        </li>
                    </ul>
                    <p v-else class="text-sm text-muted-foreground">No deals yet.</p>
                </div>

                <div class="rounded-lg border p-5">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                        Activities ({{ contact.activities?.length ?? 0 }})
                    </h2>
                    <ul v-if="contact.activities?.length" class="divide-y">
                        <li v-for="activity in contact.activities" :key="activity.id" class="flex items-start gap-3 py-2.5">
                            <span class="mt-0.5 text-base">{{ ACTIVITY_ICONS[activity.type] }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium leading-tight">{{ activity.subject }}</p>
                                <p class="text-xs text-muted-foreground">
                                    {{ activity.type }}
                                    <template v-if="activity.due_at">
                                        · {{ new Date(activity.due_at).toLocaleDateString() }}
                                    </template>
                                </p>
                            </div>
                            <span v-if="activity.completed_at" class="text-xs text-green-600 font-medium">Done</span>
                        </li>
                    </ul>
                    <p v-else class="text-sm text-muted-foreground">No activities yet.</p>
                </div>
            </div>
        </div>
    </div>
</template>
