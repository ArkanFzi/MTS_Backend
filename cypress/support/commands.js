Cypress.Commands.add('login', (email, password) => {
  return cy.session([email, password], () => {
    // First, get the CSRF cookie
    cy.request({
      url: '/sanctum/csrf-cookie',
      headers: {
        'Referer': Cypress.config('baseUrl'),
        'Origin': Cypress.config('baseUrl'),
      }
    });
    
    // Then perform login
    cy.request({
      method: 'POST',
      url: '/api/auth/login',
      body: {
        email,
        password,
      },
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Referer': Cypress.config('baseUrl'),
        'Origin': Cypress.config('baseUrl'),
      }
    }).then((response) => {
      expect(response.status).to.eq(200);
      expect(response.body.status).to.eq('success');
    });
  }, {
    validate() {
      // Check if we are still logged in as the correct user
      return cy.request({
        url: '/api/settings/profile',
        failOnStatusCode: false,
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Referer': Cypress.config('baseUrl'),
          'Origin': Cypress.config('baseUrl'),
        }
      }).then((res) => {
        if (res.status !== 200) return false;
        // Verify we are logged in as the expected user
        return res.body.data && res.body.data.email === email;
      });
    },
    cacheAcrossSpecs: true
  });
});

Cypress.Commands.add('apiClient', (options) => {
  // For SPA Auth, we need to send the X-XSRF-TOKEN header for state-changing requests.
  return cy.getCookie('XSRF-TOKEN').then((cookie) => {
    const headers = {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'Referer': Cypress.config('baseUrl'),
      'Origin': Cypress.config('baseUrl'),
      ...options.headers,
    };

    if (cookie) {
      headers['X-XSRF-TOKEN'] = decodeURIComponent(cookie.value);
    }

    return cy.request({
      ...options,
      headers: headers,
      failOnStatusCode: false,
    });
  });
});
