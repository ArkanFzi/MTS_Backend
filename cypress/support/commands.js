Cypress.Commands.add('login', (email, password) => {
  const pw = password || Cypress.env('password');
  return cy.request({
    method: 'POST',
    url: '/api/auth/login',
    body: { email, password: pw },
    headers: { 'Accept': 'application/json' },
    failOnStatusCode: false,
  }).then((response) => {
    expect(response.status).to.eq(200);
    const token = response.body.data?.token;
    expect(token, 'Token harus ada').to.exist;
    Cypress.env('token', token);
    return token;
  });
});

Cypress.Commands.add('loginAsUser', () => {
  return cy.login(Cypress.env('user_email'));
});

Cypress.Commands.add('loginAsModerator', () => {
  return cy.login(Cypress.env('mod_email'));
});

Cypress.Commands.add('loginAsAdmin', () => {
  return cy.login(Cypress.env('admin_email'));
});

Cypress.Commands.add('apiClient', (options) => {
  const token = Cypress.env('token');
  const authHeader = token ? { Authorization: `Bearer ${token}` } : {};

  return cy.request({
    ...options,
    headers: {
      ...options.headers,
      ...authHeader,
      'Accept': 'application/json',
    },
    failOnStatusCode: false,
  });
});

Cypress.Commands.add('logout', () => {
  cy.apiClient({ method: 'POST', url: '/api/auth/logout' });
  Cypress.env('token', null);
});