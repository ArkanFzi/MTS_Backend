describe('Authentication API', () => {
  const registerUrl = '/api/auth/register';
  const loginUrl = '/api/auth/login';
  const logoutUrl = '/api/auth/logout';

  it('should register a new user successfully', () => {
    const email = `newuser_${Date.now()}@email.com`;
    cy.apiClient({
      method: 'POST',
      url: registerUrl,
      body: {
        name: 'Test User',
        email: email,
        password: 'password123',
        password_confirmation: 'password123',
      },
    }).then((res) => {
      expect(res.status).to.eq(201);
      expect(res.body).to.have.property('user');
      expect(res.body.user.email).to.eq(email);
    });
  });

  it('should not register with existing email', () => {
    cy.apiClient({
      method: 'POST',
      url: registerUrl,
      body: {
        name: 'Admin',
        email: Cypress.env('admin_email'),
        password: 'password123',
        password_confirmation: 'password123',
      },
    }).then((res) => {
      expect(res.status).to.eq(422);
      expect(res.body).to.have.property('errors');
    });
  });

  it('should login successfully with correct credentials', () => {
    cy.apiClient({
      method: 'POST',
      url: loginUrl,
      body: {
        email: Cypress.env('user_email'),
        password: Cypress.env('password'),
      },
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body).to.have.property('access_token');
    });
  });

  it('should not login with wrong password', () => {
    cy.apiClient({
      method: 'POST',
      url: loginUrl,
      body: {
        email: Cypress.env('user_email'),
        password: 'wrongpassword',
      },
    }).then((res) => {
      expect(res.status).to.eq(401);
    });
  });

  it('should logout successfully', () => {
    cy.login(Cypress.env('user_email'), Cypress.env('password'));
    cy.apiClient({
      method: 'POST',
      url: logoutUrl,
    }).then((res) => {
      expect(res.status).to.eq(200);
    });
  });
});
