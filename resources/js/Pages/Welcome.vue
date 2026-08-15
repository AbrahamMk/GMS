<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Dumbbell, Users, Flame, Shield, ArrowRight,
    CheckCircle2, Star, Menu, X, MapPin, Phone, Mail,
    Share2, Globe, Play, ChevronRight, Zap, Target, Heart, Clock
} from '@lucide/vue';

const mobileOpen = ref(false);

// Public images (served from /public, referenced as runtime strings)
const heroImg = '/images/hero.jpg';
const facilityStrengthImg = '/images/facility-strength.jpg';
const facilityCardioImg = '/images/facility-cardio.jpg';
const facilityRecoveryImg = '/images/facility-recovery.jpg';
const trainerAlexImg = '/images/trainer-alex.jpg';
const trainerSarahImg = '/images/trainer-sarah.jpg';

const stats = [
    { value: '2,500+', label: 'Members' },
    { value: '25+', label: 'Expert Trainers' },
    { value: '40+', label: 'Weekly Classes' },
    { value: '24/7', label: 'Gym Access' },
];

const whyUs = [
    { num: '01', title: 'Premium Equipment', desc: 'Modern equipment designed for strength, cardio, and functional training.', icon: Dumbbell },
    { num: '02', title: 'Expert Trainers', desc: 'Get guidance from experienced coaches who understand your goals.', icon: Target },
    { num: '03', title: 'Flexible Memberships', desc: 'Choose a plan that fits your lifestyle and training routine.', icon: Shield },
    { num: '04', title: 'Community', desc: 'Train alongside motivated people who keep you accountable.', icon: Heart },
];

const programs = [
    { title: 'Strength Training', desc: 'Build muscle, increase strength, and improve overall performance.', icon: Dumbbell },
    { title: 'HIIT', desc: 'High-intensity workouts designed to challenge your limits.', icon: Flame },
    { title: 'Personal Training', desc: 'One-on-one coaching built around your goals.', icon: Target },
    { title: 'Cardio & Conditioning', desc: 'Improve endurance, stamina, and cardiovascular fitness.', icon: Zap },
    { title: 'Functional Training', desc: 'Build strength and movement that carries into everyday life.', icon: Play },
    { title: 'Group Classes', desc: 'Train together with energetic, instructor-led sessions.', icon: Users },
];

const plans = [
    {
        name: 'Basic',
        price: '$29',
        popular: false,
        features: ['Full gym access', 'Cardio & strength equipment', 'Locker access', 'Free fitness assessment'],
    },
    {
        name: 'Premium',
        price: '$49',
        popular: true,
        features: ['Everything in Basic', 'Unlimited group classes', 'Personal fitness plan', 'Sauna & recovery area'],
    },
    {
        name: 'Elite',
        price: '$79',
        popular: false,
        features: ['Everything in Premium', '4 personal training sessions', 'Nutrition consultation', 'Priority class booking'],
    },
];

const props = defineProps({
    dbTrainers: { type: Array, default: () => [] }
});

const defaultTrainers = [
    { name: 'Alex Morgan', role: 'Strength & Conditioning', img: trainerAlexImg },
    { name: 'Sarah Johnson', role: 'HIIT & Functional Training', img: trainerSarahImg },
    { name: 'Daniel Carter', role: 'Personal Training', img: '/images/trainer-daniel.jpg' },
    { name: 'Maya Williams', role: 'Yoga & Mobility', img: '/images/trainer-maya.jpg' },
];

const trainersList = props.dbTrainers && props.dbTrainers.length > 0 ? props.dbTrainers : defaultTrainers;

const facilities = [
    { name: 'Strength Zone', img: facilityStrengthImg, desc: 'Premium free weights and power racks.' },
    { name: 'Cardio Zone', img: facilityCardioImg, desc: 'State-of-the-art treadmills and bikes.' },
    { name: 'Recovery Area', img: facilityRecoveryImg, desc: 'Sauna and relaxation facilities.' },
];

const schedule = [
    { class: 'HIIT', trainer: 'Sarah', time: '6:00 AM', level: 'Intermediate' },
    { class: 'Strength', trainer: 'Alex', time: '8:00 AM', level: 'All Levels' },
    { class: 'Yoga', trainer: 'Maya', time: '5:00 PM', level: 'Beginner' },
    { class: 'Cross Training', trainer: 'Daniel', time: '6:30 PM', level: 'Advanced' },
];

const testimonials = [
    { quote: "I joined three months ago and completely changed my routine. The trainers actually care about your progress.", name: "Emma R.", initials: "ER" },
    { quote: "The equipment is excellent, the classes are amazing, and the atmosphere keeps me motivated every single day.", name: "Michael T.", initials: "MT" },
    { quote: "Best investment I've made in my health. The community here is what makes the difference.", name: "Liyana K.", initials: "LK" },
];

const selectedProgram = ref(null);

const openProgramModal = (prog) => {
    selectedProgram.value = prog;
};

const closeProgramModal = () => {
    selectedProgram.value = null;
};

const navLinks = [
    { label: 'Home', href: '#home' },
    { label: 'About Us', href: '#why' },
    { label: 'Memberships', href: '#memberships' },
    { label: 'Classes', href: '#programs' },
    { label: 'Trainers', href: '#trainers' },
    { label: 'Facilities', href: '#facilities' },
    { label: 'Contact', href: '#contact' },
];
</script>

<template>
    <Head title="FITHUB — Premium Fitness Center" />

    <div class="min-h-screen bg-[#0D0D0D] text-white font-sans antialiased overflow-x-hidden">

        <!-- ======== NAVBAR ======== -->
        <header class="fixed top-0 left-0 right-0 z-50 py-4 transition-all">
            <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
                <a href="#home" class="text-2xl font-black tracking-tighter uppercase text-white">
                    FIT<span class="text-[#FF6B35]">HUB</span>
                </a>

                <!-- Floating Glassmorphic Nav Pill (rounded-2xl / 16px radius, subtle transparency) -->
                <nav class="hidden lg:flex items-center gap-6 px-7 py-2.5 bg-white/15 backdrop-blur-xl rounded-2xl shadow-[0_8px_32px_rgba(0,0,0,0.37)] border border-white/20">
                    <a v-for="link in navLinks" :key="link.label" :href="link.href"
                       class="text-sm font-medium text-white/90 hover:text-[#FF6B35] transition duration-200">{{ link.label }}</a>
                </nav>

                <div class="hidden lg:flex items-center gap-4">
                    <Link href="/login" class="text-sm font-bold text-white hover:text-[#FF6B35] transition">
                        Login
                    </Link>
                    <Link href="/register"
                        class="bg-[#FF6B35] text-white px-5 py-2.5 rounded-lg font-black text-sm tracking-wide hover:bg-[#e55a28] transition shadow-[0_4px_14px_rgba(255,107,53,0.35)]">
                        Join Now
                    </Link>
                </div>

                <!-- Mobile hamburger -->
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 text-white hover:text-[#FF6B35]">
                    <X v-if="mobileOpen" class="w-6 h-6" />
                    <Menu v-else class="w-6 h-6" />
                </button>
            </div>

            <!-- Mobile Menu -->
            <div v-if="mobileOpen" class="lg:hidden bg-[#0D0D0D]/95 backdrop-blur-xl border-t border-white/10 px-6 py-6 space-y-4 shadow-xl">
                <a v-for="link in navLinks" :key="link.label" :href="link.href"
                   @click="mobileOpen = false"
                   class="block text-white hover:text-[#FF6B35] font-bold text-sm py-2 transition">{{ link.label }}</a>
                <div class="flex gap-3 pt-4 border-t border-white/10">
                    <Link href="/login" class="flex-1 text-center border border-white/20 rounded-lg py-3 text-sm font-bold text-white hover:bg-white/5 transition">Login</Link>
                    <Link href="/register" class="flex-1 text-center bg-[#FF6B35] text-white rounded-lg py-3 text-sm font-black hover:bg-[#e55a28] transition">Join Now</Link>
                </div>
            </div>
        </header>

        <!-- ======== HERO ======== -->
        <section id="home" class="relative min-h-screen flex items-stretch overflow-hidden">
            <!-- RIGHT: Full image visible on the right -->
            <div class="absolute inset-0 z-0">
                <img :src="heroImg" alt="Gym Training"
                    class="w-full h-full object-cover object-center" />
                <!-- Gradient: dark on left fading to transparent on right -->
                <div class="absolute inset-0 bg-gradient-to-l from-transparent via-[#0D0D0D]/60 to-[#0D0D0D]"></div>
                <!-- Bottom fade -->
                <div class="absolute inset-0 bg-gradient-to-t from-[#0D0D0D] via-transparent to-[#0D0D0D]/20"></div>
            </div>

            <!-- LEFT: Text content -->
            <div class="relative z-10 w-full max-w-7xl mx-auto px-6 flex items-center min-h-screen pt-24 pb-16">
                <div class="w-full max-w-xl"
                    v-motion
                    :initial="{ opacity: 0, x: -40 }"
                    :enter="{ opacity: 1, x: 0, transition: { type: 'spring', stiffness: 350, damping: 28, delay: 100 } }">

                    <span class="text-[11px] font-black text-[#FF6B35] bg-[#FF6B35]/10 border border-[#FF6B35]/30 px-3 py-1.5 rounded-sm uppercase tracking-[0.25em] inline-block mb-8">
                        Premium Fitness Center · Est. 2018
                    </span>

                    <h1 class="text-6xl md:text-7xl font-black tracking-tighter leading-[0.92] uppercase mb-6">
                        BUILD YOUR<br />
                        <span class="text-[#FF6B35]">STRONGER</span><br />
                        SELF.
                    </h1>

                    <p class="text-gray-400 text-base md:text-lg font-medium leading-relaxed mb-10 max-w-md">
                        Train harder. Move better. Become stronger. Premium equipment, expert trainers, and a community built to help you reach your fitness goals.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 mb-14">
                        <Link href="/register"
                            class="inline-flex items-center justify-center gap-2 bg-[#FF6B35] text-white px-8 py-4 rounded-md font-black text-sm tracking-wider hover:bg-[#e55a28] transition shadow-[0_4px_24px_rgba(255,85,0,0.4)] hover:-translate-y-0.5 active:translate-y-0 duration-200 uppercase">
                            Join Now <ArrowRight class="w-4 h-4" />
                        </Link>
                        <a href="#memberships"
                            class="inline-flex items-center justify-center gap-2 border border-white/15 hover:border-white/30 px-8 py-4 rounded-md font-bold text-sm transition hover:bg-white/5 duration-200 uppercase tracking-wider">
                            Explore Memberships
                        </a>
                    </div>

                    <!-- Stats Row -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 pt-10 border-t border-white/10">
                        <div v-for="stat in stats" :key="stat.label">
                            <div class="text-2xl md:text-3xl font-black text-[#FF6B35] mb-0.5">{{ stat.value }}</div>
                            <div class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">{{ stat.label }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======== WHY CHOOSE US ======== -->
        <section id="why" class="py-28 px-6 bg-[#0D0D0D]">
            <div class="max-w-7xl mx-auto">
                <div class="mb-16 max-w-2xl"
                    v-motion
                    :initial="{ opacity: 0, y: 30 }"
                    :visible="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 350, damping: 28 } }">
                    <p class="text-[#FF6B35] text-[11px] font-black uppercase tracking-[0.3em] mb-4">Why Choose Us</p>
                    <h2 class="text-4xl md:text-5xl font-black tracking-tighter leading-tight uppercase">
                        Everything You Need<br />to Get <span class="text-[#FF6B35]">Stronger.</span>
                    </h2>
                    <p class="mt-4 text-gray-500 font-medium leading-relaxed">Whether you're just starting or pushing your limits, we've built the space and support you need to make progress.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div v-for="(item, i) in whyUs" :key="item.num"
                        v-motion
                        :initial="{ opacity: 0, y: 30 }"
                        :visible="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 350, damping: 28, delay: i * 100 } }"
                        class="group flex gap-6 p-8 border border-white/5 bg-[#141414] hover:border-[#FF6B35]/30 hover:bg-[#171717] transition duration-300">
                        <div class="shrink-0">
                            <span class="block text-[10px] font-black text-[#FF6B35] tracking-[0.3em] uppercase mb-4">{{ item.num }}</span>
                            <div class="w-12 h-12 flex items-center justify-center bg-[#FF6B35]/10 border border-[#FF6B35]/20 group-hover:bg-[#FF6B35]/20 transition">
                                <component :is="item.icon" class="w-5 h-5 text-[#FF6B35]" />
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white mb-2">{{ item.title }}</h3>
                            <p class="text-gray-500 font-medium leading-relaxed text-sm">{{ item.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======== PROGRAMS ======== -->
        <section id="programs" class="py-28 px-6 bg-[#111111]">
            <div class="max-w-7xl mx-auto">
                <div class="mb-16 flex flex-col md:flex-row md:items-end justify-between gap-8">
                    <div>
                        <p class="text-[#FF6B35] text-[11px] font-black uppercase tracking-[0.3em] mb-4">Programs</p>
                        <h2 class="text-4xl md:text-5xl font-black tracking-tighter leading-tight uppercase">
                            TRAIN YOUR <span class="text-[#FF6B35]">WAY.</span>
                        </h2>
                    </div>
                    <Link href="/register"
                        class="inline-flex items-center gap-2 border border-white/10 hover:border-[#FF6B35]/40 px-6 py-3 font-bold text-sm transition hover:text-[#FF6B35] shrink-0 uppercase tracking-wider">
                        View All Programs <ChevronRight class="w-4 h-4" />
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="(prog, i) in programs" :key="prog.title"
                        @click="openProgramModal(prog)"
                        v-motion
                        :initial="{ opacity: 0, scale: 0.97 }"
                        :visible="{ opacity: 1, scale: 1, transition: { type: 'spring', stiffness: 350, damping: 28, delay: i * 80 } }"
                        class="group p-8 border border-white/5 bg-[#0D0D0D] hover:border-[#FF6B35]/40 hover:bg-[#121212] transition duration-300 cursor-pointer">
                        <div class="w-12 h-12 flex items-center justify-center bg-[#FF6B35]/10 border border-[#FF6B35]/20 mb-6 group-hover:bg-[#FF6B35]/20 transition">
                            <component :is="prog.icon" class="w-5 h-5 text-[#FF6B35]" />
                        </div>
                        <h3 class="text-xl font-black text-white mb-3 group-hover:text-[#FF6B35] transition uppercase tracking-tight">{{ prog.title }}</h3>
                        <p class="text-gray-500 font-medium text-sm leading-relaxed mb-4">{{ prog.desc }}</p>
                        <div class="flex items-center gap-2 text-[#FF6B35] text-xs font-black uppercase tracking-wider group-hover:translate-x-1 transition duration-200">
                            Learn more <ArrowRight class="w-3.5 h-3.5" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======== MEMBERSHIP PLANS ======== -->
        <section id="memberships" class="py-28 px-6 bg-[#0D0D0D]">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <p class="text-[#FF6B35] text-[11px] font-black uppercase tracking-[0.3em] mb-4">Membership Plans</p>
                    <h2 class="text-4xl md:text-5xl font-black tracking-tighter leading-tight uppercase">
                        FIND YOUR <span class="text-[#FF6B35]">FIT.</span>
                    </h2>
                    <p class="mt-4 text-gray-500 font-medium max-w-xl mx-auto">Flexible plans designed to match your commitment and your budget.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 max-w-5xl mx-auto">
                    <div v-for="(plan, i) in plans" :key="plan.name"
                        v-motion
                        :initial="{ opacity: 0, y: 30 }"
                        :visible="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 350, damping: 28, delay: i * 100 } }"
                        class="relative flex flex-col p-8 border transition duration-300"
                        :class="plan.popular
                            ? 'border-[#FF6B35] bg-[#FF6B35]/5 shadow-[0_0_40px_rgba(255,85,0,0.12)]'
                            : 'border-white/5 bg-[#141414] hover:border-white/10'">
                        <div v-if="plan.popular" class="absolute -top-4 left-1/2 -translate-x-1/2">
                            <span class="bg-[#FF6B35] text-white text-[10px] font-black px-4 py-1.5 uppercase tracking-[0.2em]">Most Popular</span>
                        </div>
                        <div class="mb-6">
                            <h3 class="text-xs font-black uppercase tracking-[0.2em] mb-4" :class="plan.popular ? 'text-[#FF6B35]' : 'text-gray-400'">{{ plan.name }}</h3>
                            <div class="flex items-baseline gap-1">
                                <span class="text-5xl font-black text-white">{{ plan.price }}</span>
                                <span class="text-gray-500 font-semibold text-sm">/ month</span>
                            </div>
                        </div>
                        <ul class="flex-1 space-y-3 mb-8">
                            <li v-for="feature in plan.features" :key="feature" class="flex items-start gap-3 text-sm font-medium text-gray-400">
                                <CheckCircle2 class="w-4 h-4 text-[#FF6B35] shrink-0 mt-0.5" />
                                {{ feature }}
                            </li>
                        </ul>
                        <Link href="/register"
                            class="w-full text-center py-3.5 font-black text-sm transition duration-200 uppercase tracking-wider hover:-translate-y-0.5 active:translate-y-0"
                            :class="plan.popular
                                ? 'bg-[#FF6B35] text-white hover:bg-[#e55a28] shadow-[0_4px_20px_rgba(255,85,0,0.3)]'
                                : 'border border-white/10 hover:border-white/20 hover:bg-white/5'">
                            Choose {{ plan.name }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======== TRAINERS ======== -->
        <section id="trainers" class="py-28 px-6 bg-[#111111]">
            <div class="max-w-7xl mx-auto">
                <div class="mb-16 flex flex-col md:flex-row md:items-end justify-between gap-8">
                    <div>
                        <p class="text-[#FF6B35] text-[11px] font-black uppercase tracking-[0.3em] mb-4">Trainers</p>
                        <h2 class="text-4xl md:text-5xl font-black tracking-tighter leading-tight uppercase">
                            MEET YOUR <span class="text-[#FF6B35]">COACHES.</span>
                        </h2>
                    </div>
                    <a href="#"
                        class="inline-flex items-center gap-2 border border-white/10 hover:border-[#FF6B35]/40 px-6 py-3 font-bold text-sm transition hover:text-[#FF6B35] shrink-0 uppercase tracking-wider">
                        Meet All Trainers <ChevronRight class="w-4 h-4" />
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div v-for="(trainer, i) in trainersList" :key="trainer.name"
                        v-motion
                        :initial="{ opacity: 0, y: 30 }"
                        :visible="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 350, damping: 28, delay: i * 100 } }"
                        class="group overflow-hidden border border-white/5 bg-[#141414] hover:border-[#FF6B35]/30 transition duration-300">
                        <div class="relative h-72 overflow-hidden">
                            <img v-if="trainer.img" :src="trainer.img" :alt="trainer.name"
                                class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500 brightness-90" />
                            <div v-else class="w-full h-full bg-[#1a1a1a] flex items-center justify-center">
                                <span class="text-5xl font-black text-[#FF6B35]/30">{{ trainer.name.charAt(0) }}</span>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-[#141414] via-transparent to-transparent"></div>
                            <!-- Orange accent bar on hover -->
                            <div class="absolute bottom-0 left-0 right-0 h-1 bg-[#FF6B35] scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-black text-lg text-white group-hover:text-[#FF6B35] transition">{{ trainer.name }}</h3>
                            <p class="text-[#FF6B35] text-[10px] font-black uppercase tracking-widest mt-1">{{ trainer.role }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======== FACILITIES ======== -->
        <section id="facilities" class="py-28 px-6 bg-[#0D0D0D]">
            <div class="max-w-7xl mx-auto">
                <div class="mb-16 text-center">
                    <p class="text-[#FF6B35] text-[11px] font-black uppercase tracking-[0.3em] mb-4">Facilities</p>
                    <h2 class="text-4xl md:text-5xl font-black tracking-tighter leading-tight uppercase mb-4">
                        BUILT FOR <span class="text-[#FF6B35]">PERFORMANCE.</span>
                    </h2>
                    <p class="text-gray-500 font-medium max-w-2xl mx-auto">A space designed to make every workout count. Premium equipment, spacious training areas, clean facilities, and everything you need to train comfortably.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div v-for="(facility, i) in facilities" :key="facility.name"
                        v-motion
                        :initial="{ opacity: 0, scale: 0.97 }"
                        :visible="{ opacity: 1, scale: 1, transition: { type: 'spring', stiffness: 350, damping: 28, delay: i * 100 } }"
                        class="group relative overflow-hidden border border-white/5 aspect-[4/3]">
                        <img :src="facility.img" :alt="facility.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500 brightness-75" />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0D0D0D] via-[#0D0D0D]/30 to-transparent"></div>
                        <!-- Orange line accent -->
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#FF6B35] scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6">
                            <h3 class="font-black text-xl text-white mb-1 uppercase tracking-tight">{{ facility.name }}</h3>
                            <p class="text-gray-400 text-sm font-medium">{{ facility.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======== CLASS SCHEDULE ======== -->
        <section class="py-28 px-6 bg-[#111111]">
            <div class="max-w-7xl mx-auto">
                <div class="mb-16 flex flex-col md:flex-row md:items-end justify-between gap-8">
                    <div>
                        <p class="text-[#FF6B35] text-[11px] font-black uppercase tracking-[0.3em] mb-4">Schedule</p>
                        <h2 class="text-4xl md:text-5xl font-black tracking-tighter leading-tight uppercase">
                            YOUR NEXT WORKOUT<br /><span class="text-[#FF6B35]">STARTS HERE.</span>
                        </h2>
                    </div>
                    <Link href="/member/classes"
                        class="inline-flex items-center gap-2 border border-white/10 hover:border-[#FF6B35]/40 px-6 py-3 font-bold text-sm transition hover:text-[#FF6B35] shrink-0 uppercase tracking-wider">
                        View Full Schedule <ChevronRight class="w-4 h-4" />
                    </Link>
                </div>

                <div class="border border-white/5 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-[#141414] border-b border-white/5">
                                <th class="text-left px-6 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Class</th>
                                <th class="text-left px-6 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Trainer</th>
                                <th class="text-left px-6 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Time</th>
                                <th class="text-left px-6 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Level</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr v-for="row in schedule" :key="row.class"
                                class="bg-[#0D0D0D] hover:bg-[#141414] transition duration-200 group">
                                <td class="px-6 py-5 font-black text-white uppercase tracking-tight">{{ row.class }}</td>
                                <td class="px-6 py-5 font-semibold text-gray-400">{{ row.trainer }}</td>
                                <td class="px-6 py-5">
                                    <span class="flex items-center gap-2 font-bold text-[#FF6B35]">
                                        <Clock class="w-3.5 h-3.5" />{{ row.time }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="text-[10px] font-black uppercase tracking-wider bg-white/5 border border-white/10 px-2.5 py-1 text-gray-400">{{ row.level }}</span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <Link href="/register"
                                        class="opacity-0 group-hover:opacity-100 transition text-[#FF6B35] font-black text-xs uppercase tracking-wider inline-flex items-center gap-1">
                                        Book <ArrowRight class="w-3.5 h-3.5" />
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ======== TESTIMONIALS ======== -->
        <section class="py-28 px-6 bg-[#0D0D0D]">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <p class="text-[#FF6B35] text-[11px] font-black uppercase tracking-[0.3em] mb-4">Testimonials</p>
                    <h2 class="text-4xl md:text-5xl font-black tracking-tighter leading-tight uppercase">
                        REAL PEOPLE.<br /><span class="text-[#FF6B35]">REAL PROGRESS.</span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div v-for="(t, i) in testimonials" :key="t.name"
                        v-motion
                        :initial="{ opacity: 0, y: 30 }"
                        :visible="{ opacity: 1, y: 0, transition: { type: 'spring', stiffness: 350, damping: 28, delay: i * 100 } }"
                        class="flex flex-col p-8 border border-white/5 bg-[#141414] hover:border-[#FF6B35]/20 transition duration-300">
                        <div class="flex gap-0.5 mb-6">
                            <Star v-for="s in 5" :key="s" class="w-4 h-4 fill-[#FF6B35] text-[#FF6B35]" />
                        </div>
                        <p class="text-gray-300 font-medium leading-relaxed flex-1 text-base mb-6">"{{ t.quote }}"</p>
                        <div class="flex items-center gap-3 pt-5 border-t border-white/5">
                            <div class="w-10 h-10 bg-[#FF6B35]/15 border border-[#FF6B35]/30 flex items-center justify-center text-[#FF6B35] text-xs font-black">{{ t.initials }}</div>
                            <span class="font-bold text-white text-sm">{{ t.name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======== CTA SECTION ======== -->
        <section class="py-32 px-6 bg-[#FF6B35] relative overflow-hidden">
            <!-- Texture/pattern overlay -->
            <div class="absolute inset-0 opacity-[0.07]"
                style="background-image: repeating-linear-gradient(0deg, #000 0px, #000 1px, transparent 1px, transparent 40px), repeating-linear-gradient(90deg, #000 0px, #000 1px, transparent 1px, transparent 40px);">
            </div>
            <div class="max-w-4xl mx-auto text-center relative z-10">
                <h2 class="text-5xl md:text-7xl font-black tracking-tighter leading-none uppercase mb-6 text-white">
                    READY TO GET<br />STRONGER?
                </h2>
                <p class="text-white/75 text-lg font-medium mb-10 max-w-xl mx-auto">
                    Your goals won't achieve themselves. Start your fitness journey today.
                </p>
                <Link href="/register"
                    class="inline-flex items-center gap-3 bg-[#0D0D0D] text-white px-10 py-5 font-black text-base tracking-wider hover:bg-[#1a1a1a] transition shadow-[0_8px_40px_rgba(0,0,0,0.4)] hover:-translate-y-1 active:translate-y-0 duration-200 uppercase">
                    JOIN NOW <ArrowRight class="w-5 h-5" />
                </Link>
            </div>
        </section>

        <!-- ======== FOOTER ======== -->
        <footer id="contact" class="bg-[#080808] border-t border-white/5 py-20 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                    <!-- Brand -->
                    <div class="lg:col-span-1">
                        <a href="#home" class="text-3xl font-black tracking-tighter uppercase block mb-3">
                            FIT<span class="text-[#FF6B35]">HUB</span>
                        </a>
                        <p class="text-gray-500 text-sm font-medium leading-relaxed mb-6">Train. Push. Progress.</p>
                        <div class="flex gap-3">
                            <a href="#" class="w-10 h-10 border border-white/10 flex items-center justify-center text-gray-500 hover:text-[#FF6B35] hover:border-[#FF6B35]/30 transition">
                                <Share2 class="w-4 h-4" />
                            </a>
                            <a href="#" class="w-10 h-10 border border-white/10 flex items-center justify-center text-gray-500 hover:text-[#FF6B35] hover:border-[#FF6B35]/30 transition">
                                <Globe class="w-4 h-4" />
                            </a>
                        </div>
                    </div>

                    <!-- Explore -->
                    <div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-6">Explore</h4>
                        <ul class="space-y-3">
                            <li v-for="link in [{ label: 'About', href: '#why' }, { label: 'Memberships', href: '#memberships' }, { label: 'Classes', href: '#programs' }, { label: 'Trainers', href: '#trainers' }, { label: 'Facilities', href: '#facilities' }]" :key="link.label">
                                <a :href="link.href" class="text-sm font-medium text-gray-500 hover:text-white transition">{{ link.label }}</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-6">Support</h4>
                        <ul class="space-y-3">
                            <li v-for="link in ['Contact', 'FAQ', 'Membership Policy', 'Privacy Policy']" :key="link">
                                <a href="#" class="text-sm font-medium text-gray-500 hover:text-white transition">{{ link }}</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-6">Contact</h4>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3 text-sm font-medium text-gray-500">
                                <MapPin class="w-4 h-4 text-[#FF6B35] shrink-0 mt-0.5" />
                                Addis Ababa, Ethiopia
                            </li>
                            <li class="flex items-center gap-3 text-sm font-medium text-gray-500">
                                <Phone class="w-4 h-4 text-[#FF6B35] shrink-0" />
                                +251 XXX XXX XXX
                            </li>
                            <li class="flex items-center gap-3 text-sm font-medium text-gray-500">
                                <Mail class="w-4 h-4 text-[#FF6B35] shrink-0" />
                                hello@fithub.com
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-xs font-semibold text-gray-700">© 2026 FITHUB. All rights reserved.</p>
                    <a href="/portal" class="text-xs font-semibold text-gray-700 hover:text-gray-500 transition">Staff Portal →</a>
                </div>
            </div>
        </footer>

        <!-- ======== PROGRAM DETAILS MODAL ======== -->
        <div v-if="selectedProgram" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md" @click.self="closeProgramModal">
            <div class="relative w-full max-w-xl bg-[#141414] border border-white/10 p-8 rounded-2xl shadow-2xl space-y-6">
                <button @click="closeProgramModal" class="absolute top-6 right-6 text-gray-400 hover:text-white p-2">
                    <X class="w-6 h-6" />
                </button>

                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 flex items-center justify-center bg-[#FF6B35]/10 border border-[#FF6B35]/20 rounded-xl">
                        <component :is="selectedProgram.icon" class="w-7 h-7 text-[#FF6B35]" />
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-[0.25em] text-[#FF6B35]">Program Overview</span>
                        <h3 class="text-2xl font-black text-white uppercase">{{ selectedProgram.title }}</h3>
                    </div>
                </div>

                <p class="text-gray-300 font-medium leading-relaxed">{{ selectedProgram.desc }}</p>

                <div class="space-y-3 pt-4 border-t border-white/10 text-sm">
                    <div class="flex items-center justify-between text-gray-400">
                        <span class="font-bold">Intensity Level:</span>
                        <span class="font-black text-white uppercase">High / Customized</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-400">
                        <span class="font-bold">Recommended For:</span>
                        <span class="font-black text-white uppercase">All Fitness Levels</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-400">
                        <span class="font-bold">Coach Supervision:</span>
                        <span class="font-black text-[#FF6B35] uppercase">Included</span>
                    </div>
                </div>

                <div class="pt-4 flex gap-4">
                    <button @click="closeProgramModal" class="flex-1 py-3 border border-white/10 rounded-xl font-bold text-sm text-gray-300 hover:bg-white/5 transition">
                        Close
                    </button>
                    <Link href="/register" class="flex-1 text-center py-3 bg-[#FF6B35] text-white rounded-xl font-black text-sm uppercase tracking-wider hover:bg-[#e55a28] transition shadow-[0_4px_14px_rgba(255,107,53,0.35)]">
                        Join Program
                    </Link>
                </div>
            </div>
        </div>

    </div>
</template>


