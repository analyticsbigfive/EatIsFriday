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

export interface EventType {
    id: string
    name: string
    image: string
}

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
    event_types: EventType[]
    stats: Stat[]
    venues: Venue[]
}

export const useVenues = () => {
    const { fetchData } = useApi()

    const getVenuesData = async (): Promise<VenuesData | null> => {
        return await fetchData<VenuesData>('venues.json')
    }

    const getVenues = async (): Promise<Venue[] | null> => {
        const data = await getVenuesData()
        return data?.venues || null
    }

    const getVenueById = async (id: string): Promise<Venue | null> => {
        const venues = await getVenues()
        if (!venues) return null
        return venues.find(venue => venue.id === id) || null
    }

    const getVenuesByType = async (type: string): Promise<Venue[] | null> => {
        const venues = await getVenues()
        if (!venues) return null
        return venues.filter(venue => venue.type === type)
    }

    const getVenuesByCity = async (city: string): Promise<Venue[] | null> => {
        const venues = await getVenues()
        if (!venues) return null
        return venues.filter(venue => venue.city.toLowerCase() === city.toLowerCase())
    }

    const getEventTypes = async (): Promise<EventType[] | null> => {
        const data = await getVenuesData()
        return data?.event_types || null
    }

    const getStats = async (): Promise<Stat[] | null> => {
        const data = await getVenuesData()
        return data?.stats || null
    }

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
        getEventTypes,
        getStats,
        getMetadata
    }
}
