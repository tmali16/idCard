@extends("layouts.app")
@section('content')
    <div class="h-full w-full ">
        <form action="{{route('search_bs_')}}" method="post">
            @csrf
            <div class="px-0 m-4 md:grid md:grid-cols-3 sm:grid sm:grid-cols-6">
                <dl>
                    <div class="bg-gray-50 flex flex-col">
                        <dt class="text-sm font-medium text-gray-500 p-2">
                            <x-label domain="form.bs.name"></x-label>
                        </dt>
                        <dd class="text-sm text-gray-900 sm:mt-0 sm:col-span-2 px-2 my-3">
                            <x-input domain="form.bs.name" :lable="false" value="{{ (isset($name) ? $name : '') }}" class=""></x-input>
                        </dd>
                        <div class="bg-gray-200 px-4 flex justify-center py-2">
                            <x-submit domain="form.search" class="w-full"></x-submit>
                        </div>
                        @if(count($items) > 0)
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" >
                                <thead class="text-xs text-gray-700 uppercase bg-gray-300 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Название</th>
                                        <th scope="col" class="px-6 py-3">Действие</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $item)
                                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                        <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{$item->sector_name ?? $item->lac . "_" . $item->cid}}
                                        </td>
                                        <td class="px-6 py-4">
                                            <a class="font-medium text-blue-600 dark:text-blue-500 hover:underline" target="_blank" href="{{"https://info.o.kg/yourLocation.html?lat=".$item->site_lat."&lon=".$item->site_lon."&az=".$item->azimuth."&cr=1-500&abw=64"}}">Открыть</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </dl>
            </div>
        </form>
    </div>
@endsection
