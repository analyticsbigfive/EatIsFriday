export interface BlogPost {
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
    date: string
    featured_media: string
    // Champs optionnels
    author?: {
        name: string
        avatar: string
    }
    reading_time?: string
    categories?: Array<{
        id: number
        name: string
    }>
}

export const useBlog = () => {
    const getBlogPosts = async (): Promise<BlogPost[]> => {
        try {
            // Utiliser la route serveur qui lit le JSON
            const data = await $fetch<BlogPost[]>('/data/blog-posts.json')
            return data
        } catch (error) {
            console.error('Erreur lors du chargement des articles:', error)
            return []
        }
    }

    const getBlogPostBySlug = async (slug: string): Promise<BlogPost | null> => {
        const posts = await getBlogPosts()
        return posts.find(post => post.slug === slug) || null
    }

    return {
        getBlogPosts,
        getBlogPostBySlug
    }
}
