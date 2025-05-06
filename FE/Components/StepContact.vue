<template>
    <div>
        <h2 class="text-xl font-semibold text-darkblue mb-2">Contact</h2>
        <h2 class="text-md text-gray-600 mb-4">Masukkan Informasi</h2>
        <form @submit.prevent="handleSubmit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8 mb-5">
                <div v-for="(field, index) in fields" :key="index">
                    <label :for="field.model" class="block text-darkblue mb-3 font-semibold">
                        {{ field.label }}
                    </label>
                    <div class="relative" v-if="field.model === 'univ'">
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <span v-html="getIcon(field.icon)" class="w-5 h-5 text-neutral"></span>
                        </span>
                        <select v-model="form[field.model]" @change="validateField(field.model)"
                            class="w-full pl-5 pr-5 py-2 border-2 rounded-full shadow-md border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option disabled value="">University name</option>
                            <option v-for="univ in universities" :key="univ.id" :value="univ.id">
                                {{ univ.name }}
                            </option>
                        </select>
                    </div>
                    <div class="relative" v-else>
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <span v-html="getIcon(field.icon)" class="w-5 h-5 text-neutral"></span>
                        </span>
                        <input :type="field.type" v-model="form[field.model]" :placeholder="field.placeholder"
                            @input="validateField(field.model)"
                            class="w-full pl-5 pr-5 py-2 border-2 rounded-full shadow-md border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>
                    <p v-if="errors[field.model]" class="text-red-500 text-sm mt-1">{{ errors[field.model] }}</p>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

axios.defaults.baseURL = 'http://localhost:8000';

export default {
    props: ['form'],
    data() {
        return {
            universities: [],
            fields: [
                { label: 'Name', model: 'name', placeholder: 'Jane Doe', type: 'text', icon: 'user' },
                { label: 'Email', model: 'email', placeholder: 'Your Email', type: 'email', icon: 'mail' },
                { label: 'Phone Number', model: 'phoneNum', placeholder: '0888 - 456 - 7890', type: 'text', icon: 'phone' },
                { label: 'University', model: 'univ', placeholder: '', type: 'select', icon: 'uni' },
                { label: 'Password', model: 'password', placeholder: 'Password', type: 'password' },
                { label: 'Password Confirmation', model: 'password_confirmation', placeholder: 'Confirm Password', type: 'password' }
            ],
            errors: {}
        };
    },
    mounted() {
        this.fetchUniversities();
    },
    methods: {
        validateField(field) {
            if (field === 'password' && (!this.form.password || this.form.password.length < 6)) {
                this.errors.password = 'Password must be at least 6 characters.';
            } else if (field === 'password_confirmation' && this.form.password_confirmation !== this.form.password) {
                this.errors.password_confirmation = 'Password confirmation must match the password.';
            } else if (field === 'phoneNum' && (!this.form.phoneNum || this.form.phoneNum.length < 10)) {
                this.errors.phoneNum = 'Phone number must be at least 10 digits.';
            } else if (field === 'email' && (!this.form.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email))) {
                this.errors.email = 'Email must be valid and include a proper domain.';
            } else {
                delete this.errors[field];
            }
        },
        validateForm() {
            this.errors = {};
            this.fields.forEach(field => this.validateField(field.model));
            return Object.keys(this.errors).length === 0;
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
        fetchUniversities() {
            axios.get('/api/universities')
                .then(response => {
                    this.universities = response.data;
                })
                .catch(error => {
                    console.error('Failed to load universities:', error);
                });
        }
    },
};
</script>