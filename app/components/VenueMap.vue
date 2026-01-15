<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'

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
const MAP_CENTER: [number, number] = [48.5, 2.0]
const MAP_ZOOM = 5

const createMarkers = async (L: any) => {
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

    const customIcon = L.divIcon({
      className: 'custom-venue-marker',
      html: `
        <div class="venue-marker-container">
          <div class="venue-marker-image" style="background-image: url('${markerImage}')"></div>
        </div>
      `,
      iconSize: [56, 56],
      iconAnchor: [28, 28]
    })

    const marker = L.marker([venue.lat, venue.lng], { icon: customIcon })
      .addTo(map)
      .on('click', () => {
        emit('select-venue', venue.id)
        emit('venue-clicked', venue)
        // Centrer la carte sur le marqueur cliqué
        map.flyTo([venue.lat, venue.lng], 8, { duration: 0.5 })
      })

    markers.push({ id: venue.id, marker, type: venue.type })
  })
}

onMounted(async () => {
  if (typeof window !== 'undefined') {
    const L = await import('leaflet')
    await import('leaflet/dist/leaflet.css')

    if (mapContainer.value) {
      // Initialiser la carte centrée pour voir France et sud de l'Angleterre
      map = L.map(mapContainer.value, {
        zoomControl: false,
        scrollWheelZoom: true,
        attributionControl: false
      }).setView(MAP_CENTER, MAP_ZOOM)

      // Utiliser CartoDB Positron (style épuré, similaire au thème Snazzy Maps)
      L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
        attribution: '',
        maxZoom: 19
      }).addTo(map)

      // Ajouter les contrôles de zoom en bas à droite
      L.control.zoom({
        position: 'bottomright'
      }).addTo(map)

      // Créer les marqueurs
      await createMarkers(L)
    }
  }
})

// Watch for filter changes
watch(() => props.activeFilter, async () => {
  if (map && typeof window !== 'undefined') {
    const L = await import('leaflet')
    await createMarkers(L)
    // Réinitialiser le zoom à la vue initiale
    map.flyTo(MAP_CENTER, MAP_ZOOM, { duration: 0.5 })
  }
})

// Watch for venue selection changes
watch(() => props.selectedVenue, (newId) => {
  if (map && newId) {
    const venue = props.venues.find(v => v.id === newId)
    if (venue) {
      map.flyTo([venue.lat, venue.lng], 10, { duration: 0.5 })
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
  background-color: #e0efef;
  border-radius: 0;
  overflow: hidden;
}

/* Style personnalisé pour simuler le thème Snazzy Maps (mint/teal) */
.venue-map-container :deep(.leaflet-tile-pane) {
  filter: sepia(0.2) saturate(0.8) brightness(1.05) hue-rotate(120deg);
}

.venue-map-container :deep(.leaflet-container) {
  background-color: #7dcdcd;
}

/* Marqueur personnalisé avec image */
.custom-venue-marker {
  background: transparent !important;
  border: none !important;
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

/* Override Leaflet default styles */
.leaflet-container {
  font-family: 'Plus Jakarta Sans', sans-serif;
}

/* Style des contrôles de zoom */
.leaflet-control-zoom {
  border: none !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
  border-radius: 8px !important;
  overflow: hidden;
}

.leaflet-control-zoom a {
  width: 36px !important;
  height: 36px !important;
  line-height: 36px !important;
  font-size: 18px !important;
  color: #333 !important;
  background-color: #fff !important;
  border: none !important;
  transition: background-color 0.2s ease;
}

.leaflet-control-zoom a:hover {
  background-color: #f5f5f5 !important;
  color: #FF4D6D !important;
}

.leaflet-control-zoom-in {
  border-radius: 8px 8px 0 0 !important;
  border-bottom: 1px solid #eee !important;
}

.leaflet-control-zoom-out {
  border-radius: 0 0 8px 8px !important;
}
</style>
