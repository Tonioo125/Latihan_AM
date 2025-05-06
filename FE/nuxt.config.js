export default {
  head: {
    title: 'Authentication Web App',
  },
  ssr: true,
  router: {
    extendRoutes(routes, resolve) {
      routes.push({
        path: '/',
        redirect: '/login',
      });
    }
  },
  modules: [
    '@nuxtjs/axios',
    '@nuxtjs/tailwindcss',
    '@nuxtjs/auth-next',
  ],

  axios: {
    baseURL: 'http://localhost:8000', // Change to your Laravel backend URL
    credentials: true, // Ensure credentials (cookies) are sent with requests
  },

  buildModules: ['@nuxtjs/tailwindcss'],

  auth: {
    strategies: {
      laravelSanctum: {
        provider: 'laravel/sanctum',
        url: 'http://localhost:8000',
        endpoints: {
          login: {
            url: '/login',
            method: 'post',
          },
          logout: {
            url: '/logout',
            method: 'post',
          },
          user: {
            url: '/api/user',
            method: 'get',
          }
        }
      }
    },
    redirect: {
      login: '/login', // Halaman login
      dashboard: '/admin/dashboard', // Halaman admin dashboard
      user_detail: '/user/detail', // Halaman user detail
    }
  }
}
