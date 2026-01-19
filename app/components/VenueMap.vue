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

    // Icône du stade (stadiumIcon.svg)
    const stadiumIcon = document.createElement('img')
    stadiumIcon.className = 'venue-marker-icon'
    stadiumIcon.src = '/images/stadiumIcon.svg'
    stadiumIcon.alt = venue.name || 'Stadium'

    containerDiv.appendChild(stadiumIcon)

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

/* Marqueur personnalisé avec icône stade */
.custom-venue-marker {
  cursor: pointer;
}

.venue-marker-container {
  position: relative;
  width: 79.53px;
  height: 84px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.25s ease;
}

.venue-marker-container:hover {
  transform: scale(1.5);
}

/* Icône du stade */
.venue-marker-icon {
  width: 79.53px;
  height: 84px;
  object-fit: contain;
  filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.3));
  transition: transform 0.25s ease;
}

/* Logo de la venue en exposant (haut droite) */
.venue-marker-logo {
  position: absolute;
  top: -4px;
  right: -8px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid #333;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.venue-marker-logo img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

/* Wrapper pour tooltip + marker */
.venue-marker-wrapper {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.venue-marker-tooltip {
  position: absolute;
  bottom: 100%;
  transform: translateY(-8px);
  background: rgba(13,10,0,0.95);
  color: #fff;
  padding: 6px 10px;
  border-radius: 6px;
  font-weight: 700;
  font-size: 13px;
  white-space: nowrap;
  box-shadow: 0 6px 18px rgba(0,0,0,0.35);
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.18s ease, transform 0.18s ease;
}

/* Afficher le tooltip au survol */
.venue-marker-wrapper:hover .venue-marker-tooltip {
  opacity: 1;
  transform: translateY(-12px);
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
