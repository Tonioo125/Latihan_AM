module.exports = {
  content: [
    './components/**/*.{vue,js}',
    './layouts/**/*.{vue,js}',
    './pages/**/*.{vue,js}',
    './plugins/**/*.{js,ts}',
    './nuxt.config.{js,ts}',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          light: '#a7f3d0',
          DEFAULT: '#10b981',
          dark: '#047857'
        },
        softblue: '#6ec1e4',
        mygray: '#d1d5db',
        darkblue: '#170F49',
        neutral: '#6F6C90',
        pluplu: '#4A3AFF'
      }
    }
  },
  plugins: [],
}
