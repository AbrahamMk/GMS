<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

defineProps({
    plans: {
        type: Array,
        default: () => [],
    },
    members: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    member_id: '',
    membership_plan_id: '',
    starts_at: '',
    auto_renew_enabled: false,
    notes: '',
});

function submit() {
    form.post('/memberships/activate', {
        preserveScroll: true,
        onSuccess: () => form.reset('member_id', 'membership_plan_id', 'starts_at', 'notes'),
    });
}
</script>

<template>
    <AppLayout>
        <div class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8 xl:col-start-3">
                <Panel eyebrow="Memberships" title="Activate membership">
                    <form class="space-y-5" @submit.prevent="submit">
                        <label class="space-y-2 block">
                            <span class="text-sm text-gms-text-secondary">Member</span>
                            <select v-model="form.member_id" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40">
                                <option value="" disabled>Select a member...</option>
                                <option v-for="member in members" :key="member.id" :value="member.id">
                                    {{ member.first_name }} {{ member.last_name }} ({{ member.member_code }})
                                </option>
                            </select>
                            <p v-if="form.errors.member_id" class="text-sm text-gms-error">{{ form.errors.member_id }}</p>
                        </label>

                        <label class="space-y-2 block">
                            <span class="text-sm text-gms-text-secondary">Membership Plan</span>
                            <select v-model="form.membership_plan_id" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40">
                                <option value="" disabled>Select a plan...</option>
                                <option v-for="plan in plans" :key="plan.id" :value="plan.id">
                                    {{ plan.name }} ({{ plan.code }}) - {{ plan.currency }} {{ plan.price }}
                                </option>
                            </select>
                            <p v-if="form.errors.membership_plan_id" class="text-sm text-gms-error">{{ form.errors.membership_plan_id }}</p>
                        </label>

                        <label class="space-y-2 block">
                            <span class="text-sm text-gms-text-secondary">Start date</span>
                            <input v-model="form.starts_at" type="date" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                            <p v-if="form.errors.starts_at" class="text-sm text-gms-error">{{ form.errors.starts_at }}</p>
                        </label>

                        <label class="flex items-center gap-3">
                            <input v-model="form.auto_renew_enabled" type="checkbox" class="h-5 w-5 rounded-lg border border-gms-border bg-gms-input text-gms-accent outline-none ring-0 transition focus:border-gms-accent/40" />
                            <span class="text-sm text-gms-text-secondary">Auto-renew enabled</span>
                        </label>

                        <label class="space-y-2 block">
                            <span class="text-sm text-gms-text-secondary">Notes</span>
                            <textarea v-model="form.notes" rows="3" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40"></textarea>
                            <p v-if="form.errors.notes" class="text-sm text-gms-error">{{ form.errors.notes }}</p>
                        </label>

                        <div class="flex items-center justify-between gap-4">
                            <a href="/portal/memberships" class="rounded-2xl border border-gms-border bg-gms-surface px-5 py-3 font-medium text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text">
                                Cancel
                            </a>
                            <button type="submit" :disabled="form.processing" class="rounded-2xl bg-gms-accent px-5 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover disabled:cursor-not-allowed disabled:opacity-60">
                                Activate Membership
                            </button>
                        </div>
                    </form>
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>
