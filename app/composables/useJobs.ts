import type { Venue } from './useVenues'

export interface Job {
    id: number
    slug: string
    title: {
        rendered: string
    }
    excerpt: {
        rendered: string
    }
    content: {
        rendered: string
    }
    venue_id: string
    department: string
    job_type: string
    salary: string
    requirements: string[]
    benefits: string[]
    featured_media: string
}

export interface JobWithVenue extends Job {
    venue: Venue | null
}

export const useJobs = () => {
    const { fetchData } = useApi()
    const { getVenueById, getVenues } = useVenues()

    const getJobs = async (): Promise<Job[] | null> => {
        return await fetchData<Job[]>('jobs.json')
    }

    const getJobBySlug = async (slug: string): Promise<Job | null> => {
        const jobs = await getJobs()
        if (!jobs) return null
        return jobs.find(job => job.slug === slug) || null
    }

    const getJobById = async (id: number): Promise<Job | null> => {
        const jobs = await getJobs()
        if (!jobs) return null
        return jobs.find(job => job.id === id) || null
    }

    const getJobsByVenue = async (venueId: string): Promise<Job[] | null> => {
        const jobs = await getJobs()
        if (!jobs) return null
        return jobs.filter(job => job.venue_id === venueId)
    }

    const getJobsByDepartment = async (department: string): Promise<Job[] | null> => {
        const jobs = await getJobs()
        if (!jobs) return null
        return jobs.filter(job => job.department.toLowerCase() === department.toLowerCase())
    }

    const getJobsByType = async (jobType: string): Promise<Job[] | null> => {
        const jobs = await getJobs()
        if (!jobs) return null
        return jobs.filter(job => job.job_type.toLowerCase() === jobType.toLowerCase())
    }

    // Get job with its associated venue
    const getJobWithVenue = async (slug: string): Promise<JobWithVenue | null> => {
        const job = await getJobBySlug(slug)
        if (!job) return null

        const venue = await getVenueById(job.venue_id)
        return { ...job, venue }
    }

    // Get all jobs with their associated venues
    const getJobsWithVenues = async (): Promise<JobWithVenue[] | null> => {
        const jobs = await getJobs()
        const venues = await getVenues()

        if (!jobs) return null

        const venueMap = new Map(venues?.map(v => [v.id, v]) || [])

        return jobs.map(job => ({
            ...job,
            venue: venueMap.get(job.venue_id) || null
        }))
    }

    // Get unique venue options from jobs (for filtering)
    const getJobVenueOptions = async (): Promise<{ id: string; name: string; location: string }[]> => {
        const jobs = await getJobs()
        const venues = await getVenues()

        if (!jobs || !venues) return []

        const venueMap = new Map(venues.map(v => [v.id, v]))
        const uniqueVenueIds = [...new Set(jobs.map(job => job.venue_id))]

        return uniqueVenueIds
            .map(id => venueMap.get(id))
            .filter((v): v is Venue => v !== undefined)
            .map(v => ({ id: v.id, name: v.name, location: v.location }))
    }

    return {
        getJobs,
        getJobBySlug,
        getJobById,
        getJobsByVenue,
        getJobsByDepartment,
        getJobsByType,
        getJobWithVenue,
        getJobsWithVenues,
        getJobVenueOptions
    }
}
