// This composable is now a wrapper around useVenues for backwards compatibility
// New code should use useVenues() directly

import type { Venue, VenuesData, EventType, Stat, Shop, MenuItem } from './useVenues'

// Re-export types for backwards compatibility
export type { Shop, MenuItem, Venue, EventType, Stat }

// Legacy interface that maps to the new VenuesData structure
export interface LocationsData {
    title: string
    description: string
    filter_label: string
    event_types: EventType[]
    stats: Stat[]
    map_venues: Venue[]
}

export const useLocations = () => {
    const { getVenuesData } = useVenues()

    // Transform VenuesData to LocationsData for backwards compatibility
    const getLocations = async (): Promise<LocationsData | null> => {
        const venuesData = await getVenuesData()
        if (!venuesData) return null

        return {
            title: venuesData.metadata.title,
            description: venuesData.metadata.description,
            filter_label: venuesData.metadata.filter_label,
            event_types: venuesData.event_types,
            stats: venuesData.stats,
            map_venues: venuesData.venues
        }
    }

    return {
        getLocations
    }
}
