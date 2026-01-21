<template>
  <div v-if="isOpen" class="modal-overlay" @click.self="close">
    <div class="modal-container">
      <!-- Close Button -->
      <button class="modal-close" @click="close" aria-label="Close">
        <LucideX />
      </button>

      <!-- Modal Header -->
      <div class="modal-header">
        <div class="modal-icon">
          <LucideBriefcase />
        </div>
        <h2 class="modal-title">Apply for this Position</h2>
        <p class="modal-subtitle">{{ jobTitle }}</p>
        <span class="modal-location">
          <LucideMapPin style="width: 1rem; height: 1rem;" />
          {{ jobLocation }}
        </span>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <form v-if="!submitSuccess" @submit.prevent="handleSubmit" class="apply-form">
          <div class="form-row">
            <div class="form-group">
              <label for="apply-name">Full Name *</label>
              <input
                    v-model="form.name"
                    type="text"
                    id="apply-name"
                    placeholder="Enter your full name"
                    required
                  />
                </div>
                <div class="form-group">
                  <label for="apply-email">Email Address *</label>
                  <input
                    v-model="form.email"
                    type="email"
                    id="apply-email"
                    placeholder="your.email@example.com"
                    required
                  />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="apply-phone">Phone Number *</label>
                  <input
                    v-model="form.phone"
                    type="tel"
                    id="apply-phone"
                    placeholder="+33 6 00 00 00 00"
                    required
                  />
                </div>
                <div class="form-group">
                  <label for="apply-linkedin">LinkedIn Profile</label>
                  <input
                    v-model="form.linkedin"
                    type="url"
                    id="apply-linkedin"
                    placeholder="https://linkedin.com/in/yourprofile"
                  />
                </div>
              </div>

              <div class="form-group">
                <label for="apply-resume">Resume/CV *</label>
                <div class="file-upload" :class="{ 'has-file': form.resumeName }">
                  <input
                    type="file"
                    id="apply-resume"
                    accept=".pdf,.doc,.docx"
                    required
                    @change="handleFileChange"
                  />
                  <div class="file-upload-content">
                    <LucideUpload />
                    <span v-if="form.resumeName">{{ form.resumeName }}</span>
                    <span v-else>
                      <strong>Click to upload</strong> or drag and drop<br/>
                      PDF, DOC, DOCX (max 5MB)
                    </span>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="apply-cover">Cover Letter</label>
                <textarea
                  v-model="form.coverLetter"
                  id="apply-cover"
                  rows="4"
                  placeholder="Tell us why you'd be a great fit for this role..."
                ></textarea>
              </div>

              <!-- Honeypot field (hidden from users, trap for bots) -->
              <div class="form-group honeypot-field" aria-hidden="true">
                <label for="apply-website">Website</label>
                <input
                  v-model="form.website"
                  type="text"
                  id="apply-website"
                  name="website"
                  autocomplete="off"
                  tabindex="-1"
                />
              </div>

              <div class="form-group checkbox-group">
                <label class="checkbox-label">
                  <input
                    v-model="form.consent"
                    type="checkbox"
                    required
                  />
                  <span class="checkbox-text">
                    I agree to the processing of my personal data in accordance with the <a href="/privacy" target="_blank">Privacy Policy</a> *
                  </span>
                </label>
              </div>

              <!-- Error Message Display -->
              <div v-if="submitError" class="error-message">
                <LucideAlertCircle style="width: 1.25rem; height: 1.25rem;" />
                <span>{{ submitError }}</span>
              </div>

              <button type="submit" class="btn-submit" :disabled="isSubmitting">
                <span v-if="isSubmitting" class="spinner"></span>
                <span v-else>
                  <LucideSend style="width: 1.25rem; height: 1.25rem;" />
                  Submit Application
                </span>
              </button>
            </form>

            <!-- Success State -->
            <div v-else class="success-state">
              <div class="success-icon">
                <LucideCheck />
              </div>
              <h3>Application Submitted!</h3>
              <p>Thank you for applying to <strong>{{ jobTitle }}</strong>. We've received your application and will review it shortly.</p>
              <p class="success-note">You'll receive a confirmation email at <strong>{{ form.email }}</strong></p>
              <button @click="close" class="btn-close-success">Close</button>
            </div>
          </div>
        </div>
      </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { LucideX, LucideBriefcase, LucideMapPin, LucideUpload, LucideSend, LucideCheck, LucideAlertCircle } from 'lucide-vue-next'

const props = defineProps<{
  isOpen: boolean
  jobTitle: string
  jobLocation: string
  jobSlug: string
}>()

console.log('========== JobApplyModal COMPONENT LOADED ==========')
console.log('Initial isOpen:', props.isOpen)
console.log('jobTitle:', props.jobTitle)

watch(() => props.isOpen, (newVal, oldVal) => {
  console.log('>>> JobApplyModal isOpen CHANGED:', oldVal, '->', newVal)
  if (newVal) {
    console.log('MODAL SHOULD BE VISIBLE NOW!')
  }
}, { immediate: true })

const emit = defineEmits(['close'])

const form = ref({
  name: '',
  email: '',
  phone: '',
  linkedin: '',
  resume: null as File | null,
  resumeName: '',
  coverLetter: '',
  consent: false,
  website: '' // Honeypot field
})

const isSubmitting = ref(false)
const submitSuccess = ref(false)
const submitError = ref<string | null>(null)

const close = () => {
  emit('close')
}

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    form.value.resume = target.files[0]
    form.value.resumeName = target.files[0].name
  }
}

const handleSubmit = async () => {
  isSubmitting.value = true
  submitError.value = null
  
  try {
    // Create FormData for multipart/form-data submission
    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('email', form.value.email)
    formData.append('phone', form.value.phone)
    formData.append('linkedin', form.value.linkedin || '')
    formData.append('coverLetter', form.value.coverLetter || '')
    formData.append('jobSlug', props.jobSlug)
    formData.append('website', form.value.website) // Honeypot
    
    if (form.value.resume) {
      formData.append('resume', form.value.resume)
    }
    
    // Submit to API
    const response = await $fetch('/api/applications/apply', {
      method: 'POST',
      body: formData
    })
    
    if (response.success) {
      isSubmitting.value = false
      submitSuccess.value = true
    }
  } catch (error: any) {
    isSubmitting.value = false
    
    // Handle API errors
    if (error.data?.message) {
      submitError.value = error.data.message
    } else if (error.data?.errors && Array.isArray(error.data.errors)) {
      submitError.value = error.data.errors.join(', ')
    } else if (error.statusCode === 429) {
      submitError.value = 'Trop de candidatures envoyées. Veuillez réessayer plus tard.'
    } else {
      submitError.value = 'Une erreur est survenue. Veuillez réessayer.'
    }
    
    console.error('Application submission error:', error)
  }
}

// Reset form when modal opens
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    submitSuccess.value = false
    submitError.value = null
    form.value = {
      name: '',
      email: '',
      phone: '',
      linkedin: '',
      resume: null,
      resumeName: '',
      coverLetter: '',
      consent: false,
      website: ''
    }
  }
})

// Close on escape key
const handleEscape = (e: KeyboardEvent) => {
  if (e.key === 'Escape' && props.isOpen) {
    close()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
})

// Prevent body scroll when modal is open
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})
</script>

<style scoped lang="scss">
/* Honeypot field - hidden from users but visible to bots */
.honeypot-field {
  position: absolute;
  left: -9999px;
  opacity: 0;
  height: 0;
  width: 0;
  overflow: hidden;
  pointer-events: none;
}

/* Error message styling */
.error-message {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 1rem;
  background: #FEE2E2;
  border: 1px solid #F87171;
  border-radius: 0.5rem;
  color: #DC2626;
  font-size: 0.875rem;
  margin-bottom: 1rem;
  
  svg {
    flex-shrink: 0;
    color: #DC2626;
  }
}
</style>
