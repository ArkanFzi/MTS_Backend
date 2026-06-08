describe('Moderator API', () => {
  beforeEach(() => {
    cy.login(Cypress.env('mod_email'), Cypress.env('password'));
  });

  describe('Category Management', () => {
    it('should create a category', () => {
      cy.apiClient({
        method: 'POST',
        url: '/api/moderator/categories',
        body: { name: 'New Category', slug: 'new-category', description: 'Test' },
      }).then((res) => {
        expect(res.status).to.eq(201);
        Cypress.env('temp_category_id', res.body.id);
      });
    });

    it('should update a category', () => {
      const id = Cypress.env('temp_category_id') || 1;
      cy.apiClient({
        method: 'PUT',
        url: `/api/moderator/categories/${id}`,
        body: { name: 'Updated Category' },
      }).then((res) => {
        expect(res.status).to.eq(200);
      });
    });
  });

  describe('Reports & Bans', () => {
    it('should list reports', () => {
      cy.apiClient({ method: 'GET', url: '/api/moderator/reports' }).then((res) => {
        expect(res.status).to.eq(200);
      });
    });

    it('should warn a user', () => {
      cy.apiClient({
        method: 'POST',
        url: '/api/moderator/bans/3/warn', // Warning user 3
        body: { reason: 'Test warning' },
      }).then((res) => {
        expect(res.status).to.eq(200);
      });
    });

    it('should ban a user', () => {
      cy.apiClient({
        method: 'POST',
        url: '/api/moderator/bans/3/ban',
        body: { reason: 'Test ban' },
      }).then((res) => {
        expect(res.status).to.eq(200);
      });
    });

    it('should unban a user', () => {
      cy.apiClient({
        method: 'POST',
        url: '/api/moderator/bans/3/unban',
      }).then((res) => {
        expect(res.status).to.eq(200);
      });
    });
  });

  it('should list moderation logs', () => {
    cy.apiClient({ method: 'GET', url: '/api/moderator/logs' }).then((res) => {
      expect(res.status).to.eq(200);
    });
  });
});
