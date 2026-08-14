<script setup>
import MemberLayout from '@/Layouts/MemberLayout.vue';
import Button from '@/Components/ui/button/Button.vue';
import { useForm } from '@inertiajs/vue3';
import { User, Phone, Mail, MapPin, Save, CheckCircle2 } from '@lucide/vue';
import { ref } from 'vue';

const props = defineProps({
    member: { type: Object, default: () => ({}) },
    user: { type: Object, default: () => ({}) }
});

const profileForm = useForm({
    first_name: props.member?.first_name ?? '',
    last_name: props.member?.last_name ?? '',
    phone: props.member?.phone ?? '',
    address: props.member?.address ?? '',
});

const successMsg = ref('');

const updateProfile = () => {
    profileForm.put('/member/profile', {
        preserveScroll: true,
        onSuccess: () => {
            successMsg.value = 'Profile updated successfully!';
            setTimeout(() => successMsg.value = '', 4000);
        }
    });
};
</script>

<template>
    <MemberLayout>
        <div class="space-y-6 max-w-2xl mx-auto pb-10">
            <!-- Header -->
            <div
                v-motion
                :initial="{ opacity: 0, y: -15 }"
                :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25 } }"
            >
                <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#FF6B35] bg-gms-surface inline-block px-3 py-1 rounded-full mb-3 border border-gms-border">
                    Account
                </span>
                <h1 class="text-3xl font-black tracking-tight text-gms-text">Personal Profile</h1>
                <p class="text-gms-text-muted mt-1 font-medium">Manage your personal information and contact details.</p>
            </div>

            <!-- Profile Form Card -->
            <div
                v-motion
                :initial="{ opacity: 0, y: 20 }"
                :enter="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 250, damping: 25, delay: 100 } }"
                class="rounded-3xl border border-gms-border bg-gms-surface p-6 shadow-sm space-y-6"
            >
                <!-- Flash Success Notification -->
                <div v-if="successMsg" class="flex items-center gap-2 p-4 bg-[#d1fae5] border border-[#a7f3d0] rounded-2xl text-[#059669] text-sm font-semibold transition-all">
                    <CheckCircle2 class="h-5 w-5 shrink-0" />
                    <span>{{ successMsg }}</span>
                </div>

                <form @submit.prevent="updateProfile" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-gms-text-muted mb-1.5 block">First Name</label>
                            <input
                                v-model="profileForm.first_name"
                                type="text"
                                placeholder="First Name"
                                class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-3 text-gms-text font-semibold focus:outline-none focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 transition"
                            />
                            <p v-if="profileForm.errors.first_name" class="text-xs text-[#e11d48] mt-1">{{ profileForm.errors.first_name }}</p>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-gms-text-muted mb-1.5 block">Last Name</label>
                            <input
                                v-model="profileForm.last_name"
                                type="text"
                                placeholder="Last Name"
                                class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-3 text-gms-text font-semibold focus:outline-none focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 transition"
                            />
                            <p v-if="profileForm.errors.last_name" class="text-xs text-[#e11d48] mt-1">{{ profileForm.errors.last_name }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-gms-text-muted mb-1.5 block">Phone Number</label>
                        <input
                            v-model="profileForm.phone"
                            type="text"
                            placeholder="Phone Number"
                            class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-3 text-gms-text font-semibold focus:outline-none focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 transition"
                        />
                        <p v-if="profileForm.errors.phone" class="text-xs text-[#e11d48] mt-1">{{ profileForm.errors.phone }}</p>
                    </div>

                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-gms-text-muted mb-1.5 block">Address</label>
                        <input
                            v-model="profileForm.address"
                            type="text"
                            placeholder="Address"
                            class="w-full rounded-xl border border-gms-border bg-gms-bg px-4 py-3 text-gms-text font-semibold focus:outline-none focus:border-[#FF6B35] focus:ring-2 focus:ring-[#FF6B35]/20 transition"
                        />
                        <p v-if="profileForm.errors.address" class="text-xs text-[#e11d48] mt-1">{{ profileForm.errors.address }}</p>
                    </div>

                    <div class="pt-4">
                        <button
                            type="submit"
                            :disabled="profileForm.processing"
                            class="w-full inline-flex items-center justify-center gap-2 bg-[#FF6B35] text-[#111111] font-black rounded-xl py-3.5 hover:bg-[#e55a28] transition-all shadow-[0_4px_14px_rgba(184,245,0,0.25)] hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-60"
                        >
                            <Save class="h-4 w-4" />
                            {{ profileForm.processing ? 'Saving changes…' : 'Save Profile Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MemberLayout>
</template>

