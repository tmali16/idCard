@csrf
<div class="bg-white shadow overflow-hidden" x-data="createEmployeeApp()" x-init="init()">
    <div class="px-4 py-2 bg-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            {{trans('form.employee.create')}}
        </h3>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="border-t border-gray-200">
        <x-input domain="form.user.id" :value="$user" :lable="false" type="hidden"></x-input>
        <dl>
            <div class="bg-gray-50 px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                    <x-label domain="form.user.full_name"></x-label>
                </dt>
                <dd class="text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <x-input domain="form.user.full_name" :readonly="isset($method) && $method == 'pass'"  :value="$user" :lable="false"></x-input>
                </dd>
                <dt class="text-sm font-medium text-gray-500">
                    <x-label domain="form.user.username" ></x-label>
                </dt>
                <dd class="text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <x-input domain="form.user.username" :readonly="isset($method) && $method == 'pass'" :value="$user" :lable="false"></x-input>
                </dd>
                <dt class="text-sm font-medium text-gray-500">
                    <x-label domain="form.user.role"></x-label>
                </dt>
                <dd class="text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <x-select domain="form.user.roles[]" :readonly="isset($method) && $method == 'pass'" :options="$roles" :selected="$roles" :label="false"></x-select>
                </dd>
                @if(in_array(url()->current(),[route('user.create')]) || isset($method) && $method == 'pass')
                    <dt class="text-sm font-medium text-gray-500">
                        <x-label domain="form.user.password"></x-label>
                    </dt>
                    <dd class="text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <x-input domain="form.user.password" class="" type="password" :lable="false"></x-input>
                    </dd>
                @endif
{{--                <dt class="text-sm font-medium text-gray-500">--}}
{{--                    <x-label domain="form.user.permissions"></x-label>--}}
{{--                </dt>--}}
{{--                <dd class="text-sm text-gray-900 sm:mt-0 sm:col-span-2">--}}
{{--                    <x-select domain="form.user.permissions" multiple :options="$permissions" :selected="$user" :label="false"></x-select>--}}
{{--                </dd>--}}
            </div>
        </dl>
    </div>
    <div class="bg-gray-200 px-4 flex justify-end py-2">
        <x-submit domain="form.save"></x-submit>
    </div>
</div>
