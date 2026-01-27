<template>
  <div class="legal-page">
    <div class="container py-5">
      <h1 class="font-heading display-4 fw-bold mb-4">Cookie Policy</h1>
      <p class="text-muted mb-4">Last updated: January 2025</p>

      <section class="mb-5">
        <h2 class="h3 mb-3">1. What Are Cookies?</h2>
        <p>
          Cookies are small text files that are placed on your device when you visit our website. 
          They help us provide you with a better experience by remembering your preferences and understanding how you use our site.
        </p>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">2. Types of Cookies We Use</h2>
        
        <h3 class="h5 mt-4 mb-2">Essential Cookies</h3>
        <p>
          These cookies are necessary for the website to function properly. They enable basic features like page navigation 
          and access to secure areas. The website cannot function properly without these cookies.
        </p>

        <h3 class="h5 mt-4 mb-2">Analytics Cookies</h3>
        <p>
          We use analytics cookies to understand how visitors interact with our website. This helps us improve our site's 
          functionality and user experience. These cookies collect information anonymously.
        </p>

        <h3 class="h5 mt-4 mb-2">Functional Cookies</h3>
        <p>
          These cookies allow the website to remember choices you make (such as your language preference) and provide 
          enhanced, more personal features.
        </p>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">3. Cookies We Use</h2>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="table-light">
              <tr>
                <th>Cookie Name</th>
                <th>Purpose</th>
                <th>Duration</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>_session</td>
                <td>Session management</td>
                <td>Session</td>
              </tr>
              <tr>
                <td>_ga</td>
                <td>Google Analytics - tracking</td>
                <td>2 years</td>
              </tr>
              <tr>
                <td>_gid</td>
                <td>Google Analytics - distinguishing users</td>
                <td>24 hours</td>
              </tr>
              <tr>
                <td>cookie_consent</td>
                <td>Stores your cookie preferences</td>
                <td>1 year</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">4. Managing Cookies</h2>
        <p>
          You can control and manage cookies in various ways. Most browsers allow you to:
        </p>
        <ul>
          <li>View what cookies are stored and delete them individually</li>
          <li>Block third-party cookies</li>
          <li>Block cookies from specific sites</li>
          <li>Block all cookies</li>
          <li>Delete all cookies when you close your browser</li>
        </ul>
        <p>
          Please note that blocking all cookies may affect your experience on our website and limit certain functionalities.
        </p>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">5. Browser Settings</h2>
        <p>Here's how to manage cookies in popular browsers:</p>
        <ul>
          <li><a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener">Google Chrome</a></li>
          <li><a href="https://support.mozilla.org/en-US/kb/cookies-information-websites-store-on-your-computer" target="_blank" rel="noopener">Mozilla Firefox</a></li>
          <li><a href="https://support.apple.com/guide/safari/manage-cookies-sfri11471/mac" target="_blank" rel="noopener">Apple Safari</a></li>
          <li><a href="https://support.microsoft.com/en-us/help/17442/windows-internet-explorer-delete-manage-cookies" target="_blank" rel="noopener">Microsoft Edge</a></li>
        </ul>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">6. Changes to This Policy</h2>
        <p>
          We may update this Cookie Policy from time to time. Any changes will be posted on this page with an updated 
          "Last updated" date.
        </p>
      </section>

      <section class="mb-5">
        <h2 class="h3 mb-3">7. Contact Us</h2>
        <p>
          If you have questions about our use of cookies, please contact us at:
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
  title: 'Cookie Policy - Eat Is Family',
  meta: [
    { name: 'description', content: 'Cookie Policy for Eat Is Family. Learn about how we use cookies on our website.' }
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

  h3 {
    color: #444;
  }

  p, li, td {
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

  .table {
    th {
      font-weight: 600;
    }
  }
}
</style>
