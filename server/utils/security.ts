/**
 * Security utilities for job application form
 * Provides input sanitization and validation functions
 */

// Allowed file extensions for resume upload
export const ALLOWED_EXTENSIONS = ['.pdf', '.doc', '.docx'] as const

// Allowed MIME types for resume upload
export const ALLOWED_MIME_TYPES = [
  'application/pdf',
  'application/msword',
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
] as const

// Maximum file size in bytes (5MB)
export const MAX_FILE_SIZE = 5 * 1024 * 1024

/**
 * Sanitize text input to prevent XSS attacks
 * Removes HTML tags and encodes special characters
 */
export function sanitizeInput(input: string): string {
  if (!input || typeof input !== 'string') return ''
  
  return input
    // Remove HTML tags
    .replace(/<[^>]*>/g, '')
    // Encode special HTML characters
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#x27;')
    // Remove null bytes and control characters
    .replace(/\x00/g, '')
    .replace(/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/g, '')
    .trim()
}

/**
 * Validate email format
 */
export function validateEmail(email: string): boolean {
  if (!email || typeof email !== 'string') return false
  
  // RFC 5322 compliant email regex (simplified)
  const emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/
  
  return emailRegex.test(email) && email.length <= 254
}

/**
 * Validate phone number format (international format allowed)
 */
export function validatePhone(phone: string): boolean {
  if (!phone || typeof phone !== 'string') return false
  
  // Allow digits, spaces, dashes, parentheses, and plus sign
  const phoneRegex = /^[\d\s\-+().]{6,20}$/
  
  return phoneRegex.test(phone)
}

/**
 * Validate LinkedIn URL format
 */
export function validateLinkedIn(url: string): boolean {
  if (!url) return true // LinkedIn is optional
  if (typeof url !== 'string') return false
  
  try {
    const urlObj = new URL(url)
    return urlObj.hostname.includes('linkedin.com')
  } catch {
    return false
  }
}

/**
 * Check if file extension is allowed
 */
export function isAllowedExtension(filename: string): boolean {
  if (!filename || typeof filename !== 'string') return false
  
  const ext = filename.toLowerCase().slice(filename.lastIndexOf('.'))
  return ALLOWED_EXTENSIONS.includes(ext as typeof ALLOWED_EXTENSIONS[number])
}

/**
 * Check if MIME type is allowed
 */
export function isAllowedMimeType(mimeType: string): boolean {
  if (!mimeType || typeof mimeType !== 'string') return false
  
  return ALLOWED_MIME_TYPES.includes(mimeType as typeof ALLOWED_MIME_TYPES[number])
}

/**
 * Check if file size is within limit
 */
export function isFileSizeValid(size: number): boolean {
  return typeof size === 'number' && size > 0 && size <= MAX_FILE_SIZE
}

/**
 * Generate a secure random UUID for file naming
 */
export function generateSecureFilename(originalFilename: string): string {
  const ext = originalFilename.slice(originalFilename.lastIndexOf('.'))
  const uuid = crypto.randomUUID()
  const timestamp = Date.now()
  
  return `${uuid}-${timestamp}${ext.toLowerCase()}`
}

/**
 * Validate all form fields and return validation result
 */
export interface ValidationResult {
  valid: boolean
  errors: string[]
}

export interface ApplicationFormData {
  name: string
  email: string
  phone: string
  linkedin?: string
  coverLetter?: string
  jobSlug: string
  honeypot?: string
}

export function validateFormData(data: ApplicationFormData): ValidationResult {
  const errors: string[] = []
  
  // Check honeypot (must be empty - if filled, it's a bot)
  if (data.honeypot && data.honeypot.trim() !== '') {
    errors.push('Invalid submission detected')
    return { valid: false, errors }
  }
  
  // Required fields
  if (!data.name || data.name.trim().length < 2) {
    errors.push('Name is required (minimum 2 characters)')
  }
  
  if (!data.email || !validateEmail(data.email)) {
    errors.push('Valid email address is required')
  }
  
  if (!data.phone || !validatePhone(data.phone)) {
    errors.push('Valid phone number is required')
  }
  
  if (!data.jobSlug || data.jobSlug.trim() === '') {
    errors.push('Job reference is required')
  }
  
  // Optional fields validation
  if (data.linkedin && !validateLinkedIn(data.linkedin)) {
    errors.push('Invalid LinkedIn URL')
  }
  
  // Limit text lengths
  if (data.name && data.name.length > 100) {
    errors.push('Name is too long (maximum 100 characters)')
  }
  
  if (data.coverLetter && data.coverLetter.length > 5000) {
    errors.push('Cover letter is too long (maximum 5000 characters)')
  }
  
  return {
    valid: errors.length === 0,
    errors
  }
}
