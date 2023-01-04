@php
if($attributes->has('disabled')){
    $disabled = $attributes['disabled'];
}else{
    $disabled = false;
}
if ($value != null) {
    if (gettype($value) == 'object') {
        $val = $value->{$name};
    }else{
        $val = $value;
    }
}else {
   $val = old($name);
}
@endphp
<div class="{{$div_class}} flex w-full @if($onside === 'true') flex-row @else flex-col @endif ">
    @if($lable)
        <x-label :domain="$domain" :required="$required" :class="$lableClass" ></x-label>
    @endif
    <div class="@isset($lableClass) @endisset">
        <input
            name="{{$name}}"
            id="{{$domain}}"
            placeholder="{{__($domain)}}"
            type="{{$type ?? 'text'}}"
            @if ($val)
            value="{{$val}}"
            @endif
            {{$attributes}}
            @if ($required) required @endif
            @if($disabled) disabled @endif
            class="{{$class}} @error($name) border-red-600 @enderror w-full focus:bg-white block py-1 outline-none px-2 sm:text-sm border-1 border-gray-400"
        />
        @isset($help)
            <small>{{$help}}</small>
        @endisset
        @error($name)
            <span class="text-sm text-red-600" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
