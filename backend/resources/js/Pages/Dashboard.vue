<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Parsing excel file</h2>

                        <div
                            class="block max-w-sm rounded-lg bg-white p-6 shadow-lg dark:bg-neutral-700">
                            <form>
                                <div class="relative mb-12" data-te-input-wrapper-init>

                                    <div class="flex justify-center">
                                        <div class="mb-3 w-96">

                                            <input
                                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                                aria-describedby="file_input_help"
                                                @change="uploadFile"
                                                accept=".xls,.xlsx, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel"
                                                type="file">
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300"
                                               id="file_input_help">
                                                .xlsx, .xls
                                            </p>

                                            <button
                                                :disabled="!statusUploadedFile"
                                                @click.prevent="submitFile"
                                                type="submit"
                                                class="rounded bg-primary px-6 py-2.5 text-xs font-medium
                                    leading-tight text-white shadow-md transition duration-150 ease-in-out hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg
                                    justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                                data-te-ripple-init
                                                data-te-ripple-color="light">
                                                Submit
                                            </button>

                                        </div>
                                    </div>

                                    {{ message }}

                                </div>
                            </form>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';

import {ref, computed} from "vue";
import axios from "axios";

const file = ref(null);
const message = ref('Upload file')
const statusUploadedFile = ref(false)

const fileName = computed(() => file.value?.name);
const fileExtension = computed(() => fileName.value?.substr(fileName.value?.lastIndexOf(".") + 1));
const fileMimeType = computed(() => file.value?.type);
const uploadFile = (event) => {
    file.value = event.target.files[0];
    message.value = 'File ' + fileName.value + ' uploaded, then click submit button';
    statusUploadedFile.value = true;
};

const timeOut = (msec = 3000) => {
    setTimeout(function () {
        console.log("Executed after " + Math.round(msec / 1000) + " seconds");
        window.location.reload()
    }, msec);
}

const submitFile = async () => {
    const reader = new FileReader();
    reader.readAsDataURL(file.value);
    reader.onload = async () => {
        const encodedFile = reader.result.split(",")[1];
        const data = {
            file: encodedFile,
            fileName: fileName.value,
            fileExtension: fileExtension.value,
            fileMimeType: fileMimeType.value,
        };
        try {
            message.value = 'File ' + fileName.value + ' being parsed...';
            const apiUrl = "http://127.0.0.1:8083/api/v1/parser";
            const response = await axios.post(apiUrl, data);
            console.log(response.data);
            message.value = 'File ' + fileName.value + ' parsed successful';
            statusUploadedFile.value = false;

            //timeOut();
            //window.location.reload();
        } catch (error) {
            console.error(error);
        }
    };
};

</script>
