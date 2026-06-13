describe('Profile', () => {
  it('user dapat melihat profilnya', () => {
    cy.loginAsUser().then(() => {
      cy.apiClient({ method: 'GET', url: '/api/settings/profile' }).then((res) => {
        expect(res.status).to.eq(200);
        expect(res.body).to.have.property('success', true);
        expect(res.body).to.have.property('data');
      });
    });
  });

  it('guest tidak bisa akses profil', () => {
    cy.request({
      method: 'GET',
      url: '/api/settings/profile',
      headers: { Accept: 'application/json' },
      failOnStatusCode: false,
    }).then((res) => {
      expect(res.status).to.eq(401);
    });
  });

  it('user dapat update bio', () => {
    cy.loginAsUser().then(() => {
      cy.apiClient({
        method: 'PUT',
        url: '/api/settings/profile',
        body: { bio: 'Bio baru dari Cypress' },
      }).then((res) => {
        expect(res.status).to.eq(200);
        expect(res.body.data.bio).to.eq('Bio baru dari Cypress');
      });
    });
  });

  it('user dapat update username', () => {
    cy.loginAsUser().then(() => {
      const newUsername = `user_cypress_${Date.now()}`;
      cy.apiClient({
        method: 'PUT',
        url: '/api/settings/profile',
        body: { username: newUsername },
      }).then((res) => {
        expect(res.status).to.eq(200);
        expect(res.body.data.username).to.eq(newUsername);
      });
    });
  });

it('user dapat update password dengan password lama yang benar', () => {
  cy.loginAsUser().then(() => {
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
      expect(res.body.success).to.be.true;

      // Reset password balik ke semula supaya test berikutnya bisa login
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
});
  it('user tidak bisa update password dengan password lama yang salah', () => {
    // Login ulang karena password sudah berubah di test sebelumnya
    cy.loginAsUser().then(() => {
      cy.apiClient({
        method: 'PUT',
        url: '/api/settings/password',
        body: {
          old_password: 'passwordsalah',
          new_password: 'newpassword123',
          new_password_confirmation: 'newpassword123',
        },
      }).then((res) => {
        expect(res.status).to.eq(400);
        expect(res.body.success).to.be.false;
      });
    });
  });
});