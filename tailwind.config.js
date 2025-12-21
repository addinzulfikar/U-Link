/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    "./storage/framework/views/*.php",
  ],
  theme: {
    extend: {
      colors: {
        // Xero-style Calm UI colors
        primary: {
          DEFAULT: '#1F73B7',
          light: '#4A90D9',
          dark: '#155A94',
        },
        text: {
          primary: '#111827',
          secondary: '#6B7280',
          tertiary: '#9CA3AF',
        },
        background: {
          DEFAULT: '#F9FAFB',
          paper: '#FFFFFF',
          subtle: '#F3F4F6',
        },
        border: {
          DEFAULT: '#E5E7EB',
          light: '#F3F4F6',
        },
        // Muted semantic colors (not bright!)
        success: {
          DEFAULT: '#059669',
          light: '#D1FAE5',
          text: '#065F46',
        },
        warning: {
          DEFAULT: '#D97706',
          light: '#FEF3C7',
          text: '#92400E',
        },
        danger: {
          DEFAULT: '#DC2626',
          light: '#FEE2E2',
          text: '#991B1B',
        },
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      fontSize: {
        // Xero typography scale
        'display': ['3rem', { lineHeight: '1.2', fontWeight: '600' }],
        'h1': ['2rem', { lineHeight: '1.3', fontWeight: '600' }],
        'h2': ['1.5rem', { lineHeight: '1.4', fontWeight: '600' }],
        'h3': ['1.25rem', { lineHeight: '1.5', fontWeight: '600' }],
        'body-lg': ['1.125rem', { lineHeight: '1.6', fontWeight: '400' }],
        'body': ['1rem', { lineHeight: '1.6', fontWeight: '400' }],
        'body-sm': ['0.875rem', { lineHeight: '1.5', fontWeight: '400' }],
        'caption': ['0.75rem', { lineHeight: '1.4', fontWeight: '400' }],
      },
      borderRadius: {
        'xero': '8px',
      },
      boxShadow: {
        'xero-sm': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
        'xero': '0 1px 3px 0 rgba(0, 0, 0, 0.1)',
      },
    },
  },
  plugins: [],
}
