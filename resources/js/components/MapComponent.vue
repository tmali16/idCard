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
<!--                    <label class="m-0 p-0">LAC:</label>-->
                    <v-text-field dense type="text" label="LAC:CELLID" hide-details flat light class="p-0 m-0 border-b" v-model="reqData.Lac" />
<!--                </div>-->
<!--                <div class="flex gap-x-4 items-center justify-start">-->
<!--                    <label class="m-0 p-0">CID:</label>-->
<!--                    <v-text-field dense type="number" label="CID" hide-details flat light class="p-0 m-0" v-model="reqData.Cid" />-->
                </div>
            </v-card-text>
            <v-card-actions>
                <v-btn elevation="0" @click="getBs()" class="w-32" color="success">Поиск</v-btn>
                <v-btn elevation="0" @click="clearMap()" size="sm" class="max-w-full" >Отчистить</v-btn>
            </v-card-actions>
        </v-card>
        <div class="md:hidden w-full my-2 border px-3" v-html="bsInfo"></div>
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
            reqData:{
                Lac: '',
                Cid: '',
                mnc: '1',
            },
            saveImg:{
                state:false,
                src: ''
            },
            operators: [{id: 1, title:'Билайн'}, {id: 5, title:'Мегаком'}, {id: 9, title:'О!'}],
            map: null,
            layers:[],
            currentLotLan: '',
            defZoom: 15,
            toast:{
                state: false,
                type: 'info',
                message: '',
            },
            bsInfo: '',
        }
    },
    mounted(){
        this.init();
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
            if (this.reqData.Lac.length >= 2 && this.reqData.mnc.length !== 0) {
                axios.post('/api/geo/search', this.reqData).then(o => {
                    if(o.data.status !== undefined && parseInt(o.data.status) !== 200){
                        this.showToast(o.data.messages, "error")
                    }
                    if(Array.isArray(o.data)) {
                        if(o.data.length !== 0){
                            o.data.forEach((e)=>{
                                this.createBs(e)
                            })
                        }else{
                            this.showToast("Не найдено")
                        }
                    }
                }).catch(e => {
                    console.log(e.message)
                })
            }else{
                this.showToast("Не все поля заполнены", "error")
            }
        },
        createSector(LatLon, direction){
            return new Radar({
                    radius:450, //Radius of radar sector,The unit is meter
                    angle:65, //Fan opening and closing angle 0-360
                    direction: parseInt(direction), // Fan orientation angle 0-360
                    location: LatLon.join(", ") // Longitude dimension of sector start position
                },
                {
                    online: {
                        color: '#ff0046',
                        // dashArray: [255, 2],
                        weight: 1,
                        opacity: 1,
                        fillColor: "#d21",
                        fillOpacity: 0.3,
                    },
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
            ar += '<b>Азимут:</b> ' + data.azimuth + '<br>'
            ar += '<b>Оператор:</b> ' + mnc.title + ' ('+mnc.id+')'+ '<br>'
            ar += '<b>Адрес:</b> ' + data.address + '<br>';
            ar += '<b>LAT:</b> ' + data.lat + '<br>';
            ar += '<b>LON:</b> ' + data.lon + '<br>';
            this.bsInfo = ar;
            return ar
        },
        createPopup(data, bs){
            return L.popup({
                autoPanPaddingTopLeft: [0, 30],
                autoPanPaddingBottomRight: [0, 30],
            }).setLatLng(data.lat+", "+data.lon)
            .setContent(this.createInfo(data))
        },
        createBs(data){
            let LatLon = [data.lat, data.lon]
            let sector = this.createSector(LatLon,  data.azimuth)

            let icon = L.icon({
                iconSize: [48, 48],
                iconAnchor: [24, 48],
                popupAnchor:  [-2, -24],
                iconUrl: '/images/antenna.png',
            })
            let bs = L.circle(LatLon)
                .setStyle({fillColor: '#0073ff', opacity: 1,})
            let antenna = L.marker(LatLon, {icon: icon})
                .bindPopup(this.createPopup(data, bs))
                .openPopup()

            let BsSector = L.layerGroup([bs,antenna,sector])
            this.layers.push(BsSector)
            BsSector.addTo(this.map)
            this.map.fitBounds(bs.getBounds())
            this.map.setZoom(this.defZoom)
            this.currentLotLan = LatLon
        },
        clearMap(){
            for (let i = 0; i < this.layers.length; i++){
                if(this.layers[i] != null) {
                    this.map.removeLayer(this.layers[i])
                }
            }
        },
        getImage(){
            this.map.setView(this.currentLotLan)
            // this.map.setZoom(19)
            html2canvas(document.getElementById("maps"), {useCORS: true}).then((canvas)=>{
                this.saveImg.src = canvas.toDataURL()
                this.saveImg.state = true;
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
        }
    }
}
</script>

<style scoped>
.leaflet-right  a{
    display: none;
}
</style>
