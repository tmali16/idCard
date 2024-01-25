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
                    <div class="flex flex-col text-center">
                        <v-text-field dense type="text" label="LAC и CELLID" hint="LAC и СID пишите через пробел" flat class="p-0 m-0" v-model="reqData.LacCid" />
                        <b class="text-red-600 text-xs justify-center">ИЛИ</b>
                        <v-text-field dense type="text" label="BS NAME" hint="Название секотора базы" flat class="p-0 m-0" v-model="reqData.bs_name" />
                    </div>
                </div>
            </v-card-text>
            <v-radio-group v-model="mapChange" @change="layerChange" row class="m-0" hide-details >
                <v-radio label="OSM" :value="1"></v-radio>
                <v-radio label="2GIS" :value="2"></v-radio>
                <v-radio label="Google" :value="3"></v-radio>
            </v-radio-group>
            <v-card-actions>
                <v-btn elevation="0" @click="getBs()" class="w-32" color="success">Поиск</v-btn>
                <v-btn elevation="0" @click="clearMap()" size="sm" class="max-w-full" >Отчистить</v-btn>
            </v-card-actions>
        </v-card>
        <v-card class="sm:absolute md:right-10 md:top-16 sm:px-2" style="z-index: 500;" elevation="0" max-width="500" min-width="300">
            <v-tabs v-model="tab">
                <v-tab>Секторы</v-tab>
                <v-tab>Инфо по Сек.</v-tab>
            </v-tabs>
            <v-tabs-items v-model="tab" >
                <v-tab-item >
                    <div class="flex gap-x-2 w-full justify-end">
                        <v-btn @click="hideSector = !hideSector" color="teal" dense x-small class="py-1 px-2 my-1 mx-2">
                            <v-icon small v-if="hideSector" class="text-white">mdi-eye-off</v-icon>
                            <v-icon small v-else class="text-white">mdi-eye</v-icon>
                        </v-btn>
                        <v-btn color="primary" @click="showAllSectors(bsData)" dense x-small class="py-1 px-2 my-1 mx-2">
                            <v-icon small>mdi-map-marker-multiple-outline</v-icon>
                        </v-btn>
                    </div>
                    <v-list  class="overflow-auto" style="max-height: 300px;" v-show="hideSector">
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
            </v-tabs-items>
        </v-card>

        <div class="w-full p-4 my-2 border overflow-hidden auto-height-map" id="maps"></div>
    </v-card>
    <v-snackbar v-model="toast.state" :color="toast.type" top>
        {{toast.message}}
        <template v-slot:action="{ attrs }">
            <v-btn color="red" text v-bind="attrs" @click="toast.state = false">
                Close
            </v-btn>
        </template>
    </v-snackbar>
    <v-overlay absolute :value="preload" z-index="9999">
        <v-progress-circular
            indeterminate
            size="64"
        ></v-progress-circular>
    </v-overlay>
</v-app>
</template>
<script>
export default {
    data() {
        return {
            preload: false,
            tab:[],
            hideHistory: true,
            hideSector: true,
            reqData:{
                LacCid: '',
                mnc: '1',
                bs_name: '',
            },
            saveImg:{
                state:false,
                src: ''
            },
            operators: [{id: 1, title:'Билайн', color: '#FFCA33'}, {id: 5, title:'Мегаком', color: '#2ab648'}, {id: 9, title:'О!', color: '#e2007a'}],
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
            mapChange:1,
        }
    },
    mounted(){
        setTimeout(()=>{
            this.init();
        }, 100)
        this.getHistory();
    },
    methods:{
        init(){
            this.layers = [];
            this.map = L.map('maps', {
                // zoomControl: false,
                dragging: !L.Browser.mobile,
                tap: false,
            })
            this.map.setView(new L.LatLng(42.8690, 74.5986), 12)
            L.control.scale({
                imperial: false,
                position: 'bottomright'
            }).addTo(this.map);
            let url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
            let tiles = '';
            if(this.mapChange === 2){
                tiles = L.tileLayer('http://tile2.maps.2gis.com/tiles?x={x}&y={y}&z={z}', {
                    attribution: ''
                })
            }else if(this.mapChange === 3){
                tiles = L.tileLayer('http://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                    attribution: '',
                    subdomains:['mt0','mt1','mt2','mt3']
                })
            }else {
                tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: ''
                })
            }
            tiles.addTo(this.map);
        },
        getBs(){
            if ((this.reqData.LacCid.length >= 2 && this.reqData.mnc.length !== 0) || this.reqData.bs_name.length > 3) {
                this.preload = true;
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
                    this.preload = false;
                }).catch(e => {
                    console.log(e.message)
                })
            }else{
                this.showToast("Не все поля заполнены", "error")
            }
        },
        createSector(LatLon, data){
            let mnc = this.operators.find(e=>e.id===data.mnc)
            return window.L.semiCircle(LatLon, {
                radius: 500,
                color: mnc.color
            }).setDirection(parseInt(data.azimuth), data.bandwidth)
        },
        createInfo(data){
            let ar = '';
            let mnc = this.operators.find(e=>e.id===data.mnc)
            ar += '<h3 class="text-lg text-red-600"><b>БС:</b> ' + data.sector_name + '</h3>'
            ar += '<b>LAC:</b> ' + data.lac + '<br>'
            ar += '<b>CID:</b> ' + data.ci + '<br>'
            ar += '<b>Диапазон:</b> ' + data.diapason + '<br>'
            ar += '<b>Поколение:</b> ' + data.Generation + 'G<br>'
            ar += '<b>Азимут:</b> ' + data.azimuth + '<br>'
            ar += '<b>Оператор:</b> ' + mnc.title + ' ('+mnc.id+')'+ '<br>'
            ar += '<b>Адрес:</b> ' + data.address + '<br>';
            ar += '<b>Карта: </b> <a target="_blank" href="https://info.o.kg/yourLocation.html?lat='+data.lat+'&lon='+data.lon+'&az='+data.azimuth+'&cr=1-500&abw=64">Открыть</a><br>';
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
        layerChange(){
            this.resetMap();
        },
        resetMap(){
            if(this.map !== null) {
                this.map.off();
                this.map.remove();
                //this.reset()
            }
            this.map = null
            let container = document.getElementById('maps');
            container.innerHTML  = "";
            this.init()
            if (this.bsData[0] !== undefined)
                this.createBs(this.bsData[0])
        },
        createPopup(data, bs){
            return new L.Popup({
                autoPan: false,
                autoPanPadding: L.point(100, 900),
                keepInView: false
            })
                .setLatLng(data.lat+", "+data.lon)
            .setContent(this.createInfo(data))
        },
        zoom(){
        },
        createBs(data){
            let uid = data.lac+'_'+data.ci+'_'+data.azimuth
            console.log(this.layers.length)
            for(let i =0;i<this.layers.length; i++){
                if(this.layers[i].options.uid === uid){
                    this.createInfo(data)
                    return
                }
            }
            let LatLon = [data.lat, data.lon]
            let sector = this.createSector(LatLon,  data)
            let bs = L.circle(LatLon)
                .setStyle({fillColor: '#0073ff', opacity: 1,})
                .bindPopup(this.createPopup(data))
                .openPopup()
                // .bindTooltip(this.createInfo(data), {permanent: false, }).openTooltip()
            let BsSector = L.layerGroup([bs,sector],{uid: uid})
            this.layers.push(BsSector);
            BsSector.addTo(this.map)
            this.map.fitBounds(bs.getBounds())
            if(this.map.getZoom() !== this.defZoom) {
                this.map.setZoom(14)
            }
            this.currentLotLan = LatLon
        },
        clearMap(){
            while (this.layers.length !== 0){
                for(let i = 0; i < this.layers.length; i++){
                    this.map.removeLayer(this.layers[i])
                    this.layers.splice(i, 1)
                }
                if(this.layers.length === 0)
                    break;
            }
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
        showAllSectors(bsDatas){
            for(let i =0; i < bsDatas.length; i++){
                if(bsDatas[i] !== null) {
                    if (bsDatas[i].cell !== undefined) {
                        if (bsDatas[i].cell !== null) {
                            this.createBs(bsDatas[i].cell);
                        }
                    } else {
                        this.createBs(bsDatas[i]);
                    }
                }
            }
        },
        getHistory(){
          axios.get('/api/geo/history').then(o=>{
              this.requestHistory = o.data;
          }).catch(e=>{
              this.showToast("Ошибка запроса истории" , 'history')
          })
        },
        historyBsAdd(item){
            if(!item)
                return
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
