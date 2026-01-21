import type { Venue } from './useVenues'

export interface Event {
    id: number
    slug: string
    title: string
    image: string
    description: string
    event_type: string
    venue_id: string | null
}

export interface EventWithVenue extends Event {
    venue: Venue | null
}

export const useEvents = () => {
    const { fetchData } = useApi()
    const { getVenueById, getVenues } = useVenues()

    const getEvents = async (): Promise<Event[] | null> => {
        return await fetchData<Event[]>('events.json')
    }

    const getEventBySlug = async (slug: string): Promise<Event | null> => {
        const events = await getEvents()
        if (!events) return null
        return events.find(event => event.slug === slug) || null
    }

    const getEventById = async (id: number): Promise<Event | null> => {
        const events = await getEvents()
        if (!events) return null
        return events.find(event => event.id === id) || null
    }

    const getEventsByType = async (eventType: string): Promise<Event[] | null> => {
        const events = await getEvents()
        if (!events) return null
        return events.filter(event => event.event_type.toLowerCase() === eventType.toLowerCase())
    }

    const getEventsByVenue = async (venueId: string): Promise<Event[] | null> => {
        const events = await getEvents()
        if (!events) return null
        return events.filter(event => event.venue_id === venueId)
    }

    // Get event with its associated venue
    const getEventWithVenue = async (slug: string): Promise<EventWithVenue | null> => {
        const event = await getEventBySlug(slug)
        if (!event) return null

        const venue = event.venue_id ? await getVenueById(event.venue_id) : null
        return { ...event, venue }
    }

    // Get all events with their associated venues
    const getEventsWithVenues = async (): Promise<EventWithVenue[] | null> => {
        const events = await getEvents()
        const venues = await getVenues()

        if (!events) return null

        const venueMap = new Map(venues?.map(v => [v.id, v]) || [])

        return events.map(event => ({
            ...event,
            venue: event.venue_id ? venueMap.get(event.venue_id) || null : null
        }))
    }

    return {
        getEvents,
        getEventBySlug,
        getEventById,
        getEventsByType,
        getEventsByVenue,
        getEventWithVenue,
        getEventsWithVenues
    }
}
