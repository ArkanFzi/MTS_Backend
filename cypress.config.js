import { defineConfig } from "cypress";

export default defineConfig({
  e2e: {
    baseUrl: "http://127.0.0.1:8000",
    supportFile: "cypress/support/e2e.js",
    setupNodeEvents(on, config) {},
    // Tambah ini
    experimentalSessionAndOrigin: false,
    env: {
      admin_email: "admin@email.com",
      mod_email: "moderator@email.com",
      user_email: "user@email.com",
      password: "password123",
    },
  },
});