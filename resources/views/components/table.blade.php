@props([
    'thead',
    'theadTr',
    'tbodytr',
    'paginate'
])
@php
    $thClass = 'px-6 py-2 text-left text-xs font-medium text-gray-800 uppercase tracking-wider border-r border-gray-200 border-r border-gray-300';
@endphp
<div class="flex flex-col">
    <table class="min-w-full divide-y divide-gray-200">
        @isset($thead)
            {{$thead}}
        @else
            <thead class="bg-gray-200">
            @isset($theadtr)
                {{$theadtr}}
            @else
                <tr>
                    @foreach($columns as $item)
                        @if(gettype($item)=== 'array')
                            @if(array_key_exists('permission', $item))
                                @permission($item['permission'])
                                <th scope="col" class="{{$thClass}}">
                                    {{__($item[0])}}
                                </th>
                                @endpermission
                            @endif
                        @else
                            <th scope="col" class="{{$thClass}}">
                                {{__($item)}}
                            </th>
                        @endif

                    @endforeach
                </tr>
            @endisset
            </thead>
        @endisset
        <tbody class="bg-white divide-y divide-gray-200">
        @isset($tbodytr)
            {{$tbodytr}}
        @else
            <tr>
                <td class="px-6 py-4 whitespace-nowrap"></td>
            </tr>
        @endisset
        </tbody>
    </table>
    @isset($paginate)
        {{$paginate}}
    @endisset
</div>
