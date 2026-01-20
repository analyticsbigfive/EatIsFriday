<template>
  <button class="organic-btn" :class="variant" @click="$emit('click')">
    <svg class="organic-border" viewBox="0 0 200 60" preserveAspectRatio="none">
      <!-- Ombre floue sous le bouton -->
      <defs>
        <filter id="organic-shadow" x="-50%" y="-50%" width="200%" height="200%">
          <feGaussianBlur in="SourceAlpha" stdDeviation="3"/>
          <feOffset dx="2" dy="4" result="offsetblur"/>
          <feComponentTransfer>
            <feFuncA type="linear" slope="0.5"/>
          </feComponentTransfer>
          <feMerge>
            <feMergeNode/>
            <feMergeNode in="SourceGraphic"/>
          </feMerge>
        </filter>
      </defs>
      
      <!-- Forme organique du bouton avec texture -->
      <path 
        class="border-path"
        d="M 8,12 
           C 6,10 5,8 7,6 
           C 9,4 12,3 15,2.5 
           L 45,1.5 
           C 70,0.8 95,0.5 120,1 
           L 150,1.8 
           C 165,2 180,2.5 188,4 
           C 192,5 195,7 196,10 
           C 197,13 197,16 196.5,20 
           L 197,35 
           C 197.5,42 197,48 195,52 
           C 193,55 190,57 186,58 
           C 180,58.5 170,59 155,58.8 
           L 120,58.5 
           C 95,59 70,59.2 45,58.5 
           L 15,57.5 
           C 11,57 8,56 6,54 
           C 4,52 3,50 2.5,47 
           C 2,44 2,40 2.5,35 
           L 2,20 
           C 1.8,16 2,14 3,12 
           C 4,10 6,11 8,12 Z"
        fill="none"
        stroke="currentColor"
        stroke-width="2.5"
        stroke-linecap="round"
        stroke-linejoin="round"
        filter="url(#organic-shadow)"
      />
      
      <!-- Texture supplémentaire pour effet esquisse -->
      <path 
        class="texture-line"
        d="M 10,8 C 30,7 60,6 90,6.5 C 120,7 150,7.5 185,9"
        fill="none"
        stroke="currentColor"
        stroke-width="0.8"
        opacity="0.3"
      />
      <path 
        class="texture-line"
        d="M 10,52 C 40,51 80,51 120,51.5 C 150,52 180,52.5 190,53"
        fill="none"
        stroke="currentColor"
        stroke-width="0.8"
        opacity="0.3"
      />
    </svg>
    <span class="btn-content">
      <slot />
    </span>
  </button>
</template>

<script setup lang="ts">
defineProps<{
  variant?: 'primary' | 'secondary'
}>()

defineEmits<{
  click: []
}>()
</script>
