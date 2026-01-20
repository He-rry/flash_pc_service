 @extends('layouts.app')

 @section('content')
 <div class="d-flex justify-content-between mb-3">
     <h4>Status Management</h4>
     <a href="{{ route('admin.statuses.create') }}" class="btn btn-primary">Add New Status</a>
 </div>

 <div class="card">
     <div class="card-body">
         <table class="table">
             <thead>
                 <tr>
                     <th>Name</th>
                     <th>Actions</th>
                 </tr>
             </thead>
             <tbody>
                 @foreach($statuses as $status)
                 <tr>
                     <td>{{ $status->status_name }}</td>
                     <td>
                         <a href="{{ route('admin.statuses.edit', $status->id) }}" class="btn btn-sm btn-info text-white">Edit</a>
                         <form action="{{ route('admin.statuses.destroy', $status->id) }}" method="POST" class="d-inline">
                             @csrf @method('DELETE')
                             <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                         </form>
                     </td>
                 </tr>
                 @endforeach
             </tbody>
         </table>
     </div>
 </div>
 @endsection