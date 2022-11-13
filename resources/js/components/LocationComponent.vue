<template>
    <div class=" p-2">
        <div class="flex pb-2 gap-x-4" >
            <v-text-field counter @change="objectInputFormating()"
                          hide-details v-model="request.object" dense class="m-0 p-0" solo></v-text-field>
            <v-btn  :disabled="request.object_type !== 'Phone'" color="green" class="text-white" @click="sendPulse()">
                <v-icon>mdi-pulse</v-icon>
            </v-btn>
        </div>
        <div class="p-2 bg-white my-1 flex flex-col" v-if="locInfo">
            <label v-if="locInfo.data.abonent_state"><strong>Статус абонента: </strong><span class="text-red-500">{{strg(locInfo.data.abonent_state)}}</span></label>
            <label v-if="locInfo.data.aol && parseInt(locInfo.data.aol) > 0"><strong>Состояние: </strong><span class="text-red-500"> Отключен {{locInfo.data.aol}} мин. назад</span></label>
            <label v-if="locInfo.data.aol && parseInt(locInfo.data.aol) === 0"><strong>Состояние: </strong><span class="text-green-500"> Активен</span></label>
            <label v-if="locInfo.data.text"><strong>Примечание: </strong><span class="text-info-500 overflow-hidden overflow-y-auto" style="max-width: 300px;" v-html="strg(locInfo.data.text)"></span></label>
        </div>
        <v-card>
            <v-tabs v-model="tabsMo">
                <v-tab>Карта</v-tab>
                <v-tab>Инфо</v-tab>
            </v-tabs>
            <v-tabs-items v-model="tabsMo">
                <v-tab-item>
                    <div class="p-2 overflow-hidden">
                        <div class="flex p-2" v-if="layers.length > 0">
                            <v-btn x-small class="bg-blue-500" @click="toggleLayer()">{{layerHideShow ? 'Скрыть БС' : 'Показать БС'}}</v-btn>
                        </div>
                        <div class="h-full relative" id="mapContainer" style="max-height: 640px; min-height: 400px; " ></div>
                    </div>
                </v-tab-item>
                <v-tab-item>
                    <div class="p-2 m-2">
                        <v-btn x-small class="bg-blue-800" @click="copyText('address')" tile>C</v-btn>
                        <p v-html="bsInfo" id="infoBlock" class=""></p>
                    </div>
                </v-tab-item>
            </v-tabs-items>
        </v-card>
        <v-overlay :value="overlay" z-index="999">
            <v-row class="fill-height" align-content="center" justify="center">
                <v-col class="subtitle-1 text-center" cols="12">
                    Поиск данных
                </v-col>
                <v-col cols="6">
                    <v-progress-linear color="deep-purple accent-4" indeterminate rounded height="6"></v-progress-linear>
                </v-col>
            </v-row>
        </v-overlay>
    </div>
</template>

<script>

export default {
    name: "LocationComponent",
    data() {
        return {
            operators: [{id: 1, title:'Билайн', color: '#FFCA33'}, {id: 5, title:'Мегаком', color: '#2ab648'}, {id: 9, title:'О!', color: '#e2007a'}],
            map: null,
            layers:[],
            currentLotLan: '',
            bsInfo: '',
            isSearching: false,
            layerHideShow: true,
            locInfo:'',
            request:{
                object_type:'Phone',
                object:'',
            },
            tabsMo: [0],
            overlay:false,
            lacCid: ''
        }
    },
    mounted(){
        setTimeout(()=>{
            this.init();
        }, 1000)
    },
    methods:{
        sendPulse(){
            this.overlay = true
            axios.get('/ls?num='+this.request.object).then(o=>{
                this.locInfo = o.data
                if(parseInt(o.data.status) === 200) {
                    this.createBs(this.locInfo.data)
                }
                this.overlay = false;
            }).catch(e=>{
                this.overlay = false;
            })
        },
        init() {
            this.map = new L.Map('mapContainer', {
                zoomControl: true,
                dragging: !L.Browser.mobile,
                tap: false,
            }).setView(new L.LatLng(42.8690, 74.5986), 12)
            // let url = 'http://192.168.4.1/tilecache/Cache/osm/{z}/{x}/{y}.png'
            let url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
            L.tileLayer(url, {
                attribution: ''
            }).addTo(this.map);
            // let drawnItems = L.featureGroup().addTo(this.map);
            // try {
            //     this.map.addControl(new L.Control.Draw({
            //         // position: 'topright',
            //         draw: {
            //             polygon: {
            //                 allowIntersection: false, // Restricts shapes to simple polygons
            //                 drawError: {
            //                     color: '#e1e100', // Color the shape will turn when intersects
            //                     message: '<strong>Oh snap!<strong> you can\'t draw that!' // Message that will show when intersect
            //                 },
            //                 shapeOptions: {
            //                     color: '#97009c'
            //                 }
            //             },
            //             polyline: {
            //                 shapeOptions: {
            //                     color: '#f357a1',
            //                     weight: 10
            //                 }
            //             },
            //             //disable toolbar item by setting it to false
            //             // polyline: true,
            //             circle: true, // Turns off this drawing tool
            //             // polygon: true,
            //             marker: true,
            //             rectangle: true,
            //         },
            //         edit: {
            //             featureGroup: drawnItems,
            //             // poly: {
            //             //     allowIntersection: false
            //             // }
            //         },
            //     }))
            //     this.map.on(L.Draw.Event.CREATED, (e) => {
            //         let layer = e.layer;
            //         drawnItems.addLayer(layer)
            //     })
            // }catch (e){console.log(e.message)}
            this.map.invalidateSize();
            //L.control.bigImage({position: 'topright'}).addTo(mymap);
        },
        createInfo(data){
            try{
                let ar = '';
                let mnc = this.operators.find(e=>e.id===data.mnc)
                ar += '<h3 class="text-lg text-red-600"><b>БС:</b> <span id="sector_name">' + data.sector_name + '</span></h3>'
                ar += '<b>LAC и CID:</b> <span class="text-red-600" id="laccid">' + data.lac +' '+data.ci+ '</span><br>'
                // ar += '<b>CID:</b> ' + data.ci + '<br>'
                ar += '<b>Диапазон:</b> <span id="diapason">' + data.diapason + '</span><br>'
                ar += '<b>Азимут:</b> <span id="azimuth">' + data.azimuth + '</span><br>'
                ar += '<b>Оператор:</b> <span id="title">' + mnc.title + ' ('+mnc.id+')'+ '</span><br>'
                ar += '<b>Адрес:</b> <span id="address" @click="copyText(\'address\')">' + data.address +' '+this.getDirection(data.azimuth) +'</span><br>'
                ar += '<b>Дата и время:</b> '+(new Date()).toLocaleTimeString()+'<br>'
                ar += '<b>LAC и CID имп:</b><span id="lacCid" class="border-b border-green-400">'+this.lacCid +'</span>';
                this.bsInfo = ar;
            return this.bsInfo
            }catch (e){console.log('create inf error: '+e.message)}
            return null
        },
        createBs(inf){
            this.clearMap();
            let data = inf.mp
            let uid = data.lac+'_'+data.ci+'_'+data.azimuth
            let LatLon = [data.lat, data.lon]
            this.lacCid = inf.lac + ' ' + inf.cid
            this.currentLotLan = LatLon
            if(inf.mp == null) {
                this.bsInfo += '<b>Прим. адрес:</b><span id="_address">' + inf._address + '</span><br>';
            }
            try {
                let point = null;
                if(inf.lat && inf.lng){
                    point = L.circle([inf.lat, inf.lng], {radius: 20}).setStyle({fillColor: '#ff0000', opacity: 1}).addTo(this.map)
                    this.layers.push(point)
                }
                let bs = L.circle(LatLon, {radius: 15})
                    .setStyle({fillColor: '#0073ff', opacity: 1})
                    .bindPopup(this.createPopup(data))
                let sector = this.createSector(LatLon,  data)
                let BsSector = L.layerGroup([bs, sector],{uid: uid})
                this.layers.push(BsSector);
                BsSector.addTo(this.map)
                this.map.fitBounds(bs.getBounds())
            }catch (e) {
                console.log("create BS error: "+e.message)
            }
            this.map.setZoom(15)

        },
        strg(er){
            return er.replace('\\', '\\').toString()
        },
        async copyText(id){
            let elem = document.getElementById(id)
            console.log(elem.textContent)
            let range = document.createRange()
            range.selectNodeContents(elem)
            window.getSelection().addRange(range)
            // let cpied = document.execCommand('copy')
            // if(cpied){
            //     console.log('copy')
            // }else{
            //     console.log('not copied')
            // }
            // window.getSelection().removeRange(range);
        },
        createPopup(data, bs){
            return new L.Popup({
                autoPan: false,
                autoPanPadding: L.point(100, 900),
                keepInView: false
            }).setLatLng(data.lat+", "+data.lon)
                .setContent(this.createInfo(data))
        },
        createSector(LatLon, data){
            let mnc = this.operators.find(e=>e.id === data.mnc)
            return window.L.semiCircle(LatLon, {
                radius: 500,
                color: mnc.color
            }).setDirection(parseInt(data.azimuth), 65)
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
        objectInputFormating(){
            if(this.request.object_type === "Phone") {
                this.request.object = this.request.object.replace(/^\+?((996)|(0))|\s|_|\t|\n|\D/g, '')
            }else if(this.request.object_type === "IMEI"){
                let imeiLen = this.request.object.length
                if(imeiLen > 14){
                    this.request.object = this.request.object.substring(0, imeiLen-1)
                }
            }
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
        getDirection(azimuth){
            return window.azimuthToText(azimuth);
        },
    }
}
</script>

<style scoped>

</style>
