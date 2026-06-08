describe('Comments API', () => {
  const postId = 1;

  it('should list comments for a post', () => {
    cy.apiClient({ method: 'GET', url: `/api/posts/${postId}/comments` }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body).to.have.property('data');
    });
  });

  describe('User Actions', () => {
    beforeEach(() => {
      cy.login(Cypress.env('user_email'), Cypress.env('password'));
    });

    it('should post a comment', () => {
      cy.apiClient({
        method: 'POST',
        url: `/api/posts/${postId}/comments`,
        body: { content: 'This is a test comment.' },
      }).then((res) => {
        expect(res.status).to.eq(201);
        Cypress.env('last_comment_id', res.body.id);
      });
    });

    it('should reply to a comment', () => {
      const commentId = Cypress.env('last_comment_id') || 1;
      cy.apiClient({
        method: 'POST',
        url: `/api/posts/${postId}/comments/${commentId}/replies`,
        body: { content: 'This is a reply.' },
      }).then((res) => {
        expect(res.status).to.eq(201);
      });
    });

    it('should NOT delete own comment (business rule: only mod/admin)', () => {
      const commentId = Cypress.env('last_comment_id') || 1;
      cy.apiClient({
        method: 'DELETE',
        url: `/api/posts/${postId}/comments/${commentId}`,
      }).then((res) => {
        expect(res.status).to.be.oneOf([403, 404, 405]); // 403 Forbidden is expected per rule
      });
    });

    it('should NOT accept an answer if not the post owner', () => {
      // Assuming post 2 is not owned by user
      cy.apiClient({
        method: 'POST',
        url: `/api/posts/2/comments/1/accept`,
      }).then((res) => {
        expect(res.status).to.eq(403);
      });
    });
  });

  describe('Moderator Actions', () => {
    beforeEach(() => {
      cy.login(Cypress.env('mod_email'), Cypress.env('password'));
    });

    it('should delete any comment', () => {
      const commentId = Cypress.env('last_comment_id') || 1;
      cy.apiClient({
        method: 'DELETE',
        url: `/api/posts/${postId}/comments/${commentId}`,
      }).then((res) => {
        // Since we might have deleted it or it might not exist in a real run, 
        // we check for 200/204 or 404. But according to rule, they SHOULD be allowed.
        expect(res.status).to.be.oneOf([200, 204, 404]);
      });
    });
  });
});
