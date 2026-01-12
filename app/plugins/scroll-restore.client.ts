export default defineNuxtPlugin(() => {
  const router = useRouter()
  
  // Restaure la position sauvegardée au chargement
  if (process.client) {
    // Attendre que le DOM soit prêt
    window.addEventListener('load', () => {
      const savedPosition = sessionStorage.getItem('scrollPosition')
      if (savedPosition) {
        const position = JSON.parse(savedPosition)
        // Restaurer avec un léger délai pour s'assurer que la page est rendue
        setTimeout(() => {
          window.scrollTo({
            top: position.y,
            left: position.x,
            behavior: 'instant'
          })
          // Nettoyer après restauration
          sessionStorage.removeItem('scrollPosition')
        }, 100)
      }
    })

    // Sauvegarder la position avant le rechargement
    window.addEventListener('beforeunload', () => {
      const position = {
        x: window.scrollX,
        y: window.scrollY,
        path: router.currentRoute.value.fullPath
      }
      sessionStorage.setItem('scrollPosition', JSON.stringify(position))
    })

    // Sauvegarder aussi lors de la navigation (pour back/forward)
    router.afterEach((to, from) => {
      // Sauvegarder la position de la page précédente
      if (from.fullPath) {
        const position = {
          x: window.scrollX,
          y: window.scrollY,
          path: from.fullPath
        }
        sessionStorage.setItem(`scroll_${from.fullPath}`, JSON.stringify(position))
      }
    })

    // Restaurer lors du retour arrière
    router.beforeEach((to, from, next) => {
      const savedScroll = sessionStorage.getItem(`scroll_${to.fullPath}`)
      if (savedScroll) {
        const position = JSON.parse(savedScroll)
        // Attendre que la navigation soit terminée
        nextTick(() => {
          setTimeout(() => {
            window.scrollTo({
              top: position.y,
              left: position.x,
              behavior: 'smooth'
            })
          }, 50)
        })
      }
      next()
    })
  }
})
