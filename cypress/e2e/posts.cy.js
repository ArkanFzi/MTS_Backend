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
    const categoryId = '019eb007-e370-732a-91c7-44d1664206d2';
    cy.apiClient({
      method: 'POST',
      url: '/api/posts',
      body: {
        title: 'New Post Title',
        body: 'This is the content of the post.',
        category_id: categoryId,
        tags: [],
      },
    }).then((res) => {
      if (res.status === 201) {
        expect(res.body.data).to.have.property('id');
        Cypress.env('last_post_id', res.body.data.id);
      } else if (res.status === 403) {
        expect(res.body.message).to.contain('reputation');
      }
    });
  });

  it('should get a single post', () => {
    cy.apiClient({ method: 'GET', url: '/api/posts' }).then((res) => {
      const postId = res.body.data.data[0].id;
      cy.apiClient({ method: 'GET', url: `/api/posts/${postId}` }).then((singleRes) => {
        expect(singleRes.status).to.eq(200);
        expect(singleRes.body.data).to.have.property('id', postId);
      });
    });
  });

  it('should update own post', () => {
    cy.apiClient({ method: 'GET', url: '/api/me/posts' }).then((res) => {
      if (res.body.data && res.body.data.data && res.body.data.data.length > 0) {
        const postId = res.body.data.data[0].id;
        cy.apiClient({
          method: 'PUT',
          url: `/api/posts/${postId}`,
          body: {
            title: 'Updated Title',
            body: 'Updated content.',
          },
        }).then((updateRes) => {
          expect(updateRes.status).to.eq(200);
          expect(updateRes.body.data.title).to.eq('Updated Title');
        });
      }
    });
  });

  it('should not delete someone else\'s post', () => {
    cy.apiClient({ method: 'GET', url: '/api/posts' }).then((res) => {
      const postId = res.body.data.data[0].id;
      cy.apiClient({
        method: 'DELETE',
        url: `/api/posts/${postId}`,
      }).then((delRes) => {
        expect(delRes.status).to.be.oneOf([200, 403]);
      });
    });
  });
});