// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',

  modules: [
    '@nuxt/image',
    '@nuxtjs/google-fonts',
    '@nuxtjs/leaflet'
  ],

  googleFonts: {
    families: {
      'Plus Jakarta Sans': [400, 500, 600, 700],
      'Inter': [400, 500, 600, 700],
    },
    display: 'swap'
  },

  app: {
    head: {
      charset: 'utf-8',
      viewport: 'width=device-width, initial-scale=1',
      title: 'Eat Is Family - Celebrate Food Every Day',
      meta: [
        { name: 'description', content: 'Discover amazing culinary experiences, join exciting food events, and explore career opportunities in the food industry.' },
        { name: 'keywords', content: 'food, culinary, cooking, events, jobs, careers, recipes, blog' },
        { property: 'og:title', content: 'Eat Is Family' },
        { property: 'og:description', content: 'Celebrate Food Every Day' },
        { property: 'og:type', content: 'website' }
      ],
    },
    // Préserve la position de scroll lors de la navigation
    pageTransition: { name: 'page', mode: 'out-in' }
  },

  css: [
    'bootstrap/dist/css/bootstrap.min.css',
    '~/assets/scss/main.scss'
  ],

  vite: {
    css: {
      devSourcemap: true, // Active les source maps CSS pour Chrome DevTools
      preprocessorOptions: {
        scss: {
          additionalData: '@use "~/assets/scss/_variables.scss" as *;'
        }
      }
    }
  },

  components: [
    {
      path: '~/components',
      pathPrefix: true,
    }
  ],

  devtools: { enabled: true }
})
