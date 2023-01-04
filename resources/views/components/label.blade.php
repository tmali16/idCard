<label for="{{$domain}}" class="{{$class}}">
    {{__($domain)}}
    @if (($required ?? true))
        <span class="text-red-600 ">*</span>
    @endif
</label>