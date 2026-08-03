<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

defineProps({
    members: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
});

function deleteMember(member) {
    if (confirm(`Delete member "${member.first_name} ${member.last_name}"?`)) {
        router.delete(`/portal/members/${member.id}`, {
            preserveScroll: true,
            onSuccess: () => router.visit('/portal/members', { preserveState: false }),
        });
    }
}

function onSearch(value) {
    router.get('/portal/members', { search: value || undefined }, {
        preserveState: true,
        replace: true,
    });
}
</script>

<template>
    <AppLayout>
        <Panel eyebrow="Members" title="Member directory">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <input
                    type="text"
                    placeholder="Search by name, code, phone or email…"
                    :value="filters.search"
                    @input="onSearch($event.target.value)"
                    class="w-full max-w-md rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition placeholder:text-gms-text-muted focus:border-gms-accent/40"
                />
                <a
                    href="/portal/members/create"
                    class="inline-flex items-center gap-2 rounded-2xl bg-gms-accent px-5 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Add Member
                </a>
            </div>

            <div class="overflow-hidden rounded-3xl border border-gms-border">
                <table class="min-w-full divide-y divide-gms-border text-left text-sm">
                    <thead class="bg-gms-surface text-gms-text-secondary">
                        <tr>
                            <th class="px-4 py-3 font-medium">Member Code</th>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Phone</th>
                            <th class="px-4 py-3 font-medium">Email</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Joined</th>
                            <th class="px-4 py-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gms-border">
                        <tr v-for="member in members.data" :key="member.id" class="bg-gms-elevated">
                            <td class="px-4 py-3 font-mono text-sm text-gms-text">{{ member.member_code }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gms-text">{{ member.first_name }} {{ member.last_name }}</p>
                            </td>
                            <td class="px-4 py-3 text-gms-text-secondary">{{ member.phone ?? '—' }}</td>
                            <td class="px-4 py-3 text-gms-text-secondary">{{ member.email ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="rounded-full border px-3 py-1 text-xs"
                                    :class="{
                                        'border-gms-success-border bg-gms-success-surface text-gms-success': member.status === 'active',
                                        'border-gms-warning-border bg-gms-warning-surface text-gms-warning': member.status === 'inactive',
                                        'border-gms-error-border bg-gms-error-surface text-gms-error': member.status === 'suspended',
                                    }"
                                >
                                    {{ member.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gms-text-muted">{{ member.joined_at ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a
                                        :href="`/portal/members/${member.id}`"
                                        class="rounded-xl border border-gms-border bg-gms-surface px-3 py-1.5 text-sm text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text"
                                    >
                                        View
                                    </a>
                                    <a
                                        :href="`/portal/members/${member.id}/edit`"
                                        class="rounded-xl border border-gms-border bg-gms-surface px-3 py-1.5 text-sm text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text"
                                    >
                                        Edit
                                    </a>
                                    <button
                                        type="button"
                                        class="rounded-xl border border-gms-error-border bg-gms-error-surface px-3 py-1.5 text-sm text-gms-error transition hover:bg-gms-error/10"
                                        @click="deleteMember(member)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="members.data.length === 0">
                            <td colspan="7" class="px-4 py-8 text-center text-gms-text-muted">No members found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="members.links?.length > 1" class="mt-5 flex flex-wrap items-center justify-center gap-2">
                <template v-for="(link, i) in members.links" :key="i">
                    <a
                        v-if="link.url"
                        :href="link.url"
                        class="rounded-xl border px-3 py-1.5 text-sm transition"
                        :class="link.active
                            ? 'border-gms-accent/40 bg-gms-accent/15 text-gms-accent-soft'
                            : 'border-gms-border bg-gms-surface text-gms-text-secondary hover:bg-gms-surface-hover hover:text-gms-text'"
                        v-html="link.label"
                    />
                    <span
                        v-else
                        class="rounded-xl border border-gms-border bg-gms-surface px-3 py-1.5 text-sm text-gms-text-muted"
                        v-html="link.label"
                    />
                </template>
            </div>
        </Panel>
    </AppLayout>
</template>
