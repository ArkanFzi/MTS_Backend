describe('Moderator API', () => {
  beforeEach(() => {
    cy.login(Cypress.env('mod_email'), Cypress.env('password'));
  });

  describe('Category Management', () => {
    it('should create a category', () => {
      const ts = Date.now();
      cy.apiClient({
        method: 'POST',
        url: '/api/moderator/categories',
        body: { name: `Cat ${ts}`, slug: `cat-${ts}`, description: 'Test' },
      }).then((res) => {
        expect(res.status).to.eq(201);
        Cypress.env('temp_category_id', res.body.data.id);
      });
    });

    it('should update a category', () => {
      const id = Cypress.env('temp_category_id');
      if (id) {
        cy.apiClient({
          method: 'PUT',
          url: `/api/moderator/categories/${id}`,
          body: { name: 'Updated Category' },
        }).then((res) => {
          expect(res.status).to.eq(200);
        });
      }
    });
  });

  describe('Reports & Bans', () => {
    it('should list reports', () => {
      cy.apiClient({ method: 'GET', url: '/api/moderator/reports' }).then((res) => {
        expect(res.status).to.eq(200);
      });
    });

    it('should warn a user', () => {
      cy.apiClient({ method: 'GET', url: '/api/moderator/bans' }).then((res) => {
        const userToWarn = res.body.data.data.find(u => u.email === Cypress.env('user_email'));
        if (userToWarn) {
          cy.apiClient({
            method: 'POST',
            url: `/api/moderator/bans/${userToWarn.id}/warn`,
            body: { reason: 'Test warning' },
          }).then((warnRes) => {
            expect(warnRes.status).to.eq(200);
          });
        }
      });
    });

    it('should ban a user', () => {
      cy.apiClient({ method: 'GET', url: '/api/moderator/bans' }).then((res) => {
        const userToBan = res.body.data.data.find(u => u.email === Cypress.env('user_email'));
        if (userToBan) {
          cy.apiClient({
            method: 'POST',
            url: `/api/moderator/bans/${userToBan.id}/ban`,
            body: { reason: 'Test ban' },
          }).then((banRes) => {
            expect(banRes.status).to.eq(200);
          });
        }
      });
    });

    it('should unban a user', () => {
      cy.apiClient({ method: 'GET', url: '/api/moderator/bans' }).then((res) => {
        const userToUnban = res.body.data.data.find(u => u.email === Cypress.env('user_email'));
        if (userToUnban) {
          cy.apiClient({
            method: 'POST',
            url: `/api/moderator/bans/${userToUnban.id}/unban`,
          }).then((unbanRes) => {
            expect(unbanRes.status).to.eq(200);
          });
        }
      });
    });
  });

  it('should list moderation logs', () => {
    cy.apiClient({ method: 'GET', url: '/api/moderator/logs' }).then((res) => {
      expect(res.status).to.eq(200);
    });
  });
});