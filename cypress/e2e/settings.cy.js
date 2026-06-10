describe('Settings API', () => {
  beforeEach(() => {
    cy.login(Cypress.env('user_email'), Cypress.env('password'));
  });

  it('should get profile settings', () => {
    cy.apiClient({ method: 'GET', url: '/api/settings/profile' }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body).to.have.property('email', Cypress.env('user_email'));
    });
  });

  it('should update profile', () => {
    cy.apiClient({
      method: 'PUT',
      url: '/api/settings/profile',
      body: { name: 'Updated User Name' },
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.name).to.eq('Updated User Name');
    });
  });

  it('should update password', () => {
    cy.apiClient({
      method: 'PUT',
      url: '/api/settings/password',
      body: {
        current_password: Cypress.env('password'),
        password: 'newpassword123',
        password_confirmation: 'newpassword123',
      },
    }).then((res) => {
      expect(res.status).to.eq(200);
    });

    // Revert password for other tests
    cy.apiClient({
      method: 'PUT',
      url: '/api/settings/password',
      headers: { Authorization: `Bearer ${Cypress.env('token')}` },
      body: {
        current_password: 'newpassword123',
        password: Cypress.env('password'),
        password_confirmation: Cypress.env('password'),
      },
    });
  });
});
