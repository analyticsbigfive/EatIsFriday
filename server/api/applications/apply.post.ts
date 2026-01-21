/**
 * Job Application API Endpoint
 * POST /api/applications/apply
 * 
 * Handles secure job application submissions with file upload
 */

import { readMultipartFormData, createError, H3Event } from 'h3'
import { writeFile, mkdir } from 'fs/promises'
import { join } from 'path'
import { existsSync } from 'fs'
import {
    sanitizeInput,
    validateFormData,
    isAllowedExtension,
    isAllowedMimeType,
    isFileSizeValid,
    generateSecureFilename,
    type ApplicationFormData
} from '~/server/utils/security'
import { getClientIP, checkRateLimit } from '~/server/utils/rateLimit'

// Base directory for storing uploads (outside public folder for security)
const UPLOADS_DIR = join(process.cwd(), 'server', 'uploads', 'applications')
const APPLICATIONS_DATA_DIR = join(process.cwd(), 'server', 'data', 'applications')

export default defineEventHandler(async (event: H3Event) => {
    // Only accept POST requests
    if (event.method !== 'POST') {
        throw createError({
            statusCode: 405,
            statusMessage: 'Method Not Allowed'
        })
    }

    try {
        // 1. Rate limiting check
        const clientIP = getClientIP(event)
        const rateLimitResult = checkRateLimit(clientIP)

        if (rateLimitResult.limited) {
            throw createError({
                statusCode: 429,
                statusMessage: 'Too Many Requests',
                data: {
                    message: `Trop de candidatures. Veuillez réessayer dans ${rateLimitResult.retryAfter} secondes.`,
                    retryAfter: rateLimitResult.retryAfter
                }
            })
        }

        // 2. Parse multipart form data
        const formData = await readMultipartFormData(event)

        if (!formData || formData.length === 0) {
            throw createError({
                statusCode: 400,
                statusMessage: 'Bad Request',
                data: { message: 'Données du formulaire manquantes' }
            })
        }

        // 3. Extract fields from form data
        const fields: Record<string, string> = {}
        let resumeFile: { data: Buffer; filename: string; type: string } | null = null

        for (const field of formData) {
            if (field.name === 'resume' && field.filename) {
                // Handle file
                resumeFile = {
                    data: field.data,
                    filename: field.filename,
                    type: field.type || 'application/octet-stream'
                }
            } else if (field.name && field.data) {
                // Handle text fields
                fields[field.name] = field.data.toString('utf-8')
            }
        }

        // 4. Validate form data (including honeypot check)
        const applicationData: ApplicationFormData = {
            name: fields.name || '',
            email: fields.email || '',
            phone: fields.phone || '',
            linkedin: fields.linkedin || '',
            coverLetter: fields.coverLetter || '',
            jobSlug: fields.jobSlug || '',
            honeypot: fields.website || '' // Honeypot field named "website"
        }

        const validation = validateFormData(applicationData)

        if (!validation.valid) {
            throw createError({
                statusCode: 400,
                statusMessage: 'Validation Error',
                data: {
                    message: 'Erreurs de validation',
                    errors: validation.errors
                }
            })
        }

        // 5. Validate resume file
        if (!resumeFile) {
            throw createError({
                statusCode: 400,
                statusMessage: 'Bad Request',
                data: { message: 'CV requis' }
            })
        }

        // Check file extension
        if (!isAllowedExtension(resumeFile.filename)) {
            throw createError({
                statusCode: 400,
                statusMessage: 'Invalid File Type',
                data: { message: 'Type de fichier non autorisé. Formats acceptés: PDF, DOC, DOCX' }
            })
        }

        // Check MIME type
        if (!isAllowedMimeType(resumeFile.type)) {
            throw createError({
                statusCode: 400,
                statusMessage: 'Invalid File Type',
                data: { message: 'Type MIME non autorisé' }
            })
        }

        // Check file size
        if (!isFileSizeValid(resumeFile.data.length)) {
            throw createError({
                statusCode: 400,
                statusMessage: 'File Too Large',
                data: { message: 'Le fichier est trop volumineux. Taille maximum: 5MB' }
            })
        }

        // 6. Create upload directories if they don't exist
        if (!existsSync(UPLOADS_DIR)) {
            await mkdir(UPLOADS_DIR, { recursive: true })
        }
        if (!existsSync(APPLICATIONS_DATA_DIR)) {
            await mkdir(APPLICATIONS_DATA_DIR, { recursive: true })
        }

        // 7. Generate secure filename and save file
        const secureFilename = generateSecureFilename(resumeFile.filename)
        const filePath = join(UPLOADS_DIR, secureFilename)

        await writeFile(filePath, resumeFile.data)

        // 8. Create application record
        const applicationId = crypto.randomUUID()
        const applicationRecord = {
            id: applicationId,
            jobSlug: sanitizeInput(applicationData.jobSlug),
            applicant: {
                name: sanitizeInput(applicationData.name),
                email: sanitizeInput(applicationData.email),
                phone: sanitizeInput(applicationData.phone),
                linkedin: applicationData.linkedin ? sanitizeInput(applicationData.linkedin) : null
            },
            coverLetter: applicationData.coverLetter ? sanitizeInput(applicationData.coverLetter) : null,
            resume: {
                originalFilename: resumeFile.filename,
                storedFilename: secureFilename,
                mimeType: resumeFile.type,
                size: resumeFile.data.length
            },
            metadata: {
                submittedAt: new Date().toISOString(),
                ipAddress: clientIP,
                userAgent: event.node.req.headers['user-agent'] || 'unknown'
            },
            status: 'pending'
        }

        // 9. Save application data
        const applicationFilePath = join(APPLICATIONS_DATA_DIR, `${applicationId}.json`)
        await writeFile(applicationFilePath, JSON.stringify(applicationRecord, null, 2))

        // 10. Log successful submission (for administrative purposes)
        console.log(`[APPLICATION] New application submitted:`, {
            id: applicationId,
            job: applicationData.jobSlug,
            applicant: applicationData.email,
            date: applicationRecord.metadata.submittedAt
        })

        // 11. Return success response
        return {
            success: true,
            message: 'Candidature envoyée avec succès',
            applicationId: applicationId
        }

    } catch (error: any) {
        // Re-throw H3 errors as-is
        if (error.statusCode) {
            throw error
        }

        // Log unexpected errors
        console.error('[APPLICATION ERROR]', error)

        // Return generic error for security
        throw createError({
            statusCode: 500,
            statusMessage: 'Internal Server Error',
            data: { message: 'Une erreur est survenue. Veuillez réessayer.' }
        })
    }
})
