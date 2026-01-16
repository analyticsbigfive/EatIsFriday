<script setup lang="ts">
import { onMounted, onUnmounted, ref, watch } from 'vue'

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
  selectedVenue?: string
  activeFilter?: string
}>()

const emit = defineEmits(['select-venue', 'venue-clicked'])

const mapContainer = ref<HTMLElement | null>(null)
let map: any = null
let markers: any[] = []

// Centre pour voir France et sud de l'Angleterre
const MAP_CENTER: [number, number] = [2.0, 48.5] // [lng, lat] pour MapLibre
const MAP_ZOOM = 5

// MapTiler custom style URL
const MAPTILER_STYLE = 'https://api.maptiler.com/maps/019bc79b-b5ae-7523-a6d0-a73039e2ca18/style.json?key=ktSs6eRMmo4o70YLtDSA'

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
    const markerImage = venue.image || 'https://images.unsplash.com/photo-1540747913346-19e32dc3e97e?q=80&w=200&auto=format&fit=crop'

    // Créer l'élément HTML pour le marqueur
    const el = document.createElement('div')
    el.className = 'custom-venue-marker'
    el.innerHTML = `
      <div class="venue-marker-container">
        <div class="venue-marker-image" style="background-image: url('${markerImage}')"></div>
      </div>
    `

    // Ajouter l'événement click
    el.addEventListener('click', () => {
      emit('select-venue', venue.id)
      emit('venue-clicked', venue)
      // Centrer la carte sur le marqueur cliqué
      map.flyTo({
        center: [venue.lng, venue.lat],
        zoom: 8,
        duration: 500
      })
    })

    const marker = new maplibregl.Marker({ element: el })
      .setLngLat([venue.lng, venue.lat])
      .addTo(map)

    markers.push({ id: venue.id, marker, type: venue.type })
  })
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
        center: MAP_CENTER,
        zoom: MAP_ZOOM,
        attributionControl: false
      })

      // Ajouter les contrôles de zoom
      map.addControl(new maplibregl.default.NavigationControl({ showCompass: false }), 'bottom-right')

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
      center: MAP_CENTER,
      zoom: MAP_ZOOM,
      duration: 500
    })
  }
})

// Watch for venue selection changes
watch(() => props.selectedVenue, (newId) => {
  if (map && newId) {
    const venue = props.venues.find(v => v.id === newId)
    if (venue) {
      map.flyTo({
        center: [venue.lng, venue.lat],
        zoom: 10,
        duration: 500
      })
    }
  }
})
</script>

<template>
  <div class="venue-map-wrapper">
    <div ref="mapContainer" class="venue-map-container"></div>
  </div>
</template>

<style>
.venue-map-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
}

.venue-map-container {
  width: 100%;
  height: 100%;
  background-color: #e8f4f4;
  border-radius: 0;
  overflow: hidden;
}

/* Marqueur personnalisé avec image */
.custom-venue-marker {
  cursor: pointer;
}

.venue-marker-container {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  border: 3px solid #FFFFFF;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25), 0 0 0 2px #333333;
  overflow: hidden;
  background: #FFFFFF;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.venue-marker-container:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3), 0 0 0 2px #333333;
}

.venue-marker-image {
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}

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

.maplibregl-ctrl-group button + button {
  border-top: 1px solid #eee !important;
}
</style>
