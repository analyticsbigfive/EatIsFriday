export interface Event {
    id: number
    title: string
    image: string
    description: string
    event_type: string
}

export const useEvents = () => {
    const { fetchData } = useApi()

    const getEvents = async (): Promise<Event[] | null> => {
        return await fetchData<Event[]>('events.json')
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

    return {
        getEvents,
        getEventById,
        getEventsByType
    }
}
