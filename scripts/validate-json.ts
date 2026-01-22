/**
 * Validation des fichiers JSON - Eat Is Family
 * 
 * Script de validation pour vérifier la structure et l'intégrité des fichiers JSON
 */

interface ValidationResult {
  file: string
  valid: boolean
  errors: string[]
  warnings: string[]
  size: string
}

interface BlogPost {
  id: number
  slug: string
  title: { rendered: string }
  excerpt: { rendered: string }
  content: { rendered: string }
  date: string
  featured_media: string
  // Champs optionnels
  author?: { name: string; avatar: string }
  reading_time?: string
  categories?: Array<{ id: number; name: string }>
}

interface Activity {
  id: number
  slug: string
  title: { rendered: string }
  description: string
  content: { rendered: string }
  date: string
  location: string
  capacity: number
  available_spots: number
  category: string
  price: string
  duration: string
  featured_media: string
  status: string
}

interface Event {
  id: number
  title: string
  image: string
  description: string
  event_type: string
}

interface Job {
  id: number
  slug: string
  title: { rendered: string }
  excerpt: { rendered: string }
  content: { rendered: string }
  venue_id: string
  department: string
  job_type: string
  salary: string
  requirements: string[]
  benefits: string[]
  featured_media: string
}

/**
 * Valide la structure d'un blog post
 */
function validateBlogPost(post: any, index: number): string[] {
  const errors: string[] = []
  
  if (!post.id) errors.push(`Post ${index}: Missing id`)
  if (!post.slug) errors.push(`Post ${index}: Missing slug`)
  if (!post.title?.rendered) errors.push(`Post ${index}: Missing title.rendered`)
  if (!post.excerpt?.rendered) errors.push(`Post ${index}: Missing excerpt.rendered`)
  if (!post.content?.rendered) errors.push(`Post ${index}: Missing content.rendered`)
  if (!post.date) errors.push(`Post ${index}: Missing date`)
  if (!post.featured_media) errors.push(`Post ${index}: Missing featured_media`)
  
  return errors
}

/**
 * Valide la structure d'une activité
 */
function validateActivity(activity: any, index: number): string[] {
  const errors: string[] = []
  
  if (!activity.id) errors.push(`Activity ${index}: Missing id`)
  if (!activity.slug) errors.push(`Activity ${index}: Missing slug`)
  if (!activity.title?.rendered) errors.push(`Activity ${index}: Missing title.rendered`)
  if (!activity.description) errors.push(`Activity ${index}: Missing description`)
  if (!activity.date) errors.push(`Activity ${index}: Missing date`)
  if (!activity.location) errors.push(`Activity ${index}: Missing location`)
  if (typeof activity.capacity !== 'number') errors.push(`Activity ${index}: Invalid capacity`)
  if (!activity.category) errors.push(`Activity ${index}: Missing category`)
  
  return errors
}

/**
 * Valide la structure d'un événement
 */
function validateEvent(event: any, index: number): string[] {
  const errors: string[] = []
  
  if (!event.id) errors.push(`Event ${index}: Missing id`)
  if (!event.title) errors.push(`Event ${index}: Missing title`)
  if (!event.image) errors.push(`Event ${index}: Missing image`)
  if (!event.description) errors.push(`Event ${index}: Missing description`)
  if (!event.event_type) errors.push(`Event ${index}: Missing event_type`)
  
  return errors
}

/**
 * Valide la structure d'un job
 */
function validateJob(job: any, index: number): string[] {
  const errors: string[] = []
  
  if (!job.id) errors.push(`Job ${index}: Missing id`)
  if (!job.slug) errors.push(`Job ${index}: Missing slug`)
  if (!job.title?.rendered) errors.push(`Job ${index}: Missing title.rendered`)
  if (!job.venue_id) errors.push(`Job ${index}: Missing venue_id`)
  if (!job.department) errors.push(`Job ${index}: Missing department`)
  if (!job.job_type) errors.push(`Job ${index}: Missing job_type`)
  if (!Array.isArray(job.requirements)) errors.push(`Job ${index}: Invalid requirements`)
  if (!Array.isArray(job.benefits)) errors.push(`Job ${index}: Invalid benefits`)
  
  return errors
}

/**
 * Détecte les clés dupliquées dans un objet JSON
 */
function detectDuplicateKeys(obj: any, path: string = ''): string[] {
  const errors: string[] = []
  
  if (typeof obj !== 'object' || obj === null) return errors
  
  const keys = new Set<string>()
  for (const key in obj) {
    if (keys.has(key)) {
      errors.push(`Duplicate key "${key}" at path: ${path}`)
    }
    keys.add(key)
    
    // Récursion pour les objets imbriqués
    const newPath = path ? `${path}.${key}` : key
    errors.push(...detectDuplicateKeys(obj[key], newPath))
  }
  
  return errors
}

/**
 * Valide un fichier JSON
 */
async function validateJsonFile(filePath: string, validator?: (data: any) => string[]): Promise<ValidationResult> {
  // @ts-ignore
  const fs = await import('fs')
  // @ts-ignore
  const path = await import('path')
  
  const result: ValidationResult = {
    file: path.basename(filePath),
    valid: true,
    errors: [],
    warnings: [],
    size: ''
  }
  
  try {
    // Lire le fichier
    const content = fs.readFileSync(filePath, 'utf-8')
    result.size = `${(content.length / 1024).toFixed(1)}K`
    
    // Parser le JSON
    let data
    try {
      data = JSON.parse(content)
    } catch (e: any) {
      result.valid = false
      result.errors.push(`Invalid JSON: ${e.message}`)
      return result
    }
    
    // Détecter les clés dupliquées
    const duplicates = detectDuplicateKeys(data)
    if (duplicates.length > 0) {
      result.valid = false
      result.errors.push(...duplicates)
    }
    
    // Validation personnalisée
    if (validator) {
      const validationErrors = validator(data)
      if (validationErrors.length > 0) {
        result.valid = false
        result.errors.push(...validationErrors)
      }
    }
    
  } catch (e: any) {
    result.valid = false
    result.errors.push(`Error reading file: ${e.message}`)
  }
  
  return result
}

/**
 * Valide tous les fichiers JSON
 */
async function validateAllFiles() {
  // @ts-ignore
  const path = await import('path')
  // @ts-ignore
  const publicApiPath = path.join(process.cwd(), 'public/api')
  
  console.log('🔍 Validation des fichiers JSON...\n')
  
  const validations = [
    {
      file: 'blog-posts.json',
      validator: (data: any[]) => {
        const errors: string[] = []
        if (!Array.isArray(data)) {
          errors.push('Expected an array of blog posts')
          return errors
        }
        data.forEach((post, i) => errors.push(...validateBlogPost(post, i)))
        return errors
      }
    },
    {
      file: 'activities.json',
      validator: (data: any[]) => {
        const errors: string[] = []
        if (!Array.isArray(data)) {
          errors.push('Expected an array of activities')
          return errors
        }
        data.forEach((activity, i) => errors.push(...validateActivity(activity, i)))
        return errors
      }
    },
    {
      file: 'events.json',
      validator: (data: any[]) => {
        const errors: string[] = []
        if (!Array.isArray(data)) {
          errors.push('Expected an array of events')
          return errors
        }
        data.forEach((event, i) => errors.push(...validateEvent(event, i)))
        return errors
      }
    },
    {
      file: 'jobs.json',
      validator: (data: any[]) => {
        const errors: string[] = []
        if (!Array.isArray(data)) {
          errors.push('Expected an array of jobs')
          return errors
        }
        data.forEach((job, i) => errors.push(...validateJob(job, i)))
        return errors
      }
    },
    {
      file: 'pages-content.json',
      validator: undefined
    },
    {
      file: 'site-content.json',
      validator: undefined
    },
    {
      file: 'venues.json',
      validator: undefined
    }
  ]
  
  const results: ValidationResult[] = []
  let totalErrors = 0
  let totalWarnings = 0
  
  for (const { file, validator } of validations) {
    const filePath = path.join(publicApiPath, file)
    const result = await validateJsonFile(filePath, validator)
    results.push(result)
    totalErrors += result.errors.length
    totalWarnings += result.warnings.length
  }
  
  // Afficher les résultats
  console.log('┌────────────────────────┬────────┬─────────┬──────────┬──────────┐')
  console.log('│ Fichier                │ Taille │ Statut  │ Erreurs  │ Warnings │')
  console.log('├────────────────────────┼────────┼─────────┼──────────┼──────────┤')
  
  for (const result of results) {
    const status = result.valid ? '✅ OK  ' : '❌ FAIL'
    const fileName = result.file.padEnd(22)
    const size = result.size.padEnd(6)
    const errors = result.errors.length.toString().padEnd(8)
    const warnings = result.warnings.length.toString().padEnd(8)
    
    console.log(`│ ${fileName} │ ${size} │ ${status} │ ${errors} │ ${warnings} │`)
    
    if (!result.valid) {
      result.errors.forEach(err => {
        console.log(`│   └─ ${err}`)
      })
    }
  }
  
  console.log('└────────────────────────┴────────┴─────────┴──────────┴──────────┘')
  console.log(`\n📊 Total: ${results.filter(r => r.valid).length}/${results.length} fichiers valides`)
  console.log(`   Erreurs: ${totalErrors}`)
  console.log(`   Warnings: ${totalWarnings}\n`)
  
  if (totalErrors === 0) {
    console.log('✅ Tous les fichiers JSON sont valides!\n')
    // @ts-ignore
    process.exit(0)
  } else {
    console.log('❌ Des erreurs ont été détectées dans les fichiers JSON\n')
    // @ts-ignore
    process.exit(1)
  }
}

// Exécuter la validation
// @ts-ignore
validateAllFiles()

export { validateAllFiles, validateJsonFile, ValidationResult }
