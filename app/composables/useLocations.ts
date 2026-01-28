// This composable is now a wrapper around useVenues for backwards compatibility
// New code should use useVenues() directly

import type { Venue, VenuesData, VenueType, EventType, Stat, Shop, MenuItem } from './useVenues'

// Re-export types for backwards compatibility
export type { Shop, MenuItem, Venue, VenueType, EventType, Stat }

// Legacy interface that maps to the new VenuesData structure
export interface LocationsData {
    title: string
    description: string
    filter_label: string
    venue_types: VenueType[]
    // Keep event_types as alias for backwards compatibility
    event_types: VenueType[]
    stats: Stat[]
    map_venues: Venue[]
}

export const useLocations = () => {
    const { getVenuesData } = useVenues()

    // Transform VenuesData to LocationsData for backwards compatibility
    const getLocations = async (): Promise<LocationsData | null> => {
        const venuesData = await getVenuesData()
        if (!venuesData) return null

        // Support both venue_types (new) and event_types (legacy)
        const venueTypes = venuesData.venue_types || venuesData.event_types || []

        return {
            title: venuesData.metadata.title,
            description: venuesData.metadata.description,
            filter_label: venuesData.metadata.filter_label,
            venue_types: venueTypes,
            event_types: venueTypes, // alias for backwards compatibility
            stats: venuesData.stats,
            map_venues: venuesData.venues
        }
    }

    return {
        getLocations
    }
}
