<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/Portal/Panel.vue';

const props = defineProps({
    member: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    member_code: props.member.member_code ?? '',
    first_name: props.member.first_name ?? '',
    last_name: props.member.last_name ?? '',
    gender: props.member.gender ?? '',
    date_of_birth: props.member.date_of_birth ?? '',
    phone: props.member.phone ?? '',
    email: props.member.email ?? '',
    address: props.member.address ?? '',
    emergency_contact_name: props.member.emergency_contact_name ?? '',
    emergency_contact_phone: props.member.emergency_contact_phone ?? '',
    status: props.member.status ?? 'active',
    joined_at: props.member.joined_at ?? '',
});

function submit() {
    form.put(`/portal/members/${props.member.id}`);
}
</script>

<template>
    <AppLayout>
        <Panel eyebrow="Members" title="Edit Member">
            <form class="space-y-5" @submit.prevent="submit">
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm text-gms-text-secondary">Member code</span>
                        <input v-model="form.member_code" type="text" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                        <p v-if="form.errors.member_code" class="text-sm text-gms-error">{{ form.errors.member_code }}</p>
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm text-gms-text-secondary">Status</span>
                        <select v-model="form.status" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                        <p v-if="form.errors.status" class="text-sm text-gms-error">{{ form.errors.status }}</p>
                    </label>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm text-gms-text-secondary">First name</span>
                        <input v-model="form.first_name" type="text" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                        <p v-if="form.errors.first_name" class="text-sm text-gms-error">{{ form.errors.first_name }}</p>
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm text-gms-text-secondary">Last name</span>
                        <input v-model="form.last_name" type="text" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                        <p v-if="form.errors.last_name" class="text-sm text-gms-error">{{ form.errors.last_name }}</p>
                    </label>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm text-gms-text-secondary">Gender</span>
                        <select v-model="form.gender" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40">
                            <option value="">—</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <p v-if="form.errors.gender" class="text-sm text-gms-error">{{ form.errors.gender }}</p>
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm text-gms-text-secondary">Date of birth</span>
                        <input v-model="form.date_of_birth" type="date" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                        <p v-if="form.errors.date_of_birth" class="text-sm text-gms-error">{{ form.errors.date_of_birth }}</p>
                    </label>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm text-gms-text-secondary">Phone</span>
                        <input v-model="form.phone" type="text" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                        <p v-if="form.errors.phone" class="text-sm text-gms-error">{{ form.errors.phone }}</p>
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm text-gms-text-secondary">Email</span>
                        <input v-model="form.email" type="email" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                        <p v-if="form.errors.email" class="text-sm text-gms-error">{{ form.errors.email }}</p>
                    </label>
                </div>

                <label class="space-y-2 block">
                    <span class="text-sm text-gms-text-secondary">Address</span>
                    <textarea v-model="form.address" rows="3" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40"></textarea>
                    <p v-if="form.errors.address" class="text-sm text-gms-error">{{ form.errors.address }}</p>
                </label>

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm text-gms-text-secondary">Emergency contact name</span>
                        <input v-model="form.emergency_contact_name" type="text" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                        <p v-if="form.errors.emergency_contact_name" class="text-sm text-gms-error">{{ form.errors.emergency_contact_name }}</p>
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm text-gms-text-secondary">Emergency contact phone</span>
                        <input v-model="form.emergency_contact_phone" type="text" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                        <p v-if="form.errors.emergency_contact_phone" class="text-sm text-gms-error">{{ form.errors.emergency_contact_phone }}</p>
                    </label>
                </div>

                <label class="space-y-2 block">
                    <span class="text-sm text-gms-text-secondary">Joined at</span>
                    <input v-model="form.joined_at" type="date" class="w-full rounded-2xl border border-gms-border bg-gms-input px-4 py-3 text-gms-text outline-none ring-0 transition focus:border-gms-accent/40" />
                    <p v-if="form.errors.joined_at" class="text-sm text-gms-error">{{ form.errors.joined_at }}</p>
                </label>

                <div class="flex items-center justify-end gap-3">
                    <a
                        href="/portal/members"
                        class="rounded-2xl border border-gms-border bg-gms-surface px-5 py-3 font-medium text-gms-text-secondary transition hover:bg-gms-surface-hover hover:text-gms-text"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-2xl bg-gms-accent px-5 py-3 font-medium text-gms-text-inverse transition hover:bg-gms-accent-hover disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        Update Member
                    </button>
                </div>
            </form>
        </Panel>
    </AppLayout>
</template>
