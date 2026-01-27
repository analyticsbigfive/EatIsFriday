export interface SiteContent {
    site: {
        name: string
        tagline: string
        description: string
    }
    home: any
    about: any
    contact: any
    footer: any
}

export const useSiteContent = () => {
    const { fetchData, fetchLocalData } = useApi()

    /**
     * Deep merge two objects - WordPress data takes priority
     */
    const deepMerge = (target: any, source: any): any => {
        const result = { ...target }
        for (const key in source) {
            if (source[key] !== null && source[key] !== undefined && source[key] !== '') {
                if (typeof source[key] === 'object' && !Array.isArray(source[key])) {
                    result[key] = deepMerge(target[key] || {}, source[key])
                } else {
                    result[key] = source[key]
                }
            }
        }
        return result
    }

    /**
     * Get site content - ALWAYS fetches from WordPress API first
     * Then merges with local data for complete structure
     */
    const getSiteContent = async (): Promise<SiteContent | null> => {
        // Always fetch local data for complete structure
        const localData = await fetchLocalData<SiteContent>('site-content.json')
        
        // Fetch from WordPress API
        const wpData = await fetchData<any>('site-content')
        
        if (wpData && localData) {
            console.log('%c[SiteContent] 🔄 Merging WordPress data with local structure', 'color: #FF4D6D;')
            return deepMerge(localData, wpData)
        }
        
        if (wpData) return wpData as SiteContent
        
        // If WordPress API fails, use local data
        console.log('%c[SiteContent] ⚠️ WordPress API unavailable, using local data', 'color: orange;')
        return localData
    }

    return {
        getSiteContent
    }
}

