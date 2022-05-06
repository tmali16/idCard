<template>
    <v-app class="bg-transparent">
        <v-row>
            <v-col cols="4" class="bg-blue-500 shadow border-r px-4 py-4">
                <v-card class="mx-2 bg-opacity-75 px-4">
                    <v-card-title>Поиск</v-card-title>
                    <div class="flex gap-x-4 mt-2">
                        <v-text-field dense :label="__('form.surname')" clearable v-model="searchField.surname"></v-text-field>
                        <v-text-field dense :label="__('form.name')" clearable v-model="searchField.name"></v-text-field>
                    </div>
                    <div class="flex gap-x-4">
                        <v-text-field dense type="date" :label="__('form.dob_start')" clearable v-model="searchField.dob_start"></v-text-field>
                        <v-text-field dense type="date" :label="__('form.dob_end')" clearable v-model="searchField.dob_end"></v-text-field>
                    </div>
                    <div class="flex gap-x-4 my-2">
                        <v-checkbox dense v-model="searchField.sex.male" label="Муж" hide-details/>
                        <v-checkbox dense v-model="searchField.sex.female" label="Жен" hide-details/>
                    </div>
                    <v-divider></v-divider>
                    <div class="flex gap-x-4 my-3">
                        <v-text-field dense type="datetime-local" clearable class="focus:shadow-none" :label="__('form.reg_start')" v-model="searchField.reg_start"></v-text-field>
                        <v-text-field dense type="datetime-local" clearable class="focus:shadow-none" :label="__('form.reg_end')" v-model="searchField.reg_end"></v-text-field>
                    </div>
                    <v-text-field dense :label="__('form.doc_num')" clearable v-model="searchField.doc_num"></v-text-field>
                    <v-text-field dense :label="__('form.doc_type')" clearable v-model="searchField.doc_type"></v-text-field>
                    <v-combobox dense :items="citizenshipItems" clearable item-value="id" item-text="value" :label="__('form.citizenship')" :placeholder="__('form.kpp')" v-model="searchField.citizenship" ></v-combobox>
                    <div class="flex gap-x-4">
                        <v-checkbox dense v-model="searchField.direction.in" label="Въезд" hide-details/>
                        <v-checkbox dense v-model="searchField.direction.out" label="Выезд" hide-details/>
                    </div>
                    <v-divider></v-divider>
                    <v-combobox dense :items="kppItems" item-value="id" clearable item-text="value" :label="__('form.kpp')" :placeholder="__('form.kpp')" v-model="searchField.kpp" ></v-combobox>
                    <v-text-field dense :label="__('form.flight_num')" clearable v-model="searchField.flight_num" ></v-text-field>
                    <div class="flex justify-end mb-3">
                        <v-btn color="primary" @click="search()">
                            {{__('form.search')}}
                        </v-btn>
                    </div>
                </v-card>
            </v-col>
            <v-col class="px-4">
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
            <v-col cols="3" v-if="selectedCross.show">
                <v-btn @click="selectedCross.show = false" icon size="2" absolute class="z-50" right top color="red"><v-icon>mdi-window-close</v-icon></v-btn>
               <view-component :cross_id="selectedCross.id"></view-component>
            </v-col>
        </v-row>
    </v-app>
</template>

<script>
export default {
    data(){
        return{
            dList:{data:[], header:[]},
            per_page: 5,
            loadGrid: false,
            options:{
                itemsPerPage: 10
            },
            selectedCross:{
                id:null,
                show: false
            },
            searchField:{
                surname:'',
                name:'',
                dob_start:'',
                dob_end:'',
                sex:{
                    male: true,
                    female: true,
                },
                reg_start:'',
                reg_end:'',
                doc_num:'',
                doc_type:'',
                citizenship:'',
                direction:{
                    in: true,
                    out: true,
                },
                kpp:'',
                flight_num:'',
            },
            kppItems: [],
            citizenshipItems: [],
            searchResult:[]
        }
    },
    mounted() {
        this.getKpp()
        this.getRefCitizen();
    },
    methods:{
        getKpp(){
            axios.get("/api/reference/2").then(o=>{
                this.kppItems = o.data
            })
        },
        search(){
            // if(this.searchField.surname.length > 0 || this.searchField.name.length > 0 || this.searchField.citizenship.length > 0){
                this.getSearchResult(this.searchField)
            // }
        },
        getRefCitizen(){
          axios.get('/api/reference/3').then(o=>{
              this.citizenshipItems = o.data
          })  .catch(e=>console.log(e.message))
        },
        getSearchResult(data){
            this.loadGrid= true;
            // let query = "&q.uid="+this.filter.uid + "&q.phone="+this.filter.phone
            axios.post("/api/search", data).then(d=>{
                this.dList.data = d.data.data
                this.dList.header = d.data.header
                this.selectedCross.show = false;
                this.loadGrid = false;
            }).catch(e=>{
                console.log('error get list erpp => '+e.message)
                this.loadGrid = false
            })
        },
        selectedRow(item){
            this.selectedCross.id = item.id
            this.selectedCross.show = true
        },
    }
}
</script>

<style scoped>

</style>
