import { defineConfig } from "cypress";

export default defineConfig({
  projectId: "tnkebh",

  e2e: {
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },

  component: {
    devServer: {
      framework: "vue",
      bundler: "webpack",
    },
  },
});
