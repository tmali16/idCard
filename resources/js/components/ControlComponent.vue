<template>
    <v-app class="bg-transparent px-4 relative">
        <div class="h-10"></div>
        <v-row>
            <v-col  cols="3">
                <v-card class="mx-auto" tile>
                    <v-list dense>
                        <v-subheader class="justify-between">
                            Объекты
                            <v-btn @click="newObject()" icon color="primary" class="border border-red-600">
                                <v-icon>mdi-account-plus</v-icon>
                            </v-btn>
                        </v-subheader>
                        <v-list-item-group color="primary" >
                            <v-list-item v-for="(item, index) in objectList" :key="index" @click="getObjectCross(item.id)">
                                <v-list-item-icon>
                                    <v-icon >mdi-account</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>{{item.full_name}}</v-list-item-title>
                                    <v-list-item-subtitle>{{item.dob}}</v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list-item-group>
                    </v-list>
                </v-card>
            </v-col>
            <v-col :cols="selectedCross.show ? 6 : 9">
                <v-card elevation="1" tile max-height="10rem">
                    <v-data-table
                        :loading="loadGrid"
                        :headers="dList.header"
                        :items="dList.data"
                        class="elevation-1"
                        @click:row="selectedRow"
                    ></v-data-table>
                </v-card>
            </v-col>
            <v-col v-if="selectedCross.show" cols="3">
                <view-component :cross_id="selectedCross.id" />
            </v-col>
        </v-row>
        <v-dialog v-model="newObjectModal" width="1000">
            <v-card>
                <v-tabs v-model="tab" class="px-4">
                    <v-tab>Объект</v-tab>
                    <v-tab>Настройки <span class="text-red-600">*</span></v-tab>
                </v-tabs>
                <v-tabs-items v-model="tab">
                    <v-tab-item>
                        <div class="flex px-4 py-2 w-full">
                            <v-card class="px-2 py-1 w-full" tile>
                                <v-card-subtitle>Данные объекта</v-card-subtitle>
                                <div class="flex gap-x-4">
                                    <v-text-field v-model="object.surname" :label="__('form.surname')"></v-text-field>
                                    <v-text-field v-model="object.name" :label="__('form.name')"></v-text-field>
                                    <v-text-field v-model="object.patronymic" :label="__('form.patronymic')"></v-text-field>
                                </div>
                                <div class="flex gap-x-4">
                                    <v-text-field type="date" v-model="object.dob" :label="__('form.dob')"></v-text-field>
                                    <v-radio-group v-model="object.sex_id" row class="mx-4">
                                        <v-radio label="Муж." value="1"></v-radio>
                                        <v-radio label="Жен." value="2"></v-radio>
                                    </v-radio-group>
                                </div>
                                <v-combobox v-model="object.citizenship_id" @focus="getRefCitizen" :items="citizenshipData" item-text="value" item-value="id" :hint="__('form.citizenship_id')" :label="__('form.citizenship_id')"></v-combobox>
                            </v-card>
                        </div>
                    </v-tab-item>
                    <v-tab-item>
                        <v-card class="px-2 py-2 w-full shadow-0" elevation="0" tile>
                            <div class="flex w-full gap-x-4 items-center">
                                <v-label class="mb-2">Состояние: </v-label>
                                <v-switch v-model="object.settings.active" :label="object.settings.active ? 'Включен' : 'Отключен'"></v-switch>
                            </div>
                            <div class="flex w-full gap-x-4 items-center">
                                <v-label class="w-25 mb-2">Оповещение в телеграмм: </v-label>
                                <v-switch v-model="object.settings.tgAlert" :label="object.settings.tgAlert ? 'Включен' : 'Отключен'"></v-switch>
                            </div>
                            <v-textarea solo v-model="object.settings.notes" :label="__('form.notes')" :hint="__('form.notes')"></v-textarea>
                        </v-card>
                    </v-tab-item>
                </v-tabs-items>
                <div class="flex justify-end p-4">
                    <v-btn @click="objectSave()">{{__('form.save')}}</v-btn>
                </div>
            </v-card>
        </v-dialog>
    </v-app>
</template>

<script>
export default {
    data(){
        return{
            dList:{data:[], header:[]},
            per_page: 5,
            loadGrid: false,
            newObjectModal: false,
            tab: null,
            selectedCross:{
                id:null,
                show: false
            },
            citizenshipData: [],
            objectList:[],
            object:{
                surname: '',
                name: '',
                patronymic: '',
                dob:'',
                sex_id: "1",
                citizenship_id: {
                    id: 175,
                    value: "КИРГИЗИЯ"
                },
                settings:{
                    active: true,
                    tgAlert: true,
                    notes: '',
                }
            },
            message:{
                state: true,
            }
        }
    },
    mounted(){
        this.getRefCitizen()
        this.getObjects()
    },
    methods:{
        newObject(){
            this.newObjectModal = true
        },
        getObjects(){
            axios.get('/api/control/list').then(o=>{
                this.objectList = o.data

            })
        },
        getRefCitizen(){
            axios.get('/api/reference/3').then(o=>{
                this.citizenshipData = o.data
            }).catch(e=> {
                console.log(e.message)
            })
        },
        objectSave(){
            if (this.object.surname.length > 2 && this.object.name.length > 2){
                axios.post("/api/control/add", this.object).then(d=>{
                    this.newObjectModal = false;
                    this.getObjects();
                }).catch(e=>{
                    console.log(e.message)
                })
            }
        },
        selectedRow(item){
            this.selectedCross.id = item.id
            this.selectedCross.show = true
        },
        getObjectCross(id){
            this.loadGrid= true;
            // let query = "&q.uid="+this.filter.uid + "&q.phone="+this.filter.phone
            axios.get("/api/control/"+id).then(d=>{
                this.dList.data = d.data.data
                this.dList.header = d.data.header
                this.loadGrid = false;
            }).catch(e=>{
                console.log('error get control => '+e.message)
                this.loadGrid = false
            })
        },
    }
}
</script>

<style scoped>

</style>
