/**
 * Simple in-memory rate limiter for job applications
 * Limits submissions per IP address
 */

interface RateLimitEntry {
    count: number
    firstRequest: number
}

// In-memory store for rate limiting
// In production, consider using Redis or similar
const rateLimitStore = new Map<string, RateLimitEntry>()

// Configuration
const MAX_REQUESTS_PER_WINDOW = 5 // Maximum submissions per window
const WINDOW_DURATION_MS = 60 * 60 * 1000 // 1 hour in milliseconds
const CLEANUP_INTERVAL_MS = 10 * 60 * 1000 // Cleanup every 10 minutes

/**
 * Get client IP address from the event
 */
export function getClientIP(event: any): string {
    // Try various headers for proxied requests
    const forwardedFor = event.node.req.headers['x-forwarded-for']
    if (forwardedFor) {
        // Take the first IP if multiple are present
        const ips = Array.isArray(forwardedFor) ? forwardedFor[0] : forwardedFor.split(',')[0]
        return ips.trim()
    }

    const realIP = event.node.req.headers['x-real-ip']
    if (realIP) {
        return Array.isArray(realIP) ? realIP[0] : realIP
    }

    // Fallback to socket remote address
    return event.node.req.socket?.remoteAddress || 'unknown'
}

/**
 * Check if the IP is rate limited
 * Returns null if allowed, or an error object if rate limited
 */
export function checkRateLimit(ip: string): { limited: boolean; retryAfter?: number } {
    const now = Date.now()
    const entry = rateLimitStore.get(ip)

    if (!entry) {
        // First request from this IP
        rateLimitStore.set(ip, {
            count: 1,
            firstRequest: now
        })
        return { limited: false }
    }

    // Check if window has expired
    if (now - entry.firstRequest > WINDOW_DURATION_MS) {
        // Reset the window
        rateLimitStore.set(ip, {
            count: 1,
            firstRequest: now
        })
        return { limited: false }
    }

    // Check if limit exceeded
    if (entry.count >= MAX_REQUESTS_PER_WINDOW) {
        const retryAfter = Math.ceil((entry.firstRequest + WINDOW_DURATION_MS - now) / 1000)
        return {
            limited: true,
            retryAfter
        }
    }

    // Increment counter
    entry.count++
    rateLimitStore.set(ip, entry)
    return { limited: false }
}

/**
 * Get remaining requests for an IP
 */
export function getRemainingRequests(ip: string): number {
    const entry = rateLimitStore.get(ip)

    if (!entry) {
        return MAX_REQUESTS_PER_WINDOW
    }

    const now = Date.now()
    if (now - entry.firstRequest > WINDOW_DURATION_MS) {
        return MAX_REQUESTS_PER_WINDOW
    }

    return Math.max(0, MAX_REQUESTS_PER_WINDOW - entry.count)
}

/**
 * Cleanup expired entries to prevent memory leaks
 */
function cleanupExpiredEntries(): void {
    const now = Date.now()

    for (const [ip, entry] of rateLimitStore.entries()) {
        if (now - entry.firstRequest > WINDOW_DURATION_MS) {
            rateLimitStore.delete(ip)
        }
    }
}

// Setup periodic cleanup
let cleanupInterval: NodeJS.Timeout | null = null

export function startRateLimitCleanup(): void {
    if (!cleanupInterval) {
        cleanupInterval = setInterval(cleanupExpiredEntries, CLEANUP_INTERVAL_MS)
        // Don't prevent process from exiting
        if (cleanupInterval.unref) {
            cleanupInterval.unref()
        }
    }
}

export function stopRateLimitCleanup(): void {
    if (cleanupInterval) {
        clearInterval(cleanupInterval)
        cleanupInterval = null
    }
}

// Auto-start cleanup when module is loaded
startRateLimitCleanup()
