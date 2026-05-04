<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Building2, Mail, MoreHorizontal, Phone, Search, UserPlus } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import ExportButton from '@/components/ExportButton.vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { index, create, show } from '@/routes/contacts';
import type { Contact, PaginatedData } from '@/types';

type Props = {
    contacts: PaginatedData<Contact>;
    filters: { search?: string };
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Contacts', href: index() }],
    },
});

const search = ref(props.filters.search ?? '');

let searchTimer: ReturnType<typeof setTimeout>;
let cancelSearch: (() => void) | null = null;

watch(search, (value) => {
    clearTimeout(searchTimer);   // prevent pending debounce from firing
    cancelSearch?.();            // cancel already-in-flight request
    cancelSearch = null;

    searchTimer = setTimeout(() => {
        router.get(index(), { search: value || undefined }, {
            preserveState: true,
            replace: true,
            onCancelToken: ({ cancel }) => {
                cancelSearch = cancel;
            },
        });
    }, 350);
});

function initials(contact: Contact) {
    return (contact.first_name[0] + contact.last_name[0]).toUpperCase();
}

function confirmDelete(contact: Contact) {
    if (confirm(`Delete ${contact.full_name}?`)) {
        router.delete(`/contacts/${contact.id}`);
    }
}
</script>

<template>
    <Head title="Contacts" />

    <div class="flex flex-col gap-4 p-4 sm:gap-6 sm:p-6">
        <!-- Header -->
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight sm:text-2xl">Contacts</h1>
                <p class="text-sm text-muted-foreground">{{ contacts.total }} total</p>
            </div>
            <div class="flex items-center gap-2">
                <ExportButton />
                <Button size="sm" as-child>
                    <Link :href="create()">
                        <UserPlus class="h-4 w-4 sm:mr-2" />
                        <span class="hidden sm:inline">New Contact</span>
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Search -->
        <div class="relative w-full">
            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
            <Input v-model="search" placeholder="Search contacts…" class="pl-9" />
        </div>

        <!-- List -->
        <div class="rounded-lg border divide-y">
            <div
                v-for="contact in contacts.data"
                :key="contact.id"
                class="flex items-center gap-3 px-4 py-3 hover:bg-muted/30 transition-colors"
            >
                <!-- Avatar -->
                <Link :href="show(contact)" class="shrink-0">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary">
                        {{ initials(contact) }}
                    </span>
                </Link>

                <!-- Info -->
                <div class="min-w-0 flex-1">
                    <Link :href="show(contact)" class="block hover:underline">
                        <p class="truncate font-medium leading-tight">{{ contact.full_name }}</p>
                        <p v-if="contact.job_title || contact.company" class="truncate text-xs text-muted-foreground">
                            {{ [contact.job_title, contact.company?.name].filter(Boolean).join(' · ') }}
                        </p>
                    </Link>
                    <div class="mt-1 flex flex-wrap gap-x-3 gap-y-0.5">
                        <span v-if="contact.email" class="flex items-center gap-1 text-xs text-muted-foreground min-w-0">
                            <Mail class="h-3 w-3 shrink-0" />
                            <span class="truncate">{{ contact.email }}</span>
                        </span>
                        <span v-if="contact.phone" class="flex items-center gap-1 text-xs text-muted-foreground">
                            <Phone class="h-3 w-3 shrink-0" />
                            <span>{{ contact.phone }}</span>
                        </span>
                        <span v-if="contact.company" class="flex items-center gap-1 text-xs text-muted-foreground sm:hidden">
                            <Building2 class="h-3 w-3 shrink-0" />
                            <span class="truncate">{{ contact.company.name }}</span>
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="ghost" size="icon" class="h-8 w-8 shrink-0">
                            <MoreHorizontal class="h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem as-child>
                            <Link :href="show(contact)">View</Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem as-child>
                            <Link :href="`/contacts/${contact.id}/edit`">Edit</Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem
                            class="text-destructive focus:text-destructive"
                            @click="confirmDelete(contact)"
                        >
                            Delete
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            <div v-if="contacts.data.length === 0" class="px-4 py-12 text-center text-sm text-muted-foreground">
                No contacts found.
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="contacts.last_page > 1" class="flex flex-wrap items-center justify-between gap-2 text-sm text-muted-foreground">
            <span>Showing {{ contacts.from }}–{{ contacts.to }} of {{ contacts.total }}</span>
            <div class="flex flex-wrap gap-1">
                <template v-for="link in contacts.links" :key="link.label">
                    <Button
                        v-if="link.url"
                        variant="outline"
                        size="sm"
                        :class="{ 'font-semibold border-primary text-primary': link.active }"
                        as-child
                    >
                        <Link :href="link.url" preserve-scroll v-html="link.label" />
                    </Button>
                    <Button v-else variant="ghost" size="sm" disabled v-html="link.label" />
                </template>
            </div>
        </div>
    </div>
</template>
