<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { LucidePhone, LucideMail, LucideMapPin, LucideClock, LucideLinkedin, LucideInstagram, LucideTwitter, LucideSend } from 'lucide-vue-next'
import type { ContactContent } from '~/composables/usePageContent'

const { getContactContent } = usePageContent()
const content = ref<ContactContent | null>(null)

const form = ref({
  name: '',
  email: '',
  eventType: '',
  location: '',
  date: '',
  guests: '',
  message: ''
})

const isSubmitting = ref(false)
const submitSuccess = ref(false)

onMounted(async () => {
  content.value = await getContactContent()
})

const submitForm = async () => {
  isSubmitting.value = true
  // Simulate API call
  await new Promise(resolve => setTimeout(resolve, 1500))
  isSubmitting.value = false
  submitSuccess.value = true
  // Reset form
  form.value = {
    name: '',
    email: '',
    eventType: '',
    location: '',
    date: '',
    guests: '',
    message: ''
  }
}
</script>

<template>
  <div v-if="content" class="contact-page">
    <!-- Hero Section -->
    <section class="contact-hero">
      <div class="container">
        <div class="hero-content">
          <span class="hero-tag">GET IN TOUCH</span>
          <h1 class="hero-title">
            {{ content.hero_section.title.line_1 }} 
            <span class="highlight">{{ content.hero_section.title.line_1_highlight }}</span><br/>
            {{ content.hero_section.title.line_2 }}<br/>
            {{ content.hero_section.title.line_3 }} 
            <span class="italic">{{ content.hero_section.title.line_3_highlight }}</span>
          </h1>
          <p class="hero-description">{{ content.hero_section.description }}</p>
        </div>
      </div>
      <div class="hero-decoration">
        <div class="deco-circle deco-circle-1"></div>
        <div class="deco-circle deco-circle-2"></div>
        <div class="deco-circle deco-circle-3"></div>
      </div>
    </section>

    <!-- Main Content -->
    <section class="contact-main">
      <div class="container">
        <div class="contact-grid">
          <!-- Contact Info Side -->
          <div class="contact-info">
            <div class="info-card">
              <h2 class="info-title">Contact Information</h2>
              <p class="info-subtitle">Reach out to us through any of these channels</p>
              
              <div class="info-items">
                <div class="info-item">
                  <div class="info-icon">
                    <LucidePhone />
                  </div>
                  <div class="info-content">
                    <span class="info-label">Phone</span>
                    <a href="tel:+33123456789" class="info-value">+33 1 23 45 67 89</a>
                  </div>
                </div>
                
                <div class="info-item">
                  <div class="info-icon">
                    <LucideMail />
                  </div>
                  <div class="info-content">
                    <span class="info-label">Email</span>
                    <a href="mailto:contact@eatisfrfamily.com" class="info-value">contact@eatisfrfamily.com</a>
                  </div>
                </div>
                
                <div class="info-item">
                  <div class="info-icon">
                    <LucideMapPin />
                  </div>
                  <div class="info-content">
                    <span class="info-label">Address</span>
                    <span class="info-value">123 Avenue des Champs-Élysées<br/>75008 Paris, France</span>
                  </div>
                </div>
                
                <div class="info-item">
                  <div class="info-icon">
                    <LucideClock />
                  </div>
                  <div class="info-content">
                    <span class="info-label">Business Hours</span>
                    <span class="info-value">Monday - Friday: 9am - 6pm<br/>Weekend: By appointment</span>
                  </div>
                </div>
              </div>

              <div class="social-links">
                <span class="social-label">Follow Us</span>
                <div class="social-icons">
                  <a href="#" class="social-icon" aria-label="LinkedIn">
                    <LucideLinkedin />
                  </a>
                  <a href="#" class="social-icon" aria-label="Instagram">
                    <LucideInstagram />
                  </a>
                  <a href="#" class="social-icon" aria-label="Twitter">
                    <LucideTwitter />
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Form Side -->
          <div class="contact-form-wrapper">
            <div class="form-card">
              <h2 class="form-title">Send Us a Message</h2>
              <p class="form-subtitle">Tell us about your event and we'll get back to you shortly</p>

              <form v-if="!submitSuccess" @submit.prevent="submitForm" class="contact-form">
                <!-- Row 1: Name & Email -->
                <div class="form-row">
                  <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input
                      v-model="form.name"
                      type="text"
                      id="name"
                      :placeholder="content.form.name_placeholder"
                      required
                    />
                  </div>
                  <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input
                      v-model="form.email"
                      type="email"
                      id="email"
                      :placeholder="content.form.email_placeholder"
                      required
                    />
                  </div>
                </div>

                <!-- Row 2: Event Type & Location -->
                <div class="form-row">
                  <div class="form-group">
                    <label for="eventType">Event Type</label>
                    <input
                      v-model="form.eventType"
                      type="text"
                      id="eventType"
                      :placeholder="content.form.event_type_placeholder"
                    />
                  </div>
                  <div class="form-group">
                    <label for="location">Location</label>
                    <input
                      v-model="form.location"
                      type="text"
                      id="location"
                      :placeholder="content.form.location_placeholder"
                    />
                  </div>
                </div>

                <!-- Row 3: Date & Guests -->
                <div class="form-row">
                  <div class="form-group">
                    <label for="date">Event Date</label>
                    <input
                      v-model="form.date"
                      type="date"
                      id="date"
                    />
                  </div>
                  <div class="form-group">
                    <label for="guests">Number of Guests</label>
                    <input
                      v-model="form.guests"
                      type="text"
                      id="guests"
                      :placeholder="content.form.guests_placeholder"
                    />
                  </div>
                </div>

                <!-- Row 4: Message -->
                <div class="form-group full-width">
                  <label for="message">Your Message *</label>
                  <textarea
                    v-model="form.message"
                    id="message"
                    :placeholder="content.form.message_placeholder"
                    rows="5"
                    required
                  ></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit" :disabled="isSubmitting">
                  <span v-if="isSubmitting">Sending...</span>
                  <span v-else>
                    <LucideSend style="width: 1.25rem; height: 1.25rem;" />
                    {{ content.form.submit_button }}
                  </span>
                </button>
              </form>

              <!-- Success Message -->
              <div v-else class="success-message">
                <div class="success-icon">✓</div>
                <h3>Message Sent!</h3>
                <p>Thank you for reaching out. We'll get back to you within 24-48 hours.</p>
                <button @click="submitSuccess = false" class="btn-reset">Send Another Message</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
      <div class="container">
        <h2 class="map-title">Find Us</h2>
        <div class="map-wrapper">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.2159456722566!2d2.2975776!3d48.8693156!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66fc4f8f3049b%3A0x5d5a9f0da4c39c9b!2sAv.%20des%20Champs-%C3%89lys%C3%A9es%2C%20Paris%2C%20France!5e0!3m2!1sfr!2sfr!4v1642345678901!5m2!1sfr!2sfr"
            width="100%"
            height="450"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          ></iframe>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped lang="scss">
.contact-page {
  min-height: 100vh;
  background: var(--brand-gray, #F5F5F0);
}

// Hero Section
.contact-hero {
  position: relative;
  background: linear-gradient(135deg, var(--brand-lime) 0%, #a8e063 100%);
  padding: 6rem 0 8rem;
  overflow: hidden;
}

.hero-content {
  position: relative;
  z-index: 1;
  max-width: 700px;
}

.hero-tag {
  display: inline-block;
  padding: 0.5rem 1rem;
  background: rgba(0, 0, 0, 0.1);
  border-radius: 50px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  margin-bottom: 1.5rem;
}

.hero-title {
  font-family: var(--font-heading);
  font-size: clamp(2.5rem, 5vw, 4rem);
  font-weight: 700;
  line-height: 1.1;
  margin: 0 0 1.5rem;
  color: var(--brand-dark);

  .highlight {
    position: relative;
    &::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 0.5rem;
      background: var(--brand-blue);
      z-index: -1;
      transform: rotate(-1deg);
    }
  }

  .italic {
    font-style: italic;
    color: var(--brand-pink);
  }
}

.hero-description {
  font-size: 1.125rem;
  color: rgba(0, 0, 0, 0.7);
  line-height: 1.7;
}

.hero-decoration {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
}

.deco-circle {
  position: absolute;
  border-radius: 50%;
  opacity: 0.3;
}

.deco-circle-1 {
  width: 300px;
  height: 300px;
  background: var(--brand-yellow);
  top: -100px;
  right: 10%;
}

.deco-circle-2 {
  width: 200px;
  height: 200px;
  background: var(--brand-blue);
  bottom: -50px;
  right: 30%;
}

.deco-circle-3 {
  width: 150px;
  height: 150px;
  background: var(--brand-pink);
  top: 50%;
  right: 5%;
}

// Main Content
.contact-main {
  margin-top: -4rem;
  padding-bottom: 5rem;
}

.contact-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;

  @media (min-width: 992px) {
    grid-template-columns: 380px 1fr;
  }
}

// Contact Info Card
.info-card {
  background: var(--brand-dark, #1A1A1A);
  color: white;
  padding: 2.5rem;
  border-radius: 1.5rem;
  height: fit-content;
}

.info-title {
  font-family: var(--font-heading);
  font-size: 1.75rem;
  font-weight: 700;
  margin: 0 0 0.5rem;
}

.info-subtitle {
  font-size: 0.875rem;
  opacity: 0.7;
  margin: 0 0 2rem;
}

.info-items {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.info-item {
  display: flex;
  gap: 1rem;
}

.info-icon {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  flex-shrink: 0;

  svg {
    width: 1.25rem;
    height: 1.25rem;
    color: var(--brand-lime);
  }
}

.info-content {
  display: flex;
  flex-direction: column;
}

.info-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  opacity: 0.6;
  margin-bottom: 0.25rem;
}

.info-value {
  font-size: 0.9375rem;
  line-height: 1.5;
  color: white;
  text-decoration: none;
  transition: color 0.2s ease;

  &:hover {
    color: var(--brand-lime);
  }
}

.social-links {
  margin-top: 2.5rem;
  padding-top: 2rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.social-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  opacity: 0.6;
  display: block;
  margin-bottom: 1rem;
}

.social-icons {
  display: flex;
  gap: 0.75rem;
}

.social-icon {
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  color: white;
  transition: all 0.2s ease;

  svg {
    width: 1.25rem;
    height: 1.25rem;
  }

  &:hover {
    background: var(--brand-lime);
    color: var(--brand-dark);
    transform: translateY(-2px);
  }
}

// Form Card
.form-card {
  background: white;
  padding: 2.5rem;
  border-radius: 1.5rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.form-title {
  font-family: var(--font-heading);
  font-size: 1.75rem;
  font-weight: 700;
  margin: 0 0 0.5rem;
  color: var(--brand-dark);
}

.form-subtitle {
  font-size: 0.875rem;
  color: rgba(0, 0, 0, 0.6);
  margin: 0 0 2rem;
}

.contact-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;

  @media (min-width: 576px) {
    grid-template-columns: 1fr 1fr;
  }
}

.form-group {
  display: flex;
  flex-direction: column;

  &.full-width {
    grid-column: 1 / -1;
  }

  label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--brand-dark);
    margin-bottom: 0.5rem;
  }

  input, textarea {
    padding: 0.875rem 1rem;
    border: 2px solid #e5e5e5;
    border-radius: 0.75rem;
    font-size: 1rem;
    font-family: inherit;
    transition: all 0.2s ease;
    background: #fafafa;

    &:focus {
      outline: none;
      border-color: var(--brand-pink);
      background: white;
      box-shadow: 0 0 0 3px rgba(255, 77, 109, 0.1);
    }

    &::placeholder {
      color: #aaa;
    }
  }

  textarea {
    resize: vertical;
    min-height: 120px;
  }
}

.btn-submit {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  background: var(--brand-pink);
  color: white;
  border: none;
  border-radius: 0.75rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  align-self: flex-start;

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

// Success Message
.success-message {
  text-align: center;
  padding: 3rem 1rem;
}

.success-icon {
  width: 80px;
  height: 80px;
  background: var(--brand-lime);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  margin: 0 auto 1.5rem;
  color: var(--brand-dark);
}

.success-message h3 {
  font-family: var(--font-heading);
  font-size: 1.75rem;
  margin: 0 0 0.75rem;
}

.success-message p {
  color: rgba(0, 0, 0, 0.6);
  margin: 0 0 2rem;
}

.btn-reset {
  padding: 0.75rem 1.5rem;
  background: transparent;
  border: 2px solid var(--brand-dark);
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover {
    background: var(--brand-dark);
    color: white;
  }
}

// Map Section
.map-section {
  padding: 0 0 5rem;
}

.map-title {
  font-family: var(--font-heading);
  font-size: 2rem;
  font-weight: 700;
  text-align: center;
  margin: 0 0 2rem;
}

.map-wrapper {
  border-radius: 1.5rem;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);

  iframe {
    display: block;
  }
}
</style>
