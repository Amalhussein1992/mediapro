/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#3B82F6',
        secondary: '#8B5CF6',
        dark: '#0F172A',
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        arabic: ['Noto Kufi Arabic', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
