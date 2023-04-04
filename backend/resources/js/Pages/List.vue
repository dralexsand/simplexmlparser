<template>
    <Head title="List"/>

    <AuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">List parsed data</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <ul v-for="(row, i) in rows"
                            :key="i"
                        >
                            <li>
                                {{ row[0].date }}:
                                <ul v-for="item in row">
                                    <li>{{ item }}</li>
                                    <br>
                                </ul>
                                <hr>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>

</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';

import {ref, computed, onMounted} from "vue";
import axios from "axios";

const rows = ref([])

onMounted(() => {
    let url = "http://127.0.0.1:8083/api/v1/parser/rows"
    getRows(url)
})

const getRows = async (url) => {
    await axios
        .get(url)
        .then(function (response) {
            console.log(response.data)
            rows.value = response.data
        })
        .catch(function (error) {
            console.log(error)
        })
}

onMounted(() => {

})

</script>
