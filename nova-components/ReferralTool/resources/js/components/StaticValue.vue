<template>
    <card class="flex flex-col items-center justify-center custom-card">
        <h3 class="mr-3 text-base text-80 font-bold custom-card-header">{{ card.title }}</h3>
        <p class="flex items-center text-4xl mb-4">
            {{ card.value }}
            <span v-if="card.suffix" class="item-suffix">{{ card.suffix }}</span>
        </p>
    </card>
</template>

<script>
export default {
    props: [
        'card',
    ],

    mounted() {
        //
    },
}
</script>
