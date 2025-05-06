<template>
    <div class="w-full max-w-xl mx-auto mt-10 flex flex-col items-center">
        <h2 class="font-bold text-3xl text-center text-darkblue mb-5">Registration</h2>
        <h2 class="text-md text-center mb-10">
            Silahkan mengisi formulir berikut dengan sebenar-benarnya. Bantu kami mengenali anda dengan mudah.
        </h2>

        <div class="bg-white w-full max-w-2xl p-8 rounded-3xl shadow-xl border-2 border-gray-100">
            <FormStepProgress :step="step" :steps="steps" :stepProgress="stepProgress" />
            <hr class="mb-16" />

            <form @submit.prevent="nextStep">
                <StepContact v-if="step === 1" :form="form" />
                <StepFile v-if="step === 2" :form="form" @set-fields="fileFields = $event" />
                <StepFinish v-if="step === 3" />
            </form>
            <div @click="tologin" class="flex items-center justify-center">
                <button v-if="step === 3"
                    class="px-10 py-4 my-5 rounded-full bg-pluplu text-white font-semibold">Close</button>
            </div>

        </div>

        <!-- Button outside the form -->
        <div class="w-full max-w-2xl flex justify-between mt-4">
            <button v-if="step > 1 && step !== 3" type="button" @click="prevStep"
                class="px-10 py-2 border-2 border-indigo-600 text-indigo-600 rounded-full">Previous
                Step</button>
            <button v-if="step === 1" @click="nextStep" type="button" :disabled="step === 2 && stepProgress < 100"
                class="px-10 py-2 rounded-full ml-auto" :class="{
                    'bg-indigo-600 text-white': stepProgress === 100,
                    'bg-gray-400 text-gray-200 cursor-not-allowed': stepProgress < 100
                }">
                {{ step === steps.length - 1 ? 'Submit' : 'Next Step' }}
            </button>
            <button v-if="step === 2" @click="register" type="button" :disabled="stepProgress < 100"
                class="px-10 py-2 rounded-full ml-auto" :class="{
                    'bg-indigo-600 text-white': stepProgress === 100,
                    'bg-gray-400 text-gray-200 cursor-not-allowed': stepProgress < 100
                }">
                {{ step === steps.length - 1 ? 'Submit' : 'Next Step' }}
            </button>

        </div>
    </div>
</template>


<script>
import FormStepProgress from '@/Components/FormStepProgress.vue'
import StepContact from '@/Components/StepContact.vue'
import StepFile from '@/Components/StepFile.vue'
import StepFinish from '@/Components/StepFinish.vue'
import Swal from 'sweetalert2'

export default {
    components: { FormStepProgress, StepContact, StepFile, StepFinish },
    data() {
        return {
            step: 1,
            steps: ['Contact', 'File', 'Finish'],
            form: {
                name: '',
                email: '',
                phoneNum: '',
                univ: '',
                password: '',
                uploadedFiles: {},
                fileCategoryId: ''
            },
            fileFields: []
        }
    },
    computed: {
        stepProgress() {
            if (this.step === 1) {
                let count = 0
                if (this.form.name) count++
                if (this.form.email) count++
                if (this.form.phoneNum) count++
                if (this.form.univ) count++
                if (this.form.password) count++
                if (this.form.password_confirmation) count++
                return (count / 6) * 100
            }
            if (this.step === 2) {
                let uploaded = Object.keys(this.form.uploadedFiles || {}).length
                const total = this.fileFields.length || 1
                return (uploaded / total) * 100
            }
            // keep logic for other steps
        }
    },
    methods: {
        nextStep() {
            if (this.step < this.steps.length) this.step++
            else this.submitForm()
        },
        prevStep() {
            if (this.step > 1) this.step--
        },
        tologin() {
            this.$router.push('/login')
        },
        async register() {
            const uploaded = Object.keys(this.form.uploadedFiles || {}).length
            const total = this.fileFields.length || 1
            if (uploaded < total) {
                Swal.fire('Incomplete', 'Please upload all required files.', 'error')
                return
            }
            this.step++

            try {
                const { data } = await this.$axios.post('/api/register', {
                    username: this.form.name,
                    email: this.form.email,
                    password: this.form.password,
                    university_id: this.form.univ,
                    phone: this.form.phoneNum,
                });

                const userId = data.user.id;

                for (const [label, fileData] of Object.entries(this.form.uploadedFiles)) {
                    const formData = new FormData();
                    formData.append('file', fileData.file);
                    formData.append('user_id', userId);
                    formData.append('file_category_id', fileData.categoryId);  // use stored ID

                    console.log('Uploading file for category', label);
                    for (let [key, value] of formData.entries()) {
                        console.log(`${key}:`, value);
                    }

                    await this.$axios.post('/api/user-files/upload', formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });
                }


                Swal.fire('Success', 'Registration and file upload successful!', 'success');
            } catch (error) {
                if (error.response && error.response.data && error.response.data.errors) {
                    console.error('Validation errors:', error.response.data.errors);
                    Swal.fire('Validation Error', Object.values(error.response.data.errors).join('\n'), 'error');
                } else {
                    console.error(error);
                    Swal.fire('Error', 'Something went wrong.', 'error');
                }
            }
        },
    }
}
</script>