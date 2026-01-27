<template>
  <div class="legal-page">
    <div class="container py-5">
      <h1 class="font-heading display-4 fw-bold mb-4">Terms and Conditions</h1>
      <p class="text-muted mb-4">Last updated: January 2025</p>

      <section class="mb-5">
        <h2 class="h3 mb-3">1. Acceptance of Terms</h2>
        <p>
          By accessing and using the Eat Is Family website, you accept and agree to be bound by these Terms and Conditions. 
          If you do not agree with any part of these terms, please do not use our website.
        </p>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">2. Use of Website</h2>
        <p>You agree to use our website only for lawful purposes and in a way that does not:</p>
        <ul>
          <li>Infringe on the rights of others</li>
          <li>Restrict or inhibit anyone's use of the website</li>
          <li>Introduce viruses or other malicious software</li>
          <li>Attempt unauthorized access to our systems</li>
        </ul>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">3. Job Applications</h2>
        <p>When submitting job applications through our website:</p>
        <ul>
          <li>You must provide accurate and truthful information</li>
          <li>You confirm that all documents submitted are genuine</li>
          <li>You authorize us to verify the information provided</li>
          <li>Submission of an application does not guarantee employment</li>
        </ul>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">4. Intellectual Property</h2>
        <p>
          All content on this website, including but not limited to text, graphics, logos, images, and software, 
          is the property of Eat Is Family and is protected by intellectual property laws. You may not reproduce, 
          distribute, or create derivative works without our written consent.
        </p>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">5. Third-Party Links</h2>
        <p>
          Our website may contain links to external websites. We are not responsible for the content, 
          privacy practices, or availability of these third-party sites.
        </p>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">6. Limitation of Liability</h2>
        <p>
          To the maximum extent permitted by law, Eat Is Family shall not be liable for any direct, indirect, 
          incidental, or consequential damages arising from your use of this website.
        </p>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">7. Modifications</h2>
        <p>
          We reserve the right to modify these Terms and Conditions at any time. Changes will be effective immediately 
          upon posting on this page. Your continued use of the website constitutes acceptance of the modified terms.
        </p>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">8. Governing Law</h2>
        <p>
          These Terms and Conditions are governed by and construed in accordance with the laws of France. 
          Any disputes shall be subject to the exclusive jurisdiction of French courts.
        </p>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">9. Contact</h2>
        <p>
          For questions about these Terms and Conditions, please contact us at:
        </p>
        <address>
          <strong>Eat Is Family</strong><br>
          Email: <a href="mailto:legal@eatisfamily.com">legal@eatisfamily.com</a>
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
  title: 'Terms and Conditions - Eat Is Family',
  meta: [
    { name: 'description', content: 'Terms and Conditions for using the Eat Is Family website and services.' }
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
