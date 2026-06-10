describe('Admin API', () => {
  beforeEach(() => {
    cy.login(Cypress.env('admin_email'), Cypress.env('password'));
  });

  it('should get overview stats', () => {
    cy.apiClient({ method: 'GET', url: '/api/admin/stats/overview' }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body).to.have.property('data');
    });
  });

  it('should list all users', () => {
    cy.apiClient({ method: 'GET', url: '/api/admin/users' }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body).to.have.property('data');
    });
  });

  it('should update a user\'s role', () => {
    cy.apiClient({ method: 'GET', url: '/api/admin/users' }).then((res) => {
      const userId = res.body.data.data[0].id;
      cy.apiClient({
        method: 'PUT',
        url: `/api/admin/users/${userId}/role`,
        body: { role: 'moderator' },
      }).then((putRes) => {
        expect(putRes.status).to.eq(200);
      });
    });
  });

  it('should reset a user\'s password', () => {
    cy.apiClient({ method: 'GET', url: '/api/admin/users' }).then((res) => {
      const userId = res.body.data.data[0].id;
      cy.apiClient({
        method: 'PUT',
        url: `/api/admin/users/${userId}/reset-password`,
        body: { password: 'newpassword123', password_confirmation: 'newpassword123' },
      }).then((putRes) => {
        expect(putRes.status).to.eq(200);
      });
    });
  });

  it('should NOT allow a regular user to access admin stats', () => {
    cy.login(Cypress.env('user_email'), Cypress.env('password'));
    cy.apiClient({ method: 'GET', url: '/api/admin/stats/overview' }).then((res) => {
      expect(res.status).to.eq(403);
    });
  });
});