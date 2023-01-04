<button
    id="{{$domain}}"
    type="{{$type}}"
    class="{{$class}} z-10 bg-blue-500 py-2 px-4 uppercase text-sm font-medium relative overflow-hidden text-white duration-200 ease-in  hover:bg-blue-400   hover:shadow shadow  rounded  button "
    {{$attributes}}
    >
    {{$domain ? __($domain) : $slot}}
</button>
