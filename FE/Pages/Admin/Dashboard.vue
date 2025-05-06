<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <AdminSidebar :sidebarOpen="sidebarOpen" @toggle="toggleSidebar" />

        <!-- Main content -->
        <div class="flex-1 p-8 overflow-auto">
            <h1 class="text-2xl font-bold mb-4">Pending User Dashboard</h1>

            <div class="flex gap-4 mb-6">
                <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded">
                    Pending: {{ pendingCount }}
                </div>
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">
                    Approved: {{ approvedCount }}
                </div>
            </div>

            <table class="min-w-full bg-white shadow rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="px-4 py-2">#</th>
                        <th class="px-4 py-2">Photo</th>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Actions</th>
                        <th class="px-4 py-2">User Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(user, index) in pendingUsers" :key="user.id" class="border-b">
                        <td class="px-4 py-2">{{ index + 1 }}</td>
                        <td class="px-4 py-2">
                            <img :src="getUserPhotoUrl(user) || defaultPhoto"
                                class="w-10 h-10 rounded-full object-cover" />
                        </td>

                        <td class="px-4 py-2">{{ user.username }}</td>
                        <td class="px-4 py-2">
                            <span class="text-yellow-600">
                                {{ user.approval }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <button @click="approveUser(user)"
                                class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700">
                                Approve
                            </button>
                        </td>
                        <td class="px-4 py-2">
                            <NuxtLink :to="`/Admin/UserDetail/${user.id}`">View User</NuxtLink>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import AdminSidebar from '../../Components/AdminSidebar.vue'
import defaultPhoto from '../../Components/Assets/default.svg' // Adjust the path as necessary

export default {
    components: { AdminSidebar },
    data() {
        return {
            sidebarOpen: true,
            users: [],
            defaultPhoto: defaultPhoto, // Default photo path
        }
    },
    computed: {
        pendingUsers() {
            return this.users.filter(user => user.approval === 'Pending')
        },
        approvedUsers() {
            return this.users.filter(user => user.approval === 'Approved')
        },
        pendingCount() {
            return this.pendingUsers.length
        },
        approvedCount() {
            return this.approvedUsers.length
        }
    },
    methods: {
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen
        },
        approveUser(user) {
            this.$axios
                .post(`/api/admin/users/${user.id}/approve`, {}, { withCredentials: true })
                .then(() => {
                    user.approval = 'Approved'
                })
                .catch(error => {
                    console.error('Approval failed:', error)
                })
        },
        getUserPhotoUrl(user) {
            if (user.photo_file?.file_url) {
                return `${this.$axios.defaults.baseURL}/storage/${user.photo_file.file_url}`
            }
            console.error('User photo not found:', user.photo_file)
            // Return default photo if user photo is not found
            return this.defaultPhoto
        }
    },
    async created() {
        try {
            const response = await this.$axios.get('/api/admin/users/all', { withCredentials: true })
            this.users = response.data
        } catch (error) {
            console.error('Failed to fetch users:', error)
        }
    },
    Middleware: ['auth', 'admin']
}
</script>
