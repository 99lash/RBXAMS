/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/Views/**/*.php",
    "./src/Views/Pages/*.php",
    "./src/Views/Partials/*.php",
    "./public_html/**/*.php",
    "./public_html/index.php",
  ],
  theme: {
    extend: {
      screens: {
        'xs': '425px', // Custom breakpoint for extra small screens
      }
    },
  },
  plugins: [
    require("daisyui")
  ],
}