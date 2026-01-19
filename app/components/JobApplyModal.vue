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
import { LucideX, LucideBriefcase, LucideMapPin, LucideUpload, LucideSend, LucideCheck } from 'lucide-vue-next'

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
  consent: false
})

const isSubmitting = ref(false)
const submitSuccess = ref(false)

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
  
  // Simulate API call
  await new Promise(resolve => setTimeout(resolve, 2000))
  
  isSubmitting.value = false
  submitSuccess.value = true
}

// Reset form when modal opens
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    submitSuccess.value = false
    form.value = {
      name: '',
      email: '',
      phone: '',
      linkedin: '',
      resume: null,
      resumeName: '',
      coverLetter: '',
      consent: false
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
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 1rem;
}

.modal-container {
  background: white;
  border-radius: 1.5rem;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.modal-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.2s ease;
  z-index: 1;

  svg {
    width: 1.25rem;
    height: 1.25rem;
  }

  &:hover {
    background: var(--brand-pink);
    color: white;
    transform: rotate(90deg);
  }
}

.modal-header {
  text-align: center;
  padding: 2.5rem 2rem 1.5rem;
  background: linear-gradient(135deg, var(--brand-lime) 0%, #d4f792 100%);
  border-radius: 1.5rem 1.5rem 0 0;
}

.modal-icon {
  width: 64px;
  height: 64px;
  background: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);

  svg {
    width: 1.75rem;
    height: 1.75rem;
    color: var(--brand-pink);
  }
}

.modal-title {
  font-family: var(--font-heading);
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0 0 0.5rem;
  color: var(--brand-dark);
}

.modal-subtitle {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--brand-dark);
  margin: 0 0 0.5rem;
}

.modal-location {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.875rem;
  color: rgba(0, 0, 0, 0.6);
}

.modal-body {
  padding: 2rem;
}

.apply-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.25rem;

  @media (min-width: 480px) {
    grid-template-columns: 1fr 1fr;
  }
}

.form-group {
  display: flex;
  flex-direction: column;

  label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--brand-dark);
    margin-bottom: 0.5rem;
  }

  input, textarea {
    padding: 0.75rem 1rem;
    border: 2px solid #e5e5e5;
    border-radius: 0.5rem;
    font-size: 0.9375rem;
    font-family: inherit;
    transition: all 0.2s ease;

    &:focus {
      outline: none;
      border-color: var(--brand-pink);
      box-shadow: 0 0 0 3px rgba(255, 77, 109, 0.1);
    }

    &::placeholder {
      color: #aaa;
    }
  }

  textarea {
    resize: vertical;
    min-height: 100px;
  }
}

.file-upload {
  position: relative;
  border: 2px dashed #e5e5e5;
  border-radius: 0.5rem;
  padding: 1.5rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover, &.has-file {
    border-color: var(--brand-pink);
    background: rgba(255, 77, 109, 0.05);
  }

  input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
  }
}

.file-upload-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  color: #666;
  font-size: 0.875rem;

  svg {
    width: 2rem;
    height: 2rem;
    color: var(--brand-pink);
  }

  strong {
    color: var(--brand-pink);
  }
}

.checkbox-group {
  margin-top: 0.5rem;
}

.checkbox-label {
  display: flex;
  gap: 0.75rem;
  cursor: pointer;

  input[type="checkbox"] {
    width: 1.25rem;
    height: 1.25rem;
    margin: 0;
    flex-shrink: 0;
    accent-color: var(--brand-pink);
  }
}

.checkbox-text {
  font-size: 0.8125rem;
  color: #666;
  line-height: 1.5;

  a {
    color: var(--brand-pink);
    text-decoration: underline;
  }
}

.btn-submit {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  width: 100%;
  padding: 1rem 2rem;
  background: var(--brand-pink);
  color: white;
  border: none;
  border-radius: 0.75rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-top: 0.5rem;

  &:hover:not(:disabled) {
    background: #e04460;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 77, 109, 0.3);
  }

  &:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }
}

.spinner {
  width: 1.25rem;
  height: 1.25rem;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

// Success State
.success-state {
  text-align: center;
  padding: 2rem 1rem;
}

.success-icon {
  width: 80px;
  height: 80px;
  background: var(--brand-lime);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;

  svg {
    width: 2.5rem;
    height: 2.5rem;
    color: var(--brand-dark);
  }
}

.success-state h3 {
  font-family: var(--font-heading);
  font-size: 1.5rem;
  margin: 0 0 1rem;
  color: var(--brand-dark);
}

.success-state p {
  color: #666;
  margin: 0 0 0.75rem;
  line-height: 1.6;
}

.success-note {
  font-size: 0.875rem;
  padding: 0.75rem;
  background: #f5f5f5;
  border-radius: 0.5rem;
  margin-bottom: 1.5rem !important;
}

.btn-close-success {
  padding: 0.875rem 2rem;
  background: var(--brand-dark);
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  }
}

// Modal Transitions
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;

  .modal-container {
    transform: scale(0.95) translateY(20px);
  }
}

.modal-enter-to,
.modal-leave-from {
  opacity: 1;

  .modal-container {
    transform: scale(1) translateY(0);
  }
}
</style>
