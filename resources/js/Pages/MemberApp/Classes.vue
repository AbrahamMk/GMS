<script setup>
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MemberLayout from '@/Layouts/MemberLayout.vue';
import { Search, Filter, Clock, MapPin, Users, CalendarCheck, Loader2 } from '@lucide/vue';

const props = defineProps({
    classes: { type: Array, default: () => [] },
    member: { type: Object, default: null },
});

const search = ref('');
const selectedDay = ref(0);

const days = computed(() => {
    const result = [];
    for (let i = 0; i < 7; i++) {
        const d = new Date();
        d.setDate(d.getDate() + i);
        result.push({
            label: i === 0 ? 'Today' : d.toLocaleDateString('en-US', { weekday: 'short' }),
            day: d.getDate(),
            date: d.toISOString().split('T')[0],
        });
    }
    return result;
});

const filteredClasses = computed(() => {
    let list = props.classes || [];

    // Filter by selected day
    const selectedDate = days.value[selectedDay.value]?.date;
    if (selectedDate) {
        list = list.filter(c => {
            if (!c.starts_at) return true;
            return c.starts_at.startsWith(selectedDate) || new Date(c.starts_at).toISOString().startsWith(selectedDate);
        });
    }

    // Filter by search
    if (search.value.trim()) {
        const q = search.value.toLowerCase();
        list = list.filter(c =>
            c.title?.toLowerCase().includes(q) ||
            c.trainer?.toLowerCase().includes(q)
        );
    }
    return list;
});

const formatTime = (dateStr) => {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
};

const formatDuration = (start, end) => {
    if (!start || !end) return '—';
    const diff = (new Date(end) - new Date(start)) / 60000;
    return diff >= 60 ? `${Math.round(diff / 60)}h ${diff % 60 > 0 ? `${diff % 60}m` : ''}`.trim() : `${diff}m`;
};

const bookingForm = useForm({ class_session_id: null });

const bookClass = (sessionId) => {
    bookingForm.class_session_id = sessionId;
    bookingForm.post('/portal/book-class', {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Book Classes" />

    <MemberLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            
            <!-- Header & Search -->
            <div class="sticky top-[60px] md:top-0 bg-gms-bg/90 backdrop-blur-md pt-2 pb-4 z-40 -mx-4 px-4 md:mx-0 md:px-0 transition-colors">
                <h1 class="text-3xl font-black text-gms-text mb-4" v-motion-slide-visible-top>Schedule</h1>
                
                <div class="flex gap-3" v-motion-fade-visible>
                    <div class="relative flex-1">
                        <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gms-text-muted" />
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Find a class or trainer..." 
                            class="w-full bg-gms-surface border border-gms-border text-gms-text rounded-xl py-3 pl-12 pr-4 focus:outline-none focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] transition-all"
                        >
                    </div>
                </div>
                
                <!-- Date scroller -->
                <div class="flex gap-3 mt-6 overflow-x-auto pb-2 scrollbar-hide snap-x" v-motion-fade-visible>
                    <div 
                        v-for="(day, i) in days" :key="i"
                        @click="selectedDay = i"
                        class="min-w-[70px] rounded-2xl p-3 flex flex-col items-center justify-center snap-center cursor-pointer transition-all select-none"
                        :class="selectedDay === i 
                            ? 'bg-[#FF6B35] text-white shadow-[0_4px_14px_rgba(255,107,53,0.3)]' 
                            : 'bg-gms-surface border border-gms-border hover:border-[#FF6B35] text-gms-text'"
                    >
                        <span class="text-xs font-bold uppercase">{{ day.label }}</span>
                        <span class="text-xl font-black mt-0.5">{{ day.day }}</span>
                    </div>
                </div>
            </div>

            <!-- Classes List -->
            <div class="space-y-4">
                <div 
                    v-for="(cls, i) in filteredClasses" 
                    :key="cls.id"
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { delay: i * 50 } }"
                    class="bg-gms-surface rounded-3xl p-5 border border-gms-border hover:border-[#FF6B35] transition-all group"
                >
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-black text-gms-text">{{ cls.title }}</h3>
                            <p class="text-sm text-[#FF6B35] font-bold mt-1">{{ cls.trainer }}</p>
                            <p v-if="cls.description" class="text-xs text-gms-text-muted mt-1 line-clamp-1">{{ cls.description }}</p>
                        </div>
                        <div class="text-right shrink-0 ml-4">
                            <div class="text-lg font-black text-gms-text">{{ formatTime(cls.starts_at) }}</div>
                            <div class="text-xs text-gms-text-muted font-bold mt-0.5 uppercase tracking-wider">
                                {{ formatDuration(cls.starts_at, cls.ends_at) }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-xs text-gms-text-muted font-semibold mb-5">
                        <div class="flex items-center gap-1.5">
                            <Clock class="w-4 h-4 shrink-0" />
                            <span>{{ formatTime(cls.starts_at) }} – {{ formatTime(cls.ends_at) }}</span>
                        </div>
                        <div 
                            class="flex items-center gap-1.5"
                            :class="cls.booked >= cls.capacity ? 'text-[#e11d48]' : ''"
                        >
                            <Users class="w-4 h-4 shrink-0" />
                            <span>{{ cls.booked }}/{{ cls.capacity }} spots</span>
                        </div>
                    </div>

                    <!-- Capacity bar -->
                    <div class="h-1.5 bg-gms-border rounded-full mb-5 overflow-hidden">
                        <div 
                            class="h-full rounded-full transition-all"
                            :class="cls.booked >= cls.capacity ? 'bg-[#e11d48]' : 'bg-[#FF6B35]'"
                            :style="{ width: `${Math.min((cls.booked / cls.capacity) * 100, 100)}%` }"
                        />
                    </div>

                    <button 
                        class="w-full py-3.5 rounded-xl font-black transition-all active:scale-[0.98] border-none flex items-center justify-center gap-2"
                        :class="cls.is_booked 
                            ? 'bg-gms-success-surface text-gms-success cursor-default border border-gms-success-border'
                            : cls.booked >= cls.capacity 
                                ? 'bg-gms-border text-gms-text-muted cursor-not-allowed' 
                                : 'bg-gms-text text-gms-text-inverse hover:opacity-90'"
                        :disabled="cls.booked >= cls.capacity || cls.is_booked || bookingForm.processing"
                        @click="!cls.is_booked && cls.booked < cls.capacity && bookClass(cls.id)"
                    >
                        <Loader2 v-if="bookingForm.processing && bookingForm.class_session_id === cls.id" class="w-4 h-4 animate-spin" />
                        <CalendarCheck v-else-if="cls.is_booked" class="w-4 h-4" />
                        <span>{{ cls.is_booked ? 'Booked ✓' : cls.booked >= cls.capacity ? 'Class Full' : 'Book Class' }}</span>
                    </button>
                </div>

                <!-- Empty state -->
                <div v-if="filteredClasses.length === 0" class="text-center py-16 rounded-3xl bg-gms-surface border border-gms-border">
                    <CalendarCheck class="w-12 h-12 mx-auto text-gms-text-muted mb-4 opacity-40" />
                    <p class="text-gms-text font-bold text-lg mb-1">No classes scheduled</p>
                    <p class="text-gms-text-muted text-sm">Try a different day or check back later.</p>
                </div>
            </div>

        </div>
    </MemberLayout>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
