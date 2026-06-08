Cypress.Commands.add('login', (email, password) => {
  return cy.request({
    method: 'POST',
    url: '/api/auth/login',
    body: {
      email,
      password,
    },
  }).then((response) => {
    expect(response.status).to.eq(200);
    expect(response.body).to.have.property('access_token');
    Cypress.env('token', response.body.access_token);
    return response.body.access_token;
  });
});

Cypress.Commands.add('apiClient', (options) => {
  const token = Cypress.env('token');
  const authHeader = token ? { Authorization: `Bearer ${token}` } : {};
  
  return cy.request({
    ...options,
    headers: {
      ...options.headers,
      ...authHeader,
      Accept: 'application/json',
    },
    failOnStatusCode: false,
  });
});
