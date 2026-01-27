<template>
  <div class="event-card" :class="[colorClass, { 'is-even': !isEven, 'is-odd': isEven }]">
    <div class="card-image">
      <img v-if="event.image" :src="event.image" :alt="event.title" loading="lazy" />
      <span class="event-type-badge">{{ event.event_type }}</span>
    </div>
    <div
      class="card-content"
      :style="{
        backgroundColor: currentColor,
        borderColor: currentColor
      }"
    >
      <div class="torn-edge" :class="{ 'torn-edge-right': isEven, 'torn-edge-left': !isEven }"></div>
      <h3 class="preserve-lines">{{ event.title }}</h3>
      <p class="description preserve-lines">{{ event.description }}</p>
      <NuxtLink :to="`/contact`">
        <img
          :src="isEven ? '/images/contact-even.svg' : '/images/contact-odd.svg'"
          alt="Contact"
          class="contact-image"
        />
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Event } from '~/composables/useEvents'

const props = defineProps<{
  event: Event
  colorIndex?: number
}>()

const colors = [
  '#a7f49d', // green
  '#ffffff', // white
  '#d7a8ff', // purple
  '#93cbff', // blue
  '#ffffff', // white
  '#f93257'  // red/pink
]

const colorNames = ['green', 'white', 'purple', 'blue', 'white-alt', 'red']

const currentIndex = computed(() => (props.colorIndex ?? 0) % colors.length)

const currentColor = computed(() => colors[currentIndex.value])

const colorClass = computed(() => `color-${colorNames[currentIndex.value]}`)

const isEven = computed(() => (props.colorIndex ?? 0) % 2 === 0)
</script>

<style scoped lang="scss">
.event-card {
  display: flex;
  background: white;
  border-radius: 12px;
  overflow: visible;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Even: image right, content left */
.event-card.is-even {
  flex-direction: row-reverse;
}

/* Odd: image left, content right */
.event-card.is-odd {
  flex-direction: row;
}

.event-card:hover {
  transform: translate(-2px, -2px);
}

.card-image {
  position: relative;
  flex: 1;
  min-height: 300px;
  overflow: hidden;
}

.is-even .card-image {
  border-radius: 0 12px 12px 0;
}

.is-odd .card-image {
  border-radius: 12px 0 0 12px;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.event-card:hover .card-image img {
  transform: scale(1.05);
}

.event-type-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: #FF4D6D;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 600;
}

.card-content {
  position: relative;
  flex: 1;
  padding: 1.5rem;
  padding-left: 2rem;
  padding-right: 2rem;
  border: 3px solid;
  display: flex;
  flex-direction: column;
  justify-content: space-evenly;
  h3{
    position: relative;
  }
  a{ width: 287px;
  height: 70px;
    img {
 width: 100%;
  height: 100%;  }
}
}

.is-even .card-content {
  border-right: none;
  border-radius: 12px 0 0 12px;
}

.is-odd .card-content {
  border-left: none;
  border-radius: 0 12px 12px 0;
}

/* Torn edge on the left side (for odd cards - content is on right) */
.torn-edge-left {
  position: absolute;
  top: -3px;
  bottom: -3px;
  left: -12px;
  width: 20px;
  background: inherit;
  background-color: inherit;
  clip-path: polygon(
    50% 0%,
    60% 2%, 45% 4%, 55% 6%, 48% 8%, 58% 10%,
    42% 12%, 55% 14%, 47% 16%, 60% 18%, 45% 20%,
    55% 22%, 48% 24%, 58% 26%, 42% 28%, 55% 30%,
    47% 32%, 60% 34%, 45% 36%, 55% 38%, 48% 40%,
    58% 42%, 42% 44%, 55% 46%, 47% 48%, 60% 50%,
    45% 52%, 55% 54%, 48% 56%, 58% 58%, 42% 60%,
    55% 62%, 47% 64%, 60% 66%, 45% 68%, 55% 70%,
    48% 72%, 58% 74%, 42% 76%, 55% 78%, 47% 80%,
    60% 82%, 45% 84%, 55% 86%, 48% 88%, 58% 90%,
    42% 92%, 55% 94%, 47% 96%, 60% 98%, 50% 100%,
    100% 100%, 100% 0%
  );
}

/* Torn edge on the right side (for even cards - content is on left) */
.torn-edge-right {
  position: absolute;
  top: -3px;
  bottom: -3px;
  right: -12px;
  width: 20px;
  background: inherit;
  background-color: inherit;
  clip-path: polygon(
    50% 0%,
    40% 2%, 55% 4%, 45% 6%, 52% 8%, 42% 10%,
    58% 12%, 45% 14%, 53% 16%, 40% 18%, 55% 20%,
    45% 22%, 52% 24%, 42% 26%, 58% 28%, 45% 30%,
    53% 32%, 40% 34%, 55% 36%, 45% 38%, 52% 40%,
    42% 42%, 58% 44%, 45% 46%, 53% 48%, 40% 50%,
    55% 52%, 45% 54%, 52% 56%, 42% 58%, 58% 60%,
    45% 62%, 53% 64%, 40% 66%, 55% 68%, 45% 70%,
    52% 72%, 42% 74%, 58% 76%, 45% 78%, 53% 80%,
    40% 82%, 55% 84%, 45% 86%, 52% 88%, 42% 90%,
    58% 92%, 45% 94%, 53% 96%, 40% 98%, 50% 100%,
    0% 100%, 0% 0%
  );
}


.description {  font-family: FONTSPRINGDEMO-RecoletaMedium;
  font-size: 18px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: 1.56;
  letter-spacing: normal;
  text-align: left;
  color: #000;
}


/* Responsive: stack on mobile */
@media (max-width: 768px) {
  .event-card.is-even,
  .event-card.is-odd {
    flex-direction: column;
  }

  .card-image {
    min-height: 200px;
  }

  .is-even .card-image,
  .is-odd .card-image {
    border-radius: 12px 12px 0 0;
  }

  .is-even .card-content,
  .is-odd .card-content {
    border: 3px solid;
    border-top: none;
    border-radius: 0 0 12px 12px;
  }

  .torn-edge-left,
  .torn-edge-right {
    top: -12px;
    left: -3px;
    right: -3px;
    bottom: auto;
    width: auto;
    height: 20px;
    clip-path: polygon(
      0% 50%,
      2% 60%, 4% 45%, 6% 55%, 8% 48%, 10% 58%,
      12% 42%, 14% 55%, 16% 47%, 18% 60%, 20% 45%,
      22% 55%, 24% 48%, 26% 58%, 28% 42%, 30% 55%,
      32% 47%, 34% 60%, 36% 45%, 38% 55%, 40% 48%,
      42% 58%, 44% 42%, 46% 55%, 48% 47%, 50% 60%,
      52% 45%, 54% 55%, 56% 48%, 58% 58%, 60% 42%,
      62% 55%, 64% 47%, 66% 60%, 68% 45%, 70% 55%,
      72% 48%, 74% 58%, 76% 42%, 78% 55%, 80% 47%,
      82% 60%, 84% 45%, 86% 55%, 88% 48%, 90% 58%,
      92% 42%, 94% 55%, 96% 47%, 98% 60%, 100% 50%,
      100% 100%, 0% 100%
    );
  }
}

.card-content {
  h3 {
    font-family: FONTSPRINGDEMO-RecoletaBold;
  font-size: 50px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  text-align: left;
  color:black;
}
}

.color-green{
  h3{
    z-index:1;
    &::before{
 z-index: -1;
      position: absolute;
      content: "";
      background: url(/images/decoHeaderBg.svg);
      background-size: contain;
      width: 100vw;
      max-width: 400px;
      height: 100vh;
      max-height: 80px;
      background-repeat: no-repeat;
      top: 1vh;
      left: -1vw;
    }
  }

}
.color-white{
  h3{
    z-index:1;
    &::before{
 z-index: -1;
      position: absolute;
      content: "";
      background: url(/images/events/02.svg);
      background-size: contain;
      width: 100vw;
      max-width: 400px;
      height: 100vh;
      max-height: 80px;
      background-repeat: no-repeat;
      top: 2vh;
      left: -1vw;
    }
  }

}
.color-purple{
  h3{
    z-index:1;
    &::before{
 z-index: -1;
      position: absolute;
      content: "";
      background: url(/images/decoHeaderBg.svg);
      background-size: contain;
      width: 100vw;
      max-width: 400px;
      height: 100vh;
      max-height: 80px;
      background-repeat: no-repeat;
      top: 1vh;
      left: -1vw;
    }
  }
}
.color-blue{
  h3{
    z-index:1;
    &::before{
 z-index: -1;
      position: absolute;
      content: "";
      background: url(/images/decoHeaderBg.svg);
      background-size: contain;
      width: 100vw;
      max-width: 400px;
      height: 100vh;
      max-height: 80px;
      background-repeat: no-repeat;
      top: 1vh;
      left: -1vw;
    }
  }
}
.color-white-alt{
  h3{
    z-index:1;
    &::before{
 z-index: -1;
      position: absolute;
      content: "";
      background: url(/images/events/03.svg);
      background-size: contain;
      width: 100vw;
      max-width: 400px;
      height: 100vh;
      max-height: 80px;
      background-repeat: no-repeat;
      top: 1vh;
      left: -1vw;
    }
  }
}
.color-red{
    .description  {
      color:white !important;
    }
  h3{
    z-index:1;
    color:white !important;
    &::before{
      display:none !important;
 z-index: -1;
      position: absolute;
      content: "";
      background: url(/images/decoHeaderBg.svg);
      background-size: contain;
      width: 100vw;
      max-width: 400px;
      height: 100vh;
      max-height: 80px;
      background-repeat: no-repeat;
      top: 1vh;
      left: -1vw;
    }
  
  }
}
</style>
