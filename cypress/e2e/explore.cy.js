describe('Explore API (Public)', () => {
  it('should search for posts', () => {
    cy.apiClient({ method: 'GET', url: '/api/explore/search', qs: { q: 'test' } }).then((res) => {
      expect(res.status).to.eq(200);
      expect(res.body).to.have.property('data');
    });
  });

  it('should list tags', () => {
    cy.apiClient({ method: 'GET', url: '/api/explore/tags' }).then((res) => {
      expect(res.status).to.eq(200);
    });
  });

  it('should get trending posts', () => {
    cy.apiClient({ method: 'GET', url: '/api/explore/trending' }).then((res) => {
      expect(res.status).to.eq(200);
    });
  });

  it('should get leaderboard', () => {
    cy.apiClient({ method: 'GET', url: '/api/explore/leaderboard' }).then((res) => {
      expect(res.status).to.eq(200);
    });
  });
});
