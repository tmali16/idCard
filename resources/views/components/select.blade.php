@php
if ($selected != null){
    if (gettype($selected) == 'object'){
        $val = isset($selected->id) ? $selected->id : $selected->first()->id;
    }elseif(gettype($selected)){

    }else{
        $val = $selected;
    }
}else {
    $val = old($name);
}



@endphp
<div @class([
    'flex w-full',
    'flex-row'=>!$onside,
    'flex-col'=>($onside || $help),
])
>
@if ($label)
    <x-label :domain="$domain" :required="$required" :class="$label_class"></x-label>
@endif
<select
    id="{{$domain}}"
    name="{{$name}}{{$multiple? '[]':''}}"
    {{$required ? "required" : ""}}
    {{$attributes}} {{$multiple ? 'multiple' : ''}}
    @class([
        'block w-full py-1 px-2 border-1 focus:border-blue-500 border-gray-400 bg-white focus:outline-none focus:ring-blue-500 ',
        'border-red-600'=>$errors->login->first($name)
    ])>
    @if (isset($options) && !empty($options) && empty($uri))
        @if(!$multiple)
            <option value="0">--Выберите--</option>
        @endif
        @foreach ($options as $o)
            @if (gettype($o) === "object")
                <option value="{{$o->id}}" @if(isset($o->disabled) && $o->disabled) disabled="disabled" @endif @if($o->id == $val) selected @elseif(gettype($val) == 'array' && in_array($o->id, $val)) selected @endif>{{$o->title}}</option>
            @else
                <option value="{{$o}}" @if($o == $val) selected @endif>{{$o}}</option>
            @endif
        @endforeach
    @elseif(isset($opts))
        {{$opts}}
    @else
        <option value="null">нет данных</option>
    @endif
</select>
@empty($help)
    <p class="text-xs leading-none text-gray-300" role="alert">
        <strong>{{ $help }}</strong>
    </p>
@endempty
@error($name)
<p class="text-xs leading-none text-red-600" role="alert">
    <strong>{{ $message }}</strong>
</p>
@enderror
</div>
