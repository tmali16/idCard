<template>
<v-app>
    <v-card>
        <div class="w-full" id="maps" style="height: 700px;">

        </div>
    </v-card>
</v-app>
</template>

<script>
export default {
    data() {
        return {}
    },
    mounted(){
        this.init();
    },
    methods:{
        init(){
            const map = L.map('maps', {
                attributionControl: false
            })
            map.setView(new L.LatLng(41.96766, 74.718018), 10)
            L.control.scale({
                imperial: false,
                position: 'bottomright'
            }).addTo(map);
            // L.Control.measureControl().addTo(map);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            L.polygon([
                    [42.8317644100961, 74.5803305050512],
                    [42.831962, 74.585717]
            ])
                .addTo(map)
                .bindPopup('A pretty CSS3 popup.<br> Easily customizable.')
                .openPopup();

            L.circle([42.831962, 74.585717])
                .addTo(map)
                .bindPopup('A pretty CSS3 popup.<br> Easily customizable.')
                .openPopup();
        }
    }
}
</script>

<style scoped>

</style>
