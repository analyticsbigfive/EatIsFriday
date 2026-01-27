<template>
  <div class="legal-page">
    <div class="container py-5">
      <h1 class="font-heading display-4 fw-bold mb-4">Privacy Policy</h1>
      <p class="text-muted mb-4">Last updated: January 2025</p>

      <section class="mb-5">
        <h2 class="h3 mb-3">1. Introduction</h2>
        <p>
          Eat Is Family ("we", "our", or "us") is committed to protecting your personal data and respecting your privacy. 
          This Privacy Policy explains how we collect, use, and safeguard your information when you visit our website or use our services.
        </p>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">2. Information We Collect</h2>
        <p>We may collect the following types of information:</p>
        <ul>
          <li><strong>Personal Information:</strong> Name, email address, phone number, and other contact details you provide when applying for jobs or contacting us.</li>
          <li><strong>Usage Data:</strong> Information about how you use our website, including IP address, browser type, pages visited, and time spent on our site.</li>
          <li><strong>Cookies:</strong> Small data files stored on your device to enhance your browsing experience.</li>
        </ul>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">3. How We Use Your Information</h2>
        <p>We use your information to:</p>
        <ul>
          <li>Process job applications and communicate with applicants</li>
          <li>Respond to your inquiries and provide customer support</li>
          <li>Improve our website and services</li>
          <li>Send relevant updates about job opportunities (with your consent)</li>
          <li>Comply with legal obligations</li>
        </ul>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">4. Data Sharing</h2>
        <p>
          We do not sell your personal data. We may share your information with:
        </p>
        <ul>
          <li>Venue partners for job placement purposes</li>
          <li>Service providers who assist us in operating our website</li>
          <li>Legal authorities when required by law</li>
        </ul>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">5. Your Rights</h2>
        <p>Under GDPR, you have the right to:</p>
        <ul>
          <li>Access your personal data</li>
          <li>Correct inaccurate data</li>
          <li>Request deletion of your data</li>
          <li>Object to data processing</li>
          <li>Data portability</li>
        </ul>
        <p>To exercise these rights, please contact us at <a href="mailto:privacy@eatisfamily.com">privacy@eatisfamily.com</a>.</p>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">6. Data Security</h2>
        <p>
          We implement appropriate technical and organizational measures to protect your personal data against unauthorized access, 
          alteration, disclosure, or destruction.
        </p>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">7. Contact Us</h2>
        <p>
          If you have questions about this Privacy Policy, please contact us at:
        </p>
        <address>
          <strong>Eat Is Family</strong><br>
          Email: <a href="mailto:privacy@eatisfamily.com">privacy@eatisfamily.com</a>
        </address>
      </section>

      <div class="mt-5">
        <NuxtLink to="/" class="btn btn-outline-dark">← Back to Home</NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
useHead({
  title: 'Privacy Policy - Eat Is Family',
  meta: [
    { name: 'description', content: 'Privacy Policy for Eat Is Family. Learn how we collect, use, and protect your personal information.' }
  ]
})
</script>

<style scoped lang="scss">
.legal-page {
  background-color: #f8f9fa;
  min-height: 100vh;

  h1 {
    color: #1a1a1a;
  }

  h2 {
    color: #333;
  }

  p, li {
    line-height: 1.8;
    color: #444;
  }

  ul {
    padding-left: 1.5rem;
  }

  li {
    margin-bottom: 0.5rem;
  }

  a {
    color: #667eea;
    text-decoration: none;

    &:hover {
      text-decoration: underline;
    }
  }
}
</style>
