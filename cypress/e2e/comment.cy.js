describe('Comment', () => {
  let postId;

  beforeEach(() => {
    cy.loginAsUser().then(() => {
      cy.apiClient({ method: 'GET', url: '/api/posts' }).then((res) => {
        expect(res.status).to.eq(200);
        const posts = res.body.data.data;
        // Ambil post milik user lain untuk hindari limit self-comment (maks 4)
        const otherPost = posts.find(p => p.user?.email !== Cypress.env('user_email'));
        postId = otherPost?.id ?? posts[0]?.id;
        expect(postId).to.exist;
      });
    });
  });

  it('user dapat komen pada post', () => {
    cy.apiClient({
      method: 'POST',
      url: `/api/posts/${postId}/comments`,
      body: { body: 'Ini komentar dari Cypress' },
    }).then((res) => {
      expect(res.status).to.eq(201);
    });
  });

  it('user dapat melihat komentar pada post', () => {
    cy.apiClient({
      method: 'GET',
      url: `/api/posts/${postId}/comments`,
    }).then((res) => {
      expect(res.status).to.eq(200);
    });
  });

  it('pemilik komentar dapat update komentar', () => {
    cy.apiClient({
      method: 'POST',
      url: `/api/posts/${postId}/comments`,
      body: { body: 'Komentar lama' },
    }).then((res) => {
      expect(res.status).to.eq(201);
      const cId = res.body.data?.id;
      cy.apiClient({
        method: 'PUT',
        url: `/api/posts/${postId}/comments/${cId}`,
        body: { body: 'Komentar yang sudah diupdate' },
      }).then((updateRes) => {
        expect(updateRes.status).to.eq(200);
      });
    });
  });

  it('user biasa tidak bisa hapus komentar via moderator route', () => {
    cy.apiClient({
      method: 'POST',
      url: `/api/posts/${postId}/comments`,
      body: { body: 'Komentar yang mau dihapus' },
    }).then((res) => {
      const cId = res.body.data?.id;
      cy.apiClient({
        method: 'DELETE',
        url: `/api/moderator/posts/${postId}/comments/${cId}`,
      }).then((deleteRes) => {
        expect(deleteRes.status).to.eq(403);
      });
    });
  });

  it('moderator dapat hapus komentar', () => {
    cy.apiClient({
      method: 'POST',
      url: `/api/posts/${postId}/comments`,
      body: { body: 'Komentar yang akan dihapus mod' },
    }).then((res) => {
      const cId = res.body.data?.id;
      cy.loginAsModerator().then(() => {
        cy.apiClient({
          method: 'DELETE',
          url: `/api/moderator/posts/${postId}/comments/${cId}`,
        }).then((deleteRes) => {
          expect(deleteRes.status).to.eq(200);
        });
      });
    });
  });

  it('user dapat reply komentar', () => {
    cy.apiClient({
      method: 'POST',
      url: `/api/posts/${postId}/comments`,
      body: { body: 'Komentar induk' },
    }).then((res) => {
      const cId = res.body.data?.id;
      cy.apiClient({
        method: 'POST',
        url: `/api/posts/${postId}/comments/${cId}/replies`,
        body: { body: 'Ini balasan komentar' },
      }).then((replyRes) => {
        expect(replyRes.status).to.eq(201);
        expect(replyRes.body.message).to.eq('Balasan berhasil ditambahkan');
      });
    });
  });

  it('pemilik reply dapat update reply', () => {
    cy.apiClient({
      method: 'POST',
      url: `/api/posts/${postId}/comments`,
      body: { body: 'Komentar induk untuk reply update' },
    }).then((res) => {
      expect(res.status).to.eq(201);
      const cId = res.body.data?.id;
      expect(cId, 'Comment ID harus ada').to.exist;

      cy.apiClient({
        method: 'POST',
        url: `/api/posts/${postId}/comments/${cId}/replies`,
        body: { body: 'Reply lama' },
      }).then((replyRes) => {
        expect(replyRes.status).to.eq(201);
        const rId = replyRes.body.data?.id;
        expect(rId, 'Reply ID harus ada').to.exist;

        cy.apiClient({
          method: 'PUT',
          url: `/api/posts/${postId}/comments/${cId}/replies/${rId}`,
          body: { body: 'Reply yang sudah diupdate' },
        }).then((updateRes) => {
          expect(updateRes.status).to.eq(200);
          expect(updateRes.body.message).to.eq('Balasan berhasil diedit');
        });
      });
    });
  });
});