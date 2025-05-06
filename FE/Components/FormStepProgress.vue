<template>
    <div class="flex mb-5">
        <div class="flex items-center w-full max-w-xl justify-center">
            <div v-for="(label, index) in steps" :key="index" class="flex items-center">
                <!-- Bullet -->
                <div class="w-8 h-8 flex items-center justify-center rounded-full border-2 font-semibold z-10 bg-white"
                    :class="{
                        'bg-pluplu text-white border-pluplu': step >= index + 1,
                        'text-gray-500 bg-gray-200 border-none text-md': step < index + 1
                    }">
                    {{ index + 1 }}
                </div>

                <!-- Line -->
                <div v-if="index < steps.length - 1" class="relative w-20 h-1 mx-5">
                    <div class="absolute inset-0 bg-gray-200 rounded"></div>
                    <div class="absolute inset-y-0 left-0 bg-pluplu rounded transition-all duration-300" :style="{
                        width:
                            step > index + 1
                                ? '100%'
                                : step === index + 1
                                    ? stepProgress + '%'
                                    : '0%'
                    }"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['step', 'steps', 'stepProgress']
}
</script>