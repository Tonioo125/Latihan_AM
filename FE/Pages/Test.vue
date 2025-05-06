<template>
  <div>
    <p v-if="data">{{ data.message }}</p>
    <p v-else>Loading...</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      data: null
    }
  },
  async mounted() {
    try {
      this.data = await this.$axios.$get('/ping') // No baseURL needed here
    } catch (e) {
      console.error('API fetch error:', e)
    }
  },
  Middleware: 'auth'
}
</script>