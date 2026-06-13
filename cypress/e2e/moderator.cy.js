describe('Moderator', () => {

  // ===================== ACCESS CONTROL =====================
  describe('Access Control', () => {
    it('user biasa tidak bisa akses moderator routes', () => {
      cy.loginAsUser().then(() => {
        cy.apiClient({ method: 'GET', url: '/api/moderator/reports' }).then((res) => {
          expect(res.status).to.eq(403);
        });
      });
    });

    it('admin bisa akses moderator routes', () => {
      cy.loginAsAdmin().then(() => {
        cy.apiClient({ method: 'GET', url: '/api/moderator/reports' }).then((res) => {
          expect(res.status).to.eq(200);
        });
      });
    });
  });

  // ===================== CATEGORY CRUD =====================
  describe('Category', () => {
    let categoryId;

    beforeEach(() => cy.loginAsModerator());

    it('moderator dapat membuat kategori', () => {
      cy.apiClient({
        method: 'POST',
        url: '/api/moderator/categories',
        body: {
          name: `Category ${Date.now()}`,
          slug: `category-${Date.now()}`,
          description: 'Deskripsi kategori',
        },
      }).then((res) => {
        expect(res.status).to.eq(201);
        categoryId = res.body.data?.id;
      });
    });

    it('moderator dapat update kategori', () => {
      cy.apiClient({
        method: 'POST',
        url: '/api/moderator/categories',
        body: { name: `Cat ${Date.now()}`, slug: `cat-${Date.now()}` },
      }).then((res) => {
        const id = res.body.data?.id;
        const ts = Date.now();
        cy.apiClient({
          method: 'PUT',
          url: `/api/moderator/categories/${id}`,
          body: { name: `Updated Category ${ts}`, slug: `updated-category-${ts}` },
        }).then((updateRes) => {
          expect(updateRes.status).to.eq(200);
        });
      });
    });

    it('moderator dapat hapus kategori', () => {
      cy.apiClient({
        method: 'POST',
        url: '/api/moderator/categories',
        body: { name: `Cat Delete ${Date.now()}`, slug: `cat-delete-${Date.now()}` },
      }).then((res) => {
        const id = res.body.data?.id;
        cy.apiClient({ method: 'DELETE', url: `/api/moderator/categories/${id}` }).then((deleteRes) => {
          expect(deleteRes.status).to.eq(200);
        });
      });
    });
  });

  // ===================== TAG CRUD =====================
  describe('Tag', () => {
    beforeEach(() => cy.loginAsModerator());

    it('moderator dapat CRUD tag', () => {
      const tagName = `Tag ${Date.now()}`;

      cy.apiClient({
        method: 'POST',
        url: '/api/moderator/tags',
        body: { name: tagName, color: '#ffffff' },
      }).then((res) => {
        expect(res.status).to.eq(201);
        const tagId = res.body.data?.id;

        cy.apiClient({ method: 'GET', url: '/api/explore/tags' }).then((readRes) => {
          expect(readRes.status).to.eq(200);
        });

        cy.apiClient({
          method: 'PUT',
          url: `/api/moderator/tags/${tagId}`,
          body: { name: `Tag Updated ${Date.now()}`, color: '#000000' },
        }).then((updateRes) => {
          expect(updateRes.status).to.eq(200);
        });

        cy.apiClient({ method: 'DELETE', url: `/api/moderator/tags/${tagId}` }).then((deleteRes) => {
          expect(deleteRes.status).to.eq(200);
        });
      });
    });
  });

  // ===================== BADGE CRUD =====================
  describe('Badge', () => {
    beforeEach(() => cy.loginAsModerator());

    it('moderator dapat CRUD badge', () => {
      cy.apiClient({
        method: 'POST',
        url: '/api/moderator/badges',
        body: {
          name: `Badge ${Date.now()}`,
          description: 'Test badge',
          icon_url: 'https://example.com/icon.png',
          tier: 'bronze',
          condition_type: 'reputation_points',
          condition_value: 50,
        },
      }).then((res) => {
        expect(res.status).to.eq(201);
        const badgeId = res.body.data?.id;

        cy.apiClient({ method: 'GET', url: '/api/moderator/badges' }).then((readRes) => {
          expect(readRes.status).to.eq(200);
        });

        cy.apiClient({
          method: 'PUT',
          url: `/api/moderator/badges/${badgeId}`,
          body: {
            name: `Badge Updated ${Date.now()}`,
            description: 'Updated',
            icon_url: 'https://example.com/icon2.png',
            tier: 'silver',
            condition_type: 'post_count',
            condition_value: 10,
          },
        }).then((updateRes) => {
          expect(updateRes.status).to.eq(200);
        });

        cy.apiClient({ method: 'DELETE', url: `/api/moderator/badges/${badgeId}` }).then((deleteRes) => {
          expect(deleteRes.status).to.eq(200);
        });
      });
    });
  });

  // ===================== REPORT =====================
  describe('Report', () => {
    beforeEach(() => cy.loginAsModerator());

    it('moderator dapat melihat daftar report', () => {
      cy.apiClient({ method: 'GET', url: '/api/moderator/reports' }).then((res) => {
        expect(res.status).to.eq(200);
      });
    });

    it('moderator dapat melihat detail report', () => {
      cy.loginAsUser().then(() => {
        cy.apiClient({ method: 'GET', url: '/api/posts' }).then((postsRes) => {
          const postId = postsRes.body.data.data[0]?.id;

          cy.apiClient({
            method: 'POST',
            url: '/api/reports',
            body: { target_id: postId, target_type: 'post', reason: 'Spam test' },
          }).then((reportRes) => {
            const reportId = reportRes.body.data?.id;

            cy.loginAsModerator().then(() => {
              cy.apiClient({ method: 'GET', url: `/api/moderator/reports/${reportId}` }).then((res) => {
                expect(res.status).to.eq(200);
                expect(res.body.id).to.eq(reportId);
              });
            });
          });
        });
      });
    });
  });

  // ===================== BAN & WARN =====================
  describe('Ban & Warn', () => {
    beforeEach(() => cy.loginAsModerator());

    it('moderator dapat melihat daftar ban', () => {
      cy.apiClient({ method: 'GET', url: '/api/moderator/bans' }).then((res) => {
        expect(res.status).to.eq(200);
      });
    });

    it('moderator dapat warn user', () => {
      cy.loginAsUser().then(() => {
        cy.loginAsModerator().then(() => {
          cy.apiClient({ method: 'GET', url: '/api/admin/users' }).then((res) => {
            const targetId = res.body.data?.data?.[0]?.id;
            if (!targetId) return;

            cy.apiClient({
              method: 'POST',
              url: `/api/moderator/bans/${targetId}/warn`,
              body: { reason: 'Melanggar aturan komunitas' },
            }).then((warnRes) => {
              expect(warnRes.status).to.eq(200);
            });
          });
        });
      });
    });
  });

  // ===================== LOGS & HISTORY =====================
  describe('Logs & History', () => {
    beforeEach(() => cy.loginAsModerator());

    it('moderator dapat melihat moderation logs', () => {
      cy.apiClient({ method: 'GET', url: '/api/moderator/logs' }).then((res) => {
        expect(res.status).to.eq(200);
      });
    });

    it('moderator dapat melihat history post', () => {
      cy.apiClient({ method: 'GET', url: '/api/posts' }).then((res) => {
        const postId = res.body.data.data[0]?.id;
        if (!postId) return;

        cy.apiClient({ method: 'GET', url: `/api/moderator/posts/${postId}/history` }).then((histRes) => {
          expect(histRes.status).to.eq(200);
        });
      });
    });
  });
});