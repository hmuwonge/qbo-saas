@extends('layouts.main')

@section('content')
<div class="w-full my-6">
    <div class="overflow-x-auto relative shadow-md sm:rounded-md table-responsive">
        <table class="w-full text-sm text-left text-gray-400 table table-striped table-bordered">
            <thead class="text-sm font-medium text-black uppercase bg-secondary">
                <tr>
                    <th scope="col" class="py-3 px-6">
                        Activity Time
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Error Message
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Short Server response
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Full Server Response
                    </th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-base bg-white">
                @foreach($data as $index => $error)
                <tr class="bg-red-50">
                    <th scope="row" class="py-4 px-6 font-bold text-gray-900 whitespace-nowrap">
                        {{ $error['activity_time'] }}
                    </th>
                    <td class="py-4 px-6 font-bold">
                        <p>
                            <span>{!! $error['error_message'] !!}</span><br>
                        </p>
                    </td>
                    <td class="py-4 px-6">
                        <p>
                            @if(!is_object($error['server_response']))
                                <span>{!! $error['short_server_response'] !!}</span>
                            @endif
                        </p>
                    </td>
                    <td class="py-4 px-6">
                        <p>
                            <span>{!! $error['full_server_response'] !!}</span>
                        </p>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if ($data->hasPages())
        <div class="pagination-wrapper my-1">
            <nav aria-label="Page navigation">
                {{ $data->links() }}
            </nav>
        </div>
    @endif

        {{-- <pagination class="m-6" :links="{{ json_encode($data['links']) }}" /> --}}
    </div>
</div>

@endsection
