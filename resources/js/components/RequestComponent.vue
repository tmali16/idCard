<template>
<v-app class="bg-transparent" style="background-color: transparent;">
    <v-container>
        <div class="grid grid-cols-12">
            <div class="col-span-12 md:col-span-4">
                <v-card class="w-full">
                    <v-card-title>
                        Запрос по номеру:
                    </v-card-title>
                    <v-card-text>
                        <v-text-field label="Номер телефона" hint="Номер в формате 700123456"></v-text-field>
                        <div class="flex w-full bg-gray-300">
                            <div class="text-sm rounded-md">Результат:</div>
                        </div>
                    </v-card-text>
                </v-card>
            </div>
        </div>
    </v-container>
</v-app>
</template>

<script>
export default {
    name: "RequestComponent",
    data() {
        return {

        }
    },
    mounted(){},
    methods:{},
}
</script>

<style scoped>

</style>
