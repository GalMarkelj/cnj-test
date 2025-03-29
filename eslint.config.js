import js from '@eslint/js'
import prettier from 'eslint-config-prettier'
import react from 'eslint-plugin-react'
import reactHooks from 'eslint-plugin-react-hooks'
import globals from 'globals'
import typescript from 'typescript-eslint'

/** @type {import('eslint').Linter.Config[]} */
export default [
    js.configs.recommended,
    ...typescript.configs.recommended,
    {
        ...react.configs.flat.recommended,
        ...react.configs.flat['jsx-runtime'], // Required for React 17+
        languageOptions: {
            globals: {
                ...globals.browser,
            },
        },
        rules: {
            //suppress errors for missing 'import React' in files
            'react/react-in-jsx-scope': 'off',
            '@typescript-eslint/no-require-imports': 'off',
            semi: ['warn', 'never'],
            camelcase: 'off',
            'max-len': [
                'warn',
                {
                    code: 100,
                    ignoreStrings: true,
                    ignoreComments: true,
                    ignoreTemplateLiterals: true,
                },
            ],
            'comma-dangle': [
                'warn',
                {
                    arrays: 'always-multiline',
                    objects: 'always-multiline',
                    imports: 'always-multiline',
                    exports: 'always-multiline',
                    functions: 'ignore',
                },
            ],
            quotes: ['warn', 'single', { avoidEscape: true }],
            'jsx-quotes': ['warn', 'prefer-single'],
            'react/display-name': 'off',
            'react/prop-types': 'off',
            'react/no-unescaped-entities': 'off',
        },
        settings: {
            react: {
                version: 'detect',
            },
        },
    },
    {
        plugins: {
            'react-hooks': reactHooks,
        },
        rules: {
            'react-hooks/rules-of-hooks': 'error',
            'react-hooks/exhaustive-deps': 'warn',
        },
    },
    {
        ignores: [
            'vendor',
            'node_modules',
            'public',
            'bootstrap/ssr',
            'tailwind.config.js',
            'resources/js/ziggy.js',
        ],
    },
    prettier, // Turn off all rules that might conflict with Prettier
]
