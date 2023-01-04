@extends('layouts.app')

@section('content')

    <div class="container w-full md:w-4/5 xl:w-3/5 mx-auto px-2">
        <!--Card-->
        <div id='recipients' class="p-8 mt-6 lg:mt-0 rounded shadow bg-white">
            <table id="example" class="w-full my-9" >
                <thead>
                    <tr class="">
                        <th class="bg-gray-500 py-2 border-r w-8">ID</th>
                        <th class="bg-gray-500 py-2 border-r">Name</th>
                        <th class="bg-gray-500 py-2 border-r">Username</th>
                        <th class="bg-gray-500 py-2 border-r">Role</th>
                        <th class="bg-gray-500 py-2 border-r">Permission</th>
                        <th class="bg-gray-500 py-2 border-r">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr class="border-t">
                        <td class="px-6 py-1 border-r">{{$loop->index+1}}</td>
                        <td class="px-6 py-1 border-r">{{$user->full_name}}</td>
                        <td class="px-6 py-1 border-r">{{$user->username}}</td>
                        <td class="px-6 py-1 border-r">{{$user->roles->pluck('name')->implode(',')}}</td>
                        <td class="px-6 py-1 border-r">{!! $user->rolePermissions()->get()->map(fn($v)=>'<span class="bg-blue-500 px-2 py-0.5 text-white rounded" style="font-size:10px">'.$v->slug.'</span>')->implode(' ')!!}</td>
                        <td class="px-6 py-1 border-r">
                            <a href="/user/crud/{{$user->id}}?method=edit" class="px-4 py-1 rounded-md bg-green-500 text-white">edit</a>
                            <a href="/user/crud/{{$user->id}}?method=delete" class="px-4 py-1 rounded-md">del</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <!--/Card-->
    </div>
@endsection
