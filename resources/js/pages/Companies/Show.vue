<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Globe, Mail, MapPin, Pencil, Phone, Trash2, User } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { index } from '@/routes/companies';
import type { Company } from '@/types';

const props = defineProps<{ company: Company }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Companies', href: '/companies' },
            { title: 'Company', href: '#' },
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

function confirmDelete() {
    if (confirm(`Delete ${props.company.name}?`)) {
        router.delete(`/companies/${props.company.id}`, {
            onSuccess: () => router.visit(index()),
        });
    }
}

function initials(company: Company) {
    return company.name.slice(0, 2).toUpperCase();
}
</script>

<template>
    <Head :title="company.name" />

    <div class="flex flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex min-w-0 items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary/10 text-lg font-bold text-primary">
                    {{ initials(company) }}
                </div>
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">{{ company.name }}</h1>
                    <p v-if="company.industry" class="mt-0.5 text-sm text-muted-foreground">{{ company.industry }}</p>
                    <div v-if="company.tags?.length" class="mt-2 flex flex-wrap gap-1">
                        <span
                            v-for="tag in company.tags"
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
                    <Link :href="`/companies/${company.id}/edit`">
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
                        <a v-if="company.email" :href="`mailto:${company.email}`" class="break-all hover:underline">{{ company.email }}</a>
                        <span v-else class="text-muted-foreground">—</span>
                    </li>
                    <li class="flex min-w-0 items-center gap-3">
                        <Phone class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <a v-if="company.phone" :href="`tel:${company.phone}`" class="min-w-0 truncate hover:underline">{{ company.phone }}</a>
                        <span v-else class="text-muted-foreground">—</span>
                    </li>
                    <li class="flex min-w-0 items-center gap-3">
                        <Globe class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <a v-if="company.website" :href="company.website" target="_blank" rel="noopener" class="break-all hover:underline">{{ company.website }}</a>
                        <span v-else class="text-muted-foreground">—</span>
                    </li>
                    <li class="flex min-w-0 items-start gap-3">
                        <MapPin class="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground" />
                        <span v-if="company.city || company.country" class="break-all">
                            {{ [company.address, company.city, company.country].filter(Boolean).join(', ') }}
                        </span>
                        <span v-else class="text-muted-foreground">—</span>
                    </li>
                    <li class="flex min-w-0 items-center gap-3">
                        <User class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span class="text-muted-foreground">Owner:</span>
                        <span class="min-w-0 truncate">{{ company.owner?.name }}</span>
                    </li>
                </ul>
            </div>

            <!-- Contacts & Deals -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Contacts -->
                <div class="rounded-lg border p-5">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                        Contacts ({{ company.contacts?.length ?? 0 }})
                    </h2>
                    <ul v-if="company.contacts?.length" class="divide-y">
                        <li v-for="contact in company.contacts" :key="contact.id" class="flex items-center justify-between py-2.5">
                            <div>
                                <Link :href="`/contacts/${contact.id}`" class="font-medium hover:underline">
                                    {{ contact.full_name }}
                                </Link>
                                <p v-if="contact.job_title" class="text-xs text-muted-foreground">{{ contact.job_title }}</p>
                            </div>
                            <span v-if="contact.email" class="text-xs text-muted-foreground hidden sm:block">{{ contact.email }}</span>
                        </li>
                    </ul>
                    <p v-else class="text-sm text-muted-foreground">No contacts yet.</p>
                </div>

                <!-- Deals -->
                <div class="rounded-lg border p-5">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                        Deals ({{ company.deals?.length ?? 0 }})
                    </h2>
                    <ul v-if="company.deals?.length" class="divide-y">
                        <li v-for="deal in company.deals" :key="deal.id" class="flex items-center justify-between py-2.5">
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
            </div>
        </div>
    </div>
</template>
