<template>
    <div class="container mx-auto p-8">
        <h1 class="text-2xl font-bold mb-4">Manage File Categories</h1>

        <!-- Add New File Category Form -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold">Add New File Category</h2>
            <form @submit.prevent="addCategory">
                <div class="mb-4">
                    <label for="name" class="block text-darkblue font-semibold">Category Name</label>
                    <input v-model="newCategory.name" type="text" id="name" class="border-2 rounded-lg w-full py-2 px-4"
                        required placeholder="Enter category name" />
                </div>

                <div class="mb-4">
                    <label for="icon" class="block text-darkblue font-semibold">Category Icon</label>
                    <input ref="fileInput" type="file" @change="handleIconUpload" id="icon"
                        class="border-2 rounded-lg w-full py-2 px-4" accept="image/*" required />
                </div>
                <button v-if="!editingCategoryId" type="button" @click="$router.push('/admin/dashboard')"
                    class="bg-red-600 text-white px-6 py-2 rounded-full">Back to Dashboard</button>
                <button v-if="!editingCategoryId" type="submit"
                    class="bg-indigo-600 text-white px-6 py-2 rounded-full">Add Category</button>
                <button v-if="editingCategoryId" type="submit"
                    class="bg-indigo-600 text-white px-6 py-2 rounded-full">Confirm Changes</button>

                <button v-if="editingCategoryId" type="button" @click="cancelEdit"
                    class="ml-2 bg-gray-300 text-black px-6 py-2 rounded-full">
                    Cancel
                </button>
            </form>
        </div>

        <!-- File Categories List -->
        <h2 class="text-xl font-semibold mb-4">Existing Categories</h2>
        <div v-if="categories.length > 0" class="space-y-4">
            <div v-for="category in categories" :key="category.id"
                class="flex items-center justify-between bg-white p-4 rounded-lg shadow-md">
                <div class="flex items-center">
                    <div class="w-10 h-10 mr-4 rounded-full bg-indigo-200 p-1 flex 
                        items-center justify-center overflow-hidden">
                        <img v-if="category.image_url" :src="category.image_url" alt="Category Icon"
                            class="object-contain max-w-full max-h-full" />
                    </div>

                    <span>{{ category.name }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="editCategory(category)" class="text-yellow-600 hover:text-yellow-800 mr-2">
                        Edit
                    </button>

                    <button @click="removeCategory(category.id)" class="bg-red-600 text-white px-4 py-2 rounded-full">
                        Remove
                    </button>
                </div>

            </div>
        </div>
        <p v-else>Loading file categories.</p>
    </div>
</template>

<script>
import axios from "axios";

axios.defaults.baseURL = 'http://localhost:8000';

export default {
    data() {
        return {
            newCategory: {
                name: "",
                icon: null,
            },
            categories: [],
            editingCategoryId: null,
        };
    },
    async mounted() {
        try {
            await this.$axios.get('http://localhost:8000/sanctum/csrf-cookie', {
                withCredentials: true
            }); // Important for CSRF protection
            await this.fetchCategories(); // Then fetch the data
        } catch (error) {
            if (error.response && (error.response.status === 401 || error.response.status === 403)) {
                // Redirect to login page or show message
                this.$router.push("/login");
            } else {
                console.error("Error:", error);
            }
        }

    },
    methods: {
        cancelEdit() {
            this.newCategory.name = "";
            this.newCategory.icon = null;
            this.editingCategoryId = null;
            this.$refs.fileInput.value = "";
        },
        editCategory(category) {
            this.newCategory.name = category.name;
            this.editingCategoryId = category.id;
            this.$refs.fileInput.value = ""; // Clear the file input
        },

        async fetchCategories() {
            try {
                const token = localStorage.getItem('auth_token');
                const response = await this.$axios.get("/api/file-categories", {
                    headers: {
                        Authorization: `Bearer ${token}`
                    },
                    withCredentials: true,
                });
                this.categories = response.data;
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        },

        async addCategory() {
            const formData = new FormData();
            formData.append("name", this.newCategory.name);

            if (this.newCategory.icon) {
                formData.append("img_category_url", this.newCategory.icon);
            }

            if (this.editingCategoryId) {
                await this.$axios.post(`/api/file-categories/${this.editingCategoryId}?_method=PUT`, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                        Authorization: `Bearer ${token}`,
                    },
                    withCredentials: true,
                });
            }

            const token = localStorage.getItem('auth_token');

            try {
                await this.$axios.get('/sanctum/csrf-cookie', { withCredentials: true });

                if (this.editingCategoryId) {
                    // Update category with optional icon change
                    await this.$axios.post(`/api/file-categories/${this.editingCategoryId}?_method=PUT`, formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                            Authorization: `Bearer ${token}`,
                        },
                        withCredentials: true,
                    });
                } else {
                    // Create new category
                    const exists = this.categories.some(
                        cat => cat.name.toLowerCase() === this.newCategory.name.trim().toLowerCase()
                    );
                    if (exists) {
                        alert("A category with this name already exists.");
                        return;
                    }

                    await this.$axios.post("/api/file-categories", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                            Authorization: `Bearer ${token}`,
                        },
                        withCredentials: true,
                    });
                }

                // Reset form
                this.newCategory.name = "";
                this.newCategory.icon = null;
                this.editingCategoryId = null;
                this.$refs.fileInput.value = "";
                await this.fetchCategories();
            } catch (error) {
                console.error("Error saving category:", error.response || error);
            }
        },

        handleIconUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.newCategory.icon = file;
            }
        },

        async removeCategory(categoryId) {
            try {
                // Get CSRF cookie
                await this.$axios.get('/sanctum/csrf-cookie', { withCredentials: true });

                const token = localStorage.getItem('auth_token');

                await axios.delete(`/api/file-categories/${categoryId}`, {
                    headers: {
                        Authorization: `Bearer ${token}`
                    },
                    withCredentials: true,
                });

                await this.fetchCategories();
            } catch (error) {
                console.error("Error removing category:", error);
            }
        }
    },
    Middleware: ['auth', 'admin']
};
</script>