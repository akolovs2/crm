<script setup lang="ts">
import { ref } from 'vue';
import { Download } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { contacts as exportContacts, status as exportStatus, download as exportDownload } from '@/routes/exports';

type State = 'idle' | 'pending' | 'failed';

const state = ref<State>('idle');
const errorMessage = ref('');

let pollInterval: ReturnType<typeof setInterval> | null = null;

function csrfToken(): string {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
}

function stopPolling() {
    if (pollInterval !== null) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
}

async function startExport() {
    if (state.value === 'pending') return;

    state.value = 'pending';
    errorMessage.value = '';

    let exportId: number;

    try {
        const res = await fetch(exportContacts.url(), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-XSRF-TOKEN': csrfToken(),
            },
        });

        if (!res.ok) throw new Error('Failed to start export.');

        const data = await res.json();
        exportId = data.id;
    } catch {
        state.value = 'failed';
        errorMessage.value = 'Could not start export. Please try again.';
        return;
    }

    pollInterval = setInterval(async () => {
        try {
            const res = await fetch(exportStatus.url(exportId), {
                headers: { 'Accept': 'application/json' },
            });

            if (!res.ok) throw new Error();

            const data = await res.json();

            if (data.status === 'done') {
                stopPolling();
                state.value = 'idle';
                window.location.href = exportDownload.url(exportId);
            } else if (data.status === 'failed') {
                stopPolling();
                state.value = 'failed';
                errorMessage.value = 'Export failed. Please try again.';
            }
        } catch {
            stopPolling();
            state.value = 'failed';
            errorMessage.value = 'Lost connection while waiting. Please try again.';
        }
    }, 2000);
}
</script>

<template>
    <div class="flex flex-col items-end gap-1">
        <Button
            variant="outline"
            size="sm"
            :disabled="state === 'pending'"
            @click="startExport"
        >
            <Spinner v-if="state === 'pending'" class="mr-2" />
            <Download v-else class="h-4 w-4 sm:mr-2" />
            <span class="hidden sm:inline">
                {{ state === 'pending' ? 'Preparing…' : 'Export CSV' }}
            </span>
        </Button>
        <p v-if="state === 'failed'" class="text-xs text-destructive">
            {{ errorMessage }}
        </p>
    </div>
</template>
