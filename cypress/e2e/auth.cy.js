describe('Auth', () => {
  const timestamp = Date.now();
  const newUser = {
    username: `testuser_${timestamp}`,
    email: `test_${timestamp}@email.com`,
    password: 'password123',
    password_confirmation: 'password123',
  };

  describe('Register', () => {
    it('user dapat register', () => {
      cy.apiClient({
        method: 'POST',
        url: '/api/auth/register',
        body: newUser,
      }).then((res) => {
        expect(res.status).to.eq(201);
        expect(res.body.data.user).to.include.keys(['id', 'username', 'email']);
        expect(res.body.data.user.email).to.eq(newUser.email);
      });
    });

    it('tidak bisa register dengan email yang sudah terdaftar', () => {
      cy.apiClient({ method: 'POST', url: '/api/auth/register', body: newUser });
      cy.apiClient({ method: 'POST', url: '/api/auth/register', body: newUser }).then((res) => {
        expect(res.status).to.eq(422);
        expect(res.body.errors).to.have.property('email');
      });
    });
  });

  describe('Login', () => {
    it('user dapat login dengan kredensial yang benar', () => {
      cy.login(Cypress.env('user_email')).then((token) => {
        expect(token).to.exist;
      });
    });

    it('user tidak bisa login dengan password salah', () => {
      cy.apiClient({
        method: 'POST',
        url: '/api/auth/login',
        body: { email: Cypress.env('user_email'), password: 'passwordsalah' },
      }).then((res) => {
        expect(res.status).to.eq(401);
      });
    });

    it('user yang di-ban tidak bisa login', () => {
      cy.apiClient({
        method: 'POST',
        url: '/api/auth/login',
        body: { email: 'banned_user@email.com', password: Cypress.env('password') },
      }).then((res) => {
        expect(res.status).to.eq(403);
      });
    });
  });

  describe('Logout', () => {
    it('user yang sudah login dapat logout', () => {
      cy.loginAsUser().then(() => {
        cy.apiClient({ method: 'POST', url: '/api/auth/logout' }).then((res) => {
          expect(res.status).to.eq(200);
        });
      });
    });
  });

  describe('Forgot Password', () => {
    it('user dapat request forgot password', () => {
      cy.apiClient({
        method: 'POST',
        url: '/api/auth/forgot-password',
        body: { email: Cypress.env('user_email') },
      }).then((res) => {
        expect(res.status).to.eq(200);
        expect(res.body.success).to.be.true;
      });
    });
  });
});