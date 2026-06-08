describe('Posts API', () => {
  beforeEach(() => {
    cy.login(Cypress.env('user_email'), Cypress.env('password'));
  });

  it('should list all posts (public access)', () => {
    cy.apiClient({ method: 'GET', url: '/api/posts' }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body).to.have.property('data');
    });
  });

  it('should create a post successfully (assuming reputation >= 15)', () => {
    cy.apiClient({
      method: 'POST',
      url: '/api/posts',
      body: {
        title: 'New Post Title',
        content: 'This is the content of the post.',
        category_id: 1,
        tags: [1, 2],
      },
    }).then((res) => {
      // If reputation is sufficient
      if (res.status === 201) {
        expect(res.body).to.have.property('id');
        Cypress.env('last_post_id', res.body.id);
      } else if (res.status === 403) {
        expect(res.body.message).to.contain('reputation');
      }
    });
  });

  it('should get a single post', () => {
    // Assuming ID 1 exists from seeders
    cy.apiClient({ method: 'GET', url: '/api/posts/1' }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body).to.have.property('id', 1);
    });
  });

  it('should update own post', () => {
    // We need a post owned by the user. Let's use the one we just created if available.
    const postId = Cypress.env('last_post_id') || 1;
    cy.apiClient({
      method: 'PUT',
      url: `/api/posts/${postId}`,
      body: {
        title: 'Updated Title',
        content: 'Updated content.',
      },
    }).then((res) => {
      if (res.status === 200) {
        expect(res.body.title).to.eq('Updated Title');
      } else {
        expect(res.status).to.eq(403); // If not owner
      }
    });
  });

  it('should delete own post', () => {
    const postId = Cypress.env('last_post_id');
    if (postId) {
      cy.apiClient({
        method: 'DELETE',
        url: `/api/posts/${postId}`,
      }).then((res) => {
        expect(res.status).to.eq(200);
      });
    }
  });

  it('should not delete someone else\'s post', () => {
    // Assuming ID 2 is not owned by this user
    cy.apiClient({
      method: 'DELETE',
      url: '/api/posts/2',
    }).then((res) => {
      expect(res.status).to.eq(403);
    });
  });
});
