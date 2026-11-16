/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
    "./frontend/index.html",
    "./frontend/src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        ethiopia: {
          green: '#009b48',
          yellow: '#fed100',
          red: '#e4002b',
          blue: '#002f6c',
        },
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          500: '#1e40af',
          600: '#1d4ed8',
          700: '#1e3a8a',
          800: '#172554',
        },
      },
    },
  },
  plugins: [],
}
