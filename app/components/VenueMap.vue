<script setup lang="ts">
import { onMounted, onUnmounted, ref, watch, computed } from 'vue'
import type { VenueType } from '~/composables/useVenues'

const props = defineProps<{
  venues: Array<{
    id: string
    name: string
    location: string
    type?: string
    lat: number
    lng: number
    image?: string
    image2?: string
    logo?: string
    capacity?: string
    staff_members?: number
    recent_event?: string
    guests_served?: string
    shops_count?: number
    menus_count?: number
    description?: string
  }>
  venueTypes?: VenueType[]
  selectedVenue?: string
  activeFilter?: string
}>()

const emit = defineEmits(['select-venue', 'venue-clicked'])

const mapContainer = ref<HTMLElement | null>(null)
let map: any = null
let markers: any[] = []

// Get map configuration from global settings
const { settings, loadSettings } = useGlobalSettings()

// Computed map settings with fallbacks
const mapCenter = computed<[number, number]>(() => {
  const center = settings.value?.map?.center
  if (center && Array.isArray(center) && center.length === 2) {
    return center as [number, number]
  }
  return [2.0, 48.5] // Default: France
})

const mapZoom = computed(() => settings.value?.map?.zoom || 5)

const maptilerStyle = computed(() => {
  const style = settings.value?.map?.maptiler_style
  const key = settings.value?.map?.maptiler_key
  if (style && key) {
    // Append key if not already in URL
    return style.includes('key=') ? style : `${style}?key=${key}`
  }
  // Fallback to default style
  return 'https://api.maptiler.com/maps/019bc79b-b5ae-7523-a6d0-a73039e2ca18/style.json?key=ktSs6eRMmo4o70YLtDSA'
})

// Get marker image for venue type - first from venue_types props, then from global settings
const getMarkerIcon = (venueType?: string): string => {
  const type = venueType?.toLowerCase() || ''

  // First, try to get icon from venue_types prop
  if (props.venueTypes && props.venueTypes.length > 0) {
    const venueTypeData = props.venueTypes.find(vt => vt.id.toLowerCase() === type)
    if (venueTypeData?.map_icon) {
      return venueTypeData.map_icon
    }
  }

  // Fallback to global settings markers
  const markers = settings.value?.markers
  if (markers) {
    if (type === 'stadium' && markers.stadium) return markers.stadium
    if (type === 'arena' && markers.arena) return markers.arena
    if (type === 'festival' && markers.festival) return markers.festival
    if (markers.default) return markers.default
  }

  return '/images/stadiumIcon.svg' // Final fallback
}

const createMarkers = (maplibregl: any) => {
  // Supprimer les anciens marqueurs
  markers.forEach(m => m.marker.remove())
  markers = []

  // Filtrer les venues si un filtre est actif
  const filteredVenues = props.activeFilter
    ? props.venues.filter(v => v.type === props.activeFilter)
    : props.venues

  // Ajouter les marqueurs pour chaque lieu
  filteredVenues.forEach((venue) => {
    // Créer l'élément HTML pour le marqueur (DOM construit pour éviter l'injection)
    const el = document.createElement('div')
    el.className = 'custom-venue-marker'

    const wrapper = document.createElement('div')
    wrapper.className = 'venue-marker-wrapper'

    // Tooltip (nom du stade) — sécurisé via textContent
    const tooltip = document.createElement('div')
    tooltip.className = 'venue-marker-tooltip'
    tooltip.textContent = venue.name || ''

    // Conteneur principal du marqueur
    const containerDiv = document.createElement('div')
    containerDiv.className = 'venue-marker-container'

    // Icône du venue (from global settings based on type)
    const venueIcon = document.createElement('img')
    venueIcon.className = 'venue-marker-icon'
    venueIcon.src = getMarkerIcon(venue.type)
    venueIcon.alt = venue.name || 'Venue'

    containerDiv.appendChild(venueIcon)

    // Logo de la venue en exposant (en haut à droite)
    if (venue.logo) {
      const logoDiv = document.createElement('div')
      logoDiv.className = 'venue-marker-logo'
      const logoImg = document.createElement('img')
      logoImg.src = venue.logo
      logoImg.alt = `${venue.name} logo`
      logoDiv.appendChild(logoImg)
      containerDiv.appendChild(logoDiv)
    }

    wrapper.appendChild(tooltip)
    wrapper.appendChild(containerDiv)
    el.appendChild(wrapper)

    // Click: selectionner le lieu et centrer la carte
    el.addEventListener('click', () => {
      emit('select-venue', venue.id)
      emit('venue-clicked', venue)
    })

    const marker = new maplibregl.Marker({ element: el })
      .setLngLat([venue.lng, venue.lat])
      .addTo(map)

    markers.push({ id: venue.id, marker, type: venue.type })
  })
}

onMounted(async () => {
  // Ensure settings are loaded
  if (!settings.value) {
    await loadSettings()
  }

  if (typeof window !== 'undefined') {
    const maplibregl = await import('maplibre-gl')
    await import('maplibre-gl/dist/maplibre-gl.css')

    if (mapContainer.value) {
      // Initialiser la carte avec MapLibre GL JS
      map = new maplibregl.default.Map({
        container: mapContainer.value,
        style: maptilerStyle.value,
        center: mapCenter.value,
        zoom: mapZoom.value,
        attributionControl: false,
        scrollZoom: false,
        dragRotate: false,
        touchPitch: false
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

// Watch for filter changes
watch(() => props.activeFilter, async () => {
  if (map && typeof window !== 'undefined') {
    const maplibregl = await import('maplibre-gl')
    createMarkers(maplibregl.default)
    // Réinitialiser le zoom à la vue initiale
    map.flyTo({
      center: mapCenter.value,
      zoom: mapZoom.value,
      duration: 500
    })
  }
})

// Watch for venue selection changes
watch(() => props.selectedVenue, (newId) => {
  if (map && newId) {
    const venue = props.venues.find(v => v.id === newId)
    if (venue) {
      // Aucun zoom au clic
    }
  }
})
</script>

<template>
  <div class="venue-map-wrapper">
    <div ref="mapContainer" class="venue-map-container"></div>
  </div>
</template>
