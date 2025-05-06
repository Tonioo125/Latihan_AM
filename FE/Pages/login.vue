<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="flex w-full max-w-6xl rounded-lg gap-x-3">

      <!-- Image Section -->
      <div class="w-2/3 h-full">
        <img src="../Components/Assets/login_Page.jpg" alt="Login Illustration"
          class="w-full h-full object-cover rounded-lg" />
      </div>

      <!-- Login Form Section -->
      <div class="w-1/3 p-8 flex flex-col bg-white rounded-lg justify-center shadow-md">
        <h2 class="text-4xl font-bold text-center mb-6 text-darkblue">Login</h2>
        <hr class="mb-6" />

        <form @submit.prevent="login">
          <div class="mb-4">
            <label for="email" class="block text-darkblue mb-1 font-semibold">Email</label>
            <input v-model="email" type="email" id="email"
              class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Email address" />
          </div>

          <div class="mb-6">
            <label for="password" class="block text-darkblue mb-1 font-semibold">Password</label>
            <input v-model="password" type="password" id="password"
              class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Password" />
          </div>

          <div class="flex items-center gap-4 mb-6">
            <button @click="toRegis" type="button"
              class="w-full py-2 bg-purple-600 text-white rounded-full hover:bg-gray-300">
              Register
            </button>

            <div class="font-bold text-darkblue">OR</div>

            <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded-full hover:bg-indigo-700">
              Login
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</template>

<script>
import Swal from 'sweetalert2'

export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
    }
  },
  methods: {
    async login() {
      // Show loading screen
      const loading = Swal.fire({
        title: 'Loading...',
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      try {
        // Get CSRF cookie (required for Sanctum)
        await this.$axios.get('/sanctum/csrf-cookie');

        // Attempt login
        const response = await this.$axios.post('/api/login', {
          email: this.email,
          password: this.password,
        });

        const { token, user } = response.data;

        // Save token and user in Vuex and localStorage
        await this.$store.dispatch('login', { user, token });

        // Redirect based on role using response data directly
        if (user.role === 'Admin') {
          this.$router.push('/admin/dashboard');
        } else {
          this.$router.push('/user/detail');
        }
      } catch (error) {
        console.error('Login error:', error);
        Swal.close(); // Close loading before showing error Swal
        Swal.fire({
          icon: 'error',
          title: 'Login Failed',
          text: error.response?.data?.message || 'An error occurred during login.',
          confirmButtonText: 'OK',
        });
        return; // Ensure no further execution
      }
      // Close the loading screen if no error occurs
      Swal.close();
    },
    toRegis() {
      this.$router.push('/register')
    }
  }
}
</script>


<style scoped>
.loader {
  border-top-color: #3498db;
  animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
