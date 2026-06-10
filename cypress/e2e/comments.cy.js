describe('Comments API', () => {
  it('should list comments for a post', () => {
    cy.apiClient({ method: 'GET', url: '/api/posts' }).then((res) => {
      const postId = res.body.data.data[0].id;
      cy.apiClient({ method: 'GET', url: `/api/posts/${postId}/comments` }).then((commentRes) => {
        expect(commentRes.status).to.eq(200);
        expect(commentRes.body).to.have.property('data');
      });
    });
  });

  describe('User Actions', () => {
    beforeEach(() => {
      cy.login(Cypress.env('user_email'), Cypress.env('password'));
    });

    it('should post a comment', () => {
      cy.apiClient({ method: 'GET', url: '/api/posts' }).then((res) => {
        const postId = res.body.data.data[0].id;
        cy.apiClient({
          method: 'POST',
          url: `/api/posts/${postId}/comments`,
          body: { body: 'This is a test comment.' },
        }).then((commentRes) => {
          expect(commentRes.status).to.eq(201);
          Cypress.env('last_comment_id', commentRes.body.data.id);
          Cypress.env('last_comment_post_id', postId);
        });
      });
    });

    it('should reply to a comment', () => {
      const postId = Cypress.env('last_comment_post_id');
      const commentId = Cypress.env('last_comment_id');
      if (postId && commentId) {
        cy.apiClient({
          method: 'POST',
          url: `/api/posts/${postId}/comments/${commentId}/replies`,
          body: { body: 'This is a reply.' },
        }).then((res) => {
          expect(res.status).to.eq(201);
        });
      }
    });

    it('should NOT delete own comment (business rule: only mod/admin)', () => {
      const postId = Cypress.env('last_comment_post_id');
      const commentId = Cypress.env('last_comment_id');
      if (postId && commentId) {
        cy.apiClient({
          method: 'DELETE',
          url: `/api/posts/${postId}/comments/${commentId}`,
        }).then((res) => {
          // Rule says only mod/admin can delete
          expect(res.status).to.eq(403);
        });
      }
    });
  });

  describe('Moderator Actions', () => {
    beforeEach(() => {
      cy.login(Cypress.env('mod_email'), Cypress.env('password'));
    });

    it('should delete any comment', () => {
      const postId = Cypress.env('last_comment_post_id');
      const commentId = Cypress.env('last_comment_id');
      if (postId && commentId) {
        cy.apiClient({
          method: 'DELETE',
          url: `/api/posts/${postId}/comments/${commentId}`,
        }).then((res) => {
          expect(res.status).to.be.oneOf([200, 204, 404]);
        });
      }
    });
  });
});
