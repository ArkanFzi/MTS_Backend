describe('Settings API', () => {
  beforeEach(() => {
    cy.login(Cypress.env('user_email'), Cypress.env('password'));
  });

  it('should get profile settings', () => {
    cy.apiClient({ method: 'GET', url: '/api/settings/profile' }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.data).to.have.property('email', Cypress.env('user_email'));
    });
  });

  it('should update profile', () => {
    cy.apiClient({
      method: 'PUT',
      url: '/api/settings/profile',
      body: { username: 'updateduser' },
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body.data.username).to.eq('updateduser');
    });
  });

  it('should update password', () => {
    cy.apiClient({
      method: 'PUT',
      url: '/api/settings/password',
      body: {
        old_password: Cypress.env('password'),
        new_password: 'newpassword123',
        new_password_confirmation: 'newpassword123',
      },
    }).then((res) => {
      expect(res.status).to.eq(200);
    });

    // Revert password for other tests
    cy.apiClient({
      method: 'PUT',
      url: '/api/settings/password',
      body: {
        old_password: 'newpassword123',
        new_password: Cypress.env('password'),
        new_password_confirmation: Cypress.env('password'),
      },
    });
  });
});
