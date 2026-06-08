import { defineConfig } from "cypress";

export default defineConfig({
  e2e: {
    baseUrl: "http://localhost:8000",
    supportFile: "cypress/support/e2e.js",
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
    env: {
      admin_email: "admin@email.com",
      mod_email: "moderator@email.com",
      user_email: "user@email.com",
      password: "password123",
    },
  },
});
