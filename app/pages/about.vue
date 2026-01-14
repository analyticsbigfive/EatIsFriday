<template>
  <div class="about-page">
    <section v-if="siteContent" id="aboutHero" class="page-hero">
      <div class="container-fluid d-flex flex-column justify-content-center align-items-center h-100 mt-5">
        <h1 class="heroTitle">{{ siteContent.about.hero.title }}</h1>
        <div class="d-flex">
          <div class="row">
            <div class="col-5 d-flex flex-column">
              <div class="heroTextContainer d-flex flex-column">
                <h2 class="heroSubtitle">{{ siteContent.about.hero.subtitle }}</h2>
                <p class="heroDescription">{{ siteContent.about.hero.description }}</p>
                <nuxt-link to="/">
                  <nuxt-img :src="siteContent.about.hero.buttonContact" alt="Contact Us"
                    class="contactButton img-fluid fluid-img" />

                </nuxt-link>
              </div>
            </div>
            <div class="col-7">
              <img :src="siteContent.about.hero.image.src" :alt="siteContent.about.hero.image.alt"
                class="img-fluid heroImage" />
            </div>
          </div>
        </div>
      </div>

    </section>
    <section v-if="siteContent" id="timeline">
      <h3 class="timeline-title white-text recoleta preserve-lines">{{ siteContent.about.timeline.title }}</h3>

      <div class="timeline-wrapper">
        <!-- Navigation arrows -->
        <button v-if="allEvents.length > 1" class="timeline-nav timeline-nav-prev" :class="{ disabled: !canGoPrev }"
          :disabled="!canGoPrev" @click="goToPrevEvent">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
        </button>
        <button v-if="allEvents.length > 1" class="timeline-nav timeline-nav-next" :class="{ disabled: !canGoNext }"
          :disabled="!canGoNext" @click="goToNextEvent">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
        </button>

        <!-- Sound Wave with Playhead -->
        <div ref="soundWaveContainer" class="sound-wave-container">
          <!-- Dashed center line -->
          <div class="center-line"></div>

          <!-- Playhead (moves horizontally like a cursor) -->
          <div class="playhead" :class="{ 'dragging': isDragging }" :style="{ left: playheadX + '%' }"
            @mousedown="startDrag" @touchstart="startDrag">
            <!-- Top square -->
            <div class="playhead-top-marker"></div>
            <!-- Vertical line -->
            <div class="playhead-line"></div>
            <!-- Circle on the wave line -->
            <div class="playhead-circle"></div>
            <!-- Year label at bottom -->
            <div class="playhead-year">{{ selectedEvent ?
              getYearFromDate(selectedEvent.year) : '2015' }}</div>

            <!-- Event Card (positioned relative to playhead) -->
            <div v-if="selectedEvent && isCardVisible" class="event-card" :class="{ 'event-card-left': isLastEvent }">
              <div class="event-card-content">
                <!-- Close button -->
                <button class="event-card-close" @click.stop="closeEventCard">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                      stroke-linejoin="round" />
                  </svg>
                </button>
                <div class="event-card-image">
                  <nuxt-img v-if="selectedEvent.eventImage" :src="selectedEvent.eventImage"
                    :alt="selectedEvent.title" />
                </div>
                <div class="event-card-info">
                  <h4 class="event-card-title">{{ selectedEvent.title }}</h4>
                  <p class="event-card-date">{{ selectedEvent.year }}</p>
                  <p class="event-card-description">{{ selectedEvent.event }}</p>
                  <button class="share-button">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M12 5.5C13.1046 5.5 14 4.60457 14 3.5C14 2.39543 13.1046 1.5 12 1.5C10.8954 1.5 10 2.39543 10 3.5C10 4.60457 10.8954 5.5 12 5.5Z"
                        stroke="currentColor" stroke-width="1.5" />
                      <path
                        d="M4 10C5.10457 10 6 9.10457 6 8C6 6.89543 5.10457 6 4 6C2.89543 6 2 6.89543 2 8C2 9.10457 2.89543 10 4 10Z"
                        stroke="currentColor" stroke-width="1.5" />
                      <path
                        d="M12 14.5C13.1046 14.5 14 13.6046 14 12.5C14 11.3954 13.1046 10.5 12 10.5C10.8954 10.5 10 11.3954 10 12.5C10 13.6046 10.8954 14.5 12 14.5Z"
                        stroke="currentColor" stroke-width="1.5" />
                      <path d="M5.7 9L10.3 11.5M10.3 4.5L5.7 7" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    Share This Story
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Sound wave bars - Organic waveform with cosine curve -->
          <div class="sound-wave">
            <div v-for="(bar, i) in waveBarData" :key="i" class="wave-bar-wrapper">
              <div class="wave-bar" :style="{ height: bar.height + '%' }"></div>
            </div>
          </div>

          <!-- Event markers (dots on the center line at peaks) -->
          <div v-for="(event, index) in allEvents" :key="'marker-' + event.id" class="wave-marker"
            :class="{ active: selectedEvent?.id === event.id }"
            :style="{ left: getMarkerPosition(index as number) + '%' }" @click="selectEvent(event, index as number)">
            <div class="wave-marker-dot" :style="{ background: getMarkerColor(index as number) }"></div>
          </div>
        </div>
      </div>

      <p class="timeline-instruction">{{ siteContent.about.timeline.subtitle }}</p>
    </section>
    <section v-if="siteContent?.about?.mission" id="mission">
      <div class="container-fluid">
        <h3 class="font-heading preserve-lines mt-5">{{ siteContent.about.mission.title }}</h3>
        <img :src="siteContent.about.mission.image" :alt="siteContent.about.mission.image.alt" class="img-fluid my-4" />
      </div>
      <div id="lasalle" class="row mt-4 p-3">
        <div v-for="(encadre, index) in siteContent.about.mission.encadres" :key="index" :id="`encadres-${index}`"
          class="col-md-6 decradr">
          <div class="inner-encadre">
            <h3 class="mb-2">{{ encadre.title }}</h3>
            <p class="mt-5 mb-5">{{ encadre.desc }}</p>
            <a v-if="encadre.link" :href="encadre.link" class="btn"><img :src="encadre.btn" alt=""></a>
          </div>
        </div>
      </div>
    </section>
    <section v-if="siteContent?.about?.chiffresCles" id="chiffres-cles" ref="chiffresSection">
      <div class="container-fluid">
        <div class="chiffres-grid">
          <div v-for="(stat, index) in siteContent.about.chiffresCles.stats" :key="index" class="chiffre-card">
            <span class="chiffre-number" :data-target="parseNumber(stat.number)"
              :data-suffix="parseSuffix(stat.number)">
              0{{ parseSuffix(stat.number) }}
            </span>
            <span class="chiffre-label preserve-lines">{{ stat.label }}</span>
          </div>
        </div>
      </div>
    </section>
    <section v-if="siteContent?.about?.vision" id="vision" class="my-5">
      <div class="container-fluid">
        <TwoColumnText :title="siteContent.about.vision.title" :text="siteContent.about.vision.content" />
        <img :src="siteContent.about.vision.image" :alt="siteContent.about.vision.image.alt"
          class="img-fluid my-4 rounded w-100" />
        <CtaBlock :text="homepageContent?.homepageCTA.additionalText" :link="homepageContent?.homepageCTA.link"
          :button-image="homepageContent?.homepageCTA.button2" :button-alt="homepageContent?.homepageCTA.title" />
      </div>
    </section>
    <GalleryGrid v-if="siteContent?.about?.gallery_section?.images"
      :images="siteContent.about.gallery_section.images" />
    <PartnersSection v-if="homepageContent?.partners" :title="homepageContent.partners_title"
      :partners="homepageContent.partners.map((p: any) => ({ ...p, name: p.alt }))" />
    
      <GalleryGrid v-if="siteContent?.about?.gallery_section2?.images"
      :images="siteContent.about.gallery_section2.images" />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { gsap } from 'gsap'

const { getSiteContent } = useSiteContent()
const { getContentByPath, getHomepageContent } = usePageContent()

const siteContent = ref<any>(null)
const pageContent = ref<any>(null)
const homepageContent = ref<any>(null)
const selectedEvent = ref<any>(null)
const playheadX = ref<number>(10) // Initial position at first event
const isLastEvent = ref<boolean>(false) // To position card on left for last event
const playheadColor = ref<string>('#FFD600') // Default playhead color (yellow)
const selectedEventIndex = ref<number>(0) // Track current event index
const isCardVisible = ref<boolean>(false) // Control card visibility

// Organic wave pattern configuration
// More bars between peaks for denser waveform like in the reference image
const BARS_BETWEEN_PEAKS = 35
const PEAK_DISTANCE = BARS_BETWEEN_PEAKS + 1
const PADDING_BARS = 20 // Bars before first and after last peak
const MIN_HEIGHT = 8   // Hauteur du creux (en %) - plus petit pour contraste
const MAX_HEIGHT = 100 // Hauteur du pic (en %)

// Total bars needed
const totalBars = computed(() => {
  const numEvents = allEvents.value.length || 1
  if (numEvents === 0) return 100
  // Distance from first to last peak is (numEvents - 1) * PEAK_DISTANCE
  // Plus padding on both sides
  return PADDING_BARS + (Math.max(0, numEvents - 1) * PEAK_DISTANCE) + PADDING_BARS
})

// Drag state
const isDragging = ref<boolean>(false)
const soundWaveContainer = ref<HTMLElement | null>(null)

// Chiffres clés
const chiffresSection = ref<HTMLElement | null>(null)
const chiffresAnimated = ref<boolean>(false)

// All events (no pagination)
const allEvents = computed(() => siteContent.value?.about?.timeline?.events || [])

// Navigation computed properties
const canGoNext = computed(() => selectedEventIndex.value < allEvents.value.length - 1)
const canGoPrev = computed(() => selectedEventIndex.value > 0)

// Parse number from string like "250+" or "300,000" or "100%"
const parseNumber = (str: string): number => {
  // Remove all non-numeric characters except dots
  const numStr = str.replace(/[^0-9.]/g, '')
  return parseFloat(numStr) || 0
}

// Get suffix like "+", "%", or nothing
const parseSuffix = (str: string): string => {
  if (str.includes('%')) return '%'
  if (str.includes('+')) return '+'
  return ''
}

// Format number with commas
const formatNumber = (num: number): string => {
  return num.toLocaleString('en-US', { maximumFractionDigits: 0 })
}

// Animate chiffres when section is visible
const animateChiffres = () => {
  if (chiffresAnimated.value || !chiffresSection.value) return
  chiffresAnimated.value = true

  const numberElements = chiffresSection.value.querySelectorAll('.chiffre-number')

  numberElements.forEach((el) => {
    const target = parseFloat(el.getAttribute('data-target') || '0')
    const suffix = el.getAttribute('data-suffix') || ''
    const obj = { value: 0 }

    gsap.to(obj, {
      value: target,
      duration: 2,
      ease: 'power2.out',
      onUpdate: () => {
        el.textContent = formatNumber(Math.round(obj.value)) + suffix
      }
    })
  })
}

onMounted(async () => {
  siteContent.value = await getSiteContent()
  pageContent.value = await getContentByPath('about')
  homepageContent.value = await getHomepageContent()

  // Initialize playhead position to first event (but don't show card)
  if (siteContent.value?.about?.timeline?.events?.length > 0) {
    const firstEvent = siteContent.value.about.timeline.events[0]
    selectedEvent.value = firstEvent
    selectedEventIndex.value = 0
    playheadX.value = getMarkerPosition(0)
    playheadColor.value = '#FFD600' // Keep playhead yellow
    isCardVisible.value = false // Don't show card initially
  }

  // Add global mouse event listeners for drag
  document.addEventListener('mousemove', handleMouseMove)
  document.addEventListener('mouseup', handleMouseUp)
  document.addEventListener('touchmove', handleTouchMove)
  document.addEventListener('touchend', handleTouchEnd)

  // Intersection Observer for chiffres clés animation
  if (chiffresSection.value) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            animateChiffres()
            observer.disconnect()
          }
        })
      },
      { threshold: 0.3 }
    )
    observer.observe(chiffresSection.value)
  }
})

onUnmounted(() => {
  // Clean up event listeners
  document.removeEventListener('mousemove', handleMouseMove)
  document.removeEventListener('mouseup', handleMouseUp)
  document.removeEventListener('touchmove', handleTouchMove)
  document.removeEventListener('touchend', handleTouchEnd)
})

// Extract year from date string like "13 JUNE 2015"
const getYearFromDate = (dateStr: string) => {
  const parts = dateStr.split(' ')
  return parts[parts.length - 1] || '2015'
}

// Get marker position as percentage
// Markers are placed exactly at the peak indices
const getMarkerPosition = (index: number): number => {
  if (allEvents.value.length === 0) return 50
  
  // Peak index for this event
  const peakIndex = PADDING_BARS + (index * PEAK_DISTANCE)
  
  // Convert to percentage of total width
  // We use (peakIndex + 0.5) to center it on the bar
  return ((peakIndex + 0.5) / totalBars.value) * 100
}

// Seeded random number generator for consistent "randomness"
const seededRandom = (seed: number): number => {
  const x = Math.sin(seed * 12.9898 + 78.233) * 43758.5453
  return x - Math.floor(x)
}

// Calculate wave bar data - Organic waveform with natural variations
const waveBarData = computed(() => {
  const bars: { height: number }[] = []
  const count = totalBars.value
  
  for (let i = 0; i < count; i++) {
    // Calculate position relative to nearest peak
    const relativePos = i - PADDING_BARS
    const peakIndex = Math.round(relativePos / PEAK_DISTANCE) * PEAK_DISTANCE + PADDING_BARS
    
    // Distance from current bar to nearest peak
    const dist = i - peakIndex
    
    // Calculate position in cycle (0 to 1) where 0 = peak, 0.5 = trough
    const positionInCycle = Math.abs(dist) / (PEAK_DISTANCE / 2)
    
    // Base cosine curve for smooth envelope
    const baseMultiplier = 0.5 + 0.5 * Math.cos(Math.PI * positionInCycle)
    
    // Add multiple layers of "noise" for organic feel
    // Layer 1: Medium frequency variation (like audio amplitude)
    const noise1 = seededRandom(i * 0.7) * 0.3 - 0.15
    
    // Layer 2: High frequency micro-variations
    const noise2 = seededRandom(i * 2.3 + 100) * 0.15 - 0.075
    
    // Layer 3: Very subtle wobble based on position
    const wobble = Math.sin(i * 0.4) * 0.08
    
    // Combine: base curve + variations (more variation near peaks, less at troughs)
    const variationStrength = 0.3 + baseMultiplier * 0.7 // More variation when taller
    const totalNoise = (noise1 + noise2 + wobble) * variationStrength
    
    // Apply noise to multiplier
    let finalMultiplier = baseMultiplier + totalNoise
    
    // Clamp to valid range
    finalMultiplier = Math.max(0.05, Math.min(1, finalMultiplier))
    
    // Calculate final height
    const heightPercent = MIN_HEIGHT + (MAX_HEIGHT - MIN_HEIGHT) * finalMultiplier
    
    bars.push({ height: heightPercent })
  }

  return bars
})

// Get marker color based on index - alternating colors as in reference
// Colors: Cyan, Coral, Yellow, Light Blue
const getMarkerColor = (index: number): string => {
  const colors: string[] = ['#4ECDC4', '#FF6B6B', '#FFD700', '#45B7D1']
  return colors[index % colors.length] || '#4ECDC4'
}

// Select event and move playhead
const selectEvent = (event: any, index: number) => {
  selectedEvent.value = event
  selectedEventIndex.value = index
  // Move playhead to the marker position
  playheadX.value = getMarkerPosition(index)
  // Playhead color stays yellow
  playheadColor.value = '#FFD600'
  // Check if this is the last event (for card positioning)
  isLastEvent.value = index >= allEvents.value.length - 2
  // Show the card
  isCardVisible.value = true
}

// Close the event card
const closeEventCard = () => {
  isCardVisible.value = false
}

// Drag handlers
const startDrag = (e: MouseEvent | TouchEvent) => {
  e.preventDefault()
  isDragging.value = true
}

const handleMouseMove = (e: MouseEvent) => {
  if (!isDragging.value) return
  updatePlayheadPosition(e.clientX)
}

const handleTouchMove = (e: TouchEvent) => {
  if (!isDragging.value || !e.touches[0]) return
  updatePlayheadPosition(e.touches[0].clientX)
}

const handleMouseUp = () => {
  if (isDragging.value) {
    isDragging.value = false
    snapToNearestEvent()
  }
}

const handleTouchEnd = () => {
  if (isDragging.value) {
    isDragging.value = false
    snapToNearestEvent()
  }
}

const updatePlayheadPosition = (clientX: number) => {
  const container = soundWaveContainer.value
  if (!container) return

  const rect = container.getBoundingClientRect()
  const x = clientX - rect.left
  const percentage = Math.max(0, Math.min(100, (x / rect.width) * 100))
  playheadX.value = percentage
}

const snapToNearestEvent = () => {
  const events = allEvents.value
  if (!events || events.length === 0) return

  // Find nearest event marker
  let nearestIndex = 0
  let nearestDistance = Infinity

  events.forEach((_event: any, index: number) => {
    const markerX = getMarkerPosition(index)
    const distance = Math.abs(playheadX.value - markerX)
    if (distance < nearestDistance) {
      nearestDistance = distance
      nearestIndex = index
    }
  })

  // Select the nearest event
  selectEvent(events[nearestIndex], nearestIndex)
}

// Navigation functions - navigate between individual events
const goToNextEvent = () => {
  if (canGoNext.value) {
    const nextIndex = selectedEventIndex.value + 1
    selectEvent(allEvents.value[nextIndex], nextIndex)
  }
}

const goToPrevEvent = () => {
  if (canGoPrev.value) {
    const prevIndex = selectedEventIndex.value - 1
    selectEvent(allEvents.value[prevIndex], prevIndex)
  }
}

useHead(() => ({
  title: pageContent.value?.seo?.title || 'About Us - Eat Is Family',
  meta: [
    { name: 'description', content: pageContent.value?.seo?.meta_description || 'Learn about Eat Is Family, our mission, vision, and the team behind our culinary experiences.' }
  ]
}))
</script>

<style scoped lang="scss">
#aboutHero {
  height: 930px;
  margin: 0 0 14px;
  padding: 22px 60.4px 56px 70px;
  border-radius: 2px;
  background: url(images/vectorBgAbout.svg) no-repeat center 20% #a7f49d !important;

  h1 {
    font-family: FONTSPRINGDEMO-RecoletaBold;
    font-size: 75px;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    line-height: 1.2;
    letter-spacing: normal;
    text-align: center;
    color: #000b0f;
  }
}

.content-section {
  margin-bottom: 3rem;

  h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #2d3748;
  }

  p {
    font-size: 1.125rem;
    line-height: 1.8;
    color: #4a5568;
  }
}

.values-section,
.team-section {
  margin-top: 4rem;

  h2 {
    font-size: 2rem;
    margin-bottom: 2rem;
    text-align: center;
    color: #2d3748;
  }
}

.value-card {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);

  h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: #FF4D6D;
  }
}

.team-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;

  img {
    width: 100%;
    height: 250px;
    object-fit: cover;
  }

  h3 {
    font-size: 1.25rem;
    margin: 1rem 0 0.5rem;
    color: #2d3748;
  }

  .role {
    color: #FF4D6D;
    font-weight: 600;
    margin-bottom: 1rem;
  }

  p:last-child {
    padding: 0 1.5rem 1.5rem;
    color: #4a5568;
  }
}

/* Timeline Styles */
#timeline {
  padding: 80px 20px 60px;
  background: #121212;
  min-height: 80vh;
  position: relative;
  overflow: hidden;

  .timeline-title {
    font-size: 48px;
    text-align: center;
    margin-bottom: 40px;
    line-height: 1.3;
    color: white;
  }
}

/* Instruction text below timeline */
.timeline-instruction {
  font-family: Georgia, 'Times New Roman', Times, serif;
  font-size: 16px;
  text-align: center;
  margin-top: 40px;
  color: rgba(255, 255, 255, 0.5);
  font-style: italic;
}

.timeline-wrapper {
  position: relative;
  width: 100%;
  max-width: 1600px;
  margin: 0 auto;
  padding: 0 80px;
}

/* Event Card - positioned relative to playhead */
.event-card {
  position: absolute;
  left: 30px;
  /* Right of the playhead line */
  top: 30px;
  display: flex;
  align-items: flex-start;
  gap: 0;
  z-index: 15;
  animation: fadeIn 0.4s ease-out;
}

/* When last event, position card to the left */
.event-card.event-card-left {
  left: auto;
  right: 30px;
  flex-direction: row-reverse;
}

.event-card-content {
  background: white;
  border-radius: 20px;
  padding: 20px;
  display: flex;
  gap: 20px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
  width: 420px;
  position: relative;
}

.event-card-close {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.1);
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  color: #333;
  z-index: 10;

  &:hover {
    background: rgba(0, 0, 0, 0.2);
    transform: scale(1.1);
  }

  svg {
    width: 16px;
    height: 16px;
  }
}

.event-card-decoration {
  height: 250px;
  width: auto;
  margin-left: -10px;
  opacity: 0.9;
}

.event-card-decoration.decoration-left {
  margin-left: 0;
  margin-right: -10px;
  transform: scaleX(-1);
}

.event-card-image {
  flex-shrink: 0;
  width: 160px;
  height: 180px;
  border-radius: 14px;
  overflow: hidden;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.event-card-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 8px 0;
}

.event-card-title {
  font-family: FONTSPRINGDEMO-RecoletaBold, serif;
  font-size: 22px;
  color: #000b0f;
  margin: 0 0 6px 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.event-card-date {
  font-size: 12px;
  color: #FF2E84;
  font-weight: 700;
  margin: 0 0 12px 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.event-card-description {
  font-size: 13px;
  line-height: 1.5;
  color: #333;
  margin: 0 0 14px 0;
  flex: 1;
}

.share-button {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: transparent;
  border: none;
  color: #FF2E84;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  padding: 0;
  transition: all 0.3s ease;

  &:hover {
    gap: 12px;
  }

  svg {
    width: 16px;
    height: 16px;
  }
}

/* Sound Wave Visualization */
/* Sound Wave Visualization */
.sound-wave-container {
  position: relative;
  width: 100%;
  max-width: 1100px; /* Constrain width to ensure bar density */
  height: 220px;
  //margin: 350px auto 0; /* Center horizontally */
}

/* Dashed center line - Horizon line */
.center-line {
  position: absolute;
  top: 73%;
  left: 0;
  right: 0;
  height: 1px;
  border-top: 1px dashed rgba(255, 255, 255, 0.15);
  transform: translateY(-50%);
  z-index: 5;
}

/* Organic Sound Wave - Single centered bars */
.sound-wave {
  display: flex;
  align-items: center; /* Centre les barres sur l'axe horizontal */
  justify-content: center;
  height: 100%;
  width: 100%;
  gap: 3px; /* Espacement entre les traits */
  position: absolute;
  top: 0;
  left: 0;
  margin-top: 50px;
  padding: 0 10px;
}

.wave-bar-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  flex: 1;
  min-width: 2px;
}

.wave-bar {
  width: 2px;
  background: #D81B60; /* Rose/Magenta vif */
  border-radius: 20px; /* Extrémités arrondies (capsule) */
  transition: height 0.3s ease;
}

/* Playhead - moves horizontally */
.playhead {
  position: absolute;
  top: -10px;
  transform: translateX(-50%);
  z-index: 30;
  transition: left 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: grab;
  user-select: none;
  pointer-events: auto;

  &.dragging {
    cursor: grabbing;
    transition: none;
  }
}

.playhead-top-marker {
  width: 14px;
  height: 14px;
  background: #FFD600;
}

.playhead-line {
  width: 3px;
  height: 300px;
  background: #FFD600;
}

.playhead-circle {
  display:none;
  position: absolute;
  top: 24%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: #FFD600;
  margin-top: 210px;
}

.playhead-year {
  font-family: 'Arial Black', Arial, sans-serif;
  font-size: 14px;
  font-weight: 900;
  padding: 8px 14px;
  color: #000;
  background: #FFD600;
  margin-top: 8px;
}


.wave-marker {
  position: absolute;
  top: 73%;
  transform: translate(-50%, -50%);
  cursor: pointer;
  z-index: 25;
  transition: all 0.3s ease;

  &:hover {
    transform: translate(-50%, -50%) scale(1.3);
    z-index: 26;
  }

  &.active {
    z-index: 27;

    .wave-marker-dot {
      width: 18px;
      height: 18px;
      box-shadow: 0 0 15px currentColor;
    }
  }
}

.wave-marker-dot {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  border: none;
  transition: all 0.3s ease;
}

/* Timeline Navigation Arrows */
.timeline-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: rgba(255, 255, 255, 0.6);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  z-index: 35;

  &:hover:not(.disabled) {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.4);
    color: white;
    transform: translateY(-50%) scale(1.05);
  }

  &.disabled {
    opacity: 0.25;
    cursor: not-allowed;
  }

  svg {
    width: 20px;
    height: 20px;
  }
}

.timeline-nav-prev {
  left: 20px;
}

.timeline-nav-next {
  right: 20px;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-50%) translateX(-20px);
  }

  to {
    opacity: 1;
    transform: translateY(-50%) translateX(0);
  }
}

/* Responsive */
@media (max-width: 1400px) {
  .event-card-content {
    width: 380px;
  }

  .event-card-image {
    width: 140px;
    height: 160px;
  }

  .event-card-decoration {
    height: 220px;
  }
}

@media (max-width: 1200px) {
  .event-card-content {
    width: 350px;
  }

  .event-card-image {
    width: 120px;
    height: 140px;
  }

  .event-card-decoration {
    display: none;
  }

  .timeline-wrapper {
    padding: 0 60px;
  }

  .sound-wave-container {
    height: 200px;
    margin-top: 320px;
  }

  .playhead {
    top: -280px;
  }

  .playhead-line {
    height: 360px;
  }

  .playhead-circle {
    margin-top: 180px;
  }
}

@media (max-width: 992px) {
  .timeline-wrapper {
    padding: 0 50px;
  }

  .event-card {
    left: 50%;
    transform: translateX(-50%);
  }

  .event-card.event-card-left {
    right: auto;
    left: 50%;
    transform: translateX(-50%);
  }

  .event-card-content {
    width: 320px;
  }

  .sound-wave-container {
    height: 180px;
    margin-top: 340px;
  }

  .playhead {
    top: -300px;
  }

  .playhead-line {
    height: 340px;
  }

  .playhead-circle {
    margin-top: 170px;
  }

  .timeline-nav-prev {
    left: 10px;
  }

  .timeline-nav-next {
    right: 10px;
  }
}

@media (max-width: 768px) {
  #timeline {
    padding: 40px 15px 40px;

    .timeline-title {
      font-size: 28px;
      margin-bottom: 30px;
    }
  }

  .timeline-wrapper {
    padding: 0 40px;
  }

  .timeline-instruction {
    font-size: 14px;
    margin-top: 30px;
  }

  .event-card-content {
    width: 280px;
    flex-direction: column;
    padding: 15px;
  }

  .event-card-image {
    width: 100%;
    height: 150px;
  }

  .event-card-title {
    font-size: 18px;
  }

  .event-card-description {
    font-size: 12px;
  }

  .sound-wave-container {
    height: 140px;
    margin-top: 320px;
  }

  .playhead {
    top: -280px;
  }

  .playhead-line {
    height: 300px;
  }

  .playhead-circle {
    margin-top: 140px;
  }

  .playhead-year {
    font-size: 12px;
    padding: 6px 10px;
  }

  .timeline-nav {
    width: 36px;
    height: 36px;

    svg {
      width: 16px;
      height: 16px;
    }
  }

  .timeline-nav-prev {
    left: 5px;
  }

  .timeline-nav-next {
    right: 5px;
  }
}

/* Chiffres Clés Section */
#chiffres-cles {
  background: #f9375b;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 350px;
}

.chiffres-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 40px;
  position: relative;
  z-index: 1;
}

.chiffre-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;

  &:hover {
    transform: translateY(-10px);
  }
}

.chiffre-number {
  font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 70px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  text-align: left;
  color: #fff;
}

.chiffre-label {
  font-family: FONTSPRINGDEMO-RecoletaSemiBold;
  font-size: 24px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  text-align: center;
  color: rgba(255, 255, 255, 0.85)
}

/* Responsive Chiffres Clés */
@media (max-width: 1200px) {
  .chiffres-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .chiffre-number {
    font-size: 60px;
  }
}

@media (max-width: 768px) {
  #chiffres-cles {
    padding: 60px 20px;
  }

  .chiffres-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .chiffre-card {
    padding: 30px 20px;
  }

  .chiffre-number {
    font-size: 48px;
  }

  .chiffre-label {
    font-size: 16px;
  }
}
</style>
