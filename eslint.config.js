import js from "@eslint/js";
import pluginVue from "eslint-plugin-vue";
import configPrettier from "eslint-config-prettier";
import globals from "globals";
import unusedImports from "eslint-plugin-unused-imports";

export default [
  {
    ignores: [
      "**/dist/**",
      "**/vendor/**",
      "**/public/**",
      "**/bootstrap/cache/**",
      "**/storage/**",
      "**/*.min.js"
    ]
  },
  js.configs.recommended,
  ...pluginVue.configs["flat/recommended"],
  configPrettier,
  {
    files: ["**/*.{js,vue}"],
    languageOptions: {
      ecmaVersion: "latest",
      sourceType: "module",
      globals: {
        ...globals.browser,
        ...globals.node,
        route: "readonly",
      }
    },
    plugins: {
      "unused-imports": unusedImports,
    },
    rules: {
      // General Javascript rules
      "no-unused-vars": "off", // Turned off in favor of unused-imports
      "unused-imports/no-unused-imports": "error",
      "unused-imports/no-unused-vars": [
        "warn",
        { "vars": "all", "varsIgnorePattern": "^_", "args": "after-used", "argsIgnorePattern": "^_", "caughtErrors": "none" }
      ],
      "no-console": ["warn", { allow: ["warn", "error"] }],
      
      // Vue 3 Composition API rules
      "vue/multi-word-component-names": "off",
      "vue/require-default-prop": "off",
      "vue/component-api-style": ["error", ["script-setup", "composition"]],
      "vue/no-unused-vars": "warn",
      "vue/component-name-in-template-casing": ["error", "PascalCase", {
        "registeredComponentsOnly": false,
        "ignores": ["router-link", "router-view", "transition", "transition-group", "keep-alive", "component", "slot", "teleport"]
      }]
    }
  }
];
