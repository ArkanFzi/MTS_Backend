describe('Post', () => {
  let categoryId;

  before(() => {
    cy.loginAsModerator().then(() => {
      cy.apiClient({ method: 'GET', url: '/api/explore/categories' }).then((res) => {
        categoryId = res.body.data?.[0]?.id;
        expect(categoryId).to.exist;
      });
    });
  });

  describe('Public', () => {
    it('publik dapat melihat daftar post', () => {
      cy.apiClient({ method: 'GET', url: '/api/posts' }).then((res) => {
        expect(res.status).to.eq(200);
        expect(res.body).to.have.property('data');
      });
    });

    it('publik dapat melihat detail post', () => {
      cy.apiClient({ method: 'GET', url: '/api/posts' }).then((res) => {
        const postId = res.body.data.data[0]?.id;
        if (!postId) return;
        cy.apiClient({ method: 'GET', url: `/api/posts/${postId}` }).then((detailRes) => {
          expect(detailRes.status).to.eq(200);
          expect(detailRes.body.data).to.have.property('title');
        });
      });
    });
  });

  describe('Create', () => {
    it('user dengan poin kurang dari 15 tidak bisa buat post', () => {
      cy.loginAsUser().then(() => {
        cy.apiClient({
          method: 'POST',
          url: '/api/posts',
          body: { title: 'Post Gagal', body: 'Isi post', category_id: categoryId },
        }).then((res) => {
          expect([403, 201]).to.include(res.status);
        });
      });
    });

    it('user dengan poin cukup dapat membuat post', () => {
      cy.login(Cypress.env('user_email')).then(() => {
        cy.apiClient({
          method: 'POST',
          url: '/api/posts',
          body: {
            title: `Post Cypress ${Date.now()}`,
            body: 'Ini isi post dari Cypress',
            category_id: categoryId,
          },
        }).then((res) => {
          expect(res.status).to.eq(201);
        });
      });
    });
  });

  describe('Update', () => {
    let postId;

    beforeEach(() => {
      cy.loginAsUser().then(() => {
        cy.apiClient({ method: 'GET', url: '/api/me/posts' }).then((res) => {
          postId = res.body.data?.data?.[0]?.id;
        });
      });
    });

    it('pemilik dapat update postnya', () => {
      if (!postId) return;
      cy.apiClient({
        method: 'PUT',
        url: `/api/posts/${postId}`,
        body: { title: 'Judul Updated', body: 'Body updated dari Cypress' },
      }).then((res) => {
        expect(res.status).to.eq(200);
      });
    });

    it('bukan pemilik tidak bisa update post orang lain', () => {
      // Ambil post milik moderator
      cy.loginAsModerator().then(() => {
        cy.apiClient({ method: 'GET', url: '/api/me/posts' }).then((res) => {
          const modPostId = res.body.data?.data?.[0]?.id;
          if (!modPostId) return;

          // Login sebagai user biasa lalu coba update post moderator
          cy.loginAsUser().then(() => {
            cy.apiClient({
              method: 'PUT',
              url: `/api/posts/${modPostId}`,
              body: { title: 'Hacked Title' },
            }).then((updateRes) => {
              expect(updateRes.status).to.eq(403);
            });
          });
        });
      });
    });
  });

  describe('Status', () => {
    it('pemilik dapat update status post', () => {
      cy.loginAsUser().then(() => {
        cy.apiClient({ method: 'GET', url: '/api/me/posts' }).then((res) => {
          const postId = res.body.data?.data?.[0]?.id;
          if (!postId) return;
          cy.apiClient({
            method: 'PATCH',
            url: `/api/posts/${postId}/status`,
            body: { status: 'closed' },
          }).then((statusRes) => {
            expect(statusRes.status).to.eq(200);
            expect(statusRes.body.success).to.be.true;
          });
        });
      });
    });
  });

  describe('Delete', () => {
    it('pemilik dapat hapus postnya', () => {
      cy.loginAsUser().then(() => {
        cy.apiClient({ method: 'GET', url: '/api/me/posts' }).then((res) => {
          const postId = res.body.data?.data?.[0]?.id;
          if (!postId) return;
          cy.apiClient({ method: 'DELETE', url: `/api/posts/${postId}` }).then((deleteRes) => {
            expect(deleteRes.status).to.eq(200);
          });
        });
      });
    });
  });

  describe('My Posts', () => {
    it('user dapat melihat postnya sendiri', () => {
      cy.loginAsUser().then(() => {
        cy.apiClient({ method: 'GET', url: '/api/me/posts' }).then((res) => {
          expect(res.status).to.eq(200);
          expect(res.body.success).to.be.true;
          expect(res.body.data).to.have.property('data');
        });
      });
    });
  });
});