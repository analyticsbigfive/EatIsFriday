/**
 * Composable for API calls - supports both WordPress REST API and local JSON fallback
 * 
 * Configure via environment variables:
 * - NUXT_PUBLIC_API_BASE_URL: WordPress API base URL
 * - NUXT_PUBLIC_USE_LOCAL_FALLBACK: Set to 'true' to use local JSON files
 */
export const useApi = () => {
    const config = useRuntimeConfig()
    const apiBaseUrl = config.public.apiBaseUrl as string
    const useLocalFallback = config.public.useLocalFallback as boolean

    // Log API configuration on initialization
    if (import.meta.client) {
        console.log('%c[EatIsFamily API Configuration]', 'color: #FF4D6D; font-weight: bold;')
        console.log(`📡 API Base URL: ${apiBaseUrl}`)
        console.log(`💾 Local Fallback Mode: ${useLocalFallback ? 'ENABLED (using local JSON files)' : 'DISABLED (using WordPress API)'}`)
        if (useLocalFallback) {
            console.log('%c⚠️ Running in OFFLINE mode - data from /public/api/*.json', 'color: orange;')
        } else {
            console.log('%c✅ Connected to WordPress REST API', 'color: green;')
        }
    }

    /**
     * Fetch data from WordPress API with local fallback
     * @param endpoint - API endpoint (e.g., 'activities', 'jobs', 'blog-posts')
     * @param fallbackFile - Local JSON file name for fallback (e.g., 'activities.json')
     */
    const fetchData = async <T>(endpoint: string, fallbackFile?: string): Promise<T | null> => {
        // If local fallback is enabled, use local JSON files
        if (useLocalFallback && fallbackFile) {
            console.log(`%c[API] 💾 Fetching from local: /api/${fallbackFile}`, 'color: #C8F560;')
            return await fetchLocalData<T>(fallbackFile)
        }

        try {
            // Fetch from WordPress API with cache disabled via timestamp
            console.log(`%c[API] 📡 Fetching from WordPress: ${apiBaseUrl}/${endpoint}`, 'color: #A0C4FF;')
            const data = await $fetch<T>(`${apiBaseUrl}/${endpoint}`, {
                timeout: 10000, // 10 second timeout
                // Add timestamp to bust cache (no custom headers to avoid CORS issues)
                query: {
                    _t: Date.now()
                }
            })
            console.log(`%c[API] ✅ Success: ${endpoint}`, 'color: green;')
            return data
        } catch (err) {
            console.error(`%c[API] ❌ Failed to fetch from API ${endpoint}:`, 'color: red;', err)
            return null
        }
    }

    /**
     * Fetch data from local JSON files in /api/ folder
     */
    const fetchLocalData = async <T>(filename: string): Promise<T | null> => {
        try {
            const data = await $fetch<T>(`/api/${filename}`)
            console.log(`%c[API] ✅ Local file loaded: ${filename}`, 'color: #C8F560;')
            return data
        } catch (err) {
            console.error(`%c[API] ❌ Failed to fetch local file ${filename}:`, 'color: red;', err)
            return null
        }
    }

    /**
     * Fetch single item by slug or ID from WordPress API
     */
    const fetchSingle = async <T>(endpoint: string, identifier: string | number): Promise<T | null> => {
        if (useLocalFallback) {
            console.log(`%c[API] 💾 Local mode - fetchSingle not available, use fetchData instead`, 'color: orange;')
            // For local fallback, we need to fetch all and filter
            return null
        }

        try {
            console.log(`%c[API] 📡 Fetching single: ${apiBaseUrl}/${endpoint}/${identifier}`, 'color: #A0C4FF;')
            const data = await $fetch<T>(`${apiBaseUrl}/${endpoint}/${identifier}`, {
                timeout: 10000,
                // Add timestamp to bust cache (no custom headers to avoid CORS issues)
                query: {
                    _t: Date.now()
                }
            })
            console.log(`%c[API] ✅ Success: ${endpoint}/${identifier}`, 'color: green;')
            return data
        } catch (err) {
            console.error(`%c[API] ❌ Failed to fetch ${endpoint}/${identifier}:`, 'color: red;', err)
            return null
        }
    }

    return {
        fetchData,
        fetchLocalData,
        fetchSingle,
        apiBaseUrl,
        useLocalFallback
    }
}
