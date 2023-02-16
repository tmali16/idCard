<template>
<v-app class="h-screen bg-transparent" style="background-color: transparent">
    <div class="grid grid-cols-12 relative gap-y-6 px-2">
        <div class="col-span-12 my-4">

        </div>
        <div class="col-span-12 mb-4">
            <div class="grid grid-cols-12 gap-x-4" style="max-height: 700px;">
                <div class="col-span-3 sm:col-span-3 overflow-hidden" >
                    <v-card class="mb-2 p-0">
                        <div class="bg-gray-200 p-2">
                            Информация об БС
                        </div>
                        <div class="overflow-y-auto p-2" style="max-height: 300px" v-html="bsInfo"></div>
                    </v-card>
                    <v-card v-if="can('view.objects')">
                        <div class="bg-gray-200 p-2">
                            Объекты
                        </div>
                        <v-data-table
                            dense height="420" :items="objectList" :headers="[{value:'full_name'}, {value:'aliase', cellClass:['hidden']}, {value:'phone', cellClass:['']}]"
                            :search="objectSearch" :items-per-page="-1" hide-default-header hide-default-footer>
                            <template v-slot:top>
                                <div class="flex items-center justify-center">
                                    <v-text-field
                                        v-model="objectSearch" small clearable flat class="border-b-2 m-0"
                                        hide-details prepend-inner-icon="mdi-magnify" label="Поиск">
                                    </v-text-field>
                                    <v-btn small @click="openDialog()" text class="border self-end mx-4">
                                        <v-icon>mdi-account-plus</v-icon>
                                    </v-btn>
                                </div>
                            </template>
                            <template v-slot:item.full_name="{ item }">
                                <div class="flex flex-col text-sm text-uppercase">
                                    <span class=" text-blue-500 text-secondary">{{item.aliase}}</span>
                                    <label class="text-xs">{{item.full_name}}</label>
                                </div>
                            </template>
                        </v-data-table>
                    </v-card>
                </div>
                <div class="col-span-9 sm:col-span-9 bg-gray-50" >
                    <div class="flex justify-between p-2 gap-x-4" >
                        <div class=" flex p-2 gap-x-3">
                            <div class="">
                                <v-radio-group hide-details class="m-0" v-model="request.mnc" dense row>
                                    <v-radio v-for="(v, i) in operators" dense :key="i" :label="v.title +' ('+v.id+')'" :value="v.id"></v-radio>
                                </v-radio-group>
                            </div>
                            <div class="">
                                <v-text-field dense type="text" @keypress.enter="getBs" label="LAC и CELLID или Сектор" hint="LAC и СID или Сектор пишите через пробел" flat class="p-0.5 m-0" v-model="request.text" />
                            </div>
                            <v-btn small depressed color="green" @click="getBs" class="bg-red-500 text-white">Найти</v-btn>
                        </div>
                        <div class="bg-gray-300 p-1 flex gap-x-2 w-2/1 justify-end items-center text-xs">
                            <v-btn small color="danger" class="bg-red-500 text-white"  @click="clearMap()">
                                Отчистить
                            </v-btn>
                            <v-radio-group v-model="mapChange" @change="layerChange" row class="m-0" hide-details>
                                <v-radio label="OSM" :value="1"></v-radio>
                                <v-radio label="2GIS" :value="2"></v-radio>
                                <v-radio label="Google" :value="3"></v-radio>
                            </v-radio-group>
                            <v-btn small color="danger" class="bg-red-500 text-white"  @click="resetMap()">
                                <v-icon>mdi-reload</v-icon>
                            </v-btn>
                            <v-btn small color="primary" v-if="layers.length > 0" @click="toggleLayer()">
                                {{layerHideShow ? 'Скрыть БС' : 'Показать БС'}}
                            </v-btn>
                        </div>
                    </div>
                    <div class="relative" id="mapContainer" style="max-height: 640px; min-height: 640px" ></div>
                </div>
            </div>
            <v-overlay absolute :value="isSearching" z-index="9999">
                <v-progress-circular
                    indeterminate
                    size="64"
                ></v-progress-circular>
            </v-overlay>
            <v-dialog v-model="favObjectDialog" width="500" style="z-index: 999">
                <v-card>
                    <v-card-text>
                        <div class="grid grid-cols-12">
                            <div class="col-span-12">
                                <v-text-field v-model="favObject.full_name" label="Полное имя"></v-text-field>
                            </div>
                            <div class="col-span-12">
                                <v-text-field v-model="favObject.aliase" label="Псевдоним"></v-text-field>
                            </div>
                            <div class="col-span-12">
                                <v-text-field v-model="favObject.phone" prefix="996" min="9" max="9" :rules="[favRule]" label="Номер телефона (без 0, 996)"></v-text-field>
                            </div>
                            <div class="col-span-12 bg-gray-50 flex justify-between">
                                <v-btn small @click="addFavObject" color="primary">Сохранить</v-btn>
                                <v-btn small>Отменить</v-btn>
                            </div>
                        </div>
                    </v-card-text>
                </v-card>
            </v-dialog>
        </div>
    </div>
</v-app>
</template>

<script>
export default {
    name: "ProMapComponent",
    props:['permissions'],
    data() {
        return {
            operators: [{id: 1, title:'Билайн', color: '#FFCA33'}, {id: 5, title:'MEGA', color: '#2ab648'}, {id: 9, title:'О!', color: '#e2007a'}],
            request:{
                mnc:1,
                lacCid: '',
                sectorname: ''
            },
            map: null,
            layers:[],
            sectors:[],
            favObject:{
                full_name: '',
                aliase: '',
                phone: ''
            },
            infoPanel: [1],
            favObjectDialog: false,
            favRule: false,
            objectList: [],
            objectSearch: '',
            isSearching: false,
            layerHideShow: true,
            mapChange: 1,
            currentSelection: 0,
            bsInfo:'',
        }
    },
    mounted() {
        setTimeout(()=>{
            this.init();
        }, 10);
        this.getFavObjects()
    },
    watch:{
    },
    methods: {
        getBs(){
            if (this.request.text.length >= 2 && this.request.mnc.length !== 0) {
                axios.post('/api/geo/se', this.request).then(o => {
                    if(o.data.status !== undefined && parseInt(o.data.status) !== 200){
                        // this.showToast(o.data.messages, "error")
                    }
                    if(Array.isArray(o.data)) {
                        if(o.data.length !== 0){
                            this.clearMap();
                            this.sectors = o.data
                            this.createBs(this.sectors[0])
                        }else{
                            //this.showToast("Не найдено")
                        }
                    }
                    // this.getHistory()
                }).catch(e => {
                    console.log(e.message)
                })
            }else{
                this.showToast("Не все поля заполнены", "error")
            }
        },
        getFavObjects(){
            axios.get('/api/fav/get').then(e=>{
                this.objectList = e.data
            }).catch(e=>{
                console.log("Ошибка получения списка обекта: "+e.message)
            })
        },
        addFavObject(){
            this.favRule = false
            if(this.favObject.phone.length === 9 && /\d+/g.test(this.favObject.phone)) {
                axios.post("/api/fav/add", this.favObject).then(o => {
                    this.getFavObjects();
                    this.favObjectDialog = false;
                }).catch(e => {
                    console.log("Ошибка добавлении объекта: " + e.message)
                })
            }else{
                console.log("Чтото не так!!!")
                this.favRule = true
            }
        },
        createBs(data){
            try {

                if(data != null){
                    let uid = data.lac + '_' + data.ci + '_' + data.azimuth
                    let LatLon = [data.lat, data.lon]
                    this.currentLotLan = LatLon
                    let bs = L.circle(LatLon, {radius: 15})
                        .setStyle({fillColor: '#0073ff', opacity: 1})
                        .bindPopup(this.createPopup(data))
                    let sector = this.createSector(LatLon, data)
                    let BsSector = L.layerGroup([bs, sector], {uid: uid})
                    this.layers.push(BsSector);
                    BsSector.addTo(this.map)
                    this.map.fitBounds(bs.getBounds())
                    this.map.setZoom(15)
                }else{
                    this.createInfo(data);
                }
            } catch (e) {
                console.log("create BS error: " + e.message)
            }
        },
        createSector(LatLon, data){
            let mnc = this.operators.find(e=>e.id===data.mnc)
            return window.L.semiCircle(LatLon, {
                radius: 500,
                color: mnc.color
            }).setDirection(parseInt(data.azimuth), 65)
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
        createInfo(data){
            let ar = '';
            try {
                if (data != null) {
                    let mnc = this.operators.find(e => e.id === data.mnc)
                    ar += '<h3 class="text-lg text-red-600"><b>БС:</b> ' + data.sector_name + '</h3>';
                    ar += '<b>LAC и CID:</b> <span class="text-red-600">' + data.lac + ' ' + data.ci + '</span><br>';
                    ar += '<b>Поколение:</b> ' + data.Generation + 'G<br>';
                    ar += '<b>Азимут:</b> ' + data.azimuth + '<br>';
                    ar += '<b>Оператор:</b> ' + mnc.title + ' (' + mnc.id + ')' + '<br>';
                    ar += '<b>Адрес:</b> ' + data.address + this.direction(data.azimuth) + '<br>';
                } else {
                    ar += '<h3 class="text-lg text-red-600"><b>БС:</b> ' + ((this.locInfo.data.cid == null) ? 'Сеть 4G' : 'БС не найден') + ' </h3>';
                    ar += '<b>LAC и CID:</b> <span class="text-red-600">' + this.locInfo.data.lac + ' ' + this.locInfo.data.cid + '</span><br>';
                    ar += '<b>Адрес:</b> ' + this.locInfo.data._address + '<br>';
                    ar += '<b>Дата и время:</b> <span class="">' + (new Date).toLocaleTimeString() + ' </span><br>';
                }
            }catch (e) {
                console.log('create INFO ERROR: '+e.message)
            }
            this.bsInfo = ar;
            return this.bsInfo
        },
        clearMap(){
            // if(this.map) {
            while (this.layers.length !== 0) {
                for (let i = 0; i < this.layers.length; i++) {
                    this.map.removeLayer(this.layers[i])
                    this.layers.splice(i, 1)
                }
                if (this.layers.length === 0)
                    break;
            }
            // }
        },
        toggleLayer(){
            if(this.layerHideShow) {
                this.layers.forEach(layer => {
                    this.map.removeLayer(layer)
                })
            }else{
                this.layers.forEach(layer => {
                    // this.map.removeLayer(layer)
                    // this.map.add(layer)
                    layer.addTo(this.map)
                })
            }
            this.layerHideShow = !this.layerHideShow;
        },
        openDialog(){
            this.favObjectDialog = true;
            this.favObject.full_name = '';
            this.favObject.aliase = '';
            this.favObject.phone = '';
        },
        direction(azimuth){
            return window.azimuthToText(azimuth);
        },
        init() {
            this.map = new L.Map('mapContainer', {
                zoomControl: true,
                dragging: !L.Browser.mobile,
                tap: false,
            }).setView(new L.LatLng(42.8690, 74.5986), 12)

            let url = '';
            let tiles = '';
            if(this.mapChange === 2){
                tiles =L.tileLayer('http://tile2.maps.2gis.com/tiles?x={x}&y={y}&z={z}', {
                    attribution: ''
                })
            }else if(this.mapChange === 3){
                tiles =L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                    attribution: '',
                    subdomains:['mt0','mt1','mt2','mt3']
                })
            }else {
                tiles =L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: ''
                })
            }
            tiles.addTo(this.map);
            // let drawnItems = L.featureGroup().addTo(this.map);
            try {
                // this.map.addControl(new L.Control.Draw({
                //     draw: {
                //         polygon: {
                //             allowIntersection: false, // Restricts shapes to simple polygons
                //             drawError: {
                //                 color: '#e1e100', // Color the shape will turn when intersects
                //                 message: '<strong>Oh snap!<strong> you can\'t draw that!' // Message that will show when intersect
                //             },
                //             shapeOptions: {
                //                 color: '#97009c'
                //             }
                //         },
                //         polyline: {
                //             shapeOptions: {
                //                 color: '#f357a1',
                //                 weight: 10
                //             }
                //         },
                //         circle: true,
                //         marker: true,
                //         rectangle: true,
                //     },
                //     edit: {
                //         featureGroup: drawnItems,
                //     },
                // }))
                // this.map.on(L.Draw.Event.CREATED, (e) => {
                //     let layer = e.layer;
                //     drawnItems.addLayer(layer)
                // })
            }catch (e){console.log(e.message)}
            this.map.invalidateSize();
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
            let container = document.getElementById('mapContainer');
            container.innerHTML  = "";
            this.init()
            this.selectedBsForView(this.currentSelection)
        },
        can(item){
            let a= this.permissions.find(x=>x === item);
            return a !== undefined;
        },
    }
}
</script>

<style scoped>

</style>
