<template>
    <div>
        <h2 class="text-xl font-semibold text-darkblue mb-1">File</h2>
        <h2 class="text-md text-gray-600 mb-4">Upload Berkas yang Diperlukan</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8 mb-5">
            <div v-for="(field, index) in fields" :key="index" class="relative">
                <button type="button" @click="openFileModal(field.model)"
                    class="w-full max-w-xl px-20 py-8 border-2 rounded-2xl transition-colors duration-200" :class="{
                        'border-indigo-700': !uploadedFiles[field.model],
                        'border-green-500 bg-green-50': uploadedFiles[field.model]
                    }">
                    <div class="flex flex-col items-center">
                        <span>{{ field.model }}</span>
                        <span v-if="uploadedFiles[field.model]" class="text-sm text-gray-600 mt-2">
                            {{ uploadedFiles[field.model].name }} ({{ formatFileSize(uploadedFiles[field.model].size)
                            }})
                        </span>
                    </div>
                </button>

                <!-- Remove File -->
                <button v-if="uploadedFiles[field.model]" @click="confirmRemoveFile(field.model)" type="button"
                    class="absolute top-2 right-2 text-red-500 hover:text-red-700 font-bold text-sm"
                    title="Remove file">
                    ✕
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from 'sweetalert2'

export default {
    props: ['form'],
    data() {
        return {
            fields: []
        }
    },
    mounted() {
        this.loadFileCategories()
    },
    computed: {
        uploadedFiles() {
            return this.form.uploadedFiles || {}
        },
        allFilesUploaded() {
            return Object.keys(this.uploadedFiles).length === this.fields.length
        }
    },
    methods: {
        async loadFileCategories() {
            try {
                const response = await this.$axios.get('/api/file-categories')
                this.fields = response.data.map(category => ({ model: category.name, id: category.id }))
                this.$emit('set-fields', this.fields)
            } catch (error) {
                console.error("Error loading file categories:", error)
                Swal.fire('Error', 'Failed to load file categories', 'error')
            }
        },
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes'
            const k = 1024
            const sizes = ['Bytes', 'KB', 'MB', 'GB']
            const i = Math.floor(Math.log(bytes) / Math.log(k))
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
        },
        async openFileModal(label) {
            const { value: file } = await Swal.fire({
                title: `Upload ${label}`,
                html: `<input type="file" id="fileInput" class="swal2-input"/>`,
                width: '40em',
                showCancelButton: true,
                preConfirm: () => {
                    const input = Swal.getPopup().querySelector('#fileInput')
                    if (!input.files[0]) {
                        Swal.showValidationMessage('Please select a file')
                        return false
                    }
                    return input.files[0]
                }
            })

            if (!file) return

            // Find the file category object based on the label (category name)
            const category = this.fields.find(field => field.model === label)
            if (!category) {
                Swal.fire('Error', 'File category ID is missing.', 'error')
                return
            }

            // Store both the file and the category ID
            this.$set(this.form.uploadedFiles, label, {
                file,
                categoryId: category.id,
                size: file.size // Ensure the size property is set
            })

            Swal.fire('Uploaded!', `${label} has been selected.`, 'success')

            // Optional: Debugging output
            console.log('Stored:', this.form.uploadedFiles[label])
        },
        async confirmRemoveFile(label) {
            const confirm = await Swal.fire({
                title: `Remove ${label}?`,
                text: "Are you sure you want to remove this file?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, remove it",
                cancelButtonText: "Cancel"
            });

            if (confirm.isConfirmed) {
                this.$delete(this.form.uploadedFiles, label);
                Swal.fire('Removed!', `${label} has been removed.`, 'success');
            }
        }
    }
}
</script>