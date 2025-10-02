@extends('admin.layouts.shared')
@section('title', 'User Activities')
@section('header-title', 'User Activities')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                </div>
                <div class="card-body">
                    <table id="datatables-reponsive" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>S:NO</th>
                                <th>Name</th>
                                <th>Activity Type</th>
                                <th>Details</th>
                                <th>Ip Address</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($userActivities as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->user->name ?? '' }}</td>
                                    <td>{{ $user->activity_type ?? '' }}</td>
                                    <td>{{ $user->details }}</td>
                                    <td>
                                        {{ $user->ip_address ?? '' }}
                                    </td>
                                    <td>{{ $user->created_at ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
