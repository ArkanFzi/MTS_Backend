describe('Interactions API', () => {
  beforeEach(() => {
    cy.login(Cypress.env('user_email'), Cypress.env('password'));
  });

  it('should toggle like on a post', () => {
    cy.apiClient({ method: 'GET', url: '/api/posts' }).then((res) => {
      const postId = res.body.data.data[0].id;
      cy.apiClient({
        method: 'POST',
        url: '/api/likes/toggle',
        body: { target_id: postId, target_type: 'post' },
      }).then((likeRes) => {
        expect(likeRes.status).to.eq(200);
      });
    });
  });

  it('should vote on a post', () => {
    cy.apiClient({ method: 'GET', url: '/api/posts' }).then((res) => {
      const postId = res.body.data.data[0].id;
      cy.apiClient({
        method: 'POST',
        url: '/api/votes',
        body: { target_id: postId, target_type: 'post', vote: 'up' },
      }).then((voteRes) => {
        expect(voteRes.status).to.eq(200);
      });
    });
  });

  it('should toggle bookmark', () => {
    cy.apiClient({ method: 'GET', url: '/api/posts' }).then((res) => {
      const postId = res.body.data.data[0].id;
      cy.apiClient({
        method: 'POST',
        url: '/api/bookmarks/toggle',
        body: { post_id: postId },
      }).then((bookRes) => {
        expect(bookRes.status).to.eq(200);
      });
    });
  });

  it('should follow a user', () => {
    cy.apiClient({ method: 'GET', url: '/api/admin/users' }).then((res) => {
      const userId = res.body.data.data.find(u => u.email !== Cypress.env('user_email')).id;
      cy.apiClient({
        method: 'POST',
        url: `/api/users/${userId}/follow`,
      }).then((followRes) => {
        expect(followRes.status).to.eq(200);
      });
    });
  });
});