<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, computed } from 'vue'
import { LucideSearch, LucideMapPin, LucideX } from 'lucide-vue-next'

const props = defineProps({
  locations: {
    type: Array as () => Array<{
      id: number | string
      name: string
      address: string
      lat: number
      lng: number
      jobs?: number
    }>,
    default: () => [
      { id: 1, name: 'Stade Jean Bouin', address: 'Paris, France', lat: 48.8423, lng: 2.2536, jobs: 6 },
      { id: 2, name: 'Accor Arena', address: 'Paris, France', lat: 48.8387, lng: 2.3789, jobs: 4 },
      { id: 3, name: 'Parc des Princes', address: 'Paris, France', lat: 48.8414, lng: 2.2530, jobs: 8 },
      { id: 4, name: 'Stade de France', address: 'Saint-Denis, France', lat: 48.9244, lng: 2.3601, jobs: 12 },
    ]
  },
  height: {
    type: String,
    default: '600px'
  }
})

const emit = defineEmits(['locationSelect'])

const mapContainer = ref<HTMLElement | null>(null)
let map: any = null
let markers: any[] = []

const searchQuery = ref('')
const selectedLocation = ref<any>(null)

// MapTiler custom style URL
const MAPTILER_STYLE = 'https://api.maptiler.com/maps/019bc79b-b5ae-7523-a6d0-a73039e2ca18/style.json?key=ktSs6eRMmo4o70YLtDSA'

const filteredLocations = computed(() => {
  if (!searchQuery.value) return props.locations
  return props.locations.filter(loc =>
    loc.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    loc.address.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const createMarkers = (maplibregl: any) => {
  // Supprimer les anciens marqueurs
  markers.forEach(m => m.remove())
  markers = []

  // Ajouter les marqueurs pour chaque lieu
  filteredLocations.value.forEach((location) => {
    const isSelected = selectedLocation.value?.id === location.id

    // Créer l'élément HTML pour le marqueur
    const el = document.createElement('div')
    el.className = 'custom-marker flex flex-col items-center'
    el.innerHTML = `
      <div class="w-8 h-8 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-xs font-bold transition-all duration-300 ${isSelected ? 'bg-brand-yellow scale-125' : 'bg-brand-pink'}" style="background-color: ${isSelected ? '#FFDD00' : '#FF4D6D'};">
        ${location.jobs || '•'}
      </div>
      <div class="w-0 h-0" style="border-left: 4px solid transparent; border-right: 4px solid transparent; border-top: 8px solid ${isSelected ? '#FFDD00' : '#FF4D6D'};"></div>
    `

    el.style.cursor = 'pointer'

    // Ajouter l'événement click
    el.addEventListener('click', () => {
      selectLocation(location)
    })

    const marker = new maplibregl.Marker({ element: el, anchor: 'bottom' })
      .setLngLat([location.lng, location.lat])
      .addTo(map)

    markers.push(marker)
  })
}

const selectLocation = async (location: any) => {
  selectedLocation.value = location
  emit('locationSelect', location)

  if (map) {
    map.flyTo({
      center: [location.lng, location.lat],
      zoom: 14,
      duration: 500
    })

    // Recréer les marqueurs pour mettre à jour le style sélectionné
    if (typeof window !== 'undefined') {
      const maplibregl = await import('maplibre-gl')
      createMarkers(maplibregl.default)
    }
  }
}

const closePanel = async () => {
  selectedLocation.value = null

  if (map) {
    map.flyTo({
      center: [2.3522, 48.8566],
      zoom: 11,
      duration: 500
    })

    // Recréer les marqueurs pour mettre à jour le style
    if (typeof window !== 'undefined') {
      const maplibregl = await import('maplibre-gl')
      createMarkers(maplibregl.default)
    }
  }
}

onMounted(async () => {
  if (typeof window !== 'undefined') {
    const maplibregl = await import('maplibre-gl')
    await import('maplibre-gl/dist/maplibre-gl.css')

    if (mapContainer.value) {
      // Initialiser la carte avec MapLibre GL JS
      map = new maplibregl.default.Map({
        container: mapContainer.value,
        style: MAPTILER_STYLE,
        center: [2.3522, 48.8566], // Paris [lng, lat]
        zoom: 11,
        attributionControl: false
      })

      // Attendre que la carte soit chargée avant d'ajouter les marqueurs
      map.on('load', () => {
        createMarkers(maplibregl.default)
      })
    }
  }
})

onUnmounted(() => {
  if (map) {
    map.remove()
    map = null
  }
})

// Watch for search query changes
watch(() => searchQuery.value, async () => {
  if (map && typeof window !== 'undefined') {
    const maplibregl = await import('maplibre-gl')
    createMarkers(maplibregl.default)
  }
})
</script>

<template>
  <div class="relative w-full overflow-hidden rounded-3xl border-2 border-brand-dark" :style="{ height }">
    <!-- Search Bar Overlay -->
    <div class="absolute top-4 left-4 right-4 z-[1000] flex gap-4">
      <div class="flex-1 relative">
        <div class="bg-brand-dark border-organic flex items-center px-4 py-3 gap-3">
          <LucideSearch class="w-5 h-5 text-white/70" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search job title and category here"
            class="bg-transparent text-white placeholder:text-white/50 outline-none flex-1 font-body"
          />
        </div>
      </div>

      <div class="bg-brand-dark border-organic px-4 py-3 flex items-center gap-2 cursor-pointer hover:bg-brand-dark/90 transition-colors">
        <span class="text-white font-medium">All job types</span>
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </div>
    </div>

    <!-- Map -->
    <div ref="mapContainer" class="w-full h-full"></div>

    <!-- Location Panel (when selected) -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="transform translate-y-full opacity-0"
      enter-to-class="transform translate-y-0 opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="transform translate-y-0 opacity-100"
      leave-to-class="transform translate-y-full opacity-0"
    >
      <div v-if="selectedLocation" class="absolute bottom-4 left-4 right-4 z-[1000]">
        <div class="bg-white border-organic p-6 relative">
          <button
            @click="closePanel"
            class="absolute top-4 right-4 w-8 h-8 bg-brand-yellow rounded-full flex items-center justify-center border-2 border-black hover:scale-110 transition-transform"
          >
            <LucideX class="w-4 h-4" />
          </button>

          <div class="flex items-start gap-3 mb-4">
            <div class="w-8 h-8 bg-brand-pink rounded-full flex items-center justify-center">
              <LucideMapPin class="w-4 h-4 text-white" />
            </div>
            <div>
              <h3 class="font-heading text-xl font-bold">{{ selectedLocation.name }}</h3>
              <p class="text-gray-500 text-sm">{{ selectedLocation.address }}</p>
            </div>
          </div>

          <div class="flex items-center gap-4">
            <span class="text-brand-lime font-bold">•</span>
            <span class="text-sm font-medium">{{ selectedLocation.jobs }} Open Positions</span>
          </div>

          <div class="mt-4 flex gap-3">
            <button class="btn-primary flex-1">View All Jobs</button>
            <button class="btn-secondary">Get Directions</button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style>
/* Style des contrôles de navigation MapLibre */
.maplibregl-ctrl-group {
  border: none !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
  border-radius: 8px !important;
  overflow: hidden;
}

.maplibregl-ctrl-group button {
  width: 36px !important;
  height: 36px !important;
  border: none !important;
  background-color: #fff !important;
  transition: background-color 0.2s ease;
}

.maplibregl-ctrl-group button:hover {
  background-color: #f5f5f5 !important;
}
</style>
