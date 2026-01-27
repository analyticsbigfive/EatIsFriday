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
    date?: string
}

export interface JobWithVenue extends Job {
    venue: Venue | null
}

export const useJobs = () => {
    const { fetchData, fetchSingle, useLocalFallback } = useApi()
    const { getVenueById, getVenues } = useVenues()

    /**
     * Get all jobs from WordPress API or local fallback
     */
    const getJobs = async (): Promise<Job[] | null> => {
        return await fetchData<Job[]>('jobs', 'jobs.json')
    }

    /**
     * Get a single job by slug
     */
    const getJobBySlug = async (slug: string): Promise<Job | null> => {
        // Try to fetch from API first
        if (!useLocalFallback) {
            const job = await fetchSingle<Job>('jobs', slug)
            if (job) return job
        }
        
        // Fallback: fetch all and filter
        const jobs = await getJobs()
        if (!jobs) return null
        return jobs.find(job => job.slug === slug) || null
    }

    /**
     * Get a single job by ID
     */
    const getJobById = async (id: number): Promise<Job | null> => {
        const jobs = await getJobs()
        if (!jobs) return null
        return jobs.find(job => job.id === id) || null
    }

    /**
     * Get jobs filtered by venue ID
     */
    const getJobsByVenue = async (venueId: string): Promise<Job[] | null> => {
        const jobs = await getJobs()
        if (!jobs) return null
        return jobs.filter(job => job.venue_id === venueId)
    }

    /**
     * Get jobs filtered by department
     */
    const getJobsByDepartment = async (department: string): Promise<Job[] | null> => {
        const jobs = await getJobs()
        if (!jobs) return null
        return jobs.filter(job => job.department.toLowerCase() === department.toLowerCase())
    }

    /**
     * Get jobs filtered by job type
     */
    const getJobsByType = async (jobType: string): Promise<Job[] | null> => {
        const jobs = await getJobs()
        if (!jobs) return null
        return jobs.filter(job => job.job_type.toLowerCase() === jobType.toLowerCase())
    }

    /**
     * Get job with its associated venue
     */
    const getJobWithVenue = async (slug: string): Promise<JobWithVenue | null> => {
        const job = await getJobBySlug(slug)
        if (!job) return null

        const venue = await getVenueById(job.venue_id)
        return { ...job, venue }
    }

    /**
     * Get all jobs with their associated venues
     */
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

    /**
     * Get unique venue options from jobs (for filtering)
     */
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

