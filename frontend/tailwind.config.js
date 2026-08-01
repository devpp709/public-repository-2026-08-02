/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./pages/**/*.{js,ts,jsx,tsx}",
    "./components/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        'aspect-active': '#fbbf24',
      },
    },
    screens: {
      md: '768px',
    }
  },
  plugins: [],
};