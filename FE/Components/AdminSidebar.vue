<template>
    <div :class="['transition-all duration-300', sidebarOpen ? 'w-64' : 'w-16']"
        class="bg-darkblue text-white flex flex-col h-full">
        <button @click="toggleSidebar" class="p-4 focus:outline-none">
            <span v-if="sidebarOpen">☰</span>
            <span v-else>≡</span>
        </button>

        <nav v-if="sidebarOpen" class="p-4 space-y-4 flex-1 flex flex-col justify-between">
            <div class="flex flex-col space-y-4">
                <span class="bg-blue-500 text-center text-white px-4 py-2 rounded-3xl">
                    Dashboard
                </span>
                <button @click="goToFileCategories"
                    class="bg-blue-500 text-white px-4 py-2 rounded-3xl hover:bg-blue-600">
                    Go to File Categories
                </button>
            </div>
            <button @click="logout" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                Log Out
            </button>
        </nav>
    </div>
</template>

<script>
export default {
    props: ['sidebarOpen'],
    methods: {
        toggleSidebar() {
            this.$emit('toggle')
        },
        goToFileCategories() {
            this.$router.push('/admin/filecategories')
        },
        logout() {
            const token = localStorage.getItem('token') // or wherever you store it
            this.$store.dispatch('logout');

            this.$axios.post('/api/logout', {}, {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
                .then(response => {
                    localStorage.removeItem('token') // clean up
                    this.$router.push('/login') // redirect
                })
                .catch(error => {
                    console.error("Logout error:", error)
                })

        }

    }
}
</script>

<style scoped>
.bg-darkblue {
    background-color: #1e293b;
    /* Tailwind slate-800 */
}
</style>