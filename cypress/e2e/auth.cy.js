describe('Authentication API', () => {
  const registerUrl = '/api/auth/register';
  const loginUrl = '/api/auth/login';
  const logoutUrl = '/api/auth/logout';

  it('should register a new user successfully', () => {
    const ts = Date.now();
    const email = `newuser_${ts}@email.com`;
    const username = `TestUser_${ts}`;
    cy.apiClient({
      method: 'POST',
      url: registerUrl,
      body: {
        username: username,
        email: email,
        password: 'password123',
        password_confirmation: 'password123',
      },
    }).then((res) => {
      if (res.status === 422) {
        cy.log('Validation errors:', JSON.stringify(res.body.errors));
      }
      expect(res.status).to.eq(201);
      expect(res.body.status).to.eq('success');
      expect(res.body.data).to.have.property('user');
      expect(res.body.data.user.email).to.eq(email);
    });
  });

  it('should not register with existing email', () => {
    cy.apiClient({
      method: 'POST',
      url: registerUrl,
      body: {
        username: 'Admin',
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
    cy.login(Cypress.env('user_email'), Cypress.env('password'));
    // If login command succeeds, it means session is established
  });

  it('should not login with wrong password', () => {
    // We have to hit the API here, so we'll use a different email to avoid email-based throttle
    cy.apiClient({
      method: 'POST',
      url: loginUrl,
      body: {
        email: 'wrong@email.com',
        password: 'wrongpassword',
      },
    }).then((res) => {
      expect(res.status).to.eq(401);
      expect(res.body.status).to.eq('error');
    });
  });

  it('should logout successfully', () => {
    cy.login(Cypress.env('user_email'), Cypress.env('password'));
    cy.request('/sanctum/csrf-cookie').then(() => {
      cy.apiClient({
        method: 'POST',
        url: logoutUrl,
      }).then((res) => {
        if (res.status !== 200) {
          cy.log('Logout failed:', res.status, JSON.stringify(res.body));
        }
        expect(res.status).to.eq(200);
      });
    });
  });
});
