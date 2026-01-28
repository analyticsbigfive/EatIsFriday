export interface Shop {
    id: string
    name: string
    image: string
}

export interface MenuItem {
    id: string
    name: string
    price: string
    description: string
    thumbnail: string
}

export interface Venue {
    id: string
    name: string
    location: string
    city: string
    country: string
    type: string
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
    services?: string[]
    shops?: Shop[]
    menu_items?: MenuItem[]
}

export interface VenueType {
    id: string
    name: string
    image: string
    map_icon?: string
}

// Alias for backwards compatibility
export type EventType = VenueType

export interface Stat {
    value: string
    label: string
}

export interface VenuesData {
    metadata: {
        title: string
        description: string
        filter_label: string
    }
    venue_types: VenueType[]
    // Fallback for backwards compatibility with old API responses
    event_types?: VenueType[]
    stats: Stat[]
    venues: Venue[]
}

export const useVenues = () => {
    const { fetchData, fetchSingle, useLocalFallback } = useApi()

    /**
     * Get all venues data (metadata, event_types, stats, venues) from WordPress API or local fallback
     */
    const getVenuesData = async (): Promise<VenuesData | null> => {
        return await fetchData<VenuesData>('venues', 'venues.json')
    }

    /**
     * Get all venues
     */
    const getVenues = async (): Promise<Venue[] | null> => {
        const data = await getVenuesData()
        return data?.venues || null
    }

    /**
     * Get a single venue by ID
     */
    const getVenueById = async (id: string): Promise<Venue | null> => {
        // Try to fetch from API first
        if (!useLocalFallback) {
            const venue = await fetchSingle<Venue>('venues', id)
            if (venue) return venue
        }
        
        // Fallback: fetch all and filter
        const venues = await getVenues()
        if (!venues) return null
        return venues.find(venue => venue.id === id) || null
    }

    /**
     * Get venues filtered by type
     */
    const getVenuesByType = async (type: string): Promise<Venue[] | null> => {
        const venues = await getVenues()
        if (!venues) return null
        return venues.filter(venue => venue.type === type)
    }

    /**
     * Get venues filtered by city
     */
    const getVenuesByCity = async (city: string): Promise<Venue[] | null> => {
        const venues = await getVenues()
        if (!venues) return null
        return venues.filter(venue => venue.city.toLowerCase() === city.toLowerCase())
    }

    /**
     * Get venue types
     */
    const getVenueTypes = async (): Promise<VenueType[] | null> => {
        const data = await getVenuesData()
        // Support both venue_types (new) and event_types (legacy)
        return data?.venue_types || data?.event_types || null
    }

    /**
     * @deprecated Use getVenueTypes instead
     */
    const getEventTypes = async (): Promise<VenueType[] | null> => {
        return getVenueTypes()
    }

    /**
     * Get statistics
     */
    const getStats = async (): Promise<Stat[] | null> => {
        const data = await getVenuesData()
        return data?.stats || null
    }

    /**
     * Get metadata
     */
    const getMetadata = async () => {
        const data = await getVenuesData()
        return data?.metadata || null
    }

    return {
        getVenuesData,
        getVenues,
        getVenueById,
        getVenuesByType,
        getVenuesByCity,
        getVenueTypes,
        getEventTypes, // deprecated, use getVenueTypes
        getStats,
        getMetadata
    }
}

