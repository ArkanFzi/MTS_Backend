describe('Interactions API', () => {
  beforeEach(() => {
    cy.login(Cypress.env('user_email'), Cypress.env('password'));
  });

  it('should toggle like on a post', () => {
    cy.apiClient({
      method: 'POST',
      url: '/api/likes/toggle',
      body: { type: 'post', id: 2 }, // Assuming post 2 is not owned by user
    }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body).to.have.property('liked');
    });
  });

  it('should NOT like own post', () => {
    // Assuming post 1 is owned by the user (or we created it in post.cy.js)
    cy.apiClient({
      method: 'POST',
      url: '/api/likes/toggle',
      body: { type: 'post', id: 1 },
    }).then((res) => {
      expect(res.status).to.eq(403);
    });
  });

  it('should vote on a post', () => {
    cy.apiClient({
      method: 'POST',
      url: '/api/votes',
      body: { type: 'post', id: 2, value: 1 },
    }).then((res) => {
      expect(res.status).to.eq(200);
    });
  });

  it('should NOT vote on own post', () => {
    cy.apiClient({
      method: 'POST',
      url: '/api/votes',
      body: { type: 'post', id: 1, value: 1 },
    }).then((res) => {
      expect(res.status).to.eq(403);
    });
  });

  it('should toggle bookmark', () => {
    cy.apiClient({
      method: 'POST',
      url: '/api/bookmarks/toggle',
      body: { post_id: 2 },
    }).then((res) => {
      expect(res.status).to.eq(200);
    });
  });

  it('should follow a user', () => {
    cy.apiClient({
      method: 'POST',
      url: '/api/users/2/follow',
    }).then((res) => {
      expect(res.status).to.eq(200);
    });
  });
});
