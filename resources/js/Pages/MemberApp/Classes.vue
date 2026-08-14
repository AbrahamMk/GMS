<script setup>
import { Head } from '@inertiajs/vue3';
import MemberLayout from '@/Layouts/MemberLayout.vue';
import { Search, Filter, Clock, MapPin, Users } from '@lucide/vue';

defineProps({
    classes: Array,
});

const mockClasses = [
    { id: 1, name: 'Morning HIIT', time: '07:00 AM', duration: '45m', instructor: 'Alex M.', location: 'Main Floor', booked: 18, capacity: 20, type: 'Cardio' },
    { id: 2, name: 'Powerlifting Base', time: '10:00 AM', duration: '60m', instructor: 'Marcus T.', location: 'Free Weights', booked: 8, capacity: 10, type: 'Strength' },
    { id: 3, name: 'CrossFit WOD', time: '18:00 PM', duration: '60m', instructor: 'Sarah J.', location: 'Box A', booked: 12, capacity: 20, type: 'Mixed' },
    { id: 4, name: 'Yoga Flow', time: '19:30 PM', duration: '50m', instructor: 'Elena V.', location: 'Studio 2', booked: 15, capacity: 15, type: 'Flexibility' },
];
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
                        <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gms-text-secondary" />
                        <input 
                            type="text" 
                            placeholder="Find a class..." 
                            class="w-full bg-gms-surface border border-gms-border text-gms-text rounded-xl py-3 pl-12 pr-4 focus:outline-none focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35] transition-all"
                        >
                    </div>
                    <button class="bg-gms-surface border border-gms-border p-3 rounded-xl hover:bg-gms-surface-hover transition-colors flex items-center justify-center text-gms-text">
                        <Filter class="w-5 h-5" />
                    </button>
                </div>
                
                <!-- Date scroller -->
                <div class="flex gap-3 mt-6 overflow-x-auto pb-2 scrollbar-hide snap-x" v-motion-fade-visible>
                    <div class="min-w-[70px] bg-[#FF6B35] text-[#111111] rounded-2xl p-3 flex flex-col items-center justify-center snap-center cursor-pointer shadow-[0_0_15px_rgba(184,245,0,0.2)]">
                        <span class="text-xs font-bold uppercase">Today</span>
                        <span class="text-xl font-black">14</span>
                    </div>
                    <div class="min-w-[70px] bg-gms-surface border border-gms-border rounded-2xl p-3 flex flex-col items-center justify-center snap-center cursor-pointer hover:border-[#FF6B35] transition-colors">
                        <span class="text-xs font-medium text-gms-text-muted uppercase">Wed</span>
                        <span class="text-xl font-bold text-gms-text">15</span>
                    </div>
                    <div class="min-w-[70px] bg-gms-surface border border-gms-border rounded-2xl p-3 flex flex-col items-center justify-center snap-center cursor-pointer hover:border-[#FF6B35] transition-colors">
                        <span class="text-xs font-medium text-gms-text-muted uppercase">Thu</span>
                        <span class="text-xl font-bold text-gms-text">16</span>
                    </div>
                    <div class="min-w-[70px] bg-gms-surface border border-gms-border rounded-2xl p-3 flex flex-col items-center justify-center snap-center cursor-pointer hover:border-[#FF6B35] transition-colors">
                        <span class="text-xs font-medium text-gms-text-muted uppercase">Fri</span>
                        <span class="text-xl font-bold text-gms-text">17</span>
                    </div>
                    <div class="min-w-[70px] bg-gms-surface border border-gms-border rounded-2xl p-3 flex flex-col items-center justify-center snap-center cursor-pointer hover:border-[#FF6B35] transition-colors">
                        <span class="text-xs font-medium text-gms-text-muted uppercase">Sat</span>
                        <span class="text-xl font-bold text-gms-text">18</span>
                    </div>
                </div>
            </div>

            <!-- Classes List -->
            <div class="space-y-4">
                <div 
                    v-for="(cls, i) in mockClasses" 
                    :key="cls.id"
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :enter="{ opacity: 1, y: 0, transition: { delay: i * 50 } }"
                    class="bg-gms-surface rounded-3xl p-5 border border-gms-border hover:border-[#FF6B35] transition-all group"
                >
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="inline-block px-2.5 py-1 rounded bg-gms-bg text-[10px] font-bold text-gms-text-secondary border border-gms-border mb-2 uppercase tracking-wider">
                                {{ cls.type }}
                            </span>
                            <h3 class="text-xl font-black text-gms-text">{{ cls.name }}</h3>
                            <p class="text-sm text-[#FF6B35] font-bold mt-1">{{ cls.instructor }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-black text-gms-text">{{ cls.time }}</div>
                            <div class="text-xs text-gms-text-muted font-bold mt-0.5 uppercase tracking-wider">{{ cls.duration }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-xs text-gms-text-muted font-semibold mb-5">
                        <div class="flex items-center gap-1.5">
                            <MapPin class="w-4 h-4 shrink-0 text-gms-text-muted" />
                            <span>{{ cls.location }}</span>
                        </div>
                        <div class="flex items-center gap-1.5" :class="cls.booked >= cls.capacity ? 'text-[#e11d48]' : ''">
                            <Users class="w-4 h-4 shrink-0 text-gms-text-muted" />
                            <span>{{ cls.booked }}/{{ cls.capacity }} spots</span>
                        </div>
                    </div>

                    <button 
                        class="w-full py-3.5 rounded-xl font-black transition-all active:scale-[0.98] border-none"
                        :class="cls.booked >= cls.capacity 
                            ? 'bg-gms-border text-gms-text-muted cursor-not-allowed' 
                            : 'bg-gms-text text-gms-text-inverse hover:opacity-90'"
                        :disabled="cls.booked >= cls.capacity"
                    >
                        {{ cls.booked >= cls.capacity ? 'Waitlist' : 'Book Class' }}
                    </button>
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

