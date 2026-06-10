describe('Business Rules Validation', () => {
  it('should enforce 15 reputation points for post creation', () => {
    const email = `newuser_rep_${Date.now()}@email.com`;
    cy.request('/sanctum/csrf-cookie').then(() => {
      cy.apiClient({
        method: 'POST',
        url: '/api/auth/register',
        body: {
          username: 'NoRepUser',
          email: email,
          password: 'password123',
          password_confirmation: 'password123',
        },
      }).then((res) => {
        expect(res.status).to.eq(201);

        const categoryId = '019eb007-e370-732a-91c7-44d1664206d2';

        cy.apiClient({
          method: 'POST',
          url: '/api/posts',
          body: {
            title: 'I have no rep',
            body: 'This should fail',
            category_id: categoryId,
          },
        }).then((postRes) => {
          expect(postRes.status).to.eq(403);
          expect(postRes.body.message).to.contain('poin');
        });
      });
    });
  });

  it('should prevent self-interaction (voting own post)', () => {
    cy.login(Cypress.env('user_email'), Cypress.env('password'));

    cy.apiClient({ method: 'GET', url: '/api/me/posts' }).then((res) => {
      if (res.body.data && res.body.data.data && res.body.data.data.length > 0) {
        const postId = res.body.data.data[0].id;
        cy.apiClient({
          method: 'POST',
          url: '/api/votes',
          body: { target_type: 'post', target_id: postId, vote: 'up' },
        }).then((voteRes) => {
          expect(voteRes.status).to.eq(403);
        });
      }
    });
  });

  it('should prevent banned users from logging in', () => {
    cy.login(Cypress.env('mod_email'), Cypress.env('password'));

    cy.apiClient({ method: 'GET', url: '/api/moderator/bans' }).then((res) => {
      const userToBan = res.body.data.data.find(u => u.email === Cypress.env('user_email'));
      if (userToBan) {
        const userId = userToBan.id;

        cy.apiClient({
          method: 'POST',
          url: `/api/moderator/bans/${userId}/ban`,
          body: { reason: 'Testing login block' },
        }).then(() => {
          cy.request('/sanctum/csrf-cookie').then(() => {
            cy.apiClient({
              method: 'POST',
              url: '/api/auth/login',
              body: {
                email: Cypress.env('user_email'),
                password: Cypress.env('password'),
              },
            }).then((loginRes) => {
              expect(loginRes.status).to.eq(403);
              expect(loginRes.body.message).to.match(/ban|deactivated|blokir/i);

              // Cleanup
              cy.login(Cypress.env('mod_email'), Cypress.env('password'));
              cy.apiClient({
                method: 'POST',
                url: `/api/moderator/bans/${userId}/unban`,
              });
            });
          });
        });
      }
    });
  });
});