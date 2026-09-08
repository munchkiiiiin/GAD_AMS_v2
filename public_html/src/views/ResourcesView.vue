<template>
  <div class="resources-page text-white font-body pt-20 pb-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="min-height: 100vh;">
    <!-- Hero Section -->
      <section class="mb-16 sm:mb-20">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
          <div class="space-y-6">
            <span class="inline-block px-4 py-1.5 rounded-full bg-white/10 text-white font-label text-xs font-bold uppercase tracking-widest border border-white/10">Legal Frameworks</span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-headline font-extrabold text-white leading-tight tracking-tight">Resources & <br/>Mandates</h1>
            <p class="text-slate-300 text-base sm:text-lg max-w-lg leading-relaxed">
              Access the fundamental legal documents, international treaties, and institutional policies that shape the Gender and Development landscape at Benguet State University.
            </p>
          </div>
          <div class="relative h-[300px] sm:h-[400px] rounded-2xl overflow-hidden shadow-2xl border border-white/10">
            <img alt="Academic Library" class="w-full h-full object-cover" src="/images/img_17.jpg" loading="lazy" decoding="async" />
            <div class="absolute inset-0 bg-purple-900/30 mix-blend-multiply"></div>
          </div>
        </div>
      </section>

      <!-- Filter & Search Bar -->
      <section class="mb-12 sm:mb-16">
        <div class="bg-white/5 p-4 sm:p-6 rounded-2xl border border-white/10 flex flex-col md:flex-row gap-4 items-center justify-between">
          <div class="relative w-full md:max-w-md">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
            <input v-model="searchQuery" class="w-full pl-12 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:ring-2 focus:ring-purple-500 text-white placeholder:text-slate-500 outline-none" placeholder="Search laws, policies, or mandates..." type="text"/>
          </div>
          <div class="flex flex-wrap gap-3 w-full md:w-auto">
            <button v-for="cat in categories" :key="cat" @click="activeCategory = cat" :class="activeCategory === cat ? 'bg-purple-600 text-white border-purple-500/50' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10'" class="px-5 py-2 rounded-full transition-colors font-label text-sm whitespace-nowrap border">
              {{ cat }}
            </button>
          </div>
        </div>
      </section>

      <!-- Bento Grid Layout -->
      <div class="grid grid-cols-1 md:grid-cols-12 gap-8 mb-16 sm:mb-20">
        <!-- International Mandates -->
        <div class="md:col-span-12 lg:col-span-8 space-y-8">
          <div class="flex items-center gap-4 mb-2">
            <h2 class="text-xl sm:text-2xl font-headline font-bold text-purple-400">International Mandates</h2>
            <div class="h-px flex-grow bg-white/10"></div>
          </div>
          <div class="grid md:grid-cols-2 gap-6">
            <div v-for="mandate in filteredMandates" :key="mandate.title" class="group bg-white/5 p-6 sm:p-8 rounded-2xl border border-white/10 hover:border-purple-500/40 hover:shadow-xl transition-all duration-300">
              <div class="flex justify-between items-start mb-6">
                <div class="w-12 h-12 academic-gradient rounded-lg flex items-center justify-center text-white">
                  <span class="material-symbols-outlined">{{ mandate.icon }}</span>
                </div>
                <span class="material-symbols-outlined text-slate-500 group-hover:text-purple-400 transition-colors">{{ mandate.fileIcon }}</span>
              </div>
              <h3 class="text-xl font-headline font-bold mb-3 text-white group-hover:text-purple-400 transition-colors">{{ mandate.title }}</h3>
              <p class="text-sm text-slate-300 leading-relaxed mb-6">{{ mandate.description }}</p>
              <div class="flex items-center justify-between mt-auto">
                <span class="text-xs font-label uppercase tracking-widest font-bold text-purple-400">{{ mandate.type }}</span>
                <a class="text-purple-400 font-label text-sm font-bold underline underline-offset-4 decoration-2" href="#">{{ mandate.action }}</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar / National Focus -->
        <div class="md:col-span-12 lg:col-span-4 bg-gradient-to-br from-purple-700 to-purple-900 text-white p-6 sm:p-8 rounded-2xl relative overflow-hidden flex flex-col border border-purple-500/30">
          <div class="relative z-10">
            <h2 class="text-xl sm:text-2xl font-headline font-bold mb-6">National Policy Spotlight</h2>
            <ul class="space-y-6">
              <li v-for="policy in nationalPolicies" :key="policy.id" class="group cursor-pointer">
                <p class="text-purple-200 font-bold text-xs uppercase tracking-widest mb-1">{{ policy.id }}</p>
                <p class="font-headline font-semibold text-lg group-hover:translate-x-1 transition-transform">{{ policy.title }}</p>
              </li>
            </ul>
            <button class="mt-12 w-full py-4 rounded-full bg-white text-purple-700 font-bold font-headline text-sm hover:bg-purple-100 transition-colors">View All National Laws</button>
          </div>
          <div class="absolute -bottom-10 -right-10 w-48 h-48 bg-purple-500 rounded-full opacity-20 blur-3xl"></div>
        </div>
      </div>

      <!-- Institutional Policies -->
      <div class="md:col-span-12 mb-16 sm:mb-20">
        <div class="flex items-center gap-4 mb-8">
          <h2 class="text-xl sm:text-2xl font-headline font-bold text-purple-400">Institutional Policies</h2>
          <div class="h-px flex-grow bg-white/10"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
          <div v-for="policy in institutionalPolicies" :key="policy.title" class="flex flex-col gap-4 p-1 bg-white/5 rounded-2xl border border-white/10">
            <div class="bg-white/5 p-6 rounded-xl h-full">
              <div class="flex items-center gap-3 mb-4">
                <span class="material-symbols-outlined text-purple-400">{{ policy.icon }}</span>
                <span class="font-label text-xs font-bold text-purple-400 uppercase tracking-widest">{{ policy.tag }}</span>
              </div>
              <h4 class="font-headline font-bold text-lg mb-2 text-white">{{ policy.title }}</h4>
              <p class="text-sm text-slate-300 mb-6">{{ policy.description }}</p>
              <div class="flex items-center gap-4 text-xs font-bold text-purple-400">
                <span class="material-symbols-outlined text-lg">{{ policy.actionIcon }}</span>
                <span>{{ policy.actionText }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>


  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const searchQuery = ref('');
const activeCategory = ref('All Resources');
const categories = ['All Resources', 'International', 'National', 'Institutional'];

const mandates = [
  { 
    title: 'CEDAW', 
    description: 'Convention on the Elimination of All Forms of Discrimination Against Women. Often described as an international bill of rights for women.', 
    icon: 'public', 
    fileIcon: 'picture_as_pdf', 
    type: 'UN Treaty', 
    action: 'Download Document',
    category: 'International'
  },
  { 
    title: 'BPFA', 
    description: "Beijing Platform for Action. An agenda for women's empowerment that aims at accelerating the implementation of the Nairobi Forward-looking Strategies.", 
    icon: 'flag', 
    fileIcon: 'link', 
    type: 'Strategic Agenda', 
    action: 'View Reference',
    category: 'International'
  }
];

const nationalPolicies = [
  { id: 'Republic Act 9710', title: 'Magna Carta of Women' },
  { id: 'Administrative Order', title: 'GAD Budget Guidelines' },
  { id: 'Republic Act 7877', title: 'Anti-Sexual Harassment Act' }
];

const institutionalPolicies = [
  {
    title: 'BSU GAD Guidelines',
    description: 'Internal operational frameworks for gender mainstreaming across all BSU campuses.',
    icon: 'school',
    tag: 'BSU Specific',
    actionIcon: 'download',
    actionText: 'PDF (2.4 MB)'
  },
  {
    title: 'Safe Spaces Act Implementation',
    description: 'Localized implementation protocols for the BSU community regarding safe spaces.',
    icon: 'policy',
    tag: 'Code of Conduct',
    actionIcon: 'open_in_new',
    actionText: 'External Portal'
  },
  {
    title: 'GAD 5-Year Strategic Roadmap',
    description: 'Future objectives and developmental milestones for gender parity at BSU.',
    icon: 'history_edu',
    tag: 'Strategic Plan',
    actionIcon: 'download',
    actionText: 'PDF (5.1 MB)'
  }
];

const filteredMandates = computed(() => {
  return mandates.filter(m => {
    const matchesSearch = m.title.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                         m.description.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesCategory = activeCategory.value === 'All Resources' || m.category === activeCategory.value;
    return matchesSearch && matchesCategory;
  });
});
</script>

<style scoped>
.academic-gradient {
  background: linear-gradient(135deg, #422b68 0%, #5a4281 100%);
}
</style>
