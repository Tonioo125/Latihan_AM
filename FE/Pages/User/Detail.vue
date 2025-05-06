<template>
    <div class="container mx-auto p-8">
        <div v-if="loading" class="flex justify-center items-center h-screen">
            <div class="loader"></div>
        </div>
        <div v-else class="flex flex-col lg:flex-row gap-8">
            <!-- Left Column: User Info -->
            <div class="bg-white shadow-md rounded-3xl w-full lg:w-2/3 p-6">
                <h2 class="text-xl text-center font-semibold mb-6">Member Profile</h2>
                <hr class="mb-4" />

                <div class="flex justify-center mb-6">
                    <img :src="getUserPhotoUrl(user)" class="w-32 h-32 rounded-full border-4 border-indigo-500"
                        alt="User Photo" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div v-for="(field, index) in fields" :key="index">
                        <label class="block text-darkblue font-semibold mb-1">{{ field.label }}</label>
                        <div class="flex items-center justify-between border-2 rounded-full py-2 px-5 shadow-md">
                            <span>{{ field.value }}</span>
                            <div v-html="getIcon(field.type)" class="w-5 h-5 text-indigo-500"></div>
                        </div>
                    </div>
                </div>
                <button @click="logout" class="bg-indigo-600 text-white px-6 py-2 rounded-full mt-8">
                    Log Out
                </button>
            </div>

            <!-- Right Column: Uploaded Files -->
            <div class="bg-white shadow-md rounded-3xl w-full lg:w-1/3 p-6">
                <h2 class="text-xl text-center font-semibold mb-4">Uploaded Files</h2>
                <hr class="mb-4" />
                <div v-if="user.files && user.files.length > 0">
                    <ul class="flex justify-center space-y-2">
                        <li v-for="(file, index) in user.files" :key="index">

                            <button @click="showFileDetails(file)"
                                class="flex items-center relative border-2 border-indigo-600 text-darkblue font-semibold px-32 h-20 max-w-100 rounded-xl">
                                <div
                                    class="absolute left-3 w-16 h-16 mr-4 rounded-full bg-indigo-200 p-1 flex items-center justify-center overflow-hidden">
                                    <img class="w-8 h-8" :src="file.file_category?.image_url" alt="Category Icon" />
                                </div>

                                {{ file.file_category?.name || 'File' }} {{ file.file_name }}
                            </button>
                        </li>
                    </ul>
                </div>
                <div v-else>
                    <p>No files uploaded yet.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from 'sweetalert2'

export default {
    data() {
        return {
            user: {},
            fields: [],
            defaultPhoto: '../../Components/Assets/default.svg',
            loading: true, // Add loading state
        }
    },
    async mounted() {
        try {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                this.$router.push({ name: 'login' });
                return;
            }

            const response = await this.$axios.get('/api/user', {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('auth_token')}`
                }
            });

            this.user = response.data.user;
            this.user.files = this.user.files || [];
            this.fields = [
                {
                    label: 'Name',
                    value: this.user.username || 'N/A',
                    type: 'user'
                },
                {
                    label: 'Email',
                    value: this.user.email || 'N/A',
                    type: 'mail'
                },
                {
                    label: 'Phone Number',
                    value: this.user.phone || 'N/A',
                    type: 'phone'
                },
                {
                    label: 'University',
                    value: this.user.university?.name || 'N/A',
                    type: 'uni'
                },
            ];
        } catch (error) {
            console.error('Failed to fetch user data:', error);
            alert('Failed to load user data.');
        } finally {
            this.loading = false; // Set loading to false after data is fetched
        }
    },
    methods: {
        goBack() {
            this.$router.push({ name: 'login' });
        },
        getUserPhotoUrl(user) {
            if (user.photo_file?.file_url) {
                return `${this.$axios.defaults.baseURL}/storage/${user.photo_file.file_url}`;
            }
            return this.defaultPhoto;
        },
        getIcon(type) {
            const icons = {
                mail: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
          </svg>`,
                user: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
          </svg>`,
                phone: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
          </svg>`,
                uni: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
          </svg>`
            };
            return icons[type] || '';
        },
        showFileDetails(file) {
            const downloadUrl = `${this.$axios.defaults.baseURL}/api/download/${file.file_url}`;

            Swal.fire({
                title: 'File Details',
                html: `
            <p><strong>Category:</strong> ${file.file_category?.name || 'N/A'}</p>
            <p>
                <a href="${downloadUrl}" 
                   target="_blank"
                   style="display: inline-block; background-color: #4F46E5; color: white; padding: 0.5rem 1rem; border-radius: 9999px; text-decoration: none;">
                    Download File
                </a>
            </p>
        `,
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-3xl p-6'
                }
            });
        },
        async logout() {
            this.$store.dispatch('logout');
            this.$router.push({ name: 'login' });
        }
    },
    Middleware: 'auth'
}
</script>

<style>
/* Add styles for the loader */
.loader {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #4F46E5;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>
