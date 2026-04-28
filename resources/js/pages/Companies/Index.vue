<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Building2, Globe, Mail, MoreHorizontal, Phone, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { index, create, show } from '@/routes/companies';
import type { Company, PaginatedData } from '@/types';

type Props = {
    companies: PaginatedData<Company>;
    filters: { search?: string };
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Companies', href: index() }],
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

function initials(company: Company) {
    return (company.name[0] + company.name[1]).toUpperCase();
}

function confirmDelete(company: Company) {
    if (confirm(`Delete ${company.name}?`)) {
        router.delete(`/companies/${company.id}`);
    }
}
</script>

<template>
    <Head title="Companies" />

    <div class="flex flex-col gap-4 p-4 sm:gap-6 sm:p-6">
        <!-- Header -->
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight sm:text-2xl">Companies</h1>
                <p class="text-sm text-muted-foreground">{{ companies.total }} total</p>
            </div>
            <Button size="sm" as-child>
                <Link :href="create()">
                    <Building2 class="h-4 w-4 sm:mr-2" />
                    <span class="hidden sm:inline">New Company</span>
                </Link>
            </Button>
        </div>

        <!-- Search -->
        <div class="relative w-full">
            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
            <Input v-model="search" placeholder="Search companies…" class="pl-9" />
        </div>

        <!-- List -->
        <div class="rounded-lg border divide-y">
            <div
                v-for="company in companies.data"
                :key="company.id"
                class="flex items-center gap-3 px-4 py-3 hover:bg-muted/30 transition-colors"
            >
                <!-- Avatar -->
                <Link :href="show(company)" class="shrink-0">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary">
                        {{ initials(company) }}
                    </span>
                </Link>

                <!-- Info -->
                <div class="min-w-0 flex-1">
                    <Link :href="show(company)" class="block hover:underline">
                        <p class="truncate font-medium leading-tight">{{ company.name }}</p>
                        <p v-if="company.industry" class="truncate text-xs text-muted-foreground">
                            {{ company.industry }}
                        </p>
                    </Link>
                    <div class="mt-1 flex flex-wrap gap-x-3 gap-y-0.5">
                        <span v-if="company.email" class="flex items-center gap-1 text-xs text-muted-foreground min-w-0">
                            <Mail class="h-3 w-3 shrink-0" />
                            <span class="truncate">{{ company.email }}</span>
                        </span>
                        <span v-if="company.phone" class="flex items-center gap-1 text-xs text-muted-foreground">
                            <Phone class="h-3 w-3 shrink-0" />
                            <span>{{ company.phone }}</span>
                        </span>
                        <span v-if="company.website" class="flex items-center gap-1 text-xs text-muted-foreground sm:hidden">
                            <Globe class="h-3 w-3 shrink-0" />
                            <span class="truncate">{{ company.website }}</span>
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
                            <Link :href="show(company)">View</Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem as-child>
                            <Link :href="`/companies/${company.id}/edit`">Edit</Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem
                            class="text-destructive focus:text-destructive"
                            @click="confirmDelete(company)"
                        >
                            Delete
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            <div v-if="companies.data.length === 0" class="px-4 py-12 text-center text-sm text-muted-foreground">
                No companies found.
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="companies.last_page > 1" class="flex flex-wrap items-center justify-between gap-2 text-sm text-muted-foreground">
            <span>Showing {{ companies.from }}–{{ companies.to }} of {{ companies.total }}</span>
            <div class="flex flex-wrap gap-1">
                <template v-for="link in companies.links" :key="link.label">
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
