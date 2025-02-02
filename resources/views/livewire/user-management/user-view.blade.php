<div class="container-fluid px-3 px-md-4">
    <div class="row mt-5 mb-2">
        <div class="col-6">
            <h3 class="p-0 mb-0">{{ $title ?? '' }}</h3>
            @if (isset($title))
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" wire:navigate>Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $title ?? '' }}</li>
                </ol>
            @endif
        </div>
        <div class="col-6  text-end">
         <div class="form-btn-group">
            <a class="btn btn-outline-dark  me-0" href="{{ App\Helpers\AppHelper::getPreviousUrl('user-list') }}" wire:navigate>Back</a>
         </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>Name:</th>
                            <td>{{ $data->name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $data->email }}</td>
                        </tr>
                        <tr>
                            <th>Role:</th>
                            <td>{{ \App\Helpers\AppHelper::getRole($data->role) }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="{{ $data->status ? 'text-success' : 'text-danger' }}">
                                    {{ $data->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
