<template>
<!--    <v-app class="bg-transparent">-->
        <v-card class="" tile>
            <v-carousel hide-delimiters height="250">
                <v-carousel-item src="https://cdn.pixabay.com/photo/2020/07/12/07/47/bee-5396362_1280.jpg">
                    <div class="flex w-full bg-black bg-opacity-25 justify-end p-2">
                        <v-btn small icon color="white"><v-icon>mdi-download </v-icon></v-btn>
                    </div>
                </v-carousel-item>
            </v-carousel>
            <v-list dense>
            </v-list>
            <v-list dense flat class="text-xs">
                <v-list-item dense v-for="[key, item] in Object.entries(data)" :key="key" v-if="item">
                    <v-list-item-content class="py-0 my-0 font-weight-bold" v-text="__('form.'+key)"></v-list-item-content>
                    <v-list-item-content class="py-0 my-0" v-text="item"></v-list-item-content>
                </v-list-item>
            </v-list>
        </v-card>
<!--    </v-app>-->
</template>

<script>
export default {
    props:[
        'cross_id'
    ],
    data(){
        return{
            data:{}
        }
    },
    mounted() {
    },
    created() {
    },
    watch:{
        cross_id:{
            handler(){
                this.get()
            },
            immediate:true,
            deep:true
        }
    },
    methods:{
        get(){
            axios.get('/api/cross/'+this.cross_id).then(o=>{
                this.data = o.data
            }).catch(e=>{
                console.log("view get error " +e.message)
            })
        }
    }
}
</script>

<style scoped>

</style>
