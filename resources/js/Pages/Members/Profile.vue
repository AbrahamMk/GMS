<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

const props = defineProps({
    user: {
        type: Object,
        default: null,
    },
    member: {
        type: Object,
        default: null,
    },
    membership: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    phone: props.user?.phone ?? '',
    member: {
        first_name: props.member?.first_name ?? '',
        last_name: props.member?.last_name ?? '',
        gender: props.member?.gender ?? '',
        date_of_birth: props.member?.date_of_birth ?? '',
        phone: props.member?.phone ?? '',
        email: props.member?.email ?? '',
        address: props.member?.address ?? '',
        emergency_contact_name: props.member?.emergency_contact_name ?? '',
        emergency_contact_phone: props.member?.emergency_contact_phone ?? '',
        photo_path: props.member?.photo_path ?? '',
    },
});

const displayName = computed(() => `${form.member.first_name} ${form.member.last_name}`.trim());

const submit = () => {
    form.put('/portal/profile', {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8">
                <Panel eyebrow="Self service" title="Profile management">
                    <form class="space-y-5" @submit.prevent="submit">
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Full name</span>
                                <input v-model="form.name" type="text" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                                <p v-if="form.errors.name" class="text-sm text-gms-error">{{ form.errors.name }}</p>
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Email</span>
                                <input v-model="form.email" type="email" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                                <p v-if="form.errors.email" class="text-sm text-gms-error">{{ form.errors.email }}</p>
                            </label>
                        </div>

                        <label class="space-y-2 block">
                            <span class="text-sm text-gms-text-secondary">Phone</span>
                            <input v-model="form.phone" type="text" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                        </label>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">First name</span>
                                <input v-model="form.member.first_name" type="text" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Last name</span>
                                <input v-model="form.member.last_name" type="text" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                            </label>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Gender</span>
                                <input v-model="form.member.gender" type="text" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                            </label>
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Date of birth</span>
                                <input v-model="form.member.date_of_birth" type="date" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                            </label>
                        </div>

                        <label class="space-y-2 block">
                            <span class="text-sm text-gms-text-secondary">Address</span>
                            <textarea v-model="form.member.address" rows="3" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40"></textarea>
                        </label>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Emergency contact name</span>
                                <input v-model="form.member.emergency_contact_name" type="text" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                            </label>
                            <label class="space-y-2">
                                <span class="text-sm text-gms-text-secondary">Emergency contact phone</span>
                                <input v-model="form.member.emergency_contact_phone" type="text" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                            </label>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm text-gms-text-muted">Current display: {{ displayName || 'Unnamed member' }}</p>
                            <button type="submit" :disabled="form.processing" class="rounded-2xl bg-gms-accent px-5 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover disabled:cursor-not-allowed disabled:opacity-60">
                                Save profile
                            </button>
                        </div>
                    </form>
                </Panel>
            </div>

            <div class="xl:col-span-4">
                <Panel eyebrow="Identity" title="Account snapshot">
                    <div class="space-y-4">
                        <div class="rounded-3xl border border-gms-border bg-gms-surface p-5">
                            <p class="text-xs uppercase tracking-[0.35em] text-gms-text-muted">Member code</p>
                            <p class="mt-2 text-2xl font-semibold text-gms-text">{{ member?.member_code ?? 'Pending' }}</p>
                        </div>

                        <div class="rounded-3xl border border-gms-border bg-gms-surface p-5">
                            <p class="text-xs uppercase tracking-[0.35em] text-gms-text-muted">Membership</p>
                            <p class="mt-2 text-lg font-semibold text-gms-text">{{ membership?.plan?.name ?? 'No active membership' }}</p>
                            <p class="mt-1 text-sm text-gms-text-muted">{{ membership?.starts_at ?? '-' }} to {{ membership?.ends_at ?? '-' }}</p>
                        </div>

                        <div class="rounded-3xl border border-gms-border bg-gms-surface p-5">
                            <p class="text-xs uppercase tracking-[0.35em] text-gms-text-muted">Status</p>
                            <p class="mt-2 text-lg font-semibold text-gms-text">{{ member?.status ?? 'unknown' }}</p>
                            <p class="mt-1 text-sm text-gms-text-muted">{{ membership?.remaining_visits ?? 'Unlimited' }} visits remaining</p>
                        </div>
                    </div>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>
