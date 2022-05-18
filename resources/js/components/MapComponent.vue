<template>
<v-app class="h-screen">
    <v-card class="relative" flat>
        <v-card class="sm:absolute md:left-10 md:top-16 sm:px-2" style="z-index: 500;" elevation="0" max-width="500" width="300">
            <div class="px-2 py-1 text-blue-600 text-lg">
                Поиск
            </div>
            <v-card-text class="py-1">
                <v-radio-group class="m-0" v-model="reqData.mnc" dense>
                    <v-radio v-for="(v, i) in operators" dense :key="i" :label="v.title +' ('+v.id+')'" :value="v.id"></v-radio>
                </v-radio-group>
                <div class="flex gap-x-4 items-center justify-start">
                <v-text-field dense type="text" label="LAC и CELLID" hint="LAC и СID пишите через пробел" flat class="p-0 m-0" v-model="reqData.LacCid" />
                </div>
            </v-card-text>
            <v-card-actions>
                <v-btn elevation="0" @click="getBs()" class="w-32" color="success">Поиск</v-btn>
                <v-btn elevation="0" @click="clearMap()" size="sm" class="max-w-full" >Отчистить</v-btn>
            </v-card-actions>
        </v-card>
        <v-card class="sm:absolute md:right-10 md:top-16 sm:px-2" style="z-index: 500;" elevation="0" max-width="500" min-width="300">
            <v-tabs v-model="tab">
                <v-tab>Секторы</v-tab>
                <v-tab>Инфо по Сек.</v-tab>
                <v-tab>История</v-tab>
            </v-tabs>
            <v-tabs-items v-model="tab" >
                <v-tab-item >
                    <v-list  class="overflow-auto" style="max-height: 300px;">
                        <v-list-item-group color="primary">
                            <v-list-item v-for="(item, i) in bsData" :key="i" >
                                <v-list-item-icon>
                                    <v-icon >mdi-radio-tower</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content @click="changeDb(item)">
                                    <v-list-item-title v-text="item.sector_name"></v-list-item-title>
                                    <v-list-item-subtitle v-text="item.diapason"></v-list-item-subtitle>
                                </v-list-item-content>
                                <v-list-item-action @click="hideBs(item)">
                                    <v-icon color="grey lighten-1">
                                        mdi-close
                                    </v-icon>
                                </v-list-item-action>
                            </v-list-item>
                        </v-list-item-group>
                    </v-list>
                </v-tab-item>
                <v-tab-item >
                    <div class=" w-full my-2 border px-3" v-html="bsInfo"></div>
                </v-tab-item>
                <v-tab-item>
                    <v-list class="overflow-auto" style="max-height: 300px;">
                        <v-list-item-group color="primary">
                            <v-list-item v-for="(item, i) in requestHistory" :key="i">
                                <v-list-item-content @click="historyBsAdd(item.cell)">
                                    <v-list-item-title v-text="item.lac + ' ' + item.ci"></v-list-item-title>
                                    <v-list-item-subtitle v-text="item.cell !== null ? 'Найдено' : 'Не найдено'"></v-list-item-subtitle>
                                </v-list-item-content>
        <!--                                <v-list-item-action @click="hideBs(item)">-->
        <!--                                    <v-icon color="grey lighten-1">-->
        <!--                                        mdi-close-->
        <!--                                    </v-icon>-->
        <!--                                </v-list-item-action>-->
                            </v-list-item>
                        </v-list-item-group>
                    </v-list>
                </v-tab-item>
            </v-tabs-items>
        </v-card>
        <button @click="zoom();">zoom</button>
        <div class="w-full p-4 my-2 border overflow-hidden auto-height-map" id="maps" style=""></div>
    </v-card>
    <v-snackbar v-model="toast.state" :color="toast.type" top>
        {{toast.message}}
        <template v-slot:action="{ attrs }">
            <v-btn color="red" text v-bind="attrs" @click="toast.state = false">
                Close
            </v-btn>
        </template>
    </v-snackbar>
</v-app>
</template>
<script>
// require('leaflet.bigimage')
import  Radar  from 'leaflet-radar';
import html2canvas from 'html2canvas';

export default {

    data() {
        return {
            tab:[],
            reqData:{
                LacCid: '',
                mnc: '1',
            },
            saveImg:{
                state:false,
                src: ''
            },
            operators: [{id: 1, title:'Билайн', color: '#FFCA33'}, {id: 5, title:'Мегаком', color: '#76b62a'}, {id: 9, title:'О!', color: '#e2007a'}],
            map: null,
            layers:[],
            currentLotLan: '',
            defZoom: 16,
            bsData:[],
            sectorOutline:{
                color: '#ffca33',
                dashArray: [20, 10],
                weight: 1,
                opacity: 1,
                fillColor: '#fffca33',
                fillOpacity: 0.3,
            },
            toast:{
                state: false,
                type: 'info',
                message: '',
            },
            bsInfo: '',
            requestHistory: [],
        }
    },
    mounted(){
        this.init();
        this.getHistory();
    },
    methods:{
        init(){
            this.map = L.map('maps', {
                zoomControl: false
                // attributionControl: false

            })
            this.map.setView(new L.LatLng(41.96766, 74.718018), 7.5)
            L.control.scale({
                imperial: false,
                position: 'bottomright'
            }).addTo(this.map);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: ''
            }).addTo(this.map);
        },
        getBs(){
            if (this.reqData.LacCid.length >= 2 && this.reqData.mnc.length !== 0) {
                axios.post('/api/geo/search', this.reqData).then(o => {
                    if(o.data.status !== undefined && parseInt(o.data.status) !== 200){
                        this.showToast(o.data.messages, "error")
                    }
                    if(Array.isArray(o.data)) {
                        if(o.data.length !== 0){
                            this.bsData = o.data
                            this.createBs(this.bsData[0])
                        }else{
                            this.showToast("Не найдено")
                        }
                    }
                    this.getHistory()
                }).catch(e => {
                    console.log(e.message)
                })
            }else{
                this.showToast("Не все поля заполнены", "error")
            }
        },
        createSector(LatLon, data){
            let mnc = this.operators.find(e=>e.id===data.mnc)
            this.sectorOutline.color = mnc.color
            this.sectorOutline.fillColor = mnc.color
            return new Radar(
                {
                    radius:500, //Radius of radar sector,The unit is meter
                    angle:65, //Fan opening and closing angle 0-360
                    direction: parseInt(data.azimuth), // Fan orientation angle 0-360
                    location: LatLon.join(", ") // Longitude dimension of sector start position
                },
                {
                    online: this.sectorOutline,
                    animat: {
                        color: '#238',
                        weight: 0,
                        opacity: 0,
                        fillColor: "#ff0",
                        fillOpacity: 0.05,
                        pmIgnore: false
                    },
                    text: 'BS Station',
                    step: 3  //The refresh distance of each frame of radar scanning animation. The unit is meter.
                }
            )
        },
        createInfo(data){
            let ar = '';
            let mnc = this.operators.find(e=>e.id===data.mnc)
            ar += '<h3 class="text-lg text-red-600"><b>БС:</b> ' + data.sector_name + '</h3>'
            ar += '<b>LAC:</b> ' + data.lac + '<br>'
            ar += '<b>CID:</b> ' + data.ci + '<br>'
            ar += '<b>Диапазон:</b> ' + data.diapason + '<br>'
            ar += '<b>Азимут:</b> ' + data.azimuth + '<br>'
            ar += '<b>Оператор:</b> ' + mnc.title + ' ('+mnc.id+')'+ '<br>'
            ar += '<b>Адрес:</b> ' + data.address + '<br>';
            ar += '<b>LAT:</b> ' + data.lat + '<br>';
            ar += '<b>LON:</b> ' + data.lon + '<br>';
            this.bsInfo = ar;
            return ar
        },
        hideBs(data){
            let hide = 'mdi-eye-off';
            let show = 'mdi-eye';
            let uid = data.lac+'_'+data.ci+'_'+data.azimuth
            this.layers.forEach((currentValue, index, array)=>{
                if(currentValue.options.uid === uid) {
                    this.map.removeLayer(currentValue)
                    this.layers.splice(index, 1)
                }
            })
        },
        createPopup(data, bs){
            return L.popup({
                autoPanPaddingTopLeft: [0, 30],
                autoPanPaddingBottomRight: [0, 30],
            }).setLatLng(data.lat+", "+data.lon)
            .setContent(this.createInfo(data))
        },
        zoom(){
            console.log(this.map)
        },
        createBs(data){
            let uid = data.lac+'_'+data.ci+'_'+data.azimuth
            for(let i =0;i<this.layers.length; i++){
                if(this.layers[i].options.uid === uid){
                    this.createInfo(data)
                    return
                }
            }

            let LatLon = [data.lat, data.lon]
            let sector = this.createSector(LatLon,  data).bindPopup(this.createPopup(data, bs)).openPopup()
            let icon = L.icon({
                iconSize: [48, 48],
                iconAnchor: [24, 48],
                popupAnchor:  [-2, -24],
                iconUrl: '/images/antenna.png',
            })
            let bs = L.circle(LatLon)
                .setStyle({fillColor: '#0073ff', opacity: 1,})
            let antenna = L.marker(LatLon, {icon: icon})
                // .bindPopup(this.createPopup(data, bs))
                // .openPopup()
            let BsSector = L.layerGroup([bs,sector],{uid: uid})
            this.layers.push(BsSector);
            BsSector.addTo(this.map)
            this.map.fitBounds(bs.getBounds())
            if(this.map.getZoom() !== this.defZoom) {
                this.map.setZoom(16)
            }
            this.currentLotLan = LatLon
        },
        clearMap(){
            this.layers.forEach((currentValue, index, array)=>{
                this.map.removeLayer(currentValue)
                this.layers.splice(index, 1)
            })
        },
        getZoom() {
            if (!(this.map == null)) {
                let zoom = this.map.getZoom();
                if (!(zoom == null)) return zoom;
            }
            return 1;	//default
        },
        getLat() {
            if (!(this.map == null)) {
                return this.map.getCenter().lat;
            }
            return 0;	//default
        },
        getLon() {
            if (!(this.map == null)) {
                return this.map.getCenter().lng;
            }
            return 0;	//default
        },
        showToast(msg, type="info"){
            let color = "";
            switch (type){
                case "info":
                    color = "blue"
                    break;
                case "error":
                    color= 'red';
                    break;
                default:
                    color= "info";
                    break;
            }
            this.toast.type = color;
            this.toast.message = msg;
            this.toast.state = true;
            setTimeout(()=>{
                this.toast.state = false;
            }, 5000)
        },
        changeDb(item){
            this.createBs(item)
        },
        getHistory(){
          axios.get('/api/geo/history').then(o=>{
              this.requestHistory = o.data;
          }).catch(e=>{
              this.showToast("Ошибка запроса истории" , 'history')
          })
        },
        historyBsAdd(item){
            if(!item)return

            this.createBs(item)
        }
    }
}
</script>

<style scoped>
.leaflet-right  a{
    display: none;
}
</style>
