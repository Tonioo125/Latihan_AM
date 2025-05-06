export default async function ({ $axios, redirect }) {
    try {
      await $axios.get('/api/user')
      // If user is logged in, redirect away from login/register
      return redirect('/dashboard')
    } catch (error) {
      // Let guests through
    }
  }
  